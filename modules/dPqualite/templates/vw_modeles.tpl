{{* $Id: vw_modeles.tpl 12793 2011-07-29 15:58:25Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage dPqualite
 * @version $Revision: 12793 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

function popFile(objectClass, objectId, elementClass, elementId, sfn){
  var url = new Url;
  url.addParam("nonavig", 1);
  url.ViewFilePopup(objectClass, objectId, elementClass, elementId, sfn);
}

function ZoomAjax(objectClass, objectId, elementClass, elementId, sfn){
  file_preview = elementId;
  var url = new Url("dPfiles", "preview_files");
  url.addParam("objectClass", objectClass);
  url.addParam("objectId", objectId);
  url.addParam("elementClass", elementClass);
  url.addParam("elementId", elementId);
  if(sfn && sfn!=0){
    url.addParam("sfn", sfn);
  }
  url.requestUpdate('bigView', { waitingText : "{{tr}}CFile-msg-loadimgmini{{/tr}}" });
}

</script>
<table class="main">
  <tr>
    <td class="halfPane">
      {{if $can->admin && $docGed->doc_ged_id}}
        <a class="button new" href="?m={{$m}}&amp;tab=vw_modeles&amp;doc_ged_id=0">
        {{tr}}CDocGed.create_modele{{/tr}}
        </a>
      {{/if}}
      <table class="tbl">
        <tr>
          <th class="category">{{tr}}CDocGed-titre{{/tr}}</th>
        </tr>
        {{foreach from=$modeles item=currModele}}
        <tr>
          <td>
            {{if $can->admin}}
            <a href="?m={{$m}}&amp;tab=vw_modeles&amp;doc_ged_id={{$currModele->doc_ged_id}}">
            {{else}}
            <a href="#" onclick="ZoomAjax('{{$currModele->_class}}','{{$currModele->_id}}','CFile','{{$currModele->_lastentry->file_id}}', 0);">
            {{/if}}
            {{$currModele->titre}}
            </a>
          </td>
        </tr>
        {{foreachelse}}
        <tr>
          <td>
          {{tr}}CDocGed.none-modele{{/tr}}
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
    <td class="halfPane" id="bigView" style="text-align: center;">
      {{if $can->admin}}
      <form name="ProcModeleFrm" action="?m={{$m}}" enctype="multipart/form-data" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_docgedmodele_aed" />
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="_validation" value="0" />
      {{if $docGed->doc_ged_id}}
      <input type="hidden" name="_firstModeleGed" value="0" />
      {{else}}
      <input type="hidden" name="_firstModeleGed" value="1" />
      {{/if}}
      
      <input type="hidden" name="ged[doc_ged_id]" value="{{$docGed->doc_ged_id}}" />
      <input type="hidden" name="ged[etat]" value="0" />

      <input type="hidden" name="suivi[doc_ged_suivi_id]" value="{{$docGed->_lastentry->doc_ged_suivi_id}}" />
      <input type="hidden" name="suivi[user_id]" value="{{$user}}" />  
      <input type="hidden" name="suivi[actif]" value="0" /> 
      <input type="hidden" name="suivi[etat]" value="0" /> 
      <input type="hidden" name="suivi[file_id]" value="{{$docGed->_lastentry->file_id}}" />
      
      <input type="hidden" name="object_class" value="CDocGed" />
      <input type="hidden" name="object_id" value="" />
      <input type="hidden" name="file_category_id" value="" />
              
      <table class="form">
        <tr>
          {{if $docGed->doc_ged_id}}
          <th class="title modify" colspan="2">
            {{tr}}CDocGed-title-modify-modele{{/tr}}
            <input type="hidden" name="ged[user_id]" value="{{$docGed->user_id}}" />
          {{else}}
          <th class="title" colspan="2">
            {{tr}}CDocGed-title-create-modele{{/tr}}
            <input type="hidden" name="ged[user_id]" value="{{$user}}" />
          {{/if}}
          </th>
        </tr>
        <tr>
          <th>
            <label for="ged[titre]" title="{{tr}}CDocGed-titre-modele-desc{{/tr}}">{{tr}}CDocGed-titre-modele{{/tr}}</label>
          </th>
          <td>
            <input type="text" name="ged[titre]" value="{{$docGed->titre}}" class="notNull {{$docGed->_props.titre}}" />
          </td>
        </tr>
        <tr>
          <th>
            <label for="ged[group_id]" title="{{tr}}CDocGed-group_id-desc-modele{{/tr}}">{{tr}}CDocGed-group_id{{/tr}}</label>
          </th>
          <td colspan="2">
            <select class="{{$docGed->_props.group_id}}" name="ged[group_id]">
            {{foreach from=$etablissements item=curr_etab}}
              <option value="{{$curr_etab->group_id}}" {{if ($docGed->doc_ged_id && $docGed->group_id==$curr_etab->group_id) || (!$docGed->doc_ged_id && $g==$curr_etab->group_id)}} selected="selected"{{/if}}>{{$curr_etab->text}}</option>
            {{/foreach}}
            </select>
          </td>
        </tr>
        {{if $docGed->doc_ged_id}}
        <tr>
          <th>{{tr}}CFile{{/tr}}</th>
          <td class="button">
            <a href="#" onclick="popFile('{{$docGed->_class}}','{{$docGed->_id}}','CFile','{{$docGed->_lastentry->file_id}}')" title="{{tr}}CFile-msg-loadimgmini{{/tr}}">
              <img src="?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$docGed->_lastentry->file_id}}&amp;phpThumb=1&amp;wl=64&amp;hp=64" alt="-" />
            </a>
          </td>
        </tr>
        {{/if}}
        <tr>
          <th>
            <label for="formfile[0]">
              {{if $docGed->doc_ged_id}}
              {{tr}}CDocGedSuivi-file_id-modify{{/tr}}
              {{else}}
              {{tr}}CDocGedSuivi-file_id-new{{/tr}}
              {{/if}}
            </label>
          </th>
          <td>
            <input type="file" name="formfile[0]" size="0" />
          </td>
        </tr>
        <tr>
          <td colspan="2" class="button">
            {{if $docGed->doc_ged_id}}
            <button class="modify" type="submit">
              {{tr}}Save{{/tr}}
            </button>
            <button class="trash" type="button" onclick="confirmDeletion(this.form, {typeName:'{{tr escape="javascript"}}CDocGed.one-modele{{/tr}}',objName:'{{$docGed->titre|smarty:nodefaults|JSAttribute}}'})" title="{{tr}}Delete{{/tr}}">
              {{tr}}Delete{{/tr}}
            </button>
            {{else}}
            <button class="submit" type="submit">
              {{tr}}Create{{/tr}}
            </button>
            {{/if}}
          </td>
        </tr>
      </table>
      </form>
      {{else}}
      {{include file="../../dPfiles/templates/inc_preview_file.tpl"}}
      {{/if}}
    </td>
  </tr>
</table>