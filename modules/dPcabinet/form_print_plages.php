<?php
/**
 * $Id: form_print_plages.php 19781 2013-07-05 11:02:40Z lryo $
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 19781 $
 */

CCanDo::checkRead();

$now       = CMbDT::date();

$filter = new CConsultation;
$filter->_date_min  = CValue::get("_date_min"    , "$now");
$filter->_date_max  = CValue::get("_date_max"    , "$now");
$filter->_print_ipp = CValue::get("_print_ipp", CAppUI::conf("dPcabinet CConsultation show_IPP_print_consult"));

$tomorrow  = CMbDT::date("+1 day", $now);

$week_deb  = CMbDT::date("last sunday", $now);
$week_fin  = CMbDT::date("next sunday", $week_deb);
$week_deb  = CMbDT::date("+1 day"     , $week_deb);

$rectif     = CMbDT::transform("+0 DAY", $now, "%d")-1;
$month_deb  = CMbDT::date("-$rectif DAYS", $now);
$month_fin  = CMbDT::date("+1 month", $month_deb);
$month_fin  = CMbDT::date("-1 day", $month_fin);

// Liste des praticiens
$listChir = CConsultation::loadPraticiens(PERM_READ);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("filter"  , $filter);
$smarty->assign("now"       , $now);
$smarty->assign("tomorrow"  , $tomorrow);
$smarty->assign("week_deb"  , $week_deb);
$smarty->assign("week_fin"  , $week_fin);
$smarty->assign("month_deb" , $month_deb);
$smarty->assign("month_fin" , $month_fin);
$smarty->assign("listChir"  , $listChir);

$smarty->display("form_print_plages.tpl");
