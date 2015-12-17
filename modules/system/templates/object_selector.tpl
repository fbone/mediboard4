{{* $Id: object_selector.tpl 11926 2011-04-20 12:15:38Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 11926 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

function setClose(selClass,keywords,key,val){
  var oObject = {
    objClass : selClass,
    id : key,
    view : val,
    keywords : keywords
  }
  
  var oSelector = window.opener.ObjectSelector;
  
  if (oSelector) {
    oSelector.set(oObject);
  }
  else {
    window.opener.setObject(oObject);
  }
  window.close();
}
</script>

<form action="?" name="frmSelector" method="get" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="a" value="object_selector" />
<input type="hidden" name="dialog" value="1" />
<input type="hidden" name="onlyclass" value="{{$onlyclass}}" />
{{if $onlyclass=='true'}}
<input type="hidden" name="selClass" value="{{$selClass}}" />
{{/if}}
<table class="form">
  <tr>
    <th class="title" colspan="2">Crit�res de s�lection</th>
  </tr>
  <tr>
    <th><label for="selClass">Type d'objet</label></th>
    <td colspan="2">
    	{{if $onlyclass == 'true'}}
			  <strong>{{tr}}{{$selClass}}{{/tr}}</strong>
			{{else}}
	      <select class="notNull str" name="selClass">
	        <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
	        {{foreach from=$classes key=_class item=_fields}}
	        <option value="{{$_class}}" 
	        	{{if $selClass == $_class}} selected="selected" {{/if}}
	        	{{if !$_fields|@count}} style="opacity: .6" {{/if}}
	        >	
	        	{{tr}}{{$_class}}{{/tr}}
	        </option>
	        {{/foreach}}
	       </select>
			 {{/if}}
    </td>
  </tr>

  <tr>
    <th>
    	<label for="keywords" title="Veuillez saisir un ou plusieurs mot cl�">Mots Cl�s</label>
    </th>
    <td>
    	<input class="str" type="text" name="keywords" value="{{$keywords|stripslashes}}" />
    </td>
  </tr>

	{{if $selClass}}
  {{assign var=fields value=$classes.$selClass}}
  <tr>
    <td colspan="2" class="text">
      {{if $fields|@count}}
	      <div class="small-info">
	        Mots cl�s recherch�s dans les champs suivants :
	        {{foreach from=$fields item=_field name=field}}
						{{mb_label class=$selClass field=$_field}}{{$smarty.foreach.field.last|ternary:'.':','}}
					{{/foreach}}
	      </div>
			{{else}}
	      <div class="small-warning">
	        <strong>Recherche par mot cl�s impossible</strong> : 
	        aucun champ de recherche pour ce type d'objet.
	        <br/>
	        Utilisez l'identifiant interne ci-dessous.
	      </div>
			{{/if}}
    </td>
  </tr>
	{{/if}}
  
  <tr>
    <th>
    	<label for="object_id" title="Identifiant interne de l'objet">Identifiant</label>
    </th>
    <td>
    	<input class="ref" type="text" name="object_id" value="{{$object_id}}" />
    </td>
  </tr>

  <tr>
    <td class="button" colspan="2">
    	<button class="search" type="submit">{{tr}}Search{{/tr}}</button>
    </td>
  </tr>
</table>
</form>

{{if $selClass}}
<table class="tbl">
  <tr>
    <th class="title" colspan="2">{{tr}}Results{{/tr}}</th>
  </tr>
  
  {{foreach from=$list item=_object}}
    <tr>
      <td>
      	<label onmouseover="ObjectTooltip.createEx(this, '{{$_object->_guid}}');">{{$_object}}</label>
      </td>     
      <td class="button narrow">
      	<button type="button" class="tick" onclick="setClose('{{$selClass}}', '{{$keywords|stripslashes|smarty:nodefaults|JSAttribute}}', {{$_object->_id}}, '{{$_object->_view|smarty:nodefaults|JSAttribute}}')">
      	  {{tr}}Select{{/tr}}
      	</button>
      </td>
    </tr>
	{{foreachelse}}
	  <tr>
	  	<td colspan="2" class="empty">
	  		{{tr}}{{$selClass}}.none{{/tr}}
	  	</td>
	  </tr>
  {{/foreach}}
</table>
{{/if}}