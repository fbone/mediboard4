<?php
/**
 * $Id: httpreq_vw_sorties.php 28566 2015-06-10 12:20:52Z aurelie17 $
 *
 * @category Admissions
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision: 28566 $
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();
$type            = CValue::getOrSession("type");
$services_ids    = CValue::getOrSession("services_ids");
$prat_id         = CValue::getOrSession("prat_id");
$only_confirmed  = CValue::getOrSession("only_confirmed");
$selSortis       = CValue::getOrSession("selSortis", "0");
$order_col       = CValue::getOrSession("order_col", "patient_id");
$order_way       = CValue::getOrSession("order_way", "ASC");
$date            = CValue::getOrSession("date", CMbDT::date());
$next            = CMbDT::date("+1 DAY", $date);
$filterFunction  = CValue::getOrSession("filterFunction");
$period          = CValue::getOrSession("period");
$enabled_service = CValue::getOrSession("active_filter_services", 0);

if (is_array($services_ids)) {
  CMbArray::removeValue("", $services_ids);
}

$date_actuelle = CMbDT::dateTime("00:00:00");
$date_demain   = CMbDT::dateTime("00:00:00", "+ 1 day");

$hier   = CMbDT::date("- 1 day", $date);
$demain = CMbDT::date("+ 1 day", $date);

$date_min = CMbDT::dateTime("00:00:00", $date);
$date_max = CMbDT::dateTime("23:59:59", $date);
//$date_min = "2012-09-23 00:00:00";
//$date_max = "2012-09-25 23:59:59";

if ($period) {
  $hour = CAppUI::conf("dPadmissions hour_matin_soir");
  if ($period == "matin") {
    // Matin
    $date_max = CMbDT::dateTime($hour, $date);
  }
  else {
    // Soir
    $date_min = CMbDT::dateTime($hour, $date);
  }
}

// Sorties de la journ�e
$sejour = new CSejour();

$group = CGroups::loadCurrent();

// Lien avec les patients et les praticiens
$ljoin["patients"]    = "sejour.patient_id = patients.patient_id";
$ljoin["users"]       = "sejour.praticien_id = users.user_id";

// Filtre sur les services
if (count($services_ids) && $enabled_service) {
  $ljoin["affectation"]        = "affectation.sejour_id = sejour.sejour_id AND affectation.sortie = sejour.sortie";
  $where["affectation.service_id"] = CSQLDataSource::prepareIn($services_ids);
}

// Filtre sur le type du s�jour
if ($type == "ambucomp") {
  $where[] = "`sejour`.`type` = 'ambu' OR `sejour`.`type` = 'comp'";
}
elseif ($type) {
  if ($type !== 'tous') {
    $where["sejour.type"] = " = '$type'";
  }
}
else {
  $where[] = "`sejour`.`type` != 'urg' AND `sejour`.`type` != 'seances'";
}

// Filtre sur le praticien
if ($prat_id) {
  $where["sejour.praticien_id"] = " = '$prat_id'";
}

// Seulement les confirm�es par le praticien
if ($only_confirmed) {
  $where["sejour.confirme"] = "IS NOT NULL";
}

$where["sejour.group_id"] = "= '$group->_id'";
$where["sejour.sortie"]   = "BETWEEN '$date_min' AND '$date_max'";
$where["sejour.annule"]   = "= '0'";

switch ($selSortis) {
  case 'np':
    $where['sortie_preparee'] = "= '0'";
    break;

  case 'n':
    $where[] = "(sortie_reelle IS NULL)";
    break;

  default:
}

if ($order_col != "patient_id" && $order_col != "sortie_prevue" && $order_col != "praticien_id") {
  $order_col = "patient_id";
}

if ($order_col == "patient_id") {
  $order = "patients.nom $order_way, patients.prenom $order_way, sejour.sortie_prevue";
}

if ($order_col == "sortie_prevue") {
  $order = "sejour.sortie_prevue $order_way, patients.nom, patients.prenom";
}

if ($order_col == "praticien_id") {
  $order = "users.user_last_name $order_way, users.user_first_name";
}

/** @var CSejour[] $sejours */
$sejours = $sejour->loadList($where, $order, null, null, $ljoin);

