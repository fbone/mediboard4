<?php
/**
 * $Id: CFilesSessionHandler.class.php 19180 2013-05-17 09:19:16Z phenxdesign $
 *
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 19180 $
 */

/**
 * File based Session Handler
 */
class CFilesSessionHandler implements ISessionHandler {
  /**
   * @see parent::init()
   */
  function init() {
    return ini_set("session.save_handler", "files");
  }

  /**
   * @see parent::useUserHandler()
   */
  function useUserHandler() {
    return false;
  }

  /**
   * @see parent::open()
   */
  function open() {
    return false;
  }

  /**
   * @see parent::close()
   */
  function close() {
    return false;
  }

  /**
   * @see parent::read()
   */
  function read($session_id) {
    return false;
  }

  /**
   * @see parent::write()
   */
  function write($id, $data) {
    return false;
  }

  /**
   * @see parent::destroy()
   */
  function destroy($id) {
    return false;
  }

  /**
   * @see parent::gc()
   */
  function gc($max) {
    return false;
  }

  /**
   * @see parent::listSessions()
   */
  function listSessions() {
    return array();
  }

  /**
   * @see parent::setLifeTime()
   */
  function setLifeTime($lifetime) {
  }
}