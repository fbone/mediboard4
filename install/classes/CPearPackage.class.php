<?php
/**
 * $Id: CPearPackage.class.php 22458 2014-03-15 15:00:05Z phenxdesign $
 *  
 * @package    Mediboard
 * @subpackage Installer
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    SVN: $Id: CPearPackage.class.php 22458 2014-03-15 15:00:05Z phenxdesign $ 
 * @link       http://www.mediboard.org
 */

/**
 * PEAR package prerequisite
 */
class CPearPackage extends CPrerequisite {
  public $status = "stable";

  /**
   * Check file inclusion
   *
   * @param bool $strict Check also warnings
   * 
   * @see parent::check
   * 
   * @return bool
   */
  function check($strict = true) {
    if (!$this->mandatory && !$strict) {
      return true;
    }

    return @include_once "$this->name.php";
  }

  /**
   * @see parent::getAll()
   */
  function getAll(){
    $packages = array();
    
    $package = new CPearPackage();
    $package->name = "Archive/Tar";
    $package->description = "Package de manipulation d'archives au format GNU TAR";
    $package->mandatory = true;
    $package->reasons[] = "Installation de Mediboard";
    $package->reasons[] = "Import des fonctions de GHM";
    $packages[] = $package;
    
    $package = new CPearPackage();
    $package->name = "Config";
    $package->description = "Package de manipulation de fichiers de configuration";
    $package->mandatory = true;
    $package->reasons[] = "Configuration g�n�rale de Mediboard";
    $packages[] = $package;
    
    $package = new CPearPackage();
    $package->name = "PHP/CodeSniffer";
    $package->description = "Analyseur syntaxique de code source";
    $package->status = "beta";
    $package->mandatory = false;
    $package->reasons[] = "Outil de g�nie logiciel pour v�rifier la qualit� du code source de Mediboard";
    $packages[] = $package;
    
    return $packages;
  }
}

