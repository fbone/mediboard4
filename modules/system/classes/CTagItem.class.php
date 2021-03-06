<?php
/**
 * $Id: CTagItem.class.php 23021 2014-04-30 15:10:25Z phenxdesign $
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 23021 $
 */

/**
 * Tag link: links a tag to an object
 */
class CTagItem extends CMbMetaObject {
  public $tag_item_id;
  
  public $tag_id;
  
  /** @var CTag */
  public $_ref_tag;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "tag_item";
    $spec->key   = "tag_item_id";
    $spec->uniques["object"] = array("object_class", "object_id", "tag_id");
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["tag_id"] = "ref notNull class|CTag";
    $props["object_id"] .= " cascade seekable";
    return $props;
  }
  
  /**
   * Load tag
   *
   * @param bool $cache Use cache
   *
   * @return CTag
   */
  function loadRefTag($cache = true) {
    /** @var CTag $tag */
    $tag = $this->loadFwdRef("tag_id", $cache);
    $this->_view = $tag->_view;
    return $this->_ref_tag = $tag;
  }

  /**
   * @see parent::check()
   */
  function check(){
    if ($msg = parent::check()) {
      return $msg;
    }
    
    $this->completeField("tag_id", "object_class");
    
    if ($this->loadRefTag()->object_class !== $this->object_class) {
      return "L'objet et le tag doivent appartenir � la m�me classe";
    }

    return null;
  }
}
