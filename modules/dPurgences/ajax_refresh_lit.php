<?php
/**
 * $Id: ajax_refresh_lit.php 23628 2014-06-19 14:22:01Z nicolasld $
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 23628 $
 */

CCanDo::checkEdit();

$rpu_id         = CValue::get("rpu_id");
$sortie_reelle  = CValue::get("sortie_reelle");

// Chargement du RPU
$rpu = new CRPU();
$rpu->load($rpu_id);
$rpu->loadRefSejourMutation();

// Chargement du s�jour
$sejour = $rpu->loadRefSejour();
$sejour->loadRefPatient()->loadIPP();
$sejour->loadNDA();
$sejour->loadRefsConsultations();
$sejour->loadRefCurrAffectation();

// Horaire par d�faut
$sejour->sortie_reelle = $sortie_reelle;
if (!$sejour->sortie_reelle) {
  $sejour->sortie_reelle = CMbDT::dateTime();
}

$where = array();
$where["entree"] = "<= '$sejour->sortie_reelle'";
$where["sortie"] = ">= '$sejour->sortie_reelle'";
//on recherche uniquement les lits bloqu�s pour les urgences(function_id == function_id d'urgence)
$where["function_id"] = "IS NOT NULL";

$affectation = new CAffectation();
/** @var CAffectation[] $blocages_lit */
$blocages_lit = $affectation->loadList($where);

$where["function_id"] = "IS NULL";

foreach ($blocages_lit as $blocage) {
  $blocage->loadRefLit()->loadRefChambre()->loadRefService();
  $where["lit_id"] = "= '$blocage->lit_id'";

  if ($affectation->loadObject($where)) {
    $affectation->loadRefSejour();
    $patient = $affectation->_ref_sejour->loadRefPatient();

    $blocage->_ref_lit->_view .= " indisponible jusqu'� ".CMbDT::transform($affectation->sortie, null, "%Hh%Mmin %d-%m-%Y");
    $blocage->_ref_lit->_view .= " (".$patient->_view." (".strtoupper($patient->sexe).") ";
    $blocage->_ref_lit->_view .= CAppUI::conf("dPurgences age_patient_rpu_view") ? $patient->_age.")" : ")" ;
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("sejour", $sejour);
$smarty->assign("rpu"   , $rpu);
$smarty->assign("blocages_lit", $blocages_lit);

$smarty->display("inc_form_sortie_lit.tpl");
