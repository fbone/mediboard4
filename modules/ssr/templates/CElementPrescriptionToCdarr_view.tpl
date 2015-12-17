{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if !$object->_can->read}}
  <div class="small-info">
    {{tr}}{{$object->_class}}{{/tr}} : {{tr}}access-forbidden{{/tr}}
  </div>
  {{mb_return}}
{{/if}}

{{mb_include template=CMbObject_view}}

{{assign var=element_to_cdarr value=$object}}
{{assign var=activite         value=$element_to_cdarr->_ref_activite_cdarr}}

<table class="tooltip tbl">
  <tr>
  	<td>
  		{{mb_include module=system template=inc_field_view object=$activite prop=libelle}}
			<strong>{{mb_label object=$activite field=type}}</strong>:
      {{$activite->_ref_type_activite}}
		</td>
	</tr>
</table>
