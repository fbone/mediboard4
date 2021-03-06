<?php
/**
 * $Id: CExClassFieldProperty.class.php 19290 2013-05-26 19:48:24Z phenxdesign $
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 19290 $
 */

class CExClassFieldProperty extends CMbMetaObject {
  public $ex_class_field_property_id;

  public $type;
  public $value;
  public $_value;
  public $predicate_id;
  
  /** @var CExClassField|CExClassMessage|CExClassFieldSubgroup */
  public $_ref_object;
  
  /** @var CExClassFieldPredicate */
  public $_ref_predicate;
  
  static $_style_types = array(
    "background-color" => "color",
    "color"            => "color",
    "font-weight"      => "font",
    "font-style"       => "font",
    "font-size"        => "font",
    "font-family"      => "font",
    "text-decoration"  => "font",
  );

  static $_style_values = array(
    "font-weight"     => array("bold", "normal"),
    "font-style"      => array("italic", "normal"),
    "font-family"     => array("sans-serif", "serif", "monospace", "cursive"),
    "text-decoration" => array("underline", "overline", "line-through"),
  );

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_class_field_property";
    $spec->key   = "ex_class_field_property_id";
    $spec->uniques["type"] = array("object_class", "object_id", "type", "predicate_id");
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["object_class"] = "enum notNull list|CExClassField|CExClassMessage|CExClassFieldSubgroup";
    $props["object_id"]    = "ref notNull class|CMbObject meta|object_class cascade";
    $props["predicate_id"] = "ref class|CExClassFieldPredicate cascade";
    $props["type"]         = "enum list|".implode("|", array_keys(self::$_style_types));
    $props["value"]        = "str";
    $props["_value"]       = "str";
    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();

    $this->_value = $this->value;
    if (array_key_exists($this->type, self::$_style_values)) {
      $this->_value = CAppUI::tr("$this->_class.value.$this->type.$this->value");
    }

    $this->_view = $this->getFormattedValue("type").": $this->value";
  }
  
  function isColor() {
    return self::$_style_types[$this->type] == "color";
  }
  
  static function getColorStyles() {
    $styles = array();
    foreach (self::$_style_types as $_key => $_type) {
      if ($_type == "color") {
        $styles[] = $_key;
      }
    }
    
    return $styles;
  }

  /**
   * Get default styles
   *
   * @param CExClassField|CExClassMessage|CExClassFieldSubgroup $object Object to get the default styles of
   *
   * @return array
   */
  static function getDefaultPropertiesFor(CMbObject $object) {
    static $types;

    if (empty($types)) {
      $prop = new self;
      $types = $prop->_specs["type"]->_list;
    }

    $default = array_fill_keys($types, ""); // Doit etre une chaine vide pour IE

    $properties = $object->loadRefProperties();
    foreach ($properties as $_property) {
      if ($_property->predicate_id || $_property->value == "") {
        continue;
      }

      $default[$_property->type] = $_property->value;
    }

    return $default;
  }
  
  /**
   * @param bool $cache
   * 
   * @return CExClassFieldPredicate
   */
  function loadRefPredicate($cache = true) {
    return $this->_ref_predicate = $this->loadFwdRef("predicate_id", $cache);
  }
}
