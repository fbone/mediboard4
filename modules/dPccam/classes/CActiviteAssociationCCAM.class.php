<?php

/**
 * dPccam
 *
 * Classe des associations pr�vues des actes CCAM
 *
 * @category Ccam
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

/**
 * Class CActiviteAssociationCCAM
 *
 * Associations m�dicales pr�vues code � code
 * Niveau activite
 */
class CActiviteAssociationCCAM extends CCCAM {

  public $date_effet;
  public $acte_asso;
  public $activite_asso;
  public $regle;
  public $_ref_code;

  /**
   * Mapping des donn�es depuis la base de donn�es
   *
   * @param array $row Ligne d'enregistrement de de base de donn�es
   *
   * @return void
   */
  function map($row) {
    $this->date_effet    = $row["DATEEFFET"];
    $this->acte_asso     = $row["ACTEASSO"];
    $this->activite_asso = $row["ACTIVITEASSO"];
    $this->regle         = $row["REGLE"];
    $this->_ref_code     = CCodeCCAM::getCodeInfos($this->acte_asso);
  }

  /**
   * Chargement de a liste des associations pr�vues pour une activite
   *
   * @param string $code     Code CCAM
   * @param string $activite Activit� CCAM
   *
   * @return self[][] Liste des associations pr�vues
   */
  static function loadListFromCodeActivite($code, $activite) {
    $ds = self::$spec->ds;

    $query = "SELECT p_activite_associabilite.*
      FROM p_activite_associabilite
      WHERE p_activite_associabilite.CODEACTE = %1
      AND p_activite_associabilite.ACTIVITE = %2
      ORDER BY p_activite_associabilite.DATEEFFET DESC";
    $query = $ds->prepare($query, $code, $activite);
    $result = $ds->exec($query);

    $list_asso = array();
    while ($row = $ds->fetchArray($result)) {
      $asso = new CActiviteAssociationCCAM();
      $asso->map($row);
      $list_asso[$row["DATEEFFET"]][] = $asso;
    }

    return $list_asso;
  }
}