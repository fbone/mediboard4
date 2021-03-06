<?php
/**
 * $Id: httpreq_vw_blood_salvage_personnel.php 20938 2013-11-13 11:02:47Z aurelie17 $
 *
 * @package    Mediboard
 * @subpackage bloodSalvage
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 20938 $
 */

CAppUI::requireModuleFile("bloodSalvage", "inc_personnel");

$blood_salvage_id = CValue::getOrSession("blood_salvage_id");
$blood_salvage = new CBloodSalvage();

$date  = CValue::getOrSession("date", CMbDT::date());

$modif_operation = CCanDo::edit() || $date >= CMbDT::date();

$list_nurse_sspi= CPersonnel::loadListPers("reveil");

$tabAffected = array();
$timingAffect = array();

if ($blood_salvage_id) {
  $blood_salvage->load($blood_salvage_id);
  loadAffected($blood_salvage_id, $list_nurse_sspi, $tabAffected, $timingAffect);
}

$smarty = new CSmartyDP();

$smarty->assign("modif_operation",  $modif_operation);
$smarty->assign("list_nurse_sspi",  $list_nurse_sspi);
$smarty->assign("blood_salvage",    $blood_salvage);
$smarty->assign("tabAffected",      $tabAffected);
$smarty->assign("timingAffect",     $timingAffect);

$smarty->display("inc_vw_blood_salvage_personnel.tpl");
