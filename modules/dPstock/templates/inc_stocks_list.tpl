{{* $Id: inc_stocks_list.tpl 25962 2014-11-20 10:26:34Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage Stock
 * @version $Revision: 25962 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=class value=$stock->_class}}

{{mb_include module=system template=inc_pagination change_page="changePage" 
    total=$list_stocks_count current=$start step=$conf.dPstock.$class.pagination_size}}

<table class="tbl">
  <tr>
    {{if $class == 'CProductStockGroup'}}
      <th style="width: 16px;"></th>
    {{/if}}
    <th>{{mb_title object=$stock field=product_id}}</th>
    {{if $class == 'CProductStockService'}}
      <th>{{mb_title object=$stock field=object_id}}</th>
    {{/if}}
    <th>{{mb_title object=$stock field=location_id}}</th>
    <th>{{mb_title object=$stock field=quantity}}</th>
    <th colspan="3">{{mb_title object=$stock field=_package_quantity}}</th>
    <th>{{tr}}CProductStockGroup-bargraph{{/tr}}</th>
  </tr>
  
<!-- Stocks service list -->
{{foreach from=$list_stocks item=_stock}}
  {{assign var=product value=$_stock->_ref_product}}
  <tr {{if $stock_id == $_stock->_id}}class="selected"{{/if}} id="row-{{$_stock->_guid}}">
    {{if $class == 'CProductStockGroup'}}
      <td {{if $product->_in_order}}class="ok"{{/if}}>
        {{mb_include module=stock template=inc_product_in_order}}
      </td>
    {{/if}}
    <td>
      <a href="#1" onclick="refreshEditStock('{{$_stock->_id}}', '{{$product->_id}}'); return false;">
        <span onmouseover="ObjectTooltip.createEx(this, '{{$product->_guid}}')">
          {{$product->_view|truncate:60}}
        </span>
      </a>
    </td>
    {{if $class == 'CProductStockService'}}
      <td>{{$_stock->_ref_object}}</td>
    {{/if}}
    <td>{{$_stock->_ref_location->_shortview}}</td>
    <td style="text-align: right;">
      <strong>{{$_stock->quantity}}</strong>
    </td>
    
    <td style="text-align: right;">= {{$_stock->_package_quantity}}</td>
    <td>{{$product->packaging}}</td>
    <td style="text-align: right;">
      {{if $_stock->_package_mod-0}}
      + {{$_stock->_package_mod-0}}
      {{/if}}
    </td>
    <td>{{include file="inc_bargraph.tpl" stock=$_stock}}</td>
  </tr>
{{foreachelse}}
  <tr>
    <td colspan="10" class="empty">{{tr}}{{$class}}.none{{/tr}}</td>
  </tr>
{{/foreach}}
</table>

