<?php
/**
 * $Id: vw_legende.php 18796 2013-04-15 07:41:12Z aurelie17 $
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision: 18796 $
 */
CCanDo::checkRead();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("classe", CValue::get("classe"));

$smarty->display("vw_legende.tpl");
?>
