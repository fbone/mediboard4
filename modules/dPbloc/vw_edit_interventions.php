<?php

/**
 * dPbloc
 *
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id: vw_edit_interventions.php 19148 2013-05-15 12:41:42Z rhum1 $
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

if (!($plageop_id = CValue::getOrSession("plageop_id"))) {
  CAppUI::setMsg("Vous devez choisir une plage op�ratoire", UI_MSG_WARNING);
  CAppUI::redirect("m=dPbloc&tab=vw_edit_planning");
}

// Infos sur la plage op�ratoire
$plage = new CPlageOp();
$plage->load($plageop_id);
if (!$plage->temps_inter_op) {
  $plage->temps_inter_op = "00:00:00";
}
$plage->loadRefsFwd();
$plage->loadRefChir()->loadRefFunction();
$plage->loadRefAnesth()->loadRefFunction();
$plage->loadRefsNotes();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("plage", $plage);
$smarty->display("vw_edit_interventions.tpl");
