<?php
/**
 * $Id: config.php 27171 2015-02-17 09:43:28Z aurelie17 $
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision: 27171 $
 */

$dPconfig["dPfacturation"] = array (
  "CFactureEtablissement" => array(
    "use_temporary_bill"  => "0",
    "use_auto_cloture"    => "0",
    "view_bill"           => "1"
  ),
  "CFactureCabinet" => array(
    "use_auto_cloture"  => "1",
    "view_bill"         => "1"
  ),
  "CRelance" => array(
    "use_relances"   => "0",
    "nb_days_first_relance"   => "30",
    "nb_days_second_relance"  => "60",
    "nb_days_third_relance"   => "90",
    "add_first_relance"       => "0",
    "add_second_relance"      => "0",
    "add_third_relance"       => "0",
    "nb_generate_pdf_relance" => "20",
    "message_relance1_assur"  => "",
    "message_relance2_assur"  => "",
    "message_relance3_assur"  => "",
    "message_relance1_patient" => "",
    "message_relance2_patient" => "",
    "message_relance3_patient" => ""
  ),
  "CReglement" => array(
    "use_debiteur"  => "0",
    "add_pay_not_close"     => "0",
    "use_lock_acquittement" => "0",
    "use_mode_default"      => "none",
    "use_echeancier"        => "0"
  ),
  "CRetrocession" => array(
    "use_retrocessions" => "0"
  ),
  "Other" => array(
    "use_search_easy"       => "0",
    "use_view_chainage"     => "0",
    "use_view_quantitynull" => "0",
    "use_strict_cloture"    => "0",
    "use_field_definitive"  => "0",
    "edit_bill_alone"       => "1"
  ),
  "CJournalBill" => array(
    "use_journaux"  => "0",
  ),
);
?>