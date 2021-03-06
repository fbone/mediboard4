<?php
/**
 * $Id: inc_vw_timeop_prepa.php 27868 2015-04-04 16:59:04Z mytto $
 *
 * @package    Mediboard
 * @subpackage dPstats
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 27868 $
 */

CView::enforceSlave();

$total["nbPrep"] = 0;
$total["nbPlages"] = 0;
$total["somme"] = 0;
$total["moyenne"] = 0;


$where = array();
$where["chir_id"] = CSQLDataSource::prepareIn(array_keys($listPrats));

$ljoin = array();
$ljoin["users"] = "users.user_id = temps_prepa.chir_id";

$order = "users.user_last_name ASC, users.user_first_name ASC";

$tempPrepa = new CTempsPrepa();
/** @var CTempsPrepa[] $listTemps */
$listTemps = $tempPrepa->loadList($where, $order, null, null, $ljoin);

foreach ($listTemps as $temps) {
  $temps->loadRefPraticien();
  $temps->_ref_praticien->loadRefFunction();
  $total["nbPrep"  ] += $temps->nb_prepa;
  $total["nbPlages"] += $temps->nb_plages;
  $total["somme"   ] += $temps->nb_prepa * strtotime($temps->duree_moy);
}
if ($total["nbPrep"] !=0 ) {
  $total["moyenne"] = $total["somme"] / $total["nbPrep"];
}
