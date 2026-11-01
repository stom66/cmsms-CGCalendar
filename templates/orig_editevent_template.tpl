{* edit event template *}

{* NOTE: This template requires JQUERY be enabled *}
<script type="text/javascript">
{* I would prefer to use jquery here, but as we don't need any other functionality, this function is simple enough to use *}
function toggleDisplay(elem) {
  var el = document.getElementById(elem);
  var txt = el.style.display;
  if( txt == 'none' ) {
      txt = 'block';
  } else {
      txt = 'none';
  }
  el.style.display = txt;
}
$(document).ready(function(){
   var intervals = {$plurals_json};
   $('#event_recurs').change(function(){
     var _recurs = $(this).val();
     if( _recurs == 'none' ) {
        $('#recurs_area').hide();
     } else {
        // set the label for the recur inteval
	$('#interval_plural').html(intervals[_recurs]);

        // hide and show other stuff here.
        $('#recurs_weekly,#recurs_monthly').hide();
	switch(_recurs) {
	   case 'weekly':
	      $('#recurs_weekly').show();
	      break;
	   case 'monthly':
	      $('#recurs_monthly').show();
	      break;
	}

	// show the area
        $('#recurs_area').show();
     }
   });
   $('#event_recurs').trigger('change');
});
</script>

{if isset($message)}
  {if isset($status)}
    <div class="error">{$message}</div>
  {else}
    <div class="message">{$message}</div>
  {/if}
{/if}

<div id="cgcalendar_addedit_event">
  {if $event.event_id > 0 }
    <h4>{$mod->Lang('cal_edit_event',$event.event_id)}</h4>
  {else}
    <h4>{$mod->Lang('cal_add_event')}</h4>
  {/if}

  {$use24=1}
  {$use12=$mod->GetPreference('use_twelve_hour_clock')}
  {if $use12}{$use24=0}{/if}
  {$formstart}

  <div class="row">
    <p class="row_prompt">{$mod->Lang('cal_fromdate')}:</p>
    <p class="row_input">
      {capture assign='tmp'}{$actionid}cal_startdate_{/capture}
      {html_select_date prefix=$tmp time=$event.event_date_start_ut start_year=$start_year end_year=$end_year}
      &nbsp;All Day: <input type="checkbox" name="{$actionid}cal_all_day_event" id="all_day_event" value="1"{if $event.all_day_event == 1} checked="checked"{/if} onclick="toggleDisplay('starttime'); toggleDisplay('endtime');"/>
      <span id="starttime"{if $event.all_day_event == 1} style="display: none;"{/if}> at {html_select_time use_24_hours=$use24 prefix=$tmp time=$event.event_date_start_ut minute_interval=15 display_seconds=false}</span>
    </p>
  </div>

  <div class="row">
    <p class="row_prompt">{$mod->Lang('cal_todate')}:</p>
    <p class="row_input">
      <input type="checkbox" name="{$actionid}cal_enddate_valid" value="1" {if $event.alt_end_date == 1}checked="checked"{/if} onclick="toggleDisplay('enddate');">{$mod->Lang('use_to_date')}<br/>
      {capture assign='tmp'}{$actionid}cal_enddate_{/capture}
      {$end_date=$event.event_date_end_ut|default:$smarty.now}
      <span id="enddate" {if empty($event.event_date_end) || $event.event_date_end == '' || $event.alt_end_date == 0}style="display: none;"{/if}>
        {html_select_date prefix=$tmp time=$end_date start_year=$start_year end_year=$end_year}
        <span id="endtime"{if $event.all_day_event == 1} style="display: none;"{/if}> at {html_select_time use_24_hours=$use24 time=$event.event_date_end_ut prefix=$tmp minute_interval=15 display_seconds=false}</span>
      </span>
    </p>
  </div>

  {if $event.event_parent_id < 1}
    <div class="row">{* recurs option *}
       <p class="row_prompt">{$mod->Lang('recurs')}:</p>
       <p class="row_input">
         <select name="{$actionid}cal_event_recur_period" id="event_recurs">
           {html_options options=$recur_options selected=$event.event_recur_period}
         </select>
       </p>
    </div>{* recurs option *}

    <fieldset id="recurs_area" style="display: none;">
       <legend>{$mod->Lang('cal_recur_details')}:</legend>
       <div class="row">
         <p class="row_prompt">{$mod->Lang('interval')}</span></p>
         <p class="row_input">
           {$mod->Lang('every')}
           <select name="{$actionid}cal_event_recur_interval">
	     {html_options options=$nums selected=$event.event_recur_interval}
	   </select>
	   <span id="interval_plural"></span>
         </p>
       </div>

       {* weekly recurs area *}
       <div id="recurs_weekly">
         <p class="row_prompt">{$mod->Lang('weekdays')}:</p>
         <p class="row_input">
           <select name="{$actionid}cal_event_recur_weekdays[]" multiple="multiple" size="7">
	     {html_options options=$weekdays selected=$event.event_recur_weekdays}
	   </select>
         </p>
       </div>

       <div id="recurs_monthly">
         <p class="row_prompt">{$mod->Lang('frequency')}:</p>
         <p class="row_input">
           <select name="{$actionid}cal_event_recur_monthdays" multiple="multiple" size="5">
	     {html_options options=$recur_days selected=$event.event_recur_monthdays}
	   </select>
         </p>
       </div>

       {* recurs until area *}
       <div class="row">
         <p class="row_prompt">{$mod->Lang('cal_recur_until')}:</p>
         <p class="row_input">
           <input type="date" name="{$actionid}cal_event_recur_date" placeholder="YYYY-MM-DD" value="{$event.event_date_recur_end_ut|date_format:'%Y-%m-%d'}"/>
         </p>
       </div>
       <div class="row">
         <p class="row_prompt">{$mod->Lang('max_recur_events')}:</p>
         <p class="row_input">
           <select name="{$actionid}cal_event_recur_count">
	     {html_options options=$maxiters selected=$event.event_recur_count}
	   </select>
         </p>
       </div>
    </fieldset>{* #recurs_area *}
  {/if}

  <div class="row">
    <p class="row_prompt">*{$mod->Lang('cal_title')}:</p>
    <p class="row_input">
      <input type="text" name="{$actionid}cal_event_title" value="{$event.event_title}" size="80" maxlength="255"/>
    </p>
  </div>

  <div class="row">
    <p class="row_prompt">{$mod->Lang('cal_summary')}:</p>
    <p class="row_input">
      <input type="text" name="{$actionid}cal_event_summary" value="{$event.event_summary}" size="80" maxlength="255"/>
    </p>
  </div>

  <div class="row">
    <p class="row_prompt">{$mod->Lang('cal_details')}:</p>
    <p class="row_input">
      {cge_textarea name="{$actionid}cal_event_details" rows="5" value=$event.event_details wysiwyg=1}
    </p>
  </div>

  {if isset($fields)}
    {foreach from=$fields item='field'}
      <div class="row">
        <p class="row_prompt">{$field->name}:</p>
        <p class="row_input">
          {if $field->type == 0}
      	    <input type="text" name="{$actionid}cal_field_{$field->safename}" size="40" value="{$field->value}"/>
          {elseif $field->type == 2}
     	    {cge_textarea wysiwyg=$field->extra.wysiwyg name="{$actionid}cal_field_{$field->safename}" rows="5" value="{$field->value}"}
          {else}
            {if $field->type == 1}<span>{$field->value}</span><br/>{/if}
            {$field->field}
          {/if}
        </p>
      </div>
    {/foreach}
  {/if}

  {if isset($categories)}
    {foreach from=$categories item='category'}
    <div class="row">
      <p class="row_prompt">{$category->name}:</p>
      <p class="row_input">
        {$category->field}
      </p>
    </div>
    {/foreach}
  {/if}

  <div class="row">
    <p class="row_prompt"></p>
    <p class="row_input">
      <input type="submit" name="{$actionid}cal_submit" value="{$mod->Lang('submit')}"/>
      <input type="submit" name="{$actionid}cal_cancel" value="{$mod->Lang('cancel')}"/>
    </p>
  </div>
  {$formend}
</div>{* #cgcalendar_addedit_event *}
