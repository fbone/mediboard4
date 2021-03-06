<?php
/**
 * $Id: js_error_handler.php 19290 2013-05-26 19:48:24Z phenxdesign $
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 19290 $
 */

$errorMsg   = CValue::post('errorMsg');
$url        = CValue::post('url');
$lineNumber = CValue::post('lineNumber');
$stack      = CValue::post('stack');
$location   = CValue::post('location');

$stackTrace = array();
$stack = explode("\n", $stack);

foreach($stack as $trace) {
  if (preg_match("/(?P<function>.*)\((?P<args>.*)\)@(?P<file>.*):(?P<line>.*)/", $trace, $matches)) {
    if (empty($matches["function"])) {
      $matches["function"] = "[anonymous]";
    }
    $stackTrace[] = $matches;
  }
}

if (!count($stackTrace))
  $errorMsg .= " <strong>Extended message:</strong> ".implode(" -- \n", $stack);

$errorMsg .= " <strong>Location:</strong> ".$location;

errorHandler(E_JS_ERROR, $errorMsg, $url, $lineNumber, null, $stackTrace);