$patients   = CStoredObject::massLoadFwdRef($sejours, "patient_id");
CStoredObject::massLoadFwdRef($sejours, "etablissement_sortie_id");
CStoredObject::massLoadFwdRef($sejours, "service_sortie_id");
$praticiens = CStoredObject::massLoadFwdRef($sejours, "praticien_id");
$functions  = CStoredObject::massLoadFwdRef($praticiens, "function_id");
CStoredObject::massLoadBackRefs($sejours, "affectations");

// Chargement optimis�e des prestations
CSejour::massCountPrestationSouhaitees($sejours);

CStoredObject::massLoadBackRefs($sejours, "notes");
CStoredObject::massLoadBackRefs($patients, "dossier_medical");

$operations = CStoredObject::massLoadBackRefs($sejours, "operations", "date ASC", array("annulee" => "= '0'"));
CStoredObject::massLoadBackRefs($operations, "actes_ngap", "lettre_cle DESC");

$order = "code_association, code_acte,code_activite, code_phase, acte_id";
CStoredObject::massLoadBackRefs($operations, "actes_ccam", $order);

// Chargement des NDA
CSejour::massLoadNDA($sejours);
// Chargement des IPP
CPatient::massLoadIPP($patients);

$maternite_active = CModule::getActive("maternite");

foreach ($sejours as $sejour_id => $_sejour) {
  // Filtre sur la fonction du praticien
  $praticien = $_sejour->loadRefPraticien(1);
  if ($filterFunction && $filterFunction != $praticien->function_id) {
    unset($sejours[$sejour_id]);
    continue;
  }

  // Chargement du patient
  $_sejour->loadRefPatient();

  // Chargements des notes sur le s�jour
  $_sejour->loadRefsNotes();

  // Chargement des interventions
  $whereOperations = array("annulee" => "= '0'");
  $_sejour->loadRefsOperations($whereOperations);

  foreach ($_sejour->_ref_operations as $operation) {
    $operation->loadRefsActes();
  }

  // Chargement des affectation
  $_sejour->loadRefsAffectations();
  
  if ($maternite_active && $_sejour->grossesse_id) {
    $_sejour->_sejours_enfants_ids = CMbArray::pluck($_sejour->loadRefsNaissances(), "sejour_enfant_id");
  }
  
  // Chargement des modes de sortie
  $_sejour->loadRefEtablissementTransfert();
  $_sejour->loadRefServiceMutation();
  // Chargement des appels
  $_sejour->loadRefsAppel('sortie');

}

// Si la fonction selectionn�e n'est pas dans la liste des fonction, on la rajoute
if ($filterFunction && !array_key_exists($filterFunction, $functions)) {
  $_function = new CFunctions();
  $_function->load($filterFunction);
  $functions[$filterFunction] = $_function;
}

$list_mode_sortie = array();
if (CAppUI::conf("dPplanningOp CSejour use_custom_mode_sortie")) {
  $mode_sortie = new CModeSortieSejour();
  $where = array(
    "actif" => "= '1'",
  );
  $list_mode_sortie = $mode_sortie->loadGroupList($where);
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("hier"            , $hier);
$smarty->assign("demain"          , $demain);
$smarty->assign("date_min"        , $date_min);
$smarty->assign("date_max"        , $date_max);
$smarty->assign("date_demain"     , $date_demain);
$smarty->assign("date_actuelle"   , $date_actuelle);
$smarty->assign("date"            , $date);
$smarty->assign("type"            , $type);
$smarty->assign("selSortis"       , $selSortis);
$smarty->assign("order_col"       , $order_col);
$smarty->assign("order_way"       , $order_way);
$smarty->assign("sejours"         , $sejours);
$smarty->assign("prestations"     , CPrestation::loadCurrentList());
$smarty->assign("canAdmissions"   , CModule::getCanDo("dPadmissions"));
$smarty->assign("canPatients"     , CModule::getCanDo("dPpatients"));
$smarty->assign("canPlanningOp"   , CModule::getCanDo("dPplanningOp"));
$smarty->assign("functions"       , $functions);
$smarty->assign("filterFunction"  , $filterFunction);
$smarty->assign("period"          , $period);
$smarty->assign("list_mode_sortie", $list_mode_sortie);
$smarty->assign('enabled_service' , $enabled_service);

$smarty->display("inc_vw_sorties.tpl");
