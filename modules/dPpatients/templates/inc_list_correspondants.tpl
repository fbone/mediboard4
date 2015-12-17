{{mb_default var=readonly value=false}}

{{if !$readonly}}
<script>
  Main.add(function() {
    Control.Tabs.setTabCount("correspondance", "{{$nb_correspondants}}");
  });
</script>
{{/if}}

<table style="width: 100%;" class="tbl">
  <thead>
    <tr>
      {{if !$readonly}}
        <th class="category narrow"></th>
      {{/if}}

      <th class="category">{{tr}}CCorrespondantPatient{{/tr}}</th>
      <th class="category">{{mb_title class=CCorrespondantPatient field=naissance}}</th>
      <th class="category">{{mb_title class=CCorrespondantPatient field=adresse}}</th>
      <th class="category">{{mb_title class=CCorrespondantPatient field=tel}}</th>
      <th class="category">{{mb_title class=CCorrespondantPatient field=fax}}</th>
      <th class="category">{{mb_title class=CCorrespondantPatient field=parente}}</th>
      {{if $conf.ref_pays == 1}}
        <th class="category">{{mb_title class=CCorrespondantPatient field=urssaf}}</th>
      {{/if}}
      <th class="category">{{mb_title class=CCorrespondantPatient field=email}}</th>
      <th class="category">{{mb_title class=CCorrespondantPatient field=remarques}}</th>
      <th class="category">{{mb_title class=CCorrespondantPatient field=date_debut}}</th>
      <th class="category">{{mb_title class=CCorrespondantPatient field=date_fin}}</th>
    </tr>
  </thead>
  {{if $nb_correspondants > 0}}
    {{foreach from=$correspondants_by_relation item=_correspondants key=relation}}
      {{if $_correspondants|@count}}
        <tr>
          <th class="section" colspan="{{if $conf.ref_pays == 1}}14{{else}}13{{/if}}">
            {{tr}}CCorrespondantPatient.relation.{{$relation}}{{/tr}}
          </th>
        </tr>
        {{foreach from=$_correspondants item=_correspondant}}
          <tr {{if $_correspondant->_is_obsolete}}class="hatching"{{/if}}>
            {{if !$readonly}}
              <td>
                <button {{if $_correspondant->_is_obsolete}}disabled{{/if}} type="button" class="edit notext"
                        onclick="Correspondant.edit('{{$_correspondant->_id}}', null, Correspondant.refreshList.curry('{{$patient_id}}'))">
                </button>
                <button class="copy notext" {{if $_correspondant->_is_obsolete}}disabled{{/if}}
                        onclick="Correspondant.edit('{{$_correspondant->_id}}', null, Correspondant.refreshList.curry('{{$patient_id}}'), 1)">
                  {{tr}}CCorrespondantPatient-copy{{/tr}}
                </button>

                {{if $_correspondant->_is_obsolete}}
                  <button type="button" class="duplicate notext"
                          onclick="Correspondant.duplicate('{{$_correspondant->_id}}', null, Correspondant.refreshList.curry('{{$patient_id}}'))">
                  </button>
                {{/if}}
              </td>
            {{/if}}

            <td class="text noted">
              <div style="float: right;">
                {{mb_include module=system template=inc_object_notes object=$_correspondant}}
              </div>

              {{$_correspondant->_longview}}
            </td>
            <td>{{mb_value object=$_correspondant field=naissance}}</td>
            <td class="text compact">
              <span style="white-space: nowrap;">{{$_correspondant->adresse|spancate:30}}</span>
              <span style="white-space: nowrap;">{{$_correspondant->cp}} {{$_correspondant->ville|spancate:20}}</span>
            </td>
            <td style="white-space: nowrap;">
              {{mb_value object=$_correspondant field=tel}} <br />
              {{mb_value object=$_correspondant field=mob}}
            </td>
            <td style="white-space: nowrap;">{{mb_value object=$_correspondant field=fax}}</td>
            <td {{if !$_correspondant->parente}}class="empty"{{/if}}>
              {{if $_correspondant->relation != "employeur"}}
                {{if $_correspondant->parente == "autre"}}
                  {{mb_value object=$_correspondant field=parente_autre}}
                {{else}}
                  {{mb_value object=$_correspondant field=parente}}
                {{/if}}
              {{/if}}
            </td>
            {{if $conf.ref_pays == 1}}
              <td>
                {{if $_correspondant->relation == "employeur"}}
                  {{mb_value object=$_correspondant field=urssaf}}
                {{/if}}
              </td>
            {{/if}}
            <td>{{mb_value object=$_correspondant field=email}}</td>
            <td class="text compact">
              {{if $conf.ref_pays == 1}}
                {{mb_value object=$_correspondant field=remarques}}
              {{else}}
                 Rqes:{{mb_value object=$_correspondant field=remarques}}<br/>
                 EAN : {{mb_value object=$_correspondant field=ean}}<br/>
                 Assure_id : {{mb_value object=$_correspondant field=assure_id}}<br/>
                  {{if $_correspondant->ean_id}}({{mb_value object=$_correspondant field=ean_id}}){{/if}}
              {{/if}}
            </td>
            <td>{{mb_value object=$_correspondant field=date_debut}}</td>
            <td>{{mb_value object=$_correspondant field=date_fin}}</td>
          </tr>
        {{foreachelse}}
          <tr>
            <td colspan="13" class="empty">{{tr}}CCorrespondantPatient.none{{/tr}}</td>
          </tr>
        {{/foreach}}
      {{/if}}
    {{/foreach}}
  {{else}}
    <tr>
      <td colspan="13" class="empty">{{tr}}CCorrespondantPatient.none{{/tr}}</td>
    </tr>
  {{/if}}
</table>