<?php
/**
 * $Id: CContentTabular.class.php 22658 2014-03-28 12:01:22Z nicolasld $
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 22658 $
 */

/**
 * Tabular content
 */
class CContentTabular extends CMbObject {
  public $content_id;
  
  // DB Fields
  public $content;
  public $import_id;
  public $separator;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'content_tabular';
    $spec->key   = 'content_id';
    $spec->loggable = false;
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() { 
    $props = parent::getProps();
    $props["content"]   = "text show|0";
    $props["import_id"] = "num";
    $props["separator"] = "str length|1";
    
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["messages_hprim21"]         = "CEchangeHprim21 message_content_id";
    $backProps["acquittements_hprim21"]    = "CEchangeHprim21 acquittement_content_id";
    $backProps["messages_hprimsante"]      = "CExchangeHprimSante message_content_id";
    $backProps["acquittements_hprimsante"] = "CExchangeHprimSante acquittement_content_id";
    $backProps["messages_hl7v2"]           = "CExchangeHL7v2 message_content_id";
    $backProps["acquittements_hl7v2"]      = "CExchangeHL7v2 acquittement_content_id";
    return $backProps;
  }
}
