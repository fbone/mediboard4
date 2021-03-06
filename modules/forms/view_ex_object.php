<?php
/**
 * $Id: view_ex_object.php 26472 2014-12-17 14:30:07Z armengaudmc $
 *
 * @package    Mediboard
 * @subpackage forms
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 26472 $
 */

CCanDo::checkRead();

$ex_class_id  = CValue::get("ex_class_id");
$ex_object_id = CValue::get("ex_object_id");

if (!$ex_class_id) {
  $msg = "Impossible d'afficher le formulaire sans conna�tre la classe de base";
  CAppUI::stepAjax($msg, UI_MSG_WARNING);
  trigger_error($msg, E_USER_ERROR);
  return;
}

$ex_object = new CExObject($ex_class_id);
$ex_object->load($ex_object_id);

// Cr�ation du template
$smarty = new CSmartyDP("modules/forms");
$smarty->assign("ex_object", $ex_object);
$smarty->display("view_ex_object.tpl");
