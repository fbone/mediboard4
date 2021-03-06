<?php
/**
 * $Id: CPersonnel.class.php 26602 2014-12-29 16:03:37Z aurelie17 $
 *
 * @package    Mediboard
 * @subpackage dPpersonnel
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 26602 $
 */

/**
 * Class CPersonnel
 */
class CPersonnel extends CMbObject {
  // DB Table key
  public $personnel_id;

  // DB references
  public $user_id;
  public $_ref_user;

  // DB fields
  public $emplacement;
  public $actif;

  // Form Field
  public $_user_last_name;
  public $_user_first_name;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'personnel';
    $spec->key   = 'personnel_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["user_id"]     = "ref notNull class|CMediusers";
    $props["emplacement"] = "enum notNull list|op|op_panseuse|reveil|service|iade|brancardier|sagefemme|manipulateur default|op";
    $props["actif"]       = "bool notNull default|1";

    $props["_user_last_name" ] = "str";
    $props["_user_first_name"] = "str";
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['affectations'] = 'CAffectationPersonnel personnel_id';
    $backProps['brancard_pec'] = 'CBrancardageItem pec_user_id';
    return $backProps;
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadRefUser();
  }

  /**
   * Load User
   *
   * @return CMediusers|null
   */
  function loadRefUser() {
    $this->_ref_user = $this->loadFwdRef("user_id", true);
    $this->_view = $this->getFormattedValue("emplacement") . ": " . $this->_ref_user->_view;
    return $this->_ref_user;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->getFormattedValue("emplacement") . ": " . $this->user_id;
  }

  /**
   * Load list overlay for current group
   *
   * @param array $where   where
   * @param array $order   order
   * @param int   $limit   limit
   * @param array $groupby groupby
   * @param array $ljoin   ljoin
   *
   * @return array[CPersonnel]
   */
  function loadGroupList($where = array(), $order = null, $limit = null, $groupby = null, $ljoin = array()) {
    $ljoin["users_mediboard"] = "users_mediboard.user_id = personnel.user_id";
    $ljoin["functions_mediboard"] = "functions_mediboard.function_id = users_mediboard.function_id";
    // Filtre sur l'établissement
    $g = CGroups::loadCurrent();
    $where["functions_mediboard.group_id"] = "= '$g->_id'";

    return $this->loadList($where, $order, $limit, $groupby, $ljoin);
  }

  /**
   * Charge le personnel pour l'établissement courant
   *
   * @param string $emplacement Emplacement du personnel
   * @param bool   $actif       Seulement les actifs
   * @param bool   $groupby     Grouper par utilisateur
   *
   * @return CPersonnel[]
   */
  static function loadListPers($emplacement, $actif = true, $groupby = false){
    $personnel = new CPersonnel();

    $where = array();

    if (is_array($emplacement)) {
      $where["emplacement"] = $personnel->_spec->ds->prepareIn($emplacement);
    }
    else {
      $where["emplacement"] = "= '$emplacement'";
    }

    // Could have been ambiguous with CMediusers.actif
    if ($actif) {
      $where[] = "personnel.actif = '1'";
      $where[] = "users_mediboard.actif = '1'";
    }

    $ljoin["users"]           = "personnel.user_id = users.user_id";
    $ljoin["users_mediboard"] = "users_mediboard.user_id = users.user_id";

    $order = "users.user_last_name";

    $group = $groupby ? "personnel.user_id" : null;

    /** @var CPersonnel[] $personnels */
    $personnels = $personnel->loadGroupList($where, $order, null, $group, $ljoin);
    $users = CMbObject::massLoadFwdRef($personnels, "user_id");
    CMbObject::massLoadFwdRef($users, "function_id");

    foreach ($personnels as $_personnel) {
      $_personnel->loadRefUser()->loadRefFunction();
    }
    return $personnels;
  }
}
