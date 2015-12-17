<?php /* $Id: index.php 7320 2009-11-14 22:42:28Z lryo $ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision: 7320 $
* @author Sébastien Fillonneau
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_edit_menu"       , TAB_EDIT);
$module->registerTab("vw_planning_repas"  , TAB_READ);
$module->registerTab("vw_edit_repas"      , TAB_EDIT);
$module->registerTab("vw_quantite"        , TAB_EDIT);
$module->registerTab("vw_create_archive"  , TAB_ADMIN);
?>