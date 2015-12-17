{{* $Id: inc_vw_admissions.tpl 11726 2011-04-03 14:06:56Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: 11726 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Calendar.regField(getForm("changeDatePermissions").date, null, {noView: true});
</script>

{{mb_include module=admissions template=inc_refresh_page_message}}

<table class="tbl" id="admissions">
  <tr>
    <th class="title" colspan="10">
      <a href="?m=dPadmissions&tab=vw_idx_permissions&date={{$hier}}" style="display: inline"><<<</a>
      {{$date|date_format:$conf.longdate}}
      <form name="changeDatePermissions" action="?" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="vw_idx_permissions" />
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      </form>
      <a href="?m=dPadmissions&tab=vw_idx_permissions&date={{$demain}}" style="display: inline">>>></a>
      <br />
      
      <em style="float: left; font-weight: normal;">
      {{$affectations|@count}}
      {{if $type_externe == "depart"}}
        D�part(s)
      {{else}}
        Retour(s)
      {{/if}}
      </em>
  
      <select style="float: right" name="filterFunction" style="width: 16em;" onchange="reloadPermission(this.value);">
        <option value=""> &mdash; Toutes les fonctions</option>
        {{foreach from=$functions item=_function}}
          <option value="{{$_function->_id}}" {{if $_function->_id == $filterFunction}}selected="selected"{{/if}} class="mediuser" style="border-color: #{{$_function->color}};">{{$_function}}</option>
        {{/foreach}}
      </select>
    </th>
  </tr>
  
  {{assign var=url value="?m=$m&tab=vw_idx_permissions&type_externe=$type_externe"}}
  <tr>
    <th class="narrow">Valider</th>
    <th>
      Patient
    </th>
    <th class="narrow">
      <input type="text" size="3" onkeyup="Admissions.filter(this, 'admissions')" id="filter-patient-name" />
    </th>
    <th>Praticien</th>
    <th>Heure</th>
    {{if $type_externe == "depart"}}
    <th>Chambre</th>
    <th>Destination</th>
    {{else}}
    <th>Provenance</th>
    <th>Chambre</th>
    {{/if}}
    <th>Dur�e</th>
  </tr>

  {{foreach from=$affectations item=_aff}}
    {{assign var=_sejour value=$_aff->_ref_sejour}}
    <tr class="sejour-type-default sejour-type-{{$_sejour->type}} {{if !$_sejour->facturable}} non-facturable {{/if}}" id="permission{{$_aff->_id}}">
      {{mb_include module=admissions template="inc_vw_permission_line" _sejour=$_aff->_ref_sejour nodebug=true}}
    </tr>
  {{foreachelse}}
  <tr>
    <td colspan="10" class="empty">{{tr}}None{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>