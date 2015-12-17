<?php

/**
 * dPccam
 *
 * Classe des proc�dures CCAM
 *
 * @category Ccam
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

/**
 * Class CProcedureCCAM
 *
 * Proc�dures
 * Niveau Acte
*/
class CProcedureCCAM extends CCCAM {
  public $date_effet;
  public $code_procedure;
  public $_ref_code;

  /**
   * Mapping des donn�es depuis la base de donn�es
   *
   * @param array $row Ligne d'enregistrement de de base de donn�es
   *
   * @return void
  */
  function map($row) {
    $this->date_effet     = $row["DATEEFFET"];
    $this->code_procedure = $row["CODEPROCEDURE"];
    $this->_ref_code      = CCodeCCAM::getCodeInfos($this->code_procedure);
  }

  /**
   * Chargement de a liste des proc�dures pour un code
   *
   * @param string $code Code CCAM
   *
   * @return self[] Liste des proc�dures
   */
  static function loadListFromCode($code) {
    $ds = self::$spec->ds;

    $query = "SELECT p_acte_procedure.*
      FROM p_acte_procedure
      WHERE p_acte_procedure.CODEACTE = %
      ORDER BY p_acte_procedure.DATEEFFET DESC, p_acte_procedure.CODEPROCEDURE ASC";
    $query = $ds->prepare($query, $code);
    $result = $ds->exec($query);

    $list_procedures = array();
    while ($row = $ds->fetchArray($result)) {
      $procedure = new CProcedureCCAM();
      $procedure->map($row);
      $list_procedures[$row["DATEEFFET"]][] = $procedure;
    }

    return $list_procedures;
  }
}