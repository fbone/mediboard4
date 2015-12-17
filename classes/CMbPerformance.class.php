<?php

/**
 * $Id: CMbPerformance.class.php 22209 2014-02-26 10:29:02Z phenxdesign $
 *  
 * @category Classes
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision: 22209 $
 * @link     http://www.mediboard.org
 */
 
/**
 * Performance profling, to place markers at differents times
 */
class CMbPerformance {
  static $steps = array();

  private static $previous;

  private static $startTime;
  private static $endTime;
  private static $dbTime;

  /**
   * Tells if the application is in profiling mode
   *
   * @return bool
   */
  static function isProfiling() {
    static $cache = null;

    if ($cache === null) {
      if (isset($_COOKIE["mediboard-profiling"])) {
        $cookie = stripslashes($_COOKIE["mediboard-profiling"]);
        $cache = json_decode($cookie) == 1;
      }
      else {
        $cache = false;
      }
    }

    return $cache;
  }

  /**
   * Start the timer
   *
   * @return void
   */
  static function start(){
    self::$startTime = microtime(true);
    self::$previous = self::$startTime;
  }

  /**
   * Place a marking
   *
   * @param string $label Marker label
   *
   * @return void
   */
  static function mark($label) {
    if (!self::isProfiling()) {
      return;
    }

    if (isset(self::$steps[$label])) {
      return;
    }

    $time = microtime(true);

    $duration = $time - self::$previous;
    $duration = (float)number_format($duration*1000, 5, ".", "");

    self::$steps[$label] = array(
      "label" => $label,
      "time"  => self::$previous*1000,
      "dur"   => $duration,
      "mem"   => memory_get_usage(true),
    );

    self::$previous = $time;
  }

  /**
   * Output the profiling data
   *
   * @return string
   */
  static function out() {
    self::$endTime = microtime(true);

    $data = array(
      "start" => self::$startTime * 1000,
      "end"   => self::$endTime * 1000,
      "steps" => array_values(self::$steps),
      "db"    => self::$dbTime,
      "size"  => ob_get_length(),
    );

    return $data;
  }

  /**
   * Final call
   *
   * @return void
   */
  static function end(){
    CMbPerformance::mark("app");
    self::$dbTime = CApp::$performance["dataSourceTime"];
  }

  /**
   * Write HTTP header containing profiling data
   *
   * @return void
   */
  static function writeHeader() {
    if (!self::isProfiling()) {
      return;
    }

    if (headers_sent()) {
      return;
    }

    global $m, $action, $dosql;

    $req = "$m|".(empty($dosql) ? $action : $dosql);

    header("X-Mb-Timing: ".json_encode(self::out()));
    header("X-Mb-Req: $req");
    header("X-Mb-RequestUID: ".CApp::getRequestUID());
  }
}
