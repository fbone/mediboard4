<?php
/**
 * $Id: idx_messages.php 19290 2013-05-26 19:48:24Z phenxdesign $
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 19290 $
 */

CCanDo::checkRead();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->display("idx_messages.tpl");
