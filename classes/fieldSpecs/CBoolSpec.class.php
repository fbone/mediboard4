<?php 
/**
 * $Id: CBoolSpec.class.php 24389 2014-08-12 07:44:48Z charlyecho $
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision: 24389 $
 */

/**
 * Boolean value (0 or 1)
 */
class CBoolSpec extends CMbFieldSpec {
  public $_list;
  public $_locales;
  public $typeEnum;

  static $_default_no = true; // @todo : faire en sorte de se passer de �a

  /**
   * @see parent::__construct()
   */
  function __construct($className, $field, $prop = null, $aProperties = array()) {
    parent::__construct($className, $field, $prop, $aProperties);
    foreach ($this->_list = array(0,1) as $value) {
      $this->_locales[$value] = CAppUI::tr("bool.$value");
    }
  }

  /**
   * @see parent::getSpecType()
   */
  function getSpecType() {
    return "bool";
  }

  /**
   * @see parent::getDBSpec()
   */
  function getDBSpec(){
    return "ENUM('0','1')";
  }

  /**
   * @see parent::getOptions()
   */
  function getOptions(){
    return array(
      'default' => 'bool',
      'typeEnum' => array('radio', 'select', 'checkbox'),
    ) + parent::getOptions();
  }

  /**
   * @see parent::getValue()
   */
  function getValue($object, $smarty = null, $params = array()) {
    return CAppUI::tr("bool.".$object->{$this->fieldName});
  }

  /**
   * @see parent::checkOptions()
   */
  function checkOptions(){
    parent::checkOptions();

    if ($this->default === null) {
      $this->default = (self::$_default_no ? 0 : "");
    }
  }

  /**
   * @see parent::checkProperty()
   */
  function checkProperty($object){
    $value = $object->{$this->fieldName};

    // Has to be numeric
    $value = CMbFieldSpec::checkNumeric($value, true);
    if ($value === null) {
      return "N'est pas une cha�ne num�rique";
    }

    // Only two options
    if ($value !== 0 && $value != 1) {
      return "Ne peut �tre diff�rent de 0 ou 1";
    }

    return null;
  }

  /**
   * @see parent::getFormHtmlElement()
   */
  function getFormHtmlElement($object, $params, $value, $className){
    $sHtml         = "";
    $field         = CMbString::htmlSpecialChars($this->fieldName);
    $typeEnum      = CMbArray::extract($params, "typeEnum", $this->typeEnum ? $this->typeEnum : "radio");
    $separator     = CMbArray::extract($params, "separator");
    $disabled      = CMbArray::extract($params, "disabled");
    $readonly      = CMbArray::extract($params, "readonly"); 
    $default       = CMbArray::extract($params, "default", $this->default);
    $form          = CMbArray::extract($params, "form"); // needs to be extracted
    $className     = CMbString::htmlSpecialChars(trim("$className $this->prop"));
    $extra         = CMbArray::makeXmlAttributes($params);

    // Empty label
    if ($emptyLabel = CMbArray::extract($params, "emptyLabel")) {
      $emptyLabel = "&mdash; ". CAppUI::tr($emptyLabel);
    }

    switch ($typeEnum) {
      case "radio":
        // Attributes for all inputs
        $attributes = array(
          "type" => "radio",
          "name" => $field,
        );

        if (null === $value) {
          $value = "$default";
        }

        for ($i = 1; $i >= 0; $i--) {
          $attributes["value"] = "$i"; 
          $attributes["checked"] = $value === "$i" ? "checked" : null; 
          $attributes["disabled"] = $disabled === "$i" || $readonly ? "disabled" : null; 
          $attributes["class"] = $className;

          $xmlAttributes = CMbArray::makeXmlAttributes($attributes);
          $sHtml .= "\n<input $xmlAttributes $extra />";

          $sTr = CAppUI::tr("bool.$i");
          $sHtml .= "\n<label for=\"{$field}_$i\">$sTr</label> ";

          if ($separator && $i != 0) {
            $sHtml .= "\n$separator";
          }
        }
        return $sHtml;

      case "checkbox":
        $disabled = $readonly ? "disabled=\"1\"" : $disabled;

        if (null === $value) {
          $value = "$default";
        }

        if ($value !== null && $value == 1) {
          $checked = " checked=\"checked\""; 
        }
        else {
          $checked = "";
          $value = "0";
        }

        $sHtml = '<input type="checkbox" name="__'.$field.'" 
          onclick="$V(this.form.'.$field.', $V(this)?1:0);" '.$checked.' '.$disabled.' />';

        $sHtml .= '<input type="hidden" name="'.$field.'" '.$extra.' value="'.$value.'" />';
        return $sHtml;

      case "select":
        $disabled = $readonly ? "disabled=\"1\"" : $disabled;
        $sHtml       = "<select name=\"$field\" class=\"$className\" $disabled $extra>";

        if ($emptyLabel) {
          if ($value === null) {
            $sHtml    .= "\n<option value=\"\" selected=\"selected\">$emptyLabel</option>";
          }
          else {
            $sHtml    .= "\n<option value=\"\">$emptyLabel</option>";
          }
        }

        foreach ($this->_locales as $key => $item) {
          if (($value !== null && $value === "$key") || ($value === null && "$key" === "$this->default" && !$emptyLabel)) {
            $selected = " selected=\"selected\""; 
          }
          else {
            $selected = "";
          }
          $sHtml    .= "\n<option value=\"$key\" $selected>$item</option>";
        }
        $sHtml      .= "\n</select>";
        return $sHtml;
    }
  }

  /**
   * @see parent::getLabelForAttribute()
   */
  function getLabelForAttribute($object, &$params){
    $typeEnum  = CMbArray::extract($params, "typeEnum", "radio");

    switch ($typeEnum) {
      //case "radio":    return "{$this->fieldName}_1";
      case "checkbox":
        return "__$this->fieldName";

      default:
      case "radio":
      case "select":
        return $this->fieldName;
    }
  }

  /**
   * @see parent::sample()
   */
  function sample($object, $consistent = true){
    parent::sample($object, $consistent);
    $object->{$this->fieldName} = rand(0, 1);
  }

  /**
   * @see parent::regressionSamples()
   */
  function regressionSamples() {
    return array("0","1");
  }

  /**
   * @see parent::getLitteralDescription()
   */
  function getLitteralDescription() {
    return "Bool�en au format : '0, 1'. ".
      parent::getLitteralDescription();
  }
}
