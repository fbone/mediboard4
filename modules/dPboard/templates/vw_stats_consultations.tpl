{{* $Id: vw_stats_consultations.tpl 9256 2010-06-21 13:20:44Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage dPboard
 * @version $Revision: 9256 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $prat->_id}}

<script type="text/javascript">
var graphs = {{$graphs|@json}};
Main.add(function(){
  graphs.each(function(g, i){
    Flotr.draw($('graph-'+i), g.series, g.options);
  });
});
</script>

<form name="filters" action="?" method="get" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="dPboard" />
  
  <table class="form">
    <tr>
      <th colspan="4" class="category">Statistiques de consultation</th>
    </tr>
    
    <tr>
      <th>{{mb_label object=$filterConsultation field="_date_min"}}</th>
      <td>{{mb_field object=$filterConsultation field="_date_min" form="filters" register=true canNull="false"}} </td>
      <th>{{mb_label object=$filterConsultation field="_date_max"}}</th>
      <td>{{mb_field object=$filterConsultation field="_date_max" form="filters" register=true canNull="false"}} </td>
    </tr>
    
    <tr>
      <td colspan="4" class="button"><button type="submit" class="search">Afficher</button></td>
    </tr>
  </table>
</form>

{{foreach from=$graphs item=graph key=key}}
  <div style="width: 600px; height: 350px; float: left; margin: 1em;" id="graph-{{$key}}"></div>
{{/foreach}}

{{/if}}