<?php
/**
 * $Id: config.php 22488 2014-03-18 14:08:49Z flaviencrochard $
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 22488 $
 */

$dPconfig["dPcabinet"] = array(
  "keepchir" => 1,
  "display_nb_consult" => "cab",
  "CPlageconsult" => array (
    "hours_start"      => "8",
    "hours_stop"       => "20",
    "minutes_interval" => "15",
    "hour_limit_matin" => "12",
  ),
  "CConsultation" => array(
    "use_last_consult"       => 0,
    "show_examen"            => 1,
    "show_histoire_maladie"  => 0,
    "show_conclusion"        => 0,
    "show_IPP_print_consult" => 0,
    "show_motif_consult_immediate" => 1,
    "attach_consult_sejour"  => 0,
    "create_consult_sejour"  => 0,
    "minutes_before_consult_sejour" => 1,
    "hours_after_changing_prat"     => 0,
    "fix_doc_edit"                  => 0,
    "search_sejour_all_groups"      => 0,
    "consult_readonly"              => 0,
    "surbooking_readonly"    => 1,
    "tag"                    => "",
    "default_taux_tva"       => "0|19.6",
    "auto_refresh_frequency" => "90"
  ),
  "CConsultAnesth" => array(
    "feuille_anesthesie" => "print_fiche",
    /* Format des champs auto :
     * %N - Nom praticien interv
     * %P - Pr�nom praticien interv
     * %S - Initiales praticien interv
     * %L - Libell� intervention
     * %I - Jour intervention
     * %i - Heure intervention
     * %E - Jour d'entr�e
     * %e - Heure d'entr�e
     * %T - Type de s�jour (A, O, E...)
     */
    "format_auto_motif"     => "Pr�-an�sth. %I %L %S",
    "format_auto_rques"     => "%T %E %e",
    "view_premedication"    => "0",
    "show_facteurs_risque"  => "0",
    "show_mallampati"       => "0",
    "check_close"           => "0",
  ),
  "Comptabilite" => array(
    "show_compta_tiers" => 1
  ),
  "Tarifs" => array(
    "show_tarifs_etab" => 0
  )
);
