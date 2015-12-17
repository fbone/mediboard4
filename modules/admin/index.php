<?php

/**
 * $Id: index.php 25259 2014-10-13 12:40:25Z charlyecho $
 *
 * @category Admin
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision: 25259 $
 * @link     http://www.mediboard.org
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_edit_users", TAB_EDIT);
$module->registerTab("edit_perms", TAB_EDIT);
$module->registerTab("edit_prefs", TAB_EDIT);
$module->registerTab("vw_all_perms", TAB_READ);
$module->registerTab("vw_edit_tokens", TAB_EDIT);
$module->registerTab("vw_functional_perms", TAB_EDIT);
$module->registerTab("vw_bris_de_glace", TAB_ADMIN);