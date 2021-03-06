<?php 
/**
 * $Id: print_noncote.php 23929 2014-07-09 08:42:32Z aurelie17 $
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision: 23929 $
 */

CCanDo::checkEdit();
// R�cup�ration des param�tres
$filter = new CPlageconsult();
$filter->_date_min = CValue::getOrSession("_date_min", CMbDT::date());
$filter->_date_max = CValue::getOrSession("_date_max", CMbDT::date());
$filter->_type_affichage  = CValue::getOrSession("_type_affichage" , 1);

// Filtre sur les praticiens
$chir_id = CValue::getOrSession("chir");
$listPrat = CConsultation::loadPraticiensCompta($chir_id);

// On recherche toutes les consultations non cot�s
$ljoin = array();
$ljoin["plageconsult"] = "consultation.plageconsult_id = plageconsult.plageconsult_id";

$where = array();
$where["tarif"] = " IS NULL ";
$where["codes_ccam"] = " IS NULL";
$where["patient_id"] = " IS NOT NULL";
$where["secteur1"] = " = 0";
$where["secteur2"] = " = 0";
$where["plageconsult.chir_id"] = CSQLDataSource::prepareIn(array_keys($listPrat));
$where["plageconsult.date"] = "BETWEEN '$filter->_date_min' AND '$filter->_date_max'";

$order = "plageconsult.date, plageconsult.chir_id";

$consultation = new CConsultation();
/** @var CConsultation[] $listConsults */
$listConsults = $consultation->loadList($where, $order, null, null, $ljoin);

$listConsults_date = array();
foreach ($listConsults as $consult) {
  $consult->loadRefPatient();
  $consult->loadRefPlageConsult();
  $listConsults_date[$consult->_ref_plageconsult->date]["consult"][$consult->_id] = $consult;
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("today"              , CMbDT::date());
$smarty->assign("filter"             , $filter);
$smarty->assign("listPrat"           , $listPrat);
$smarty->assign("listConsults"       , $listConsults);
$smarty->assign("listConsults_date"  , $listConsults_date);

$smarty->display("print_noncote.tpl");