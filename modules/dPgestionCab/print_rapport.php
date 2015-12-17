<?php
/**
 * $Id: print_rapport.php 19621 2013-06-20 20:40:45Z phenxdesign $
 *
 * @package    Mediboard
 * @subpackage GestionCab
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 19621 $
 */

CCanDo::checkRead();

$ds = CSQLDataSource::get("std");
$user = CMediusers::get();

$libelle          = CValue::getOrSession("libelle"          , "");
$rubrique_id      = CValue::getOrSession("rubrique_id"      , 0);
$mode_paiement_id = CValue::getOrSession("mode_paiement_id" , 0);

$filter = new CGestionCab;
$filter->_date_min = CValue::getOrSession("_date_min");
$filter->_date_max = CValue::getOrSession("_date_max");
$filter->libelle = CValue::getOrSession("libelle");
$filter->rubrique_id = CValue::getOrSession("rubrique_id");
$filter->mode_paiement_id = CValue::getOrSession("mode_paiement_id");

$where             = array();
$where[]           = "function_id IS NULL OR function_id = '$user->function_id'";

$listRubriques     = new CRubrique;
$listRubriques     = $listRubriques->loadList($where);

$listModesPaiement = new CModePaiement;
$listModesPaiement = $listModesPaiement->loadList($where);

$listGestionCab    = new CGestionCab();
$where["date"]     = "BETWEEN '$filter->_date_min' AND '$filter->_date_max'";

if ($libelle) {
  $where["libelle"] = "LIKE '%$libelle%'";
}
if ($rubrique_id) {
  $where["rubrique_id"] = "= '$rubrique_id'";
}
if ($mode_paiement_id) {
  $where["mode_paiement_id"] = "= '$mode_paiement_id'";
}

$order = "date ASC";
$listGestionCab    = $listGestionCab->loadList($where, $order);
foreach ($listGestionCab as $key => $fiche) {
  $listGestionCab[$key]->loadRefsFwd();
}

$sql = "SELECT rubrique_id, SUM(montant) AS value" .
    "\nFROM `gestioncab`" .
    "\nWHERE date BETWEEN '$filter->_date_min' AND '$filter->_date_max'" .
    "\nAND function_id = '$user->function_id'";

if ($libelle) {
  $sql .= "\nAND libelle LIKE '%$libelle%'";
}
if ($rubrique_id) {
  $sql .= "\nAND rubrique_id = '$rubrique_id'";
}
if ($mode_paiement_id) {
  $sql .= "\nAND mode_paiement_id = '$mode_paiement_id'";
}

$sql .= "\nGROUP BY rubrique_id";
$totaux = $ds->loadList($sql);

$sql = "SELECT SUM(montant) AS value, 0 as invar" .
    "\nFROM `gestioncab`" .
    "\nWHERE date BETWEEN '$filter->_date_min' AND '$filter->_date_max'" .
    "\nAND function_id = '$user->function_id'";

if ($libelle) {
  $sql .= "\nAND libelle LIKE '%$libelle%'";
}
if ($rubrique_id) {
  $sql .= "\nAND rubrique_id = '$rubrique_id'";
}
if ($mode_paiement_id) {
  $sql .= "\nAND mode_paiement_id = '$mode_paiement_id'";
}

$sql .= "\nGROUP BY invar";
$total = $ds->loadResult($sql);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("filter"           , $filter);
$smarty->assign("libelle"          , $libelle);
$smarty->assign("rubrique_id"      , $rubrique_id);
$smarty->assign("mode_paiement_id" , $mode_paiement_id);
$smarty->assign("listRubriques"    , $listRubriques);
$smarty->assign("listModesPaiement", $listModesPaiement);
$smarty->assign("listGestionCab"   , $listGestionCab);
$smarty->assign("totaux"           , $totaux);
$smarty->assign("total"            , $total);

$smarty->display("print_rapport.tpl");
