<?php
/**
 * $Id: do_configure.php 21614 2014-01-08 16:57:12Z flaviencrochard $
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 21614 $
 */

global $mbpath, $dPconfig;

CCanDo::checkAdmin();

$mbpath = "";
CMbArray::extract($_POST, "m");
CMbArray::extract($_POST, "dosql");
CMbArray::extract($_POST, "suppressHeaders");
$ajax = CMbArray::extract($_POST, "ajax");

$config_db = CAppUI::conf("config_db");

if ($config_db) {
  $configs = $_POST;

  // Ne pas inclure de config relatives aux bases de donn�es
  foreach ($_POST as $key => $_config) {
    if (in_array($key, CMbConfig::$forbidden_values) || $key == "db") {
      unset($configs[$key]);
    }
    else {
      unset($_POST[$key]);
    }
  }

  $configs = array_map_recursive('stripslashes', $configs);

  // DB Version
  $inserts = array();
  $ds = CSQLDataSource::get("std");

  $list = array();
  CMbConfig::buildConf($list, $configs, null);

  foreach ($list as $key => $value) {
    $query = "INSERT INTO `config_db`
      VALUES (%1, %2)
      ON DUPLICATE KEY UPDATE value = %3";
    $query = $ds->prepare($query, $key, $value, $value);

    if ($ds->exec($query) === false) {
      CAppUI::setMsg("Configure-failed-modify", UI_MSG_ERROR);
    }
    else {
      CAppUI::setMsg("Configure-success-modify");
    }
  }
}

$mbConfig = new CMbConfig();

$result = $mbConfig->update($_POST);
if (PEAR::isError($result)) {
  CAppUI::setMsg("Configure-failed-modify", UI_MSG_ERROR, $result->getMessage());
}
else {
  CAppUI::setMsg("Configure-success-modify");
}

$mbConfig->load();
$dPconfig = $mbConfig->values;

if ($config_db) {
  CMbConfig::loadValuesFromDB();
}

// Cas Ajax
if ($ajax) {
  echo CAppUI::getMsg();
  CApp::rip();
}
