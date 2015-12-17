<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Intervenant d'activit� CdARR
 */
class CIntervenantCdARR extends CCdARRObject {  
  public $code;
  public $libelle;

  static $cached = array();

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table       = 'intervenant';
    $spec->key         = 'code';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    // DB Fields
    $props["code"]    = "str notNull length|2";
    $props["libelle"] = "str notNull maxLength|50";

    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "$this->code - $this->libelle";
    $this->_shortview = $this->code;
  }

  /**
   * Get an instance from the code
   *
   * @param string $code Code
   *
   * @return CIntervenantCdARR
   **/
  static function get($code) {
    if (!$code) {
      return new self();
    }

    if ($intervenant = SHM::get("intervenant_$code")) {
      return $intervenant;
    }

    $intervenant = new self();
    $intervenant->load($code);
    SHM::put("intervenant_$code", $intervenant);

    return $intervenant;
  }
}
