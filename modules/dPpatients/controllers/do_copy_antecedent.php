<?php
/**
 * $Id: do_copy_antecedent.php 19219 2013-05-21 12:26:07Z phenxdesign $
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 19219 $
 */

class CDoCopyAntecedent extends CDoObjectAddEdit {
  function CDoCopyAntecedent() {
    $this->CDoObjectAddEdit("CAntecedent");
  }  
  
  function doBind() {
    parent::doBind();
    
    // recuperation du sejour_id
    $_sejour_id = CValue::post("_sejour_id"  , null);

    // si pas de sejour_id, redirection
    if (!$_sejour_id) {
       $this->doRedirect();
    }
    
    // Creation du nouvel antecedent
    unset($_POST["antecedent_id"]);
    $this->_obj = $this->_old;
    $this->_obj->_id = null;
    $this->_obj->antecedent_id = null;
    
    // Calcul de la valeur de l'id du dossier_medical du sejour
    $this->_obj->dossier_medical_id = CDossierMedical::dossierMedicalId($_sejour_id, "CSejour");
  }
}

$do = new CDoCopyAntecedent();
$do->doIt();
