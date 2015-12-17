{{if $debiteur_dec}}
  <input name="debiteur_desc" type="text" value="{{$debiteur->description}}" />
{{else}}
  <form name="Edit-CDebiteur" action="?m={{$m}}" method="post" onsubmit="return Debiteur.submit(this);">
    {{mb_key    object=$debiteur}}
    {{mb_class  object=$debiteur}}
    <input type="hidden" name="del" value="0"/>
    <table class="form">
      {{mb_include module=system template=inc_form_table_header object=$debiteur}}
      <tr>
        <th>{{mb_label object=$debiteur field=numero}}</th>
        <td>{{mb_field object=$debiteur field=numero}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$debiteur field=nom}}</th>
        <td>{{mb_field object=$debiteur field=nom}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$debiteur field=description}}</th>
        <td>{{mb_field object=$debiteur field=description textearea=1}}</td>
      </tr>
      <tr>
        <td class="button" colspan="2">
          {{if $debiteur->_id}}
            <button class="submit" type="submit">{{tr}}Save{{/tr}}</button>
            <button class="trash" type="reset" onclick="confirmDeletion(this.form,{typeName:'le d�biteur du',objName: $V(this.form.nom) })">
              {{tr}}Delete{{/tr}}
            </button>
          {{else}}
            <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
          {{/if}}
        </td>
      </tr>
    </table>
  </form>
{{/if}}