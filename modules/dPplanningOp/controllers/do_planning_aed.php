<?php
/**
 * $Id: do_planning_aed.php 26995 2015-02-02 09:07:16Z phenxdesign $
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 26995 $
 */

global $m;

$do = new CDoObjectAddEdit("COperation");
$do->doBind();

if (intval(CValue::post("del", null))) {
  CValue::setSession("operation_id");
  $do->redirectDelete = "m=$m&tab=vw_edit_planning&operation_id=0";
  $do->doDelete();
}
else {
  if ($do->_obj->plageop_id && ($do->_old->plageop_id != $do->_obj->plageop_id)) {
    $do->_obj->rank = 0;
  }
  
  $do->doStore();

  if (CModule::getActive("forms") && CValue::post("_set_fin_op") && CValue::post("fin_op") == "current") {
    $ex_class_events = CExClassEvent::getForObject($do->_obj, "fin_intervention", "required");
    echo CExClassEvent::getJStrigger($ex_class_events);
  }

  if ($do->_obj->plageop_id && $do->_old->plageop_id && ($do->_old->plageop_id != $do->_obj->plageop_id)) {
    $plageop = new CPlageOp;
    $plageop->load($do->_old->plageop_id);
    $plageop->spec_id = "";
    $plageop->store();
  }
  $m = CValue::post("otherm", $m);
  if ($m == "dPhospi") {
    $do->redirectStore = "m=$m#operation".$do->_obj->operation_id;
  }
  $do->redirectStore = "m=$m&operation_id=".$do->_obj->operation_id;
}

$do->doRedirect();
