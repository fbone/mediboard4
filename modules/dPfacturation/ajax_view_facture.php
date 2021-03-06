<?php
/**
 * $Id: ajax_view_facture.php 26880 2015-01-26 15:41:43Z aurelie17 $
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision: 26880 $
 */
CCanDo::checkEdit();
$facture_id   = CValue::getOrSession("facture_id");
$object_class = CValue::getOrSession("object_class", "CFactureCabinet");

$facture = new $object_class;
$assurances_patient = array();

if ($facture_id) {
  /* @var CFactureCabinet $facture */
  $facture->load($facture_id);
  $facture->loadRefPatient();
  $facture->_ref_patient->loadRefsCorrespondantsPatient("date_debut DESC, date_fin DESC");
  $facture->loadRefPraticien();
  $facture->loadRefAssurance();
  $facture->loadRefsObjects();
  $facture->loadRefsReglements();
  $facture->loadRefsRelances();
  $facture->loadRefsItems();
  $facture->loadRefsNotes();
}

$reglement   = new CReglement();
$banque      = new CBanque();
$banques     = $banque->loadList(null, "nom");

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("facture"       , $facture);
$smarty->assign("reglement"     , $reglement);
$smarty->assign("banques"       , $banques);

if (!CValue::get("not_load_banque")) {
  $smarty->assign("factures"    , array(new CFactureEtablissement()));
}

$smarty->assign("etat_ouvert"   , CValue::getOrSession("etat_ouvert", 1));
$smarty->assign("etat_cloture"  , CValue::getOrSession("etat_cloture", 1));
$smarty->assign("show_button"   , CValue::get("show_button", 1));
$smarty->assign("date"          , CMbDT::date());
$smarty->assign("chirSel"       , CValue::getOrSession("chirSel", "-1"));

$smarty->display("inc_vw_facturation.tpl");
