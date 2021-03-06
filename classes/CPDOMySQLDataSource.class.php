<?php 
/**
 * $Id: CPDOMySQLDataSource.class.php 22549 2014-03-20 14:17:39Z phenxdesign $
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision: 22549 $
 */

/**
 * Class CPDOMySQLDataSource
 */
class CPDOMySQLDataSource extends CPDODataSource {
  protected $driver_name = "mysql";

  /**
   * Get the used grammar
   *
   * @return CSQLGrammarMySQL|mixed
   */
  function getQueryGrammar() {
    return new CSQLGrammarMySQL();
  }

  /**
   * @see parent::version()
   */
  function version() {
    return $this->loadResult("SELECT VERSION()");
  }
}
