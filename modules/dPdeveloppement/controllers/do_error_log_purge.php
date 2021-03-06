<?php

/**
 * $Id: do_error_log_purge.php 20032 2013-07-24 09:48:57Z kgrisel $
 *
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision: 20032 $
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$error_log = new CErrorLog();
$ds = $error_log->getDS();
$query = "TRUNCATE {$error_log->_spec->table}";
$ds->exec($query);

$error_log_data = new CErrorLogData();
$ds = $error_log->getDS();
$query = "TRUNCATE {$error_log_data->_spec->table}";
$ds->exec($query);

CAppUI::stepAjax("Journaux d'erreur vid�s");