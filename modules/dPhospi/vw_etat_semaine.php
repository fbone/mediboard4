<?php
/**
 * $Id: vw_etat_semaine.php 21839 2014-01-30 14:54:52Z rhum1 $
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 21839 $
 */

CCanDo::checkRead();

$type_admission = CValue::getOrSession("type_admission", "ambucomp");

// Liste des chirurgiens
$listChirs = array();
$listPats = array();

// R�cup�ration des admissions � affecter
function loadSejourNonAffectes($where) {
  global $listChirs, $listPats, $listFunctions;

  $group = CGroups::loadCurrent();
  
  $leftjoin = array(
    "affectation"     => "sejour.sejour_id = affectation.sejour_id"
  );
  $where["sejour.group_id"] = "= '$group->_id'";
  $where[] = "affectation.affectation_id IS NULL";
  
  $sejourNonAffectes = new CSejour;
  $sejourNonAffectes = $sejourNonAffectes->loadList($where, null, null, null, $leftjoin);
  
  foreach ($sejourNonAffectes as $keySejour => $valSejour) {
    $sejour =& $sejourNonAffectes[$keySejour];
  }
  
  return $sejourNonAffectes;
}

$today = CMbDT::date()." 01:00:00";
$to = CMbDT::dateTime("-1 second", $today);
$list = array();
for ($i = 1; $i <= 7; $i++) {
  $from = CMbDT::dateTime("+1 second", $to);
  $to = CMbDT::dateTime("+1 day", $to);
  $where = array();
  $where["annule"] = "= '0'";
  switch ($type_admission) {
    case "ambucomp" :
      $where[] = "sejour.type = 'ambu' OR sejour.type = 'comp' OR sejour.type = 'ssr'";
      break;
    case "0" :
      break;
    default :
      $where["sejour.type"] = "= '$type_admission'"; 
  }
  $where["sejour.entree"] = "BETWEEN '$from' AND '$to'";
  $list[$from] = loadSejourNonAffectes($where);
}

// Cr�ation du template
$smarty = new CSmartyDP();


$smarty->assign("list" , $list);
$smarty->assign("type_admission" , $type_admission);

$smarty->display("vw_etat_semaine.tpl");

