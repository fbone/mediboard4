<?php
/**
 * $Id: CHL7v2DataTypeComposite.class.php 20987 2013-11-18 11:20:01Z phenxdesign $
 * 
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision: 20987 $
 */

class CHL7v2DataTypeComposite extends CHL7v2DataType {
  public $components = array();
  public $description;
  
  /*protected */function __construct($message, $datatype, $version, $extension) {
    parent::__construct($message, $datatype, $version, $extension);
    
    $specs = $this->getSpecs();
    $this->description = $specs->queryTextNode("description");
  }
  
  function getRegExpMB() {
    //
  }
  
  function getRegExpHL7() {
    //
  }
  
  function toHL7($components, CHL7v2Field $field){
    $hl7 = array();
    
    foreach ($components as $k => $component) {
      if (!array_key_exists($k, $this->components)) {
        break;
      }
      
      $hl7[] = $this->components[$k]->toHL7($component, $field);
    }
    
    return $hl7;
  }
  
  function validate($components_data, CHL7v2Field $field) {
    // Sometimes, we have a string here (OBR-32-1-2)
    if (!is_array($components_data)) {
      $components_data = array($components_data);
    }
    
    foreach ($components_data as $k => $component_data) {
      if (!array_key_exists($k, $this->components)) {
        break;
      }
      
      if (!$this->components[$k]->validate($component_data, $field)) {
        $field->error(CHL7v2Exception::INVALID_DATA_FORMAT, var_export($component_data, true)." ($this->type)", $field);
        return false;
      }
    }
    
    return true;
  }
}
