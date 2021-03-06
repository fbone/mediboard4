<?php
/**
 * $Id: CHL7v2TableEntry.class.php 28812 2015-07-02 07:03:51Z clementberard $
 * 
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision: 28812 $
 */

/**
 * Class CHL7v2TableEntry 
 * HL7 Table Entry
 */
class CHL7v2TableEntry extends CHL7v2TableObject { 
  // DB Table key
  public $table_entry_id;
  
  public $number;
  
  public $code_hl7_from;
  public $code_hl7_to;
  
  public $code_mb_from;
  public $code_mb_to;
  
  public $description;
  public $user;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "table_entry";
    $spec->key   = "table_entry_id";
    $spec->uniques["number_code_hl7"] = array("number", "code_hl7_from");
    $spec->uniques["number_code_mb"]  = array("number", "code_mb_from");
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["number"]        = "num notNull maxLength|5 seekable";
    $props["code_hl7_from"] = "str maxLength|30 protected";
    $props["code_hl7_to"]   = "str maxLength|30 protected";
    $props["code_mb_from"]  = "str maxLength|30 protected";
    $props["code_mb_to"]    = "str maxLength|30 protected";
    $props["description"]   = "str seekable";
    $props["user"]          = "bool notNull default|0";
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    return array();
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_view      = $this->description;
    $this->_shortview = $this->number;
  }

  /**
   * Get table values
   *
   * @param string $table   Table HL7
   *
   * @param bool   $from_mb true
   *
   * @return array The table
   */
  static function getTable($table, $from_mb = true) {
    return CHL7v2::getTable($table, $from_mb);
  }

  /**
   * Get HL7 value
   *
   * @param string $table   Table HL7
   * @param string $mbValue Mediboard value
   *
   * @return string
   */
  static function mapTo($table, $mbValue) {
    return CHL7v2::getTableHL7Value($table, $mbValue);
  }

  /**
   * Get Mediboard value
   *
   * @param string $table    Table HL7
   * @param string $hl7Value HL7 value
   *
   * @return string
   */
  static function mapFrom($table, $hl7Value) {
    if ($value = CHL7v2::getTableMbValue($table, $hl7Value)) {
      return $value;
    }
    
    return null;
  }

  /**
   * Get table description
   *
   * @param string $table    Table HL7
   * @param string $hl7Value HL7 value
   *
   * @return string
   */
  static function getDescription($table, $hl7Value) {
    if ($value = CHL7v2::getTableDescription($table, $hl7Value)) {
      return $value;
    }

    return null;
  }
}
