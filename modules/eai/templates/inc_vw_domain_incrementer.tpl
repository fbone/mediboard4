{{*
 * View incrementer/domain EAI
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
*}}
<div id="incrementer_domain">
  {{if count($domain->_ref_group_domains) == 0}}
    <div class="small-warning">{{tr}}CDomain-msg-no group domain{{/tr}}</div>
    {{mb_return}}
  {{/if}}

  {{if !$domain->incrementer_id && !$domain->actor_id && ($domain->_is_master_ipp || $domain->_is_master_nda)}}
    <button type="button" class="add" onclick="Domain.editIncrementer(null, '{{$domain->_id}}')">{{tr}}Add{{/tr}}</button>
  {{/if}}
  {{if $domain->incrementer_id}}
    {{mb_include template=inc_list_incrementer incrementer=$domain->_ref_incrementer}}
  {{else}}
    {{if $domain->actor_id}}
      <div class="small-warning">{{tr}}CDomain-incrementer-none{{/tr}}</div>
    {{else}}
      {{if !$domain->_is_master_ipp && !$domain->_is_master_nda}}
        <div class="small-warning">{{tr}}CDomain-incrementer-no_master{{/tr}}</div>
      {{else}}
        <div class="small-info">{{tr}}CDomain-incrementer_id-desc{{/tr}}</div>
      {{/if}}
    {{/if}}
  {{/if}}
</div>