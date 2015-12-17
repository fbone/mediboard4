{{* $Id: vw_object_history.tpl 11724 2011-04-01 14:56:27Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 11724 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl" style="text-align: center">
  {{foreach from=$logs item="log"}}
  <tr>
    <td>{{mb_ditto name=user value=$log->_ref_user->_view}}</td>
    <td>{{mb_value object=$log field=date format=relative}}</td>
  </tr>
	
	{{foreachelse}}
  <tr>
    <td class="empty">{{tr}}CUserLog.none{{/tr}}</td>
  </tr>
  {{/foreach}}

  {{if $more}}
  <tr>
    <td colspan="2">
      <em>
        {{$more}}
        {{tr}}CUserLog.more{{/tr}}
      </em>
    </td>
  </tr>
  {{/if}}
  
</table>