<?php
/**
 * $Id: httpreq_vw_recuperation_start_timing.php 24465 2014-08-19 09:55:18Z flaviencrochard $
 *
 * @package    Mediboard
 * @subpackage bloodSalvage
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 24465 $
 */

CCanDo::checkRead();
$blood_salvage_id = CValue::getOrSession("blood_salvage_id");
$date             = CValue::getOrSession("date", CMbDT::date());
$modif_operation  = CCanDo::edit() || $date >= CMbDT::date();
$timing           = CValue::getOrSession("timing");

$blood_salvage = new CBloodSalvage();
if ($blood_salvage_id) {
  $blood_salvage->load($blood_salvage_id);
  $timing["_recuperation_start"]       = array();
  $max_add_minutes = CAppUI::conf("dPsalleOp max_add_minutes");
  foreach ($timing as $key => $value) {
    for ($i = -CAppUI::conf("dPsalleOp max_sub_minutes"); $i < $max_add_minutes && $blood_salvage->$key !== null; $i++) {
      $timing[$key][] = CMbDT::time("$i minutes", $blood_salvage->$key);
    }
  }
}
// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("blood_salvage"  , $blood_salvage  );
$smarty->assign("date"           , $date           );
$smarty->assign("modif_operation", $modif_operation);
$smarty->assign("timing",          $timing);

$smarty->display("inc_vw_recuperation_start_timing.tpl");
