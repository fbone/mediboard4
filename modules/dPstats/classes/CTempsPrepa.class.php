<?php
/**
 * $Id: CTempsPrepa.class.php 24959 2014-09-25 10:13:26Z rhum1 $
 *
 * @package    Mediboard
 * @subpackage dPstats
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 24959 $
 */

/**
 * Class CTempsPrepa
 *
 * Classe de mining des temps de pr�paration du patient
 *
 * @todo Passer au mining framework�
 */
class CTempsPrepa extends CMbObject {
  // DB Table key
  public $temps_prepa_id;

  // DB Fields
  public $chir_id;
  public $nb_prepa;
  public $nb_plages;
  public $duree_moy;
  public $duree_ecart;

  // Object References
  /** @var  CMediusers */
  public $_ref_praticien;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'temps_prepa';
    $spec->key   = 'temps_prepa_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["chir_id"]     = "ref class|CMediusers";
    $specs["nb_plages"]   = "num pos";
    $specs["nb_prepa"]    = "num pos";
    $specs["duree_moy"]   = "time";
    $specs["duree_ecart"] = "time";
    return $specs;
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    $this->loadRefPraticien();
    $this->_ref_praticien->loadRefFunction();
  }

  /**
   * Chargement du praticien
   *
   * @return CMediusers Le praticien li�
   */
  function loadRefPraticien() {
    return $this->_ref_praticien = $this->loadFwdRef("praticien_id", 1);
  }
}
