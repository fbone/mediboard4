{{* $Id: inc_edit_user.tpl 12741 2011-07-23 12:32:03Z mytto $ *}}

{{*
  * @package Mediboard
  * @subpackage admin
  * @version $Revision: 12741 $
  * @author SARL OpenXtrem
  * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  *}}

{{if $user->template}} 
<table class="tbl">
  {{foreach from=$user->_ref_profiled_users item=_user}}
  <tr>
    <td><span onmouseover="ObjectTooltip.createEx(this, '{{$_user->_guid}}');">{{$_user}}</span></td>
  </tr>
  {{foreachelse}}
  <tr>
    <td class="empty">{{tr}}CUser-none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>
   
{{else}}
<div class="small-info">
  {{tr}}CUser-msg-noprofiled-not-template{{/tr}}
</div>
{{/if}}

