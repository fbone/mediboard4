<?php
/**
 * $Id: do_tarif_aed.php 26681 2015-01-07 10:29:45Z rhum1 $
 *
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 26681 $
 */

if (CValue::post("reloadAlltarifs")) {
  $tarif = new CTarif();
  $where = array();
  $praticien_id = CValue::post("praticien_id");
  $function_id  = CValue::post("function_id");
  if($praticien_id) {
    $where["chir_id"] = "= '$praticien_id'";
  }
  if($function_id) {
    $where["function_id"] = "= '$function_id'";
  }
  $tarifs = $tarif->loadList($where);
  foreach ($tarifs as $_tarif) {
    /* @var CTarif $_tarif*/
    $_tarif->_update_montants = 1;
    $_tarif->updateMontants();
    if ($msg = $_tarif->store()) {
      CAppUI::setMsg($_tarif->_id.$msg, UI_MSG_ERROR);
    }
  }
  CAppUI::setMsg("Tarifs mis � jour", UI_MSG_OK);
  echo CAppUI::getMsg();
}
elseif (CValue::post("modifTauxVingPct")) {
  $where = array();
  $where["taux_tva"] = "= '19.6'";
  $tarif = new CTarif();
  $nb_tarif = $tarif->countList($where);;
  $tarifs = $tarif->loadList($where);;
  foreach ($tarifs as $_tarif) {
    /* @var CTarif $_tarif*/
    $_tarif->taux_tva = '20';
    $_tarif->_update_montants = 1;
    $_tarif->updateMontants();
    if ($msg = $_tarif->store()) {
      CAppUI::setMsg($_tarif->_id.$msg, UI_MSG_ERROR);
    }
  }
  CAppUI::setMsg("$nb_tarif tarifs mis � jour", UI_MSG_OK);
  echo CAppUI::getMsg();
}
else {
  $do = new CDoObjectAddEdit("CTarif", "tarif_id");

  // redirection vers la comptabilite dans le cas de la creation d'un nouveau tarif dans la consult
  if (isset($_POST["_tab"])) {
    $do->redirect = "m=dPcabinet&tab=".$_POST["_tab"];
  }
  $do->doIt();
}