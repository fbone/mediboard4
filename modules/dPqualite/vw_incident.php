<?php
/**
 * $Id: vw_incident.php 19316 2013-05-28 09:33:17Z rhum1 $
 *
 * @package    Mediboard
 * @subpackage Qualite
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 19316 $
 */

CCanDo::checkRead();

$fiche_ei_id = CValue::get("fiche_ei_id", 0);

//R�cup�ration du type de fiche � g�n�rer et de la RSPO concern�e.
$type_ei_id = CValue::get("type_ei_id");
$blood_salvage_id = CValue::get("blood_salvage_id");

$fiche  = new CFicheEi();
$listFct = new CFunctions();

// Droit admin et edition de fiche
if (CCanDo::admin() && $fiche_ei_id) {
  $fiche->load($fiche_ei_id);
}

// Chargement des Utilisateurs
if (CCanDo::admin()) {
  $listFct = CMediusers::loadFonctions(PERM_READ);
  foreach ($listFct as $fct) {
    $fct->loadRefsUsers();
  }
}

$fiche->loadRefsFwd();
if (!$fiche->_ref_evenement) {
  $fiche->_ref_evenement = array();
}

/*
 * Si l'on est dans le cas o� nous souhaitons pr�remplir automatiquement 
 * quelques champs � l'aide du mod�le de fiche d'incident (module cell saver).
 */
if ($type_ei_id) {
  $type_fiche = new CTypeEi();
  $type_fiche->load($type_ei_id);
  $fiche->elem_concerne = $type_fiche->concerne;
  $fiche->descr_faits = $type_fiche->desc;
  $fiche->evenements = $type_fiche->evenements;
  $fiche->type_incident = $type_fiche->type_signalement;
  $fiche->_ref_evenement =  $type_fiche->_ref_evenement;
  
  if ($blood_salvage_id) {
    $blood_salvage = new CBloodSalvage();
    $blood_salvage->load($blood_salvage_id);
    $blood_salvage->loadRefsFwd();
    
    if ($fiche->elem_concerne == "pat") {
      $fiche->elem_concerne_detail = $blood_salvage->_ref_patient->_view;
    }
    if ($fiche->elem_concerne == "mat") {
      $fiche->elem_concerne_detail = $blood_salvage->_ref_cell_saver->_view;
    }
  }
}

// Liste des Cat�gories
$firstdiv = null;

$categorie = new CEiCategorie();
/** @var CEiCategorie[] $listCategories */
$listCategories = $categorie->loadList(null, "nom");
foreach ($listCategories as $key => $_categorie) {
  if ($firstdiv===null) {
    $firstdiv = $key;
  }
  $_categorie->loadRefsBack();
  $_categorie->_checked = null;
  foreach ($_categorie->_ref_items as $keyItem => $_item) {
    if (in_array($keyItem, $fiche->_ref_evenement)) {
      $_item->_checked = true;
      if ($_categorie->_checked) {
        $_categorie->_checked .= "|$keyItem";
      }
      else {
        $_categorie->_checked = $keyItem;
      }
    }
    else {
      $_item->_checked = false;
    }
  }
}

if (!$fiche->date_incident) {
  $fiche->date_incident = CMbDT::dateTime();
}
$fiche->updateFormFields();
  
// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("fiche"          , $fiche);
$smarty->assign("firstdiv"       , $firstdiv);
$smarty->assign("listCategories" , $listCategories);
$smarty->assign("listFct"        , $listFct);

$smarty->display("vw_incident.tpl");
