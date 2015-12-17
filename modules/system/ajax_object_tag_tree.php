<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$object_class  = CValue::get("object_class");
$parent_tag_id = CValue::get("parent_tag_id");
$columns       = CValue::get("col");
$group_id      = CValue::get("group_id");

$parent_tag = new CTag();
$parent_tag->load($parent_tag_id);

$tree = CTag::getTree($object_class, $parent_tag);

$smarty = new CSmartyDP();
$smarty->assign("tree",         $tree);
$smarty->assign("object_class", $object_class);
$smarty->assign("parent_tag",   $parent_tag);
$smarty->assign("root",         !$parent_tag->_id);
$smarty->assign("columns",      $columns);
$smarty->assign("group_id",     $group_id);
$smarty->display("inc_object_tag_tree.tpl");
