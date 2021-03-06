<?php
/**
 * $Id: ajax_edit_ex_constraint.php 17759 2013-01-14 11:27:24Z phenxdesign $
 *
 * @package    Mediboard
 * @subpackage forms
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 17759 $
 */

CCanDo::checkEdit();

$ex_constraint_id  = CValue::get("ex_constraint_id");
$ex_class_event_id = CValue::get("ex_class_event_id");

$ex_constraint = new CExClassConstraint;

if (!$ex_constraint->load($ex_constraint_id)) {
  $ex_constraint->ex_class_event_id = $ex_class_event_id;
}
else {
  $ex_constraint->loadRefsNotes();
}

$ex_constraint->loadTargetObject();
$event = $ex_constraint->loadRefExClassEvent();

$options = $event->getHostClassOptions();
$host_field_suggestions = CValue::read($options, "hostfield_sugg", array());

$list = $event->buildHostFieldsList();

$smarty = new CSmartyDP();
$smarty->assign("ex_constraint", $ex_constraint);
$smarty->assign("class_fields", $list);
$smarty->assign("host_field_suggestions", $host_field_suggestions);
$smarty->display("inc_edit_ex_constraint.tpl");
