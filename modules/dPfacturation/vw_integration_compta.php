<?php
/**
 * $Id: vw_integration_compta.php 28593 2015-06-15 14:40:38Z aurelie17 $
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 28593 $
 */

CCanDo::checkEdit();
$facture_class  = CValue::get("facture_class");
$factures_id    = CValue::get("factures", array());
$definitive     = CValue::get("definitive", 0);
$tiers_soldant  = CValue::get("tiers_soldant", 0);
$uniq_checklist = CValue::get("uniq_checklist", 0);
$date_min       = CValue::get("_date_min");
$date_max       = CValue::get("_date_max");

if ($factures_id) {
  $factures_id    = explode("|", $factures_id);
}

// Factures qui viennent d'�tre imprim�es
$factures = array();
$facture = new $facture_class;
if (count($factures_id)) {
  $where = array();
  $where["facture_id"] = CSQLDataSource::prepareIn(array_values($factures_id));
  $factures = $facture->loadList($where);

  //Impression des factures et justificatifs pdf
  $facture_pdf = new CEditPdf();
  $facture_pdf->factures = $factures;
  $facture_pdf->printBill($tiers_soldant);

  $journal_pdf = new CEditJournal();
  $journal_pdf->type_pdf = "debiteur";
  $journal_pdf->factures = $factures;
  foreach ($journal_pdf->factures as $fact) {
    /** @var CFacture $fact */
    $fact->loadRefsObjects();
    $fact->loadRefPatient();
    $fact->loadRefPraticien();
    $fact->loadRefsReglements();
    $fact->isRelancable();
  }

  if (!$uniq_checklist) {
    $journal_pdf->editJournal(false);
  }

  $journal_pdf->type_pdf = "checklist";
  $journal_pdf->definitive = $definitive;
  $journal_pdf->editJournal(false, !$uniq_checklist);

  if ($definitive) {
    foreach ($factures as $_facture) {
      if (!$_facture->definitive) {
        $_facture->definitive = 1;
        if ($msg = $_facture->store()) {
          mbLog($msg);
        }
      }
    }
  }
}
else {
  $where = array();
  $where["cloture"] = "BETWEEN '$date_min' AND '$date_max'";
  $factures = $facture->loadList($where);
}

//R�partition des factures selon leur mois de cloture
$mois = array();
foreach ($factures as $_facture) {
  $cloture = CMbDT::format($_facture->cloture, "%B %Y");
  if (!isset($mois[$cloture])) {
    $mois[$cloture] = array();
    $mois[$cloture]["factures_id"] = "";
  }
  else {
    $mois[$cloture]["factures_id"] .= "|";
  }
  $mois[$cloture]["factures"][] = $_facture;
  $mois[$cloture]["factures_id"] .= $_facture->_id;
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("mois"         , $mois);
$smarty->assign("facture_class", $facture_class);

$smarty->display("vw_integration_compta.tpl");
