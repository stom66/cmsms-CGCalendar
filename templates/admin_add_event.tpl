{cgcss_add}
@media screen and (max-width: 1200px) {
  .grid_8 {
    width: auto !important;
  }
  .grid_4 {
    width: auto !important;
  }
}
textarea {
  width: initial;
  height: initial;
}
{/cgcss_add}

<script>
$(function(){
  var autochange_enddate = {if $event.event_id > 0}0{else}1{/if};
  var labels = []
  labels["daily"] = "{$mod->Lang('plural_daily')}"
  labels["weekly"] = "{$mod->Lang('plural_weekly')}"
  labels["monthly"] = "{$mod->Lang('plural_monthly')}"
  labels["yearly"] = "{$mod->Lang('plural_yearly')}"

  var recurperiod = $('#recurperiod')
  var recur_sub = $('#recur_sub');
  var recurdetails = $('#recurdetails')
  var recurweekly = $('#recur_weekly')
  var recurmonthly = $('#recur_monthly')
  var recuruntil = $('#recur_until')
  var recurnevents = $('#recur_nevents')
  var intervallabel = $('#intervallabel')

  //////

  $('#startdate :input').change(function(){
    if( !autochange_enddate ) return
    var name = $(this).attr('name').replace('start','end')
    var name2 = $(this).attr('name').replace('startdate','event_recur_date')
    var s = ":input[name='" + name + "']"
    $(s).val($(this).val())
    var s2 = ":input[name='" + name2 + "']"
    $(s2).val($(this).val())
  });
  $('#startdate :input').first().trigger('change')

  $('#enddate :input').change(function(){
    autochange_enddate = 0
  })

  $('#all_day_event').click(function(){
    if( $(this).attr('checked') ) {
      $('#starttime').hide();
      $('#endtime').hide();
      $('#use_enddate').hide();
    }
    else {
      $('#starttime').show();
      $('#endtime').show();
      $('#use_enddate').show();
    }
  });

  $('#enddate_valid').click(function(){
    if( $(this).attr('checked') ) {
      $('#enddate').show();
    }
    else {
      $('#enddate').hide();
    }
  });

  var weekday_changed = false
  $('#recur_weekly select').change(function(ev){
     weekday_changed = true
  })
  $('#startdate select').change(function(ev){
     if( weekday_changed ) return
     // buuld a date object, and get the weekday
     var month = $('[name={$actionid}startdate_Month]').val();
     var day = $('[name={$actionid}startdate_Day]').val();
     var year = $('[name={$actionid}startdate_Year]').val();
     var date = new Date(year, month, day)
     var weekday = date.getDay()
     $('#recur_weekly select').val(weekday);
  }).trigger('change')

  $('#recurperiod').change(function(){
     var val = $(this).val();
     if( val !== 'none' ) {
        intervallabel.text(labels[val])
        switch( val ) {
	   case 'daily':
	      recurmonthly.hide()
	      recurweekly.show()
	      break
	   case 'weekly':
	      recurmonthly.hide()
	      recurweekly.show()
	      break
	   case 'monthly':
	      recurmonthly.show(	)
	      recurweekly.hide()
	      break
	   case 'yearly':
	      recurmonthly.hide()
	      recurweekly.hide()
	      break
	}
        recur_sub.show()
     } else {
        recur_sub.hide()
     }
  }).trigger('change');

  $('#update_children').click(function(){
    $('#recur_all').toggle()
    $('#recurperiod').trigger('change')
  });

});
</script>

{if $event.event_id > 0}
<h3>{$mod->Lang('edit_event')}</h3>
{else}
<h3>{$mod->Lang('cal_addevent')}</h3>
{/if}

{if $event.event_id > 0 && $event.event_parent_id > 0}
<div class="information" style="display: block;">{$mod->Lang('info_edit_childevent')}</div>
{/if}
{if $event.event_id > 0 && $event.event_recur_period != 'none' && $event.event_parent_id == -1}
<div class="information" style="display: block;">{$mod->Lang('info_edit_parentevent')}</div>
{/if}

{$formstart}{$hidden|default:''}

