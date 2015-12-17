{{* $Id: CExamenLabo_view.tpl 17168 2012-11-02 11:16:37Z rhum1 $ *}}

{{if !$object->_can->read}}
  <div class="small-info">
    {{tr}}{{$object->_class}}{{/tr}} : {{tr}}access-forbidden{{/tr}}
  </div>
  {{mb_return}}
{{/if}}

{{assign var="examen" value=$object}}

<table class="tbl tooltip">
  <tr>
    <th>
      {{$examen->_view}}
    </th>
  </tr>
  <tr>
    <td>
      <strong>Classifications:</strong>
      <ul>
        {{foreach from=$examen->_ref_catalogues item="_catalogue"}}
        <li>{{$_catalogue->_view}}</li>
        {{/foreach}}
      </ul>
      
      <strong>Type:</strong>
      <em>
        {{mb_value object=$examen field="type"}}
        {{if $examen->type == "num"}}
          : {{$examen->unite}}
          ({{$examen->min}} &ndash; {{$examen->max}})
        {{/if}}
      </em>
      <br/>
      
      {{if $examen->deb_application || $examen->fin_application}}
      <strong>
        {{tr}}{{$examen->_class}}-application{{/tr}}
      </strong>
      <em>
        de {{$examen->deb_application|date_format:"%d/%m/%Y"|default:"-"}}
        �  {{$examen->fin_application|date_format:"%d/%m/%Y"|default:"-"}}
      </em>
      <br/>
      {{/if}}
      
      <strong>
        {{mb_label object=$examen field=applicabilite}} 
      </strong>
      <em>
        {{mb_value object=$examen field=applicabilite}} 
        {{if $examen->age_min || $examen->age_max}}
          ({{$examen->age_min}} &ndash; {{$examen->age_max}} ans)
        {{/if}}
      </em>
      <br/>
      
      {{if $examen->temps_conservation}}
      <strong>
        {{mb_label object=$examen field=temps_conservation}}
      </strong>
      <em>
        {{mb_value object=$examen field=temps_conservation}} unit�s ?
      </em>
      <br/>
      {{/if}}
      
      {{if $examen->realisateur}}
      <strong>
        {{mb_label object=$examen field=realisateur}}
      </strong>
      <em>
        {{$examen->_ref_realisateur->_view}}
        (
        {{if $examen->execution_lun}}L{{else}}&ndash;{{/if}}
        {{if $examen->execution_mar}}M{{else}}&ndash;{{/if}}
        {{if $examen->execution_mer}}M{{else}}&ndash;{{/if}}
        {{if $examen->execution_jeu}}J{{else}}&ndash;{{/if}}
        {{if $examen->execution_ven}}V{{else}}&ndash;{{/if}}
        {{if $examen->execution_sam}}S{{else}}&ndash;{{/if}}
        {{if $examen->execution_dim}}D{{else}}&ndash;{{/if}}
        )
      </em>
      <br/>
      {{/if}}

    </td>
  </tr>
</table>