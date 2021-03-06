<?php

/**
 * $Id$
 *
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */

/**
 * specDomain: V13922 (C-0-D10882-V13922-cpt)
 */
class CCDAEntityClassRoot extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'ENT',
    'HCE',
    'RGRP',
  );
  public $_union = array (
    'EntityClassLivingSubject',
    'EntityClassMaterial',
    'EntityClassOrganization',
    'EntityClassPlace',
  );


  /**
   * Retourne les propriétés
   *
   * @return array
   */
  function getProps() {
    parent::getProps();
    $props["data"] = "str xml|data enum|".implode("|", $this->getEnumeration(true));
    return $props;
  }
}