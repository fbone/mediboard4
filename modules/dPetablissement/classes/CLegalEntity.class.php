<?php

/**
 * $Id$
 *
 * @category Etablissement
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */

/**
 * Class CEntity
 *
 */
class CLegalEntity extends CEntity {
  // DB Fields
  public $legal_entity_id;

  public $name;
  public $finess;
  public $rmess;
  public $address;
  public $zip_code;
  public $city;
  public $country;
  public $insee;
  public $siren;
  public $nic;
  public $legal_status_code;

  // Forward Ref
  public $_refs_authorizations;
  public $_refs_groups;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'legal_entity';
    $spec->key   = 'legal_entity_id';
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["groups"] = "CGroups legal_entity_id";

    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    $props["name"]            = "str notNull confidential";
    $props["finess"]          = "numchar length|9 confidential mask|9xS9S99999S9 control|luhn";
    $props["rmess"]           = "numchar length|9 confidential mask|9xS9S99999S9 control|luhn";
    $props["address"]         = "text confidential";
    $props["zip_code"]        = "str minLength|4 maxLength|10";
    $props["city"]            = "str maxLength|50 confidential";
    $props["country"]         = "num length|3";
    $props["insee"]           = "numchar length|3";
    $props["siren"]           = "numchar length|9 confidential mask|9xS9S99999S9 control|luhn";
    $props["nic"]             = "num length|5";
    $props["legal_status_code"] = "ref class|CLegalStatus autocomplete|description show|0";

    return $props;
  }


  /**
   * @see parent::mapEntityTo()
   */
  function mapEntityTo () {
    $this->_name = $this->name;
  }

  /**
   * @see parent::mapEntityFrom()
   */
  function mapEntityFrom () {
    $this->name = $this->_name;
  }

  /**
   * loading Method for CGroups associated to a LegalEntity
   *
   * @return CGroups[]
   */
  function loadRefsGroups() {
    return $this->_refs_groups = $this->loadBackRefs("groups");
  }

}
