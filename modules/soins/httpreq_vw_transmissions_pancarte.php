<?php
/**
 * $Id: httpreq_vw_transmissions_pancarte.php 25809 2014-11-10 09:14:30Z asmiane $
 *
 * @category Soins
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision: 25809 $
 * @link     http://www.mediboard.org
 */

$service_id = CValue::get("service_id");
$date       = CValue::get("date");
$date_min   = CValue::get("date_min");
$user_id    = CValue::get("user_id");
$degre      = CValue::get("degre");
$load_transmissions = CValue::get("transmissions");
$load_observations = CValue::get("observations");
$refresh = CValue::get("refresh");
$real_time = CValue::getOrSession('real_time', 0);

if ($date == CMbDT::date()) {
  $date_max = CMbDT::dateTime();
}
else {
  $date_max = CMbDT::date("+ 1 DAY", $date)." 00:00:00";
}

$nb_hours = CAppUI::conf("soins Pancarte transmissions_hours", CGroups::loadCurrent()->_guid);
$date_min = CMbDT::dateTime(" - $nb_hours HOURS", $date_max);

$order_col = CValue::get("order_col", "date");
$order_way = CValue::get("order_way", "DESC");

// Chargement du service
$service = new CService();
$service->load($service_id);
$transmissions = array();
$observations = array();
$users = array();
$ljoin = array();
$where = array();
$where["affectation.service_id"] = " = '$service_id'";

if ($real_time) {
  $time = CMbDT::time();
  $where["affectation.entree"] = " <= '$date $time'";
  $where["affectation.sortie"] = " >= '$date $time'";
}
else {
  $where[] = "date >= '$date_min' AND date <= '$date_max'";
}
if ($user_id) {
  $where["user_id"] = " = '$user_id'";
}
if ($degre) {
  if ($degre == "urg_normal") {
    $where["degre"] = "IN('low', 'high')";
  }
  if ($degre == "urg") {
    $where["degre"] = "= 'high'";
  }
}
  
// Chargement des transmissions
if ($load_transmissions == "1") {
  $ljoin["sejour"] = "transmission_medicale.sejour_id = sejour.sejour_id";
  $ljoin["affectation"] = "sejour.sejour_id = affectation.sejour_id";

  $transmission = new CTransmissionMedicale();
  $transmissions = $transmission->loadList($where, null, null, "transmission_medicale_id", $ljoin);
}

// Chargement des observations
if ($load_observations == "1") {
  $ljoin["sejour"] = "observation_medicale.sejour_id = sejour.sejour_id";
  $ljoin["affectation"] = "sejour.sejour_id = affectation.sejour_id";

  $observation = new CObservationMedicale();
  $observations = $observation->loadList($where, null, null, "observation_medicale_id", $ljoin);
}

$cibles = array();
$trans_and_obs = array();
foreach ($transmissions as $_transmission) {
  $_transmission->loadRefsFwd();
  $_transmission->_ref_sejour->loadRefPatient();
  $_transmission->_ref_sejour->loadRefsAffectations();
  $_transmission->_ref_sejour->_ref_last_affectation->loadRefLit();
        
  $patient = $_transmission->_ref_sejour->_ref_patient;
  $lit = $_transmission->_ref_sejour->_ref_last_affectation->_ref_lit;
  
  if ($order_col == "patient_id") {
    $key = $patient->nom.$patient->prenom.$patient->_id.$_transmission->date;
  }
  if ($order_col == "date") {
    $key= $_transmission->date;
  }
  if ($order_col == "lit_id") {
    $key = $lit->_view.$lit->_id.$_transmission->date;
  }
  $_transmission->calculCibles($cibles);
  $trans_and_obs[$key][$_transmission->_id] = $_transmission;
  $_transmission->_ref_user->loadRefFunction();
  $users[$_transmission->user_id] = $_transmission->_ref_user; 
}

foreach ($observations as $_observation) {
  $_observation->loadRefsFwd();
  $_observation->_ref_sejour->loadRefPatient();
  $_observation->_ref_sejour->loadRefsAffectations();
  $_observation->_ref_sejour->_ref_last_affectation->loadRefLit();
  
  $patient = $_observation->_ref_sejour->_ref_patient;
  $lit = $_observation->_ref_sejour->_ref_last_affectation->_ref_lit;
  
  if ($order_col == "patient_id") {
    $key = $patient->nom.$patient->prenom.$patient->_id.$_observation->date;
  }
  if ($order_col == "date") {
    $key= $_observation->date;
  }
  if ($order_col == "lit_id") {
    $key = $lit->_view.$lit->_id.$_observation->date;
  }
  $trans_and_obs[$key][$_observation->_id] = $_observation;
  $_observation->_ref_user->loadRefFunction();
  $users[$_observation->user_id] = $_observation->_ref_user; 
}

// Tri du tableau
if ($order_way == "ASC") {
  ksort($trans_and_obs);
}
else {
  krsort($trans_and_obs);
}

$filter_obs = new CObservationMedicale();
$filter_obs->degre = $degre;
$filter_obs->user_id = $user_id;

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("order_way"     , $order_way);
$smarty->assign("order_col"     , $order_col);
$smarty->assign("cibles"        , $cibles);
$smarty->assign("service"       , $service);
$smarty->assign("transmissions" , $transmissions);
$smarty->assign("observations"  , $observations);
$smarty->assign("trans_and_obs" , $trans_and_obs);
$smarty->assign("filter_obs"    , $filter_obs);
$smarty->assign("users"         , $users);
$smarty->assign("with_filter"   , "1");
$smarty->assign("date_min"      , $date_min);
$smarty->assign("date_max"      , $date_max);
$smarty->assign('real_time'     , $real_time);

if ($user_id || $degre || $refresh) {
  $smarty->display('../../dPprescription/templates/inc_vw_transmissions.tpl'); 
}
else {
  $smarty->display('inc_vw_transmissions_pancarte.tpl'); 
}
