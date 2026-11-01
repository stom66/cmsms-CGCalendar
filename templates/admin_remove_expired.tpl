<script type="text/javascript">
  $(document).ready(function(){
     $('#select_all').click(function(){
       var _v = $(this).is(':checked');
       if( _v ) {
          $('.cal_select').attr('checked','checked');
       } else {
          $('.cal_select').removeAttr('checked');
       }
     })
  });
</script>

<h3>{$mod->Lang('cal_delete_expired')}</h3>

{form_start}
<div class="pageoverflow">
  <p class="pageinput">
    <input type="submit" name="{$actionid}cal_submit" value="{$mod->Lang('submit')}"/>
    <input type="submit" name="{$actionid}cal_cancel" value="{$mod->Lang('cancel')}"/>
  </p>
</div>
<h4>Events to Delete:</h4>
<table class="pagetable">
  <thead>
    <tr>
      <th>{$mod->Lang('cal_id')}</th>
      <th>{$mod->Lang('cal_title')}/th>
      <th>{$mod->Lang('cal_summary')}</th>
      <th>{$mod->Lang('cal_fromdate')}</th>
      <th>{$mod->Lang('cal_todate')}</th>
      <th>{$mod->Lang('cal_recur_until')}</th>
      <th class="pageicon">
        <input type="checkbox" id="select_all" value="1" title="Select All"/>
      </th>
    </tr>
  </thead>
  <tbody>
  {foreach $events as $one_event}
    <tr class="{cycle values='row1,row2'}">
      <td>{$one_event.event_id}</td>
      <td>{$one_event.event_title}</td>
      <td>{$one_event.event_summary|summarize:30}</td>
      <td>{$one_event.event_date_start|date_format:'%x %X'}</td>
      <td>{if !empty($one_event.event_date_end)}{$one_event.event_date_end|date_format:'%x %X'}{/if}</td>
      <td>
        {if !empty($one_event.event_recur_last)}
	  {if $one_event.event_recur_last > $smarty.now}
	    <span style="color: red;">{$one_event.event_recur_last|date_format:'%x %X'}</span>
	  {else}
	    <span>{$one_event.event_recur_last|date_format:'%x %X'}</span>
	  {/if}
        {/if}
      </td>
      <td>
         {if empty($one_event.event_recur_last) || $one_event.event_recur_last < $smarty.now}
         <input type="checkbox" name="{$actionid}cal_select[]" value="{$one_event.event_id}" class="cal_select"/>
	 {/if}
      </td>
    </tr>
  {/foreach}
  </tbody>
</table>
{form_end}