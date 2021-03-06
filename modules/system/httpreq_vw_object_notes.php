<?php
/**
 * $Id: httpreq_vw_object_notes.php 19290 2013-05-26 19:48:24Z phenxdesign $
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 19290 $
 */

$object = mbGetObjectFromGet("object_class", "object_id", "object_guid");

$object->loadRefsNotes(PERM_READ);

foreach ($object->_ref_notes as $note) {
  $note->_ref_user->loadRefsFwd();
  $note->_date_relative = CMbDate::relative($note->date);
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("notes", $object->_ref_notes);
$smarty->assign("object", $object);
$smarty->display("vw_object_notes.tpl");
