<?php /** $Id: write_usermessage.php 20898 2013-11-07 10:19:55Z charlyecho $ **/

/**
* @package Mediboard
* @subpackage dPportail
* @version $Revision: 20898 $
* @author Thomas Despoix
*/

CCanDo::checkRead();
$user = CUser::get();
$usermessage = new CUserMessage();
$usermessage->from        = $user->_id;
$usermessage->to          = CValue::get("to");
$usermessage->subject     = CValue::get("subject");
$usermessage->in_reply_to = CValue::get("in_reply_to");
$usermessage->load(CValue::getOrSession("usermessage_id"));
$usermessage->loadRefsFwd();

// V�rifiction de la premi�re lecture par le destinataire
if ($usermessage->to == $user->_id && $usermessage->date_sent && ! $usermessage->date_read) {
  $usermessage->date_read = CMbDT::dateTime();
  $usermessage->store();
}

if ($usermessage->in_reply_to) {
  $origin = $usermessage->loadOriginMessage();
  if ($origin->_id) {
    if (!$usermessage->subject) {
      $usermessage->subject = "Re: ".$origin->subject;
    }
    $usermessage->to = $origin->from;
  }
}

if ($usermessage->to) {
  $usermessage->loadRefUsersTo();
}
// Historique des messages avec le destinataire
$where = array();
$where[] = "(usermessage.from = '$usermessage->from' AND usermessage.to = '$usermessage->to')".
           "OR (usermessage.from = '$usermessage->to' AND usermessage.to = '$usermessage->from')";
$where["date_sent"] =" IS NOT NULL";

$historique = $usermessage->loadList($where, "date_sent DESC", "20", "date_sent, subject");
CMbObject::massLoadFwdRef($historique, "from");
CMbObject::massLoadFwdRef($historique, "to");

/** @var $historique CUserMessage[] */
foreach ($historique as $_mail) {
  $_mail->loadRefUserFrom();
  $_mail->loadRefUsersTo();
}

// Initialisation de CKEditor
$templateManager = new CTemplateManager();
$templateManager->editor = "ckeditor";
$templateManager->simplifyMode = true;

if ($usermessage->date_sent) {
  $templateManager->printMode = true;
}

$templateManager->initHTMLArea();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("usermessage"   , $usermessage);
$smarty->assign("historique", $historique);

$smarty->display("write_usermessage.tpl");
