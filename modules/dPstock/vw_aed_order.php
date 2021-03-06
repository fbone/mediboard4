<?php
/**
 * $Id: vw_aed_order.php 19286 2013-05-26 16:59:04Z phenxdesign $
 *
 * @package    Mediboard
 * @subpackage Stock
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 19286 $
 */

CCanDo::checkEdit();

$category_id = CValue::getOrSession('category_id');
$societe_id  = CValue::getOrSession('societe_id');
$letter      = CValue::getOrSession('letter');
$_autofill   = CValue::get('_autofill');

// Categories list
$category = new CProductCategory();
$list_categories = $category->loadList(null, 'name');

// Suppliers list
$list_societes = CSociete::getSuppliers(false);

$order = new CProductOrder;
$list_orders = $order->search("waiting", null, 30);

foreach ($list_orders as $_order) {
  $_order->countBackRefs("order_items");
  $_order->loadRefsOrderItems();
}

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('_autofill',       $_autofill);

$smarty->assign('list_categories', $list_categories);
$smarty->assign('category_id',     $category_id);

$smarty->assign('list_societes',   $list_societes);
$smarty->assign('societe_id',      $societe_id);
$smarty->assign('letter',          $letter);

$smarty->assign('list_orders',     $list_orders);

$smarty->display('vw_aed_order.tpl');
