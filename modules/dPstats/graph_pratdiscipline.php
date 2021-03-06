<?php
/**
 * $Id: graph_pratdiscipline.php 27974 2015-04-15 10:35:17Z kgrisel $
 *
 * @package    Mediboard
 * @subpackage dPstats
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 27974 $
 */

/**
 * R�cup�ration du graphique du nombre d'interventions par praticien
 *
 * @param string $debut         Date de d�but
 * @param string $fin           Date de fin
 * @param int    $prat_id       Identifiant du praticien
 * @param int    $salle_id      Identifiant de la salle
 * @param int    $bloc_id       Identifiant du bloc
 * @param int    $func_id       Identifiant du cabinet
 * @param int    $discipline_id Identifiant de la discipline
 * @param string $codeCCAM      Code CCAM
 * @param string $type_hospi    Type d'hospitalisation
 * @param bool   $hors_plage    Prise en compte des hors plage
 *
 * @return array
 */
function graphPraticienDiscipline(
    $debut = null, $fin = null, $prat_id = 0, $salle_id = 0, $bloc_id = 0,
    $func_id = 0, $discipline_id = 0, $codeCCAM = "", $type_hospi = "", $hors_plage = true
) {
  if (!$debut) {
    $debut = CMbDT::date("-1 YEAR");
  }
  if (!$fin) {
    $fin = CMbDT::date();
  }

  $discipline = new CDiscipline();
  $discipline->load($discipline_id);
  
  $ticks = array();
  $serie_total = array(
    'label' => 'Total',
    'data' => array(),
    'markers' => array('show' => true),
    'bars' => array('show' => false)
  );
  for ($i = $debut; $i <= $fin; $i = CMbDT::date("+1 MONTH", $i)) {
    $ticks[] = array(count($ticks), CMbDT::transform("+0 DAY", $i, "%m/%Y"));
    $serie_total['data'][] = array(count($serie_total['data']), 0);
  }
  
  $user = new CMediusers();
  $ljoin = array(
    "users" => "users.user_id = users_mediboard.user_id",
    "functions_mediboard" => "functions_mediboard.function_id = users_mediboard.function_id"
  );
  $where = array(
    "functions_mediboard.group_id" => "= '".CGroups::loadCurrent()->_id."'"
  );
  if ($discipline_id) {
    $where["users_mediboard.discipline_id"] = " = '$discipline_id'";
  }
  if ($prat_id) {
    $where["users_mediboard.user_id"] = " = '$prat_id'";
  }
  
  $user_types = array("Chirurgien", "Anesth�siste", "M�decin");
  $utypes_flip = array_flip(CUser::$types);
  if (is_array($user_types)) {
    foreach ($user_types as $key => $value) {
      $user_types[$key] = $utypes_flip[$value];
    }
     $where["users.user_type"] = CSQLDataSource::prepareIn($user_types);
  }

  $order = "`users`.`user_last_name`, `users`.`user_first_name`";

  $users = $user->loadList($where, $order, null, null, $ljoin);

  // Gestion du hors plage
  $where_hors_plage = !$hors_plage ? "AND operations.plageop_id IS NOT NULL" : "";
  
  $total = 0;
  $series = array();
  foreach ($users as $user) {
    $serie = array(
      'data' => array(),
      'label' => utf8_encode($user->_view)
    );
      
    $query = "SELECT COUNT(operations.operation_id) AS total,
      DATE_FORMAT(operations.date, '%m/%Y') AS mois,
      DATE_FORMAT(operations.date, '%Y%m') AS orderitem,
      users_mediboard.user_id
      FROM operations
      INNER JOIN sallesbloc ON operations.salle_id = sallesbloc.salle_id
      LEFT JOIN sejour ON operations.sejour_id = sejour.sejour_id
      LEFT JOIN users_mediboard ON operations.chir_id = users_mediboard.user_id
      LEFT JOIN users ON users_mediboard.user_id = users.user_id
      WHERE operations.annulee = '0'
      AND operations.date BETWEEN '$debut' AND '$fin'
      $where_hors_plage
      AND sejour.group_id = '".CGroups::loadCurrent()->_id."'
      AND users_mediboard.user_id = '$user->_id'";
  
    if ($type_hospi) {
      $query .= "\nAND sejour.type = '$type_hospi'";
    }
    if ($discipline_id) {
      $query .= "\nAND users_mediboard.discipline_id = '$discipline_id'";
    }
    if ($codeCCAM) {
      $query .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'";
    }
    if ($salle_id) {
      $query .= "\nAND sallesbloc.salle_id = '$salle_id'";
    }
    elseif ($bloc_id) {
      $query .= "\nAND sallesbloc.bloc_id = '$bloc_id'";
    }
    
    $query .= "\nGROUP BY mois ORDER BY orderitem";
  
    $result = $user->_spec->ds->loadlist($query);
    
    foreach ($ticks as $i => $tick) {
      $f = true;
      foreach ($result as $r) {
        if ($tick[1] == $r["mois"]) {
          $serie['data'][] = array($i, $r["total"]);
          $serie_total["data"][$i][1] += $r["total"];

          $total += $r['total'];
          $f = false;
        }
      }
      if ($f) {
        $serie["data"][] = array(count($serie["data"]), 0);
      }
    }
    
    $series[] = $serie;
  }
  
  $series[] = $serie_total;

  $title = "Nombre d'interventions par praticien";
  $subtitle = "$total op�rations";
  if ($discipline_id) {
    $subtitle .= " - $discipline->_view";
  }
  if ($codeCCAM) {
    $subtitle .= " - CCAM : $codeCCAM";
  }
  if ($type_hospi) {
    $subtitle .= " - ".CAppUI::tr("CSejour.type.$type_hospi");
  }
  
  $options = array(
    'title' => utf8_encode($title),
    'subtitle' => utf8_encode($subtitle),
    'xaxis' => array('labelsAngle' => 45, 'ticks' => $ticks),
    'yaxis' => array('autoscaleMargin' => 1),
    'bars' => array('show' => true, 'stacked' => true, 'barWidth' => 0.8),
    'HtmlText' => false,
    'legend' => array('show' => true, 'position' => 'nw'),
    'grid' => array('verticalLines' => false),
    'spreadsheet' => array(
      'show' => true,
      'csvFileSeparator' => ';',
      'decimalSeparator' => ',',
      'tabGraphLabel' => utf8_encode('Graphique'),
      'tabDataLabel' => utf8_encode('Donn�es'),
      'toolbarDownload' => utf8_encode('Fichier CSV'),
      'toolbarSelectAll' => utf8_encode('S�lectionner tout le tableau')
    )
  );
  return array('series' => $series, 'options' => $options);
}