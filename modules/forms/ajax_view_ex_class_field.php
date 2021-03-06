<?php
/**
 * $Id: ajax_view_ex_class_field.php 26927 2015-01-28 12:52:44Z phenxdesign $
 *
 * @package    Mediboard
 * @subpackage forms
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 26927 $
 */

CCanDo::checkEdit();

$ex_class_field_id = CValue::get("ex_class_field_id");
$form_name = CValue::get("form_name");
$value = CValue::get("value");

$ex_class_field = new CExClassField;
$ex_class_field->load($ex_class_field_id);

$ex_class_id = $ex_class_field->loadRefExGroup()->ex_class_id;
$ex_object = new CExObject($ex_class_id);

$ex_object->{$ex_class_field->name} = $value;

$spec = CExConcept::getConceptSpec($ex_class_field->prop);
if ($spec instanceof CEnumSpec) {
  $ex_class_field->updateEnumSpec($spec);
}

$ex_class_field->readonly = "0";
$ex_class_field->hidden = "0";

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("ex_field", $ex_class_field);
$smarty->assign("ex_object", $ex_object);
$smarty->assign("form", $form_name);
$smarty->assign("is_predicate", true);
$smarty->display("inc_ex_object_field.tpl");
