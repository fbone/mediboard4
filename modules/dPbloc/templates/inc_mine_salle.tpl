{{*
 * $Id$
 *  
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<h2>Exploration de donn�es</h2>

<script>
  submitMine = function() {
    var form = getForm("mine");
    form.onsubmit();
  };
</script>

<form name="mine" method="get" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="dPbloc"/>
  <input type="hidden" name="a" value="ajax_datamine_salle" />
  <input type="hidden" name="miner_class" value="CDailySalleOccupation" />
  <input type="hidden" name="phase" value="mine" />
  <label>
    <input type="checkbox" name="auto" value="1" />Auto
  </label>

  <button onclick="$V(this.form.phase, 'mine'); this.form.onsubmit();" class="change">Mine</button>
  <button onclick="$V(this.form.phase, 'remine'); this.form.onsubmit();" class="change">ReMine</button>
  <button onclick="$V(this.form.phase, 'postmine'); this.form.onsubmit();" class="change">PostMine</button>
</form>