<?php
/**
 * $Id: httpreq_do_synchronize_sejours.php 20186 2013-08-19 07:47:12Z phenxdesign $
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 20186 $
 */

global $can, $m, $g;

$affectation = new CAffectation;
$dateMin = CValue::get("dateMin", "YYYY-MM-JJ");
$where = array();
if ($dateMin != "YYYY-MM-JJ") {
  $where["sortie"] = ">= '$dateMin 00:00:00'";
}

$listAffectations = $affectation->loadList($where);

$entrees = 0;
$sorties = 0;

foreach ($listAffectations as &$curr_aff) {
  $curr_aff->loadRefsFwd();
   
  $changeSejour = 0;
  
  if (!$curr_aff->_ref_prev->affectation_id && $curr_aff->sejour_id) {
    if ($curr_aff->entree != $curr_aff->_ref_sejour->entree_prevue) {
      $curr_aff->_ref_sejour->entree_prevue = $curr_aff->entree;
      $changeSejour = 1;
      $entrees++;
    }
  }
  if (!$curr_aff->_ref_next->affectation_id  && $curr_aff->sejour_id) {
    if ($curr_aff->sortie != $curr_aff->_ref_sejour->sortie_prevue) {
      $curr_aff->_ref_sejour->sortie_prevue = $curr_aff->sortie;
      $changeSejour = 1;
      $sorties++;
    }
  }
  if ($changeSejour) {
    if ($msg = $curr_aff->store()) {
      CAppUI::stepAjax("Erreur avec l'affectation $curr_aff->_id : $msg", UI_MSG_ERROR);
    }
  }
}

$result = $entrees + $sorties;

CAppUI::stepAjax("$result sejour(s) modifi�s : $entrees entr�e(s) et $sorties sortie(s)", UI_MSG_OK);

