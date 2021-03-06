<?php
/**
 * $Id: ajax_list_keeper.php 18926 2013-04-25 08:53:02Z kgrisel $
 *
 * @category Password Keeper
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org */

CPasswordKeeper::checkHTTPS();

CCanDo::checkAdmin();

$password_keeper_id = CValue::getOrSession("password_keeper_id");

// Récupération de la liste des trousseaux
$keeper = new CPasswordKeeper();
$user   = CMediusers::get();

$keeper->user_id = $user->_id;

$keepers = $keeper->loadList("user_id = '$user->_id'","keeper_name");
$counts  = array();
foreach ($keepers as $_keeper) {
  $_keeper->loadBackRefs("categories", "category_name");
  $counts[$_keeper->_id] = $_keeper->countBackRefs("categories");
}

$smarty = new CSmartyDP();
$smarty->assign("keepers"           , $keepers);
$smarty->assign("password_keeper_id", $password_keeper_id);
$smarty->assign("counts"            , $counts);
$smarty->display("inc_list_keeper.tpl");