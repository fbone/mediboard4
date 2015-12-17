<?php
/**
 * $Id: IMbMutex.class.php 24615 2014-09-01 10:52:44Z phenxdesign $
 *
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    SVN: $Id: IMbMutex.class.php 24615 2014-09-01 10:52:44Z phenxdesign $
 */

/**
 * Manage locking files to deal with concurrency
 */
interface IMbMutex {
  const DEFAULT_TIMEOUT    = 300.0;  // seconds
  const DEFAULT_POLL_DELAY = 100000; // milliseconds (0.1 second)

  /**
   * Constructor
   *
   * @param string $key Mutex identifier
   */
  function __construct($key, $label = null);

  /**
   * Acquire the semaphore by putting a lock on it
   *
   * @param float $duration   The max time in seconds to acquire the semaphore (max 10s)
   * @param int   $poll_delay Poll delay in microseconds
   *
   * @return float Time spent waiting, in seconds
   */
  function acquire($duration = self::DEFAULT_TIMEOUT, $poll_delay = self::DEFAULT_POLL_DELAY);

  /**
   * Puts the mutex in a "lock" mode : doesn't wait if the mutex is aquired, waits otherwise
   *
   * @param float $duration The lock TTL
   *
   * @return bool true if the lock is free, false otherwise
   */
  function lock($duration = self::DEFAULT_TIMEOUT);

  /**
   * Releases the lock
   *
   * @return void
   */
  function release();
}
