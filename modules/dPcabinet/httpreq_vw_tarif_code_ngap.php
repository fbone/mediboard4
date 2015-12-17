<?php
/**
 * $Id: httpreq_vw_tarif_code_ngap.php 24174 2014-07-28 09:07:43Z asmiane $
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 24174 $
 */

CCanDo::checkRead();

$acte = new CActeNGAP;
$acte->quantite     = CValue::get("quantite", "1");
$acte->code         = CValue::get("code");
$acte->coefficient  = CValue::get("coefficient", "1");
$acte->demi         = CValue::get("demi");
$acte->complement   = CValue::get("complement");
$acte->executant_id = CValue::get('executant_id');
$acte->gratuit      = CValue::get('gratuit');
$acte->updateMontantBase();
$acte->getLibelle();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("acte"  , $acte);
$smarty->display("inc_vw_tarif_ngap.tpl");
