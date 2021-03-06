<?php
/**
 * Overwrite a locale for an instance
 * $Id: CTranslationOverwrite.class.php 22913 2014-04-24 07:32:25Z charlyecho $
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 22913 $
 */

/**
 * Class CTranslationOverwrite
 */
class CTranslationOverwrite extends CMbObject {
  public $translation_id;

  public $source;
  public $translation;
  public $language;
  public $_old_translation;

  public $_in_cache;

  /**
   * @see parent::getSpec()
   *
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "translation";
    $spec->key = "translation_id";
    $spec->uniques['trad'] = array('source');
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   *
   * @return array
   */
  function getBackProps() {
    return $backProps = parent::getBackProps();
  }

  /**
   * load the activated translation from mediboard (used to compare with the sql one)
   *
   * @param array $locales the locales array
   *
   * @return string
   */
  function loadOldTranslation($locales = array()) {
    if (!count($locales)) {
      $locales = array();
      $locale = CAppUI::pref("LOCALE", "fr");

      foreach (CAppUI::getLocaleFilesPaths($locale) as $_path) {
        include_once $_path;
      }
    }

    return $this->_old_translation = isset($locales[$this->source]) ? $locales[$this->source] : "";
  }

  /**
   * check if the translation is cached
   *
   * @return bool
   */
  function checkInCache() {
    static $locales;

    if (!$locales) {
      $locales = CAppUI::flattenCachedLocales(CAppUI::$lang);
    }

    return $this->_in_cache = (isset($locales[$this->source]) && ($locales[$this->source] == $this->translation));
  }

  /**
   * @see parent::updatePlainFields()
   */
  function updatePlainFields() {
    parent::updatePlainFields();

    $this->translation = CMbString::purifyHTML($this->translation);
  }

  /**
   * @see parent::check()
   *
   * @return string
   */
  function check() {
    return parent::check();
  }

  /**
   * Transform the mb locales with the overwrite system
   *
   * @param array       $locales  locales from mediboard
   * @param string|null $language language chosen, if not defined, use the preference.
   *
   * @return array $locales locales transformed
   */

  function transformLocales($locales, $language=null) {
    $ds = $this->_spec->ds;
    $where = array(
      "language" => $ds->prepare("=%", $language ? $language : CAppUI::pref("LOCALE")),
    );

    $query = new CRequest();
    $query->addSelect("source, translation");
    $query->addTable("translation");
    $query->addWhere($where);
    $overwrites = $ds->loadList($query->makeSelect());

    foreach ($overwrites as $_overwrite) {
      $locales[$_overwrite["source"]] = $_overwrite["translation"];
    }

    return $locales;
  }

  /**
   * Get the properties of our class as string
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["source"]      = "str notNull";
    $props["language"]    = "enum notNull list|".implode('|', CAppUI::getAvailableLanguages())." default|fr";
    $props["translation"] = "text notNull";
    return $props;
  }
}