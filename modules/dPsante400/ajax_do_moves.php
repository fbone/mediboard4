<?php /** $Id: ajax_do_moves.php 19326 2013-05-28 14:01:35Z mytto $ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision: 19326 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


CCanDo::checkAdmin();

$marked = CValue::get("marked");
$action = CValue::get("action");

$type = CValue::get("type");
$types = $type == "all" ? CMouvFactory::getTypes() : array($type);

$marked = CValue::get("marked");
$marked = $marked == "all" ? array("0", "1") : array($marked);

foreach ($types as $_type) {
  $mouv = CMouvFactory::create($_type);
  if ($action == "count") {
    $mouv->loadLatest();
    CAppUI::stepAjax(
      "Latest available trigger for type '%s' is '%s' dating '%s'", UI_MSG_OK,
      $_type,
      $mouv->rec, 
      $mouv->when
    );
      
    foreach ($marked as $_marked) {
      $count = $mouv->count($_marked);
      CAppUI::stepAjax(
        "%s - %s : %s disponibles ", UI_MSG_OK,
        CAppUI::tr("CMouvement400-type-$_type"),
        CAppUI::tr("CMouvement400-marked-$_marked"), 
        $count
      );
    }
  }
  
  if ($action == "obsolete") {
    $mouv->loadOldest();
    CAppUI::stepAjax(
      "Oldest available trigger for type '%s' is '%s' dating '%s'", UI_MSG_OK,
      $_type,
      $mouv->rec,
      $mouv->when
    );

    $count = $mouv->count(true, $mouv->rec);
    CAppUI::stepAjax(
      "Counting '%s' obsolete marked triggers",
      $count ? UI_MSG_WARNING : UI_MSG_OK,
      $count
    );

    $max = 1000;
    $count = $mouv->markObsoleteTriggers($mouv->rec, $max);
    if (is_string($count)) {
      CAppUI::stepAjax("Error marking obsolete trigger: %s", UI_MSG_WARNING, $count);
    }
    else {
      CAppUI::stepAjax("Marked '%s' obsolete triggers", UI_MSG_OK, $count);
    }
  }

  if ($action == "purge") {
    $mouv->loadOldest();
    CAppUI::stepAjax(
      "Oldest available trigger for type '%s' is '%s' dating '%s'", UI_MSG_OK,
      $_type,
      $mouv->rec,
      $mouv->when
    );

    $count = $mouv->countOlderMarks($mouv->rec);
    CAppUI::stepAjax(
      "Counting '%s' purgeable trigger marks ",
      $count ? UI_MSG_WARNING : UI_MSG_OK,
      $count
    );

    $max = 1000;
    $count = $mouv->purgeOlderMarks($mouv->rec);
    if (is_string($count)) {
      CAppUI::stepAjax("Error purging old marks: %s", UI_MSG_WARNING, $count);
    }
    else {
      CAppUI::stepAjax("Purged '%s' old marks", UI_MSG_OK, $count);
    }
  }
}
