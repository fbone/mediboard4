<?php
/**
 * $Id: do_facturecabinet_aed.php 20068 2013-07-26 13:21:27Z rhum1 $
 *
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision: 20068 $
 */

$do = new CDoObjectAddEdit("CFactureCabinet", "facture_id");
$do->doIt();
