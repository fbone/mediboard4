<?php
/**
 * $Id: httpreq_import_catalogue.php 19285 2013-05-26 13:10:13Z phenxdesign $
 *
 * @package    Mediboard
 * @subpackage Labo
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 19285 $
 */

global $can, $m, $remote_name;

$can->needsAdmin();

/**
 * Catalogue import
 */
function importCatalogue($cat, $parent_id = null) { 
  global $remote_name;

  CApp::setTimeLimit(180);

  // On rend toutes les analyses du catalogue obsoletes
  $idAnalyse = new CIdSante400();
  $idAnalyse->tag = $remote_name;
  $idAnalyse->object_class = "CExamenLabo";
  $idAnalyses = $idAnalyse->loadMatchingList();
  foreach ($idAnalyses as $_id_analyse) {
    $examenLabo = new CExamenLabo();
    $examenLabo->identifiant = $_id_analyse->id400;
    $examenLabo->loadMatchingObject();
    if ($examenLabo->_id) {
      $examenLabo->obsolete = 1;
      $examenLabo->store();
    }
  }

  $idCatalogue = new CIdSante400();
  $idCatalogue->tag = $remote_name;
  $idCatalogue->object_class = "CCatalogueLabo";
  $idCatalogues = $idCatalogue->loadMatchingList();
  foreach ($idCatalogues as $_id_catalogue) {
    $catalogueLabo = new CCatalogueLabo();
    $catalogueLabo->identifiant = $_id_catalogue->id400;
    $catalogueLabo->loadMatchingObject();
    if ($catalogueLabo->_id) {
      $catalogueLabo->obsolete = 1;
      $catalogueLabo->store();
    }
  }

  $compteur["analyses"] = 0;
  $compteur["chapitres"] = 0;
  $compteur["sousChapitre"] = 0; 

  $catalogues = array();
  // Creation du catalogue global LABO
  $catal = new CCatalogueLabo();
  $catalogue = new CCatalogueLabo();
  $catal->identifiant = substr(hash('md5', $remote_name), 0, 4);  // libelle modifi� par hash

  $catal->libelle = $remote_name;
  $catal->pere_id = $parent_id;

  // creation de son id400
  $idCat = new CIdSante400();
  $idCat->tag = $remote_name;
  $idCat->id400 = $remote_name;

  $catal->obsolete = 0;
  $idCat->bindObject($catal);
  //CAppUI::stepAjax("Catalogue '$catal->libelle' import�", UI_MSG_OK);

  $path = $remote_name;
  // on met a jour $catalogues
  $catalogues[$path] = $catal;

  //Parcours des analyses
  foreach ($cat->analyse as $_analyse) {
    $chapitre = (string) $_analyse->chapitre;
    $path = "$remote_name/$chapitre/";
    if (!$chapitre) {
      $path = $remote_name;
    }

    $catChapitre = new CCatalogueLabo();

    // si le catalogue n'existe pas deja
    if (!array_key_exists($path,$catalogues)) {
      // creation du catalogue
      $catChapitre->identifiant = substr(hash('md5', $chapitre), 0, 4);  // libelle modifi� par hash;
      $catChapitre->libelle = $chapitre;
      $catChapitre->pere_id = $catal->_id;
      $catChapitre->decodeUtfStrings();

      //creation de l'id400 
      $idCatChapitre = new CIdSante400();
      $idCatChapitre->tag = $remote_name;
      $idCatChapitre->id400 = substr(hash('md5', $chapitre), 0, 4);

      $catChapitre->obsolete = 0;
      $idCatChapitre->bindObject($catChapitre);

      //CAppUI::stepAjax("Catalogue '$catChapitre->libelle' import�", UI_MSG_OK);
      $compteur["chapitres"]++;
      // on met a jour $catalogues
      $catalogues[$path] = $catChapitre;
    }

    $catChapitre = $catalogues[$path];
    $catalogue = $catChapitre;
    // si il y a un sous chapitre a creer==> le pere du sous chapitre est $catalogue->_id;
    $sschapitre = (string) $_analyse->sschapitre;

    if ($sschapitre) {
      // modification du path
      $path .= $sschapitre;

      $catssChapitre = new CCatalogueLabo();

      if (!array_key_exists($path,$catalogues)) {
        // creation du catalogue
        $catssChapitre->identifiant = substr(hash('md5', $sschapitre), 0, 4);  // libelle modifi� par hash;
        $catssChapitre->libelle = $sschapitre;
        $catssChapitre->pere_id = $catChapitre->_id;
        $catssChapitre->decodeUtfStrings();
        //creation de l'id400
        $idCatssChapitre = new CIdSante400();
        $idCatssChapitre->tag = $remote_name;
        $idCatssChapitre->id400 = substr(hash('md5', $sschapitre), 0, 4);

        $catssChapitre->obsolete = 0;
        $idCatssChapitre->bindObject($catssChapitre);

        //CAppUI::stepAjax("Sous Catalogue '$catssChapitre->libelle' import�", UI_MSG_OK);
        $compteur["sousChapitre"]++; 
        //on met � jour les catalogues
        $catalogues[$path] = $catssChapitre;
      }
      $catssChapitre = $catalogues[$path];
      $catalogue = $catssChapitre;
    }
    // Code de l'analyse
    $catAtt = $_analyse->attributes();
    $code = $catAtt["code"];

    $idAnalyse = new CIdSante400();
    $idAnalyse->tag = $remote_name;
    $idAnalyse->id400 = (string) $code;


    $analyse = new CExamenLabo();
    $analyse->identifiant = (string) $code;
    $analyse->libelle = (string) $_analyse->libelle;
    $analyse->decodeUtfStrings();
    $analyse->technique = (string) $_analyse->technique;

    $analyse->duree_execution = (string) $_analyse->delaitechnique;

    $materiel = utf8_decode((string) $_analyse->materiel);
    $materiel = trim($materiel);

    switch ($materiel) {
      case "SANG VEINEUX":
        $analyse->type_prelevement = "sang";
        break;

      case "URINE":
        $analyse->type_prelevement = "urine";
        break;

      case "BIOPSIE":
        $analyse->type_prelevement = "biopsie";
        break;
    }

    //$analyse->applicabilite = (string) $_analyse->applicablesexe;
    $analyse->execution_lun = (string) $_analyse->joursrealisation->lundi;
    $analyse->execution_mar = (string) $_analyse->joursrealisation->mardi;
    $analyse->execution_mer = (string) $_analyse->joursrealisation->mercredi;
    $analyse->execution_jeu = (string) $_analyse->joursrealisation->jeudi;
    $analyse->execution_ven = (string) $_analyse->joursrealisation->vendredi;
    $analyse->execution_sam = (string) $_analyse->joursrealisation->samedi;
    $analyse->execution_dim = (string) $_analyse->joursrealisation->dimanche;

    $analyse->catalogue_labo_id = $catalogue->_id;
    $analyse->type = "num";

    $analyse->obsolete = 0;
    $idAnalyse->bindObject($analyse);
    //CAppUI::stepAjax("Analyse '$analyse->identifiant' import�e", UI_MSG_OK);
    $compteur["analyses"]++;
  }// fin du foreach

  CAppUI::stepAjax("Analyses Import�es: ".$compteur["analyses"].", Chapitres Import�s: ".$compteur["chapitres"].", Sous chapitres Import�s: ".$compteur["sousChapitre"], UI_MSG_OK);
}

