<?php

/**
 * dPbloc
 *
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id: legende.php 19358 2013-05-30 14:42:44Z rhum1 $
 * @link     http://www.mediboard.org
 */

?>

<table class="tbl">
  <tr>
    <td class="patient-not-arrived">M. PATIENT Patient</td>
    <td class="text">Patient non encore dans l'�tablissement</td>
  </tr>
  <tr>
    <td class="septique">M. PATIENT Patient</td>
    <td class="text">Patient septique</td>
  </tr>   
  <tr>
    <td style="background-color:#ffa"></td>
    <td class="text">Patient entr� au bloc</td>
  </tr>       
  <tr>
    <td style="background-color:#cfc"></td>
    <td class="text">
      Intervention en cours
      <br />
      (heure d'entr�e en salle r�elle affich�e)
    </td>
  </tr>
  <tr>
    <td style="background-image:url(images/icons/ray.gif); background-repeat:repeat;"></td>
    <td class="text">
      Intervention termin�e
      <br />
      (heure d'entr�e en salle, de sortie de salle et dur�e r�elles affich�es)
    </td>
  </tr> 
  <tr>
    <td style="background-color:#fcc"></td>
    <td class="text">Probl�me de timing</td>
  </tr>
  <tr>
    <td style="background-color:#ccf"></td>
    <td class="text">Intervention d�plac�e dans une autre salle</td>
  </tr>
  <tr>
    <td>
      <span class="mediuser" style="border-color: #F99">&nbsp;</span>
    </td>
    <td class="text">Aucun acte cod�</td>
  </tr>
</table>