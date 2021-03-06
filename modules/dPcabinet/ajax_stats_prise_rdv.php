<?php
/**
 * $Id: ajax_stats_prise_rdv.php 21492 2013-12-20 09:24:10Z aurelie17 $
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 21492 $
 */

CCanDo::checkRead();
// Current user and current function
$mediuser = CMediusers::get();
$function = $mediuser->loadRefFunction();

// Filter
$filter = new CPlageconsult();
$filter->_user_id           = CValue::get("_user_id", null);
$filter->_date_min          = CValue::get("_date_min", CMbDT::date("last month"));
$filter->_date_max          = CValue::get("_date_max", CMbDT::date());

$ds = $filter->_spec->ds;

$stats_creation = array();
$prats_creation = array();

if ($filter->_user_id) {
  $query = "CREATE TEMPORARY TABLE consultation_creation AS 
    SELECT user_log.user_id as user_id, plageconsult.chir_id as chir_id
    FROM user_log
    LEFT JOIN consultation ON consultation.consultation_id = user_log.object_id
    LEFT JOIN plageconsult ON plageconsult.plageconsult_id = consultation.plageconsult_id
    WHERE user_log.object_class = 'CConsultation'
    AND user_log.date BETWEEN '$filter->_date_min 00:00:00' AND '$filter->_date_max 23:59:59'
    AND user_log.type = 'create'";
  $ds->exec($query);
  
  $query = "SELECT consultation_creation.user_id, count(consultation_creation.user_id) AS total
    FROM consultation_creation
    WHERE consultation_creation.chir_id = '$filter->_user_id'
    GROUP BY consultation_creation.user_id
    ORDER BY total DESC";
    
  $stats_creation = $ds->loadList($query);
  
  $where = array();
  $where["user_id"] = CSQLDataSource::prepareIn(CMbArray::pluck($stats_creation, "user_id"));
  /* @var CMediusers[] $prats_creation*/
  $prats_creation = $mediuser->loadList($where);
  CMbObject::massLoadFwdRef($prats_creation, "function_id");
  
  foreach ($prats_creation as $_prat) {
    $_prat->loadRefFunction();
  }
}

$smarty = new CSmartyDP();

$smarty->assign("filter"        , $filter);
$smarty->assign("prats_creation", $prats_creation);
$smarty->assign("stats_creation", $stats_creation);

$smarty->display("inc_stats_prise_rdv.tpl");