// Check import configuration
$clCconfig = CAppUI::conf("$m CCatalogueLabo");

if (null == $remote_name = $clCconfig["remote_name"]) {
  CAppUI::stepAjax("Remote name not configured", UI_MSG_ERROR);
}

if (null == $remote_url = $clCconfig["remote_url"]) {
  CAppUI::stepAjax("Remote URL not configured", UI_MSG_ERROR);
}

if (false === $content = file_get_contents($remote_url)) {
  CAppUI::stepAjax("Couldn't connect to remote url", UI_MSG_ERROR);
}

// Check imported catalogue document
$doc = new CMbXMLDocument;

if (!$doc->loadXML($content)) {
  CAppUI::stepAjax("Document is not well formed", UI_MSG_ERROR);
}

$tmpPath = "tmp/dPlabo/import_catalogue.xml";
CMbPath::forceDir(dirname($tmpPath));
$doc->save($tmpPath);
$doc->load($tmpPath);

if (!$doc->schemaValidate("modules/$m/remote/catalogue.xsd")) {
  CAppUI::stepAjax("Document is not valid", UI_MSG_ERROR);
}

CAppUI::stepAjax("Document is valid", UI_MSG_OK);

// Check access to idSante400
$canSante400 = CModule::getCanDo("dPsante400");
if (!$canSante400->edit) {
  CAppUI::stepAjax("No permission for module 'dPsante400' or module not installed", UI_MSG_ERROR);
}

// Import catalogue
$cat = new SimpleXMLElement($content);
try {
  importCatalogue($cat);
} 
catch (CMbException $e) {
  $e->stepAjax();
}
