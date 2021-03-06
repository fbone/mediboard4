<?php

/**
 * dPbloc
 *
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id: index.php 19900 2013-07-12 14:26:21Z rhum1 $
 * @link     http://www.mediboard.org
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_edit_planning"      , TAB_READ);
//$module->registerTab("vw_edit_interventions" , TAB_EDIT);
$module->registerTab("vw_suivi_salles"       , TAB_EDIT);
if (CModule::getActive("reservation")) {
  $module->registerTab("vw_planning"         , TAB_READ);
}
$module->registerTab("vw_urgences"           , TAB_EDIT);
$module->registerTab("vw_departs_us"         , TAB_EDIT);
if (CAppUI::conf("dPbloc CPlageOp systeme_materiel") == "standard") {
  $module->registerTab("vw_idx_materiel"     , TAB_EDIT);
}
else {
  $module->registerTab("vw_ressources"       , TAB_EDIT);
}
$module->registerTab("vw_blocages"           , TAB_EDIT);

$module->registerTab("vw_idx_blocs"          , TAB_ADMIN);
$module->registerTab("print_planning"        , TAB_READ);
