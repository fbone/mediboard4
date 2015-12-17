{{* $Id: inc_ei_categorie.tpl 28539 2015-06-08 13:30:16Z armengaudmc $ *}}

{{*
 * @package Mediboard
 * @subpackage dPqualite
 * @version $Revision: 28539 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $categorie->ei_categorie_id}}
  <a class="button new" href="?m={{$m}}&amp;tab=vw_edit_ei&amp;ei_categorie_id=0">
    {{tr}}CEiCategorie.create{{/tr}}
  </a>
{{/if}}
<form name="editCategorie" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
  {{mb_key object=$categorie}}
  {{mb_class object=$categorie}}
  <table class="form">
    <tr>
      {{if $categorie->ei_categorie_id}}
      <th colspan="2" class="title modify">
        {{tr}}CEiCategorie-title-modify{{/tr}} : {{$categorie->_view}}
        {{else}}
      <th colspan="2" class="title">
        {{tr}}CEiCategorie-title-create{{/tr}}
        {{/if}}
      </th>
    </tr>
    <tr>
      <th>{{mb_label object=$categorie field="nom"}}</th>
      <td>{{mb_field object=$categorie field="nom"}}</td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        {{if $categorie->ei_categorie_id}}
          <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
          <button class="trash" type="button"
                  onclick="confirmDeletion(this.form,{typeName:'{{tr escape="javascript"}}CEiCategorie.one{{/tr}}',objName:'{{$categorie->_view|smarty:nodefaults|JSAttribute}}'})">{{tr}}Delete{{/tr}}</button>
        {{else}}
          <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>
<br />
<table class="tbl">
  <tr>
    <th>{{tr}}CEiCategorie-nom-court{{/tr}}</th>
  </tr>
  {{foreach from=$listCategories item=curr_cat}}
    <tr>
      <td class="text">
        <a href="?m={{$m}}&amp;tab=vw_edit_ei&amp;ei_categorie_id={{$curr_cat->ei_categorie_id}}"
           title="{{tr}}CEiCategorie.modify{{/tr}}">
          {{$curr_cat->nom}}
        </a>
      </td>
    </tr>
    {{foreachelse}}
    <tr>
      <td colspan="2" class="empty">
        {{tr}}CEiCategorie.none{{/tr}}
      </td>
    </tr>
  {{/foreach}}
</table>