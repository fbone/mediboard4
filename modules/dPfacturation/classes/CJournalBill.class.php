<?php
/**
 * $Id: CJournalBill.class.php 20826 2013-10-31 10:45:28Z aurelie17 $
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 20826 $
 */

/**
 * Les journaux de facture
 *
 */
class CJournalBill extends CMbObject {
  // DB Table key
  public $journal_id;

  // DB Fields
  public $nom;
  public $type;
  public $checklist_id;

  // References
  public $_ref_checklist;
  public $_ref_liaisons;
  public $_ref_factures = array();

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'facture_journal';
    $spec->key   = 'journal_id';
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["journal_link"]  = "CJournalLiaison journal_id";
    $backProps["checklist"]     = "CJournalBill checklist_id";
    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["nom"]     = "text notNull";
    $specs["type"]    = "enum list|paiement|debiteur|rappel|checklist";
    $specs["checklist_id"] = "ref class|CJournalBill";
    return $specs;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }

  /**
   * @see parent::store()
   */
  function store() {
    $create = $this->_id;
    // Standard store
    if ($msg = parent::store()) {
      return $msg;
    }
    if (!$create && count($this->_factures)) {
      //$this->completeField("journal_id");
      foreach ($this->_factures as $facture) {
        $link = new CJournalLiaison();
        $link->journal_id   = $this->journal_id;
        $link->object_id    = $facture->_id;
        $link->object_class = $facture->_class;
        if ($msg = $link->store()) {
          return $msg;
        }
      }
    }
  }

  /**
   * Charge la checklist
   *
   * @return CJournalBill
   */
  function loadRefCheckList() {
    return $this->_ref_checklist = $this->loadFwdRef("checklist_id", true);
  }

  /**
   * Charge les factures correspondant au journal
   *
   * @return CFacture[]
   */
  function loadRefsFacture() {
    $this->_ref_liaisons = $this->loadBackRefs("journal_link");
    foreach ($this->_ref_liaisons as $lien) {
      /* @var CJournalLiaison $lien*/
      $facture = $lien->loadTargetObject();
      $this->_ref_factures[$facture->_id] = $facture;
    }
    return $this->_ref_factures;
  }

}