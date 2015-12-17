<?php
/**
 * $Id: CModePaiement.class.php 19621 2013-06-20 20:40:45Z phenxdesign $
 *
 * @package    Mediboard
 * @subpackage GestionCab
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 19621 $
 */

/**
 * Mode de paiement
 */
class CModePaiement extends CMbObject {
  // DB Table key
  public $mode_paiement_id;

  // DB Fields
  public $function_id;
  public $nom;

  /** @var CFunctions */
  public $_ref_function;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'mode_paiement';
    $spec->key   = 'mode_paiement_id';
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["fiches_compta"] = "CGestionCab mode_paiement_id";
    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["function_id"] = "ref class|CFunctions";
    $props["nom"]         = "str notNull seekable";
    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "Mode de paiement '".$this->nom."'";
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    $this->_ref_function = new CFunctions();
    $this->_ref_function->load($this->function_id);
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
