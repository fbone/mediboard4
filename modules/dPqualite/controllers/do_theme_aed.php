<?php
/**
 * $Id: do_theme_aed.php 19316 2013-05-28 09:33:17Z rhum1 $
 *
 * @package    Mediboard
 * @subpackage Qualite
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 19316 $
 */

$do = new CDoObjectAddEdit("CThemeDoc", "doc_theme_id");
$do->doIt();
