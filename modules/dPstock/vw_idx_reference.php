<?php
/**
 * $Id: vw_idx_reference.php 19286 2013-05-26 16:59:04Z phenxdesign $
 *
 * @package    Mediboard
 * @subpackage Stock
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 19286 $
 */

CCanDo::checkEdit();

$reference_id = CValue::getOrSession('reference_id');
$societe_id   = CValue::getOrSession('societe_id');
$category_id  = CValue::getOrSession('category_id');
$product_id   = CValue::getOrSession('product_id');
$keywords     = CValue::getOrSession('keywords');
$letter       = CValue::getOrSession('letter', "%");
$show_all     = CValue::getOrSession('show_all');

$filter = new CProduct;
$filter->societe_id = $societe_id;
$filter->category_id = $category_id;

CProductOrderItem::$_load_lite = true;

// Loads the expected Reference
$reference = new CProductReference();

// If a reference ID has been provided, 
// we load it and its associated product
if ($reference->load($reference_id)) {
  $reference->loadRefsFwd();
  $reference->_ref_product->loadRefsFwd();
  $reference->loadRefsNotes();
}

// else, if a product_id has been provided,
// we load it and its associated reference
else if ($product_id) {
  $reference->product_id = $product_id;
  $product = new CProduct();
  $product->load($product_id);
  $reference->_ref_product = $product;
}

// If a supplier ID is provided, we make a corresponding reference
else if ($societe_id) {
  $reference->societe_id = $societe_id;
}

$reference->loadRefsFwd();

if (!$reference->_id) {
  $reference->quantity = 1;
  $reference->price = 0;
}

// Categories list
$category = new CProductCategory();
$list_categories = $category->loadList(null, 'name');

$lists = $reference->loadRefsObjects();

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('reference',       $reference);
$smarty->assign('lists_objects',   $lists);
$smarty->assign('list_categories', $list_categories);

$smarty->assign('filter',          $filter);
$smarty->assign('keywords',        $keywords);
$smarty->assign('letter',          $letter);
$smarty->assign('show_all',        $show_all);

$smarty->display('vw_idx_reference.tpl');
