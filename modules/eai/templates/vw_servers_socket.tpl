{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage eai
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=eai script=socket_server}}

<table class="tbl">
  <tr>
    <th class="narrow">Processus #id</th>
    <th class="narrow">Port</th>
    <th class="narrow">Type</th>
    <th class="narrow">Accessible</th>
    <th class="narrow">Lanc�</th>
    <th class="narrow">{{tr}}Actions{{/tr}}</th>
    <th> Statistiques </th>
  </tr>
  {{foreach from=$processes key=process_id item=_process}}
    {{unique_id var=uid}}
    <tbody id="{{$uid}}">
      {{mb_include module=eai template=inc_server_socket}}
    </tbody>
  {{foreachelse}}
    <tr>
      <td colspan="7" class="empty">Aucun serveur actif</td>
    </tr>
  {{/foreach}}
</table>