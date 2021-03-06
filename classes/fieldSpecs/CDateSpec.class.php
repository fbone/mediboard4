<?php 
/**
 * $Id: CDateSpec.class.php 21047 2013-11-22 16:19:40Z charlyecho $
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision: 21047 $
 */

/**
 * Date type : DD-MM-YYYY
 */
class CDateSpec extends CMbFieldSpec {
  public $progressive;

  /**
   * @see parent::getSpecType()
   */
  function getSpecType() {
    return "date";
  } 

  /**
   * @see parent::getDBSpec()
   */
  function getDBSpec(){
    return "DATE";
  }

  /**
   * @see parent::getOptions()
   */
  function getOptions(){
    return array(
      'progressive' => 'bool',
    ) + parent::getOptions();
  }

  /**
   * @see parent::getValue()
   */
  function getValue($object, $smarty = null, $params = array()) {
    if ($smarty) {
      include_once $smarty->_get_plugin_filepath('modifier', 'date_format');
    }

    $propValue = $object->{$this->fieldName};
    $format = CValue::first(@$params["format"], CAppUI::conf("date"));
    return ($propValue && $propValue != "0000-00-00") ? 
      ($this->progressive ? $this->progressiveFormat($propValue) : smarty_modifier_date_format($propValue, $format)) :
      "";
    // TODO: test and use strftime($format, strtotime($propValue)) instead of smarty
  }

  /**
   * Format a progressive date
   *
   * @param date $value The date
   *
   * @return string
   */
  function progressiveFormat($value) {
    $parts = explode('-', $value);
    return (intval($parts[2]) ? $parts[2].'/' : '').(intval($parts[1]) ? $parts[1].'/' : '').$parts[0];
  }

  /**
   * @see parent::checkProperty()
   */
  function checkProperty($object){
    $propValue = &$object->{$this->fieldName};

    // Vérification du format
    $matches = array();
    if (!preg_match("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/", $propValue, $matches)) {
      if ($propValue === 'current'|| $propValue ===  'now') {
        $propValue = CMbDT::date();
        return null;
      } 
      return "Format de date invalide : '$propValue'";
    }

    // Mois grégorien
    $mois = intval($matches[2]);
    if (!CMbRange::in($mois, $this->progressive ? 0 : 1, 12)) { // Possibilité de mettre des mois vides ()
      return "Mois '$mois' non compris entre 1 et 12 ('$propValue')";
    }

    // Jour grégorien
    $jour = intval($matches[3]);
    if (!CMbRange::in($jour, $this->progressive ? 0 : 1, 31)) {
      return "Jour '$jour' non compris entre 1 et 31 ('$propValue')";
    }
  }

  /**
   * @see parent::sample()
   */
  function sample($object, $consistent = true){
    parent::sample($object, $consistent);
    $object->{$this->fieldName} = sprintf(
      "19%02d-%02d-%02d", 
      self::randomString(CMbFieldSpec::$nums, 2),
      self::randomString(CMbFieldSpec::$months, 1),
      self::randomString(CMbFieldSpec::$days, 1)
    );
  }

  /**
   * @see parent::getFormHtmlElement()
   */
  function getFormHtmlElement($object, $params, $value, $className) {
    return $this->getFormElementDateTime($object, $params, $value, $className, CAppUI::conf("date"));
  }

  /**
   * @see parent::getLitteralDescription()
   */
  function getLitteralDescription() {
    return "Date au format : 'YYYY-MM-DD'. ".
    parent::getLitteralDescription();
  }
}
