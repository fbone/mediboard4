<?php

/**
 * $Id: ajax_ldap_hexa_to_registry.php 18997 2013-05-02 09:24:16Z rhum1 $
 *
 * @category Admin
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision: 18997 $
 * @link     http://www.mediboard.org
 */

$id_ext = new CIdSante400;
$id_ext->tag = CAppUI::conf("admin LDAP ldap_tag");
$id_ext->object_class = "CUser";
$list = $id_ext->loadMatchingList();

if (count($list) == 0) {
  CAppUI::setMsg("Aucun identifiant � convertir");
}

$count = 0;

foreach ($list as $_id_ext) {
  if (strpos($_id_ext->id400, "-") !== false) {
    continue;
  }
  
  $count++;
  
  $_id_ext->id400 = CLDAP::convertHexaToRegistry($_id_ext->id400);
  $_id_ext->last_update = CMbDT::dateTime();
  
  if ($msg = $_id_ext->store()) {
    CAppUI::setMsg($msg, UI_MSG_WARNING);
  }
  else {
    CAppUI::setMsg("Identifiant converti");
  }
}

if ($count == 0) {
  CAppUI::setMsg("Aucun identifiant � convertir");
}

echo CAppUI::getMsg();