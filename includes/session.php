<?php
/**
 * Session handling and initialisation
 *
 * @package    Mediboard
 * @subpackage includes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Id: session.php 23029 2014-05-02 07:56:28Z phenxdesign $
 */

// Load AppUI from session
global $rootName;

// Don't ignore user abort as long as session is still locked
ignore_user_abort(false);

// Manage the session variable(s)
$session_name = preg_replace("/[^a-z0-9]/i", "", $rootName);

session_name($session_name);

if (get_cfg_var("session.auto_start") > 0) {
  session_write_close();
}

CSessionHandler::setHandler(CAppUI::conf("session_handler"));

// Start session
CSessionHandler::start();

// Ignore aborted HTTP request, so that PHP finishes the current script
ignore_user_abort(true);

// Register shutdown function to end the session
register_shutdown_function(array("CSessionHandler", "writeClose"));

// Check if the session was made via a temporary token
// and save its expiration date
if (isset($_SESSION["token_expiration"])) {
  CAppUI::$token_expiration = $_SESSION["token_expiration"];
}

// Reset session if it expired
if (CAppUI::isTokenSessionExpired()) {
  CAppUI::$token_expiration = null;

  // Free the session data
  CSessionHandler::end(true);

  // Start it back
  CSessionHandler::start();
}

// Check if session has previously been initialised
if (empty($_SESSION["AppUI"]) || isset($_GET["logout"])) {
  $_SESSION["AppUI"] = CAppUI::init();
}

CAppUI::$instance =& $_SESSION["AppUI"];
CAppUI::$instance->session_name = $session_name;

if (!isset($_SESSION["locked"])) {
  $_SESSION["locked"] = false;
}

CAppUI::checkSessionUpdate();

if (!isset($_SESSION['browser'])) {
  /** Basic browser detection */
  $browser = array(
    'version'   => '0.0.0',
    'majorver'  => 0,
    'minorver'  => 0,
    'build'     => 0,
    'name'      => 'unknown',
    'mobile'    => false,
    'deprecated'=> false,
    'useragent' => '',
    'ie8'       => false,
  );

  $browsers = array(
    'firefox', 'msie', 'opera', 'chrome', 'safari', 'mozilla', 'seamonkey', 'konqueror', 'netscape',
    'gecko', 'navigator', 'mosaic', 'lynx', 'amaya', 'omniweb', 'avant', 'camino', 'flock', 'aol'
  );

  $minimal_versions = CAppUI::conf("browser_compat");

  if (isset($_SERVER['HTTP_USER_AGENT'])) {
    $browser['useragent'] = $_SERVER['HTTP_USER_AGENT'];
    $user_agent = strtolower($browser['useragent']);
    foreach ($browsers as $_browser) {
      $match = array();
      if (preg_match("/($_browser)[\/ ]?([0-9.]*)/", $user_agent, $match)) {
        $browser['name'] = $match[1];
        $browser['version'] = $match[2];

        // Special case of Opera http://dev.opera.com/articles/view/opera-ua-string-changes/
        $match = array();
        if ($browser['name'] == "opera" && preg_match("/(version)\/([0-9.]*)/", $user_agent, $match)) {
          $browser['version'] = $match[2];
        }

        @list($browser['majorver'], $browser['minorver'], $browser['build']) = explode('.', $browser['version']);
        break;
      }
    }

    $ios = preg_match("/(ipad|iphone)/", $user_agent, $matches);
    if ($ios) {
      $browser['name'] = $matches[1];
    }

    // Check IE8 because it's deprecated
    $matches = array();
    if (preg_match("/trident\\/(\\d\\.\\d)/", $user_agent, $matches)) {
      $trident = $matches[1];
      /*
       * Trident:
       *  - 4.0 => IE 8
       *  - 5.0 => IE 9
       *  - 6.0 => IE 10
       *  - 7.0 => IE 11
       *  - ...
       */
      if ($trident < 5.0) {
        $browser["ie8"] = true;
      }
    }

    //detect if the browser is host on mobile device
    $mobile = new Mobile_Detect();
    $browser['mobile'] = $mobile->isMobile() && !$mobile->isTablet();
    $browser['tablet'] = $mobile->isTablet();
    CAppUI::$instance->touch_device = $browser['tablet'];
  }

  $browser['deprecated'] = isset($minimal_versions[$browser['name']]) &&
                             version_compare($browser['version'],  $minimal_versions[$browser['name']], "<") &&
                             !$browser['mobile'];

  $_SESSION['browser'] =& $browser;
}
else {
  $browser =& $_SESSION['browser'];
}
