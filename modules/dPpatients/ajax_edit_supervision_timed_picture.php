<?php
/**
 * $Id: ajax_edit_supervision_timed_picture.php 20428 2013-09-20 12:14:48Z phenxdesign $
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 20428 $
 */

CCanDo::checkAdmin();

$supervision_timed_picture_id = CValue::getOrSession("supervision_timed_picture_id");

$picture = new CSupervisionTimedPicture();
$picture->load($supervision_timed_picture_id);
$picture->loadRefsNotes();
$picture->loadRefsFiles();

$tree = CMbPath::getTree("modules/dPpatients/images/supervision");

$smarty = new CSmartyDP();
$smarty->assign("picture",  $picture);
$smarty->assign("tree",  $tree);
$smarty->display("inc_edit_supervision_timed_picture.tpl");
