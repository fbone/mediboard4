<?php
/**
 * $Id: do_items_liaisons_aed.php 26993 2015-02-02 08:16:19Z flaviencrochard $
 *
 * @package    Mediboard
 * @subpackage dPhospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 26993 $
 */

$liaisons_j = CValue::post("liaisons_j");
$liaisons_p = CValue::post("liaisons_p");
$sejour_id  = CValue::post("sejour_id");

if (is_array($liaisons_p)) {
  foreach ($liaisons_p as $liaison_id=>$quantite) {
    $item_liaison = new CItemLiaison;
    $item_liaison->load($liaison_id);

    // Enregistrement si la quantit� est valide
    if ($quantite) {
      $item_liaison->quantite = $quantite;
      $item_liaison->store();
    }
    // Suppression sinon
    else {
      $item_liaison->delete();
    }
  }
}

if (is_array($liaisons_j)) {
  foreach ($liaisons_j as $prestation_id => $by_date) {
    foreach ($by_date as $date=>$liaison) {
      $souhait_id = null;
      $realise_id = null;
      $sous_item_id = null;

      $item_liaison = new CItemLiaison();
      
      // Liaison utilis�e pour l'affichage
      // Pas de store
      if ((@isset($liaison['souhait']['temp']) && !@isset($liaison['realise']['new'])) ||
          (@isset($liaison['realise']['temp']) && !@isset($liaison['souhait']['new']))) {
        continue;
      }
      
      if (isset($liaison['souhait'])) {
        if (isset($liaison['souhait']['new'])) {
          $souhait_id = $liaison['souhait']['new']['item_souhait_id'];
          $sous_item_id = @$liaison['souhait']['new']['sous_item_id'];
        }
        else {
          $souhait_id = reset($liaison['souhait']);
          if ($souhait_id) {
            $souhait_id = @$souhait_id['item_souhait_id'];
          }
          $sous_item_id = @reset($liaison['souhait']);
          if ($sous_item_id) {
            $sous_item_id = @$sous_item_id['sous_item_id'];
          }
          if ($sous_item_id === null) {
            $sous_item_id = "";
          }
          $item_liaison->load(reset(array_keys($liaison['souhait'])));
        }
      }
      
      if (isset($liaison['realise'])) {
        if (isset($liaison['realise']['new'])) {
          $realise_id = $liaison['realise']['new']['item_realise_id'];
        }
        else {
          $realise_id = reset($liaison['realise']);
          if ($realise_id) {
            $realise_id = @$realise_id['item_realise_id'];
          }
          if (!$item_liaison->_id) {
            $item_liaison->load(reset(array_keys($liaison['realise'])));
          }
        }
      }

      if (!$item_liaison->_id) {
        $item_liaison->date = $date;
        $item_liaison->sejour_id = $sejour_id;
      }

      // On ne store que si c'est nouvelle liaison
      // ou un changement de niveau
      if (!$item_liaison->_id ||
          $item_liaison->item_souhait_id != $souhait_id ||
          $item_liaison->item_realise_id != $realise_id ||
          $item_liaison->sous_item_id != $sous_item_id) {

        $item_liaison->item_souhait_id = $souhait_id;
        $item_liaison->item_realise_id = $realise_id;
        $item_liaison->sous_item_id = $sous_item_id;
        $item_liaison->store();
      }
    }
  }
}

CAppUI::setMsg("CItemLiaison-modified_selection");

echo CAppUI::getMsg();
CApp::rip();