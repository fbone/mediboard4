<?php
/**
 * $Id: ajax_vw_select_seances.php 26916 2015-01-27 16:12:04Z aurelie17 $
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 26916 $
 */

CCanDo::checkRead();

$date = CValue::getOrSession("date", CMbDT::date());
$therapeute_id = CValue::get("therapeute_id");
$equipement_id = CValue::get("equipement_id");
$prescription_line_element_id = CValue::get("prescription_line_element_id");

$monday = CMbDT::date("last monday", CMbDT::date("+1 day", $date));
$sunday = CMbDT::date("next sunday", CMbDT::date("-1 DAY", $date));

// Chargement de la ligne
$line_element = new CPrescriptionLineElement();
$line_element->load($prescription_line_element_id);
$element_prescription_id = $line_element->element_prescription_id;

// Chargement des seances en fonction des parametres selectionnés
$seance = new CEvenementSSR();
$ljoin = array();
$ljoin[] = "evenement_ssr AS evt_seance ON (evt_seance.seance_collective_id = evenement_ssr.evenement_ssr_id)";
$ljoin["prescription_line_element"] =
  "evt_seance.prescription_line_element_id = prescription_line_element.prescription_line_element_id";

$where = array();
$where["evenement_ssr.sejour_id"] = " IS NULL";
$where["evenement_ssr.debut"] = "BETWEEN '$monday 00:00:00' AND '$sunday 23:59:59'";
$where["evenement_ssr.therapeute_id"] = " = '$therapeute_id'";

if ($equipement_id) {
  $where["evenement_ssr.equipement_id"] = " = '$equipement_id'";
}
else {
  $where["evenement_ssr.equipement_id"] = " IS NULL";
}
$where["prescription_line_element.element_prescription_id"] = " = '$element_prescription_id'";

$seances = $seance->loadList($where, null, null, "evenement_ssr_id", $ljoin);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("seances", $seances);
$smarty->display("inc_vw_select_seance.tpl");
