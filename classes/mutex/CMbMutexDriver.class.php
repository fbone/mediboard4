<?php 
/**
 * $Id: CMbMutexDriver.class.php 24629 2014-09-02 10:47:02Z phenxdesign $
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision: 24629 $
 */

/**
 * Semaphore implementation to deal with concurrency
 */
abstract class CMbMutexDriver implements IMbMutex {
  /** @var string Mutex key name */
  protected $key;

  protected $label;

  /** @var int Computed expire time in seconds */
  protected $expire;

  /**
   * @see parent::__construct()
   */
  function __construct($key, $label = null) {
    $this->key   = $key;
    $this->label = $label;
  }

  /**
   * @see parent::acquire()
   */
  function acquire($duration = self::DEFAULT_TIMEOUT, $poll_delay = self::DEFAULT_POLL_DELAY) {
    $start = $this->getTime();

    do {
      $this->expire = $this->timeout($duration);

      // Set lock if not already here and acquire it
      if ($this->setLock($duration)) {
        break;
      }

      // Recover an abandonned lock and acquire it
      if ($this->recover($duration)) {
        break;
      }

      // Sleep a little
      usleep($poll_delay);

    } while (true);

    return $this->getTime() - $start;
  }

  /**
   * @see parent::lock()
   */
  function lock($duration = self::DEFAULT_TIMEOUT) {
    $this->expire = $this->timeout($duration);
    
    // Set lock if not already here and acquire it
    if ($this->setLock($duration)) {
      return true;
    }

    // Recover an abandonned lock and acquire it
    if ($this->recover($duration)) {
      return true;
    }

    return false;
  }

    /**
   * Recover an abandoned lock
   *
   * @param float $duration Lock duration
   *
   * @return bool Was the lock acquired?
   */
  abstract protected function recover($duration);

  /**
   * Sets the lock, this must be atomic
   *
   * @param float $duration Duration in seconds
   *
   * @return bool True if the lock was put
   */
  abstract protected function setLock($duration);

  /**
   * Get current time in seconds
   *
   * @return float
   */
  protected function getTime() {
    return microtime(true);
  }

  /**
   * Generates an expire time based on the current time
   *
   * @param float $duration Lock duration
   *
   * @return float timeout
   */
  protected function timeout($duration) {
    return (float) ($this->getTime() + $duration + 1);
  }

  /**
   * Tells if the lock can be released
   *
   * @return bool
   */
  protected function canRelease(){
    return $this->getTime() < $this->expire;
  }

  /**
   * Get the lock key name
   *
   * @return string
   */
  protected function getLockKey() {
    $prefix = CApp::getAppIdentifier();
    return "$prefix-mutex-{$this->key}";
  }
}
