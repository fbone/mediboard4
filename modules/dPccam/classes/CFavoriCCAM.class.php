<?php
/**
 * $Id: CFavoriCCAM.class.php 23340 2014-05-27 15:05:38Z rhum1 $
 *
 * @package    Mediboard
 * @subpackage ccam
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 23340 $
 */

/**
 * Favoris CCAM
 */
class CFavoriCCAM extends CMbObject {
  public $favoris_id;

  // DB Fields
  public $object_class;
  public $favoris_user;
  public $favoris_code;

  // Form fields
  public $_filter_class;
  public $_ref_code;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'ccamfavoris';
    $spec->key   = 'favoris_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["favoris_user"] = "ref notNull class|CUser";
    $props["favoris_code"] = "str notNull length|7 seekable";
    $props["object_class"] = "str notNull";
    $props["_filter_class"] = "enum list|CConsultation|COperation|CSejour";
    return $props;
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    $this->_ref_code = CDatedCodeCCAM::get($this->favoris_code);
  }

  /**
   * Fill the order
   *
   * @param int    $user_id    User id
   * @param string $class      Favori class name (CFavoriCCAM or CFavoriCIM10)
   * @param bool   $ref_favori boolean
   * @param int     $tag_id     Tag id
   *
   * @return array The list ordered
   */
  static function getOrdered($user_id = 0, $class = null, $ref_favori = false, $tag_id = null) {
    $listOrdered = array();
    if ($user_id) {
      $where["favoris_user"] = "= '$user_id'";
      if ($class) {
        $where["object_class"] = "= '$class'";
      }

      $ljoin = array();
      if ($tag_id) {
        $ljoin["tag_item"] = "tag_item.object_id = favoris_id AND tag_item.object_class = 'CFavoriCCAM'";
        $where["tag_item.tag_id"] = "= '$tag_id'";
      }

      $fav = new CFavoriCCAM();
      /** @var CFavoriCCAM[] $favoris */
      $favoris = $fav->loadList($where, "favoris_code", null, null, $ljoin);

      foreach ($favoris as $_favori) {
        /** @var CDatedCodeCCAM $code */
        $code = CDatedCodeCCAM::get($_favori->favoris_code);
        $code->getChaps();

        $code->class = $_favori->object_class;
        $code->favoris_id = $_favori->favoris_id;
        $code->occ = 0;

        if ($ref_favori) {
          $_favori->loadRefsTagItems();
          $code->_ref_favori = $_favori;
        }

        $chapitre =& $code->chapitres[0];
        $listOrdered[$chapitre["code"]]["nom"] = $chapitre["nom"];
        $listOrdered[$chapitre["code"]]["codes"][$_favori->favoris_code] = $code;
      }
    }

    return $listOrdered;
  }

  /**
   * Returns the tree
   *
   * @param int $user_id User id
   *
   * @return array
   */
  static function getTree($user_id) {
    return self::getTreeGeneric($user_id, "CFavoriCCAM");
  }

  /**
   * Returns the tag items tree with all the favoris
   *
   * @param int    $user_id      User id
   * @param string $favori_class Favori class name (CFavoriCCAM or CFavoriCIM10)
   *
   * @return array
   */
  static function getTreeGeneric($user_id, $favori_class) {
    $tree = CTag::getTree($favori_class);

    self::getFavorisTree($tree, $user_id, $favori_class);

    return $tree;
  }

  /**
   * Fill in the subtree with associated favoris objects
   *
   * @param array  &$subtree     Subtree of tag items
   * @param int    $user_id      User id
   * @param string $favori_class Favori class name (CFavoriCCAM or CFavoriCIM10)
   *
   * @return void
   */
  static function getFavorisTree(&$subtree, $user_id, $favori_class) {
    /** @var CMbObject $favori */
    $favori = new $favori_class;

    $table_name = $favori->_spec->table;
    $where = array(
      "$table_name.favoris_user" => "= '$user_id'",
    );

    if ($subtree["parent"]) {
      $where["tag_item.tag_id"] = "= '{$subtree['parent']->tag_id}'";
    }
    else {
      $where["tag_item.tag_id"] = "IS NULL";
    }

    $ljoin = array(
      "tag_item" => "tag_item.object_id = $table_name.favoris_id AND tag_item.object_class = '$favori_class'",
    );

    $subtree["objects"] = $favori->loadList($where, null, null, null, $ljoin);

    foreach ($subtree["children"] as &$_subtree) {
      self::getFavorisTree($_subtree, $user_id, $favori_class);
    }
  }
}
