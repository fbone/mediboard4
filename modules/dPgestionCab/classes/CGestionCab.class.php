<?php
/**
 * $Id: CGestionCab.class.php 19621 2013-06-20 20:40:45Z phenxdesign $
 *
 * @package    Mediboard
 * @subpackage GestionCab
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 19621 $
 */

/**
 * The CGestionCab Class
 */
class CGestionCab extends CMbObject {
  // DB Table key
  public $gestioncab_id;

  // DB Fields
  public $function_id;
  public $libelle;
  public $date;
  public $rubrique_id;
  public $montant;
  public $mode_paiement_id;
  public $num_facture;
  public $rques;

  //Filter Fields
  public $_date_min;
  public $_date_max;

  /** @var CFunctions */
  public $_ref_function;

  /** @var CRubrique */
  public $_ref_rubrique;

  /** @var CModePaiement */
  public $_ref_mode_paiement;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'gestioncab';
    $spec->key   = 'gestioncab_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["function_id"]      = "ref notNull class|CFunctions";
    $props["libelle"]          = "str notNull seekable";
    $props["date"]             = "date notNull";
    $props["rubrique_id"]      = "ref notNull class|CRubrique";
    $props["montant"]          = "currency notNull min|0";
    $props["mode_paiement_id"] = "ref notNull class|CModePaiement";
    $props["num_facture"]      = "num notNull";
    $props["rques"]            = "text";
    $props["_date_min"]        = "date";
    $props["_date_max"]        = "date moreThan|_date_min";
    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "Fiche '".$this->libelle."'";
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    // fonction (cabinet)
    $this->_ref_function = new CFunctions();
    $this->_ref_function->load($this->function_id);

    // rubrique
    $this->_ref_rubrique = new CRubrique();
    $this->_ref_rubrique->load($this->rubrique_id);

    // mode de paiement
    $this->_ref_mode_paiement = new CModePaiement();
    $this->_ref_mode_paiement->load($this->mode_paiement_id);
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($permType) {
    if (!$this->_ref_function) {
      $this->loadRefsFwd();
    }
    return $this->_ref_function->getPerm($permType);
  }
}
