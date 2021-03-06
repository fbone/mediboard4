<?php
/**
 * $Id: vw_resume.php 23944 2014-07-10 12:32:07Z phenxdesign $
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 23944 $
 */

CCanDo::checkRead();

$patient_id = CValue::get("patient_id");

$patient = new CPatient;
$patient->load($patient_id);

$listPrat = CConsultation::loadPraticiens(PERM_EDIT, null, null, null, false);

$patient->loadRefsDocItems();

foreach ($patient->_ref_files_by_cat as $cat => $files_by_cat) {
  foreach ($files_by_cat as $key => $_file) {
    if ($_file->annule) {
      unset($patient->_ref_files_by_cat[$cat][$key]);
    }
  }
}

$where = array();
$where["plageconsult.chir_id"] = CSQLDataSource::prepareIn(array_keys($listPrat));
$patient->loadRefsConsultations($where);
$patient->loadRefsSejours();

$dossier_medical = $patient->loadRefDossierMedical();
$dossier_medical->loadRefsAntecedents();
$dossier_medical->loadRefsTraitements();
$prescription = $dossier_medical->loadRefPrescription();

if ($prescription && is_array($prescription->_ref_prescription_lines)) {
  foreach ($dossier_medical->_ref_prescription->_ref_prescription_lines as $_line) {
    $_line->loadRefsPrises();
  }
}

$consultations =& $patient->_ref_consultations;
$sejours =& $patient->_ref_sejours;

// Consultations
foreach ($consultations as $consultation) {
  $consultation->loadRefsDocItems(false);
  $consultation->countDocItems();
  $consultation->loadRefConsultAnesth();
  $consultation->loadRefsExamsComp();
  $consultation->loadRefsFichesExamen();
  $consultation->loadRefsActesCCAM();
  $consultation->loadRefsActesNGAP();
  $consultation->loadRefsReglements();
  $consultation->loadRefPlageConsult();
  $consultation->_ref_plageconsult->_ref_chir->loadRefFunction();

  $_latest_constantes = CConstantesMedicales::getLatestFor($patient, null, array("poids", "taille"), $consultation);
  $consultation->_latest_constantes = $_latest_constantes[0];
  
  // Affichage des ordonnances
  $consultation->loadRefsPrescriptions();
  if (isset($consultation->_ref_prescriptions["externe"])) {
    $consultation->_ref_prescriptions["externe"]->loadRefsFiles();
    foreach ($consultation->_ref_prescriptions["externe"]->_ref_files as $key => $_file) {
      if ($_file->annule) {
        unset($consultation->_ref_prescriptions["externe"]->_ref_files[$key]);
      }
    }
  }
}

// Sejours
$where = array();
$where["chir_id"] = CSQLDataSource::prepareIn(array_keys($listPrat));
foreach ($patient->_ref_sejours as $sejour) {
  $sejour->loadRefPraticien();
  $sejour->loadRefsPrescriptions();
  $sejour->loadRefsOperations($where);
  $sejour->loadRefsDocItems(false);
  foreach ($sejour->_ref_operations as $operation) {
    $operation->loadRefPlageOp();
    $operation->loadRefChir();
    $operation->loadRefsDocItems(false);
    $operation->loadExtCodesCCAM();
  }
}

// Filtre sur les praticiens
$listPrat = CConsultation::loadPraticiensCompta();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("patient" , $patient);
$smarty->assign("listPrat", $listPrat);

$smarty->display("vw_resume.tpl");
