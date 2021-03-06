<?php
/**
 * $Id: httpreq_vw_object.php 19290 2013-05-26 19:48:24Z phenxdesign $
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 19290 $
 */

$object = mbGetObjectFromGet("object_class", "object_id", "object_guid");

// Look for view options
$options = CMbArray::filterPrefix($_GET, "view_");

$object->loadView();

// If no template is defined, use generic
$template = $object->makeTemplatePath("view");
$template = is_file("modules/$template") ?
   $template : 
  "system/templates/CMbObject_view.tpl";

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("object", $object);
$smarty->display("../../$template");
