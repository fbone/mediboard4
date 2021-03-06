<?php
/**
 * $Id: ajax_edit_evenement_perop.php 26484 2014-12-18 09:07:56Z phenxdesign $
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 26484 $
 */

$evenement_guid = CValue::get("evenement_guid");
$operation_id   = CValue::get("operation_id");
$datetime       = CValue::get("datetime");

$interv = new COperation;
$interv->load($operation_id);
$interv->loadRefAnesth();

if (!$datetime) {
  $datetime = CMbDT::date($interv->_datetime)." ".CMbDT::time();
}

list($evenement_class, $evenement_id) = explode("-", $evenement_guid);

/** @var CAnesthPerop $evenement */
$evenement = new $evenement_class;

if ($evenement_id) {
  $evenement->load($evenement_id);
  $evenement->loadRefsNotes();
}
else {
  $evenement->datetime = $datetime;
}
$evenement->operation_id = $interv->_id;

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("evenement", $evenement);
$smarty->assign("datetime",  $datetime);
$smarty->assign("operation", $interv);
$smarty->display("inc_edit_evenement_perop.tpl");
