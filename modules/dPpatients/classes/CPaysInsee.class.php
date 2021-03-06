<?php
/**
 * $Id: CPaysInsee.class.php 22693 2014-04-01 14:59:17Z flaviencrochard $
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 22693 $
 */

/**
 * Pays Insee
 */
class CPaysInsee extends CMbObject {
  // DB Fields
  public $numerique;
  public $alpha_2;
  public $alpha_3;
  public $nom_fr;
  public $nom_ISO;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->dsn         = 'INSEE';
    $spec->incremented = false;
    $spec->table       = 'pays';
    $spec->key         = 'numerique';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["numerique"] = "numchar length|3";
    $specs["alpha_2"  ] = "str length|2";
    $specs["alpha_3"  ] = "str length|3";
    $specs["nom_fr"   ] = "str";
    $specs["nom_ISO"  ] = "str";
    return $specs;
  }

  /**
   * Retourne le code Alpha-3 du pays
   *
   * @param int $numerique Numero de pays
   *
   * @return string
   */
  static function getAlpha3($numerique) {
    $pays = new self;
    $pays->load($numerique);
    
    return $pays->alpha_3;
  }

  /**
   * Retourne le code Alpha-3 du pays
   *
   * @param string $alpha_3 Valeur du pays en alpha-3
   *
   * @return string
   */
  static function getPaysByAlpha($alpha_3) {
    $pays = new self;
    $pays->alpha_3 = $alpha_3;
    $pays->loadMatchingObject();

    return $pays;
  }

  /**
   * Retourne le nom fran�ais du pays
   *
   * @param string $numerique
   *
   * @return mixed
   */
  static function getNomFR($numerique) {
    static $noms = array();

    if (array_key_exists($numerique, $noms)) {
      return $noms[$numerique];
    }

    $pays = new self;
    $pays->numerique = $numerique;
    $pays->loadMatchingObject();

    return $noms[$numerique] = $pays->nom_fr;
  }

}
