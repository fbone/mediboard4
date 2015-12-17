<td colspan="{{if isset($colspan|smarty:nodefaults)}}{{$colspan}}{{/if}}">
  {{if ($object->_class == "CFactureEtablissement" || $object->_class == "CFactureCabinet") && $object->cloture}}
    {{assign var="ref" value=_ref_$name}}
    {{$object->$ref->nom}}
  {{else}}
    <select name="{{$name}}" style="width: 15em;" 
      {{if $object->_class == "CFactureCabinet" || $object->_class == "CFactureEtablissement" }}onchange="return onSubmitFormAjax(this.form);"
      {{else}}
      onchange="Value.synchronize(this, 'editSejour');"
      {{/if}}>
      <option value="" {{if !$object->$name}}selected="selected" {{/if}}>&mdash; Choisir une assurance</option>
      {{foreach from=$patient->_ref_correspondants_patient item=_assurance}}
        <option value="{{$_assurance->_id}}" {{if $object->$name == $_assurance->_id}} selected="selected" {{/if}}>
          {{$_assurance->nom}}
          {{mb_include module=system template=inc_interval_date from=$_assurance->date_debut to=$_assurance->date_fin}}
        </option>
      {{/foreach}}
    </select>
  {{/if}}
</td>