<?php
/**
 * $Id: CViewSenderSource.class.php 24018 2014-07-17 14:47:31Z phenxdesign $
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 24018 $
 */

/**
 * View sender source class. 
 * @abstract Encapsulate an FTP source for view sending purposes only
 */
class CViewSenderSource extends CMbObject {
  // DB Table key
  public $source_id;
  
  // DB fields
  public $name;
  public $libelle;
  public $group_id;
  public $actif;
  public $archive;
  
  // Form fields
  public $_type_echange;

  /** @var CSourceFTP */
  public $_ref_source_ftp;
  
  /** @var CGroups */
  public $_ref_group;
  
  public $_reachable;
  
  /** @var CSourceToViewSender[] */
  public $_ref_senders;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "view_sender_source";
    $spec->key   = "source_id";
    $spec->uniques["name"] = array("name");
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["name"]     = "str notNull";
    $props["libelle"]  = "str";
    $props["group_id"] = "ref notNull class|CGroups autocomplete|text";
    $props["actif"]    = "bool notNull";
    $props["archive"]  = "bool notNull";
    
    $props["_reachable"] = "bool";
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["senders_link"] = "CSourceToViewSender source_id";
    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_type_echange = $this->_class;
    $this->_view         = $this->name . ($this->libelle ? " - $this->libelle" : "");
  }

  function loadRefGroup() {
    return $this->_ref_group = $this->loadFwdRef("group_id", 1);
  }
  
  function loadRefSourceFTP() {
    return $this->_ref_source_ftp = CExchangeSource::get("$this->_guid", "ftp", true, $this->_type_echange);
  }
  
  function loadRefSenders() {
    $senders_link = $this->loadBackRefs("senders_link");
    return $this->_ref_senders = CMbObject::massLoadFwdRef($senders_link, "sender_id");
  }
}