<div class="pageoverflow">
  <p class="pageinput">
    <input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>
    <input type="submit" name="{$actionid}cancel" value="{$mod->Lang('cancel')}"/>
  </p>
</div>

{capture assign='use24'}{$mod->GetPreference('use_twelve_hour_clock')}{/capture}
{if $use24 == 1}
  {assign var='use24' value=0}
{else}
  {assign var='use24' value=1}
{/if}

<div class="c_full cf">
  <div class="grid_8" style="margin-left: 0;">
    <div class="c_full cf">
      <label class="grid_3">*{$mod->Lang('cal_fromdate')}:</label>
      <div class="grid_9">
        {capture assign='tmp'}{$actionid}startdate_{/capture}
        <span id="startdate">{html_select_date prefix=$tmp time=$event.event_date_start_ut start_year=$start_year end_year=$end_year}</span>
        &nbsp;<span id="starttime"{if $event.all_day_event == 1} style="display: none;"{/if}> <span style="padding-right: 0.5em;">@</span> {html_select_time use_24_hours=$use24 prefix=$tmp time=$event.event_date_start_ut minute_interval=15 display_seconds=false}</span>
        &nbsp;{$mod->Lang('allday')}: <input type="checkbox" name="{$actionid}all_day_event" id="all_day_event" value="1" {if $event.all_day_event} checked="checked"{/if}/>
        {assign var='s_date' value=$event.event_date_start_ut}{assign var='e_date' value=$event.event_date_end_ut}<br/>
        <label id="use_enddate"><input type="checkbox" id="enddate_valid" name="{$actionid}enddate_valid" value="1" {if $s_date != $e_date && $e_date != 0}checked="checked"{/if}/>{$mod->Lang('use_to_date')}</label>
      </div>
    </div>

    <div id="enddate" class="c_full cf" {if $s_date == $e_date || $e_date == 0}style="display: none;"{/if}>
      <label class="grid_3">{$mod->Lang('cal_todate')}:</label>
      <div class="grid_8">
        {capture assign='tmp'}{$actionid}enddate_{/capture}
        {html_select_date prefix=$tmp  time=$event.event_date_end_ut start_year=$start_year end_year=$end_year}<span id="endtime"{if $event.all_day_event == 1} style="display: none;"{/if}> <span style="padding-right: 0.5em;">@</span> {html_select_time use_24_hours=$use24 time=$event.event_date_end_ut prefix=$tmp minute_interval=15 display_seconds=false}</span>
      </div>
    </div>

    <div class="c_full cf">
      <label class="grid_3">*{$mod->Lang('cal_title')}:</label>
      <input type="text" class="grid_8" name="{$actionid}event_title" value="{$event.event_title}" maxlength="255"/>
    </div>

    {if isset($event_summary)}
    <div class="c_full cf">
      <label class="grid_3">{$mod->Lang('cal_summary')}:</label>
      <input type="text" class="grid_8" name="{$actionid}event_summary" value="{$event.event_summary}" maxlength="255"/>
    </div>
    {/if}

    {* to here *}
    <div id="recur_all" {if $event.event_recur_period != 'none' && $event.event_id > 0}style="display: none;"{/if}>
      <div class="c_full cf">
        <label class="grid_3">{$mod->Lang('cal_recur_period')}:</label>
        {capture assign='tmp'}{$actionid}event_recur_period{/capture}
        <select class="grid_8" id="recurperiod" name="{$actionid}event_recur_period">
          {foreach from=$recur_options key=key item=value}
            <option value="{$value}" {if $value == $event_recur_period}selected="selected"{/if}>{$key}</option>
          {/foreach}
        </select>
      </div>

      <div id="recur_sub" style="display: none;">
        <div class="c_full cf" id="recurdetails">
          <label class="grid_3">{$mod->Lang('interval')}:</label>
          <div class="grid_8">
            {capture assign='tmp'}{$actionid}event_recur_interval{/capture}
            {html_options name=$tmp selected=$event.event_recur_interval options=$nums}&nbsp;<span id="intervallabel">{$repeat_str}</span><br/>
          </div>
        </div>
        <div class="c_full cf" id="recur_weekly">
          <label class="grid_3">{$mod->Lang('weekdays')}:</label>
          <p class="grid_8">{$input_weekdays}</p>
        </div>
        <div class="c_full cf" id="recur_monthly">
          <label class="grid_3">{$mod->Lang('frequency')}:</label>
          <select name="{$actionid}event_recur_monthdays[]" multiple="multiple" size="5" class="grid_8">
            {html_options options=$recur_days selected=$event.event_recur_monthdays}
          </select>
        </div>
        <div class="c_full cf" id="recur_until">
          <label class="grid_3">{$mod->Lang('cal_recur_until')}:</label>
          {capture assign='tmp'}{$actionid}event_recur_date_{/capture}
          <p class="grid_8">
            {html_select_date time=$event.event_date_recur_end_ut prefix=$tmp start_year=$start_year end_year=$end_year}
          </p>
        </div>
        <div class="c_full cf" id="recur_nevents">
          <label class="grid_3">{$mod->Lang('max_recur_events')}:</label>
	  <select name="{$actionid}event_recur_count" class="grid_8">
	    {html_options options=$maxiters selected=$event.event_recur_count}
	  </select>
        </div>
      </div>{* #recur_sub *}
    </div>{* #recur_all *}
  </div>{* .grid_8 *}

  <div class="grid_4">
    {if isset($event.owner_name)}
    <div class="c_full cf">
      <label class="grid_3">{$mod->Lang('owner')}:</label>
      <div class="grid_9">{$event.owner_name}</div>
    </div>
    {/if}

    <div class="c_full cf">
      <label class="grid_3">{$mod->Lang('status')}:</label>
      {$tmp = [ 'D'=>$mod->Lang('draft'), 'P'=>$mod->Lang('published') ]}
      <select name="{$actionid}event_status" class="grid_8">
         {html_options options=$tmp selected=$event.event_status}
      </select>
    </div>
    {if isset($categories)}
      <br/>
      <p class="c_full cf"><strong>{$mod->Lang('cal_categories')}:</strong></p>
      <div class="c_full cf">
      {foreach from=$categories item='one'}
        <label style="float: left; width: 50%;">{$one->field} {$one->name}</label>
      {/foreach}
      </div>
      <hr/>
    {/if}

    {if isset($allow_overlap)}
    <div class="pageoverflow">
      <p class="pagetext">{$mod->Lang('cal_overlap')}:</p>
      <p class="pageinput">
        <input type="hidden" name="{$actionid}event_allows_overlap" value="0"/>
        <input type="checkbox" name="{$actionid}event_allows_overlap" value="1" checked="checked"/>
        <br/>{$mod->Lang('cal_info_overlap')}
      </p>
    </div>
    {/if}

    {if $event.event_id > 0 && $event.event_recur_period != 'none' && $event.event_parent_id == -1}
    <div class="pageoverflow">
      <p class="pagetext">{$mod->Lang('update_children')}  ({$event.event_parent_id}):</p>
      <p class="pageinput">
        {capture assign='tmp'}{$actionid}update_children{/capture}
        <input id="update_children" type="checkbox" name="{$tmp}" value="1"/>
      </p>
    </div>
    {/if}

    {if $event.event_id > 0 && $event.event_parent_id > 0}
    <div class="pageoverflow">
      <p class="pagetext">{$mod->Lang('prompt_removefromparent')}</p>
      <p class="pageinput">
        {cge_yesno_options prefix=$actionid name=removefromparent}
        <br/>{$mod->Lang('info_removefromparent')}
      </p>
    </div>
    {/if}
  </div>{* .grid_4 *}
</div>

<hr/>
<div class="c_full cf">
    {if isset($event_details)}
    <div class="pageoverflow">
      <p class="pagetext">{$mod->Lang('cal_details')}:</p>
      <p class="pageinput">{$event_details}</p>
    </div>
    {/if}

    {if isset($fields)}
      {foreach from=$fields item='one'}
      <div class="pageoverflow">
        <p class="pagetext">{$one->name}</p>
        <p class="pageinput">{$one->field}</p>
      </div>
      {/foreach}
    {/if}
</div>{* c_full *}

{$formend}
