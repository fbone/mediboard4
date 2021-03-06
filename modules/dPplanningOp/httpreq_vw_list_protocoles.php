<?php
/**
 * $Id: httpreq_vw_list_protocoles.php 24664 2014-09-04 07:44:25Z charlyecho $
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 24664 $
 */

global $dialog;

if ($dialog) {
  CCanDo::checkRead();
}
else {
  CCanDo::checkEdit();
}

// L'utilisateur est-il chirurgien?
$chir_id      = CValue::getOrSession("chir_id");
$mediuser     = CMediusers::get($chir_id);
if (!$mediuser->isPraticien()) {
  $mediuser = new CMediusers();
}
$function_id  = CValue::getOrSession("function_id");
$type         = CValue::getOrSession("type", "interv"); 
$page         = CValue::get("page");
$sejour_type  = CValue::get("sejour_type");
$step = 30;

$protocole = new CProtocole();
$where = array();

$chir     = new CMediusers();
$chir->load($chir_id);
if ($chir->_id) {
  $chir->loadRefFunction();
  $functions = array($chir->function_id);
  $chir->loadBackRefs("secondary_functions");
  foreach ($chir->_back["secondary_functions"] as $curr_sec_func) {
    $functions[] = $curr_sec_func->function_id;
  }
  $list_functions = implode(",", $functions);
  $where [] = "protocole.chir_id = '$chir->_id' OR protocole.function_id IN ($list_functions)";
}
else {
  $where["function_id"] = " = '$function_id'";
}

$where["for_sejour"] = $type == 'interv' ? "= '0'" : "= '1'";

if ($sejour_type) {
  $where["type"] = "= '$sejour_type'";
}

$order = "libelle_sejour, libelle, codes_ccam";

$list_protocoles  = $protocole->loadListWithPerms(PERM_READ, $where, $order, "{$page[$type]},$step");

$systeme_materiel_expert = CAppUI::conf("dPbloc CPlageOp systeme_materiel") == "expert";
foreach ($list_protocoles as $_prot) {
  $_prot->loadRefsFwd();
  if ($systeme_materiel_expert == "expert") {
    $_prot->_types_ressources_ids = implode(",", CMbArray::pluck($_prot->loadRefsBesoins(), "type_ressource_id"));
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("list_protocoles"   , $list_protocoles);
$smarty->assign("total_protocoles"  , $protocole->_totalWithPerms);
$smarty->assign("page"              , $page);
$smarty->assign("step"              , $step);
$smarty->assign("chir"              , $mediuser);
$smarty->assign("type"              , $type);

$smarty->display("inc_list_protocoles.tpl");
