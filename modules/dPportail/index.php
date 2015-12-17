<?php
/**
 * $Id: index.php 19621 2013-06-20 20:40:45Z phenxdesign $
 *
 * @package    Mediboard
 * @subpackage Portail
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 19621 $
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_forumtheme"  , TAB_READ);
$module->registerTab("vw_forumthread" , TAB_READ);
$module->registerTab("vw_forummessage", TAB_READ);