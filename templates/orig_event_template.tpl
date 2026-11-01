<div class="calendar-event">
<h1>{$event.event_title}</h1>

{* create a canonical variable to use in the page header... note we must copy it to the global scope *}
{if isset($event.url)}{$canonical=$event.url scope=global}{/if}

{assign var=month_number value=$event.event_date_start|date_format:"%m"}
{assign var=end_month_number value=$event.event_date_end|date_format:"%m"}
{if $event.event_date_start == $event.event_date_end || $event.event_date_end == 0}
  <div class="calendar-date-from"><span class="calendar-date-title">{$lang.date}: </span>{$event.event_date_start|cms_date_format} {$event.event_date_start|date_format:"%X"}</div>
{else}
{if $event.event_date_start|date_format:"%d%m%Y" == $event.event_date_end|date_format:"%d%m%Y"}
  <div class="calendar-date-from"><span class="calendar-date-title">{$lang.date}: </span>{$event.event_date_start|cms_date_format} {$event.event_date_start|date_format:"%X"} {$lang.to} {$event.event_date_end|date_format:"%H:%M"}</div>
{else}
  <div class="calendar-date-from"><span class="calendar-date-title">{$lang.date}: </span>{$event.event_date_start|date_format:"%e"} {$event_date_start|date_format:'%B'} {$event.event_date_start|date_format:"%Y %H:%M"} {$lang.to} {$event.event_date_end|date_format:"%d"} {$evnet_date_end|date_format:'%B'} {$event.event_date_end|date_format:"%Y %H:%M"}</div>
{/if}
{/if}
{if $event.event_summary !="" && $event.event_details ==""}
	<div class="calendar-summary"><span class="calendar-summary-title">{$lang.summary}: </span>{$event.event_summary}</div>
{/if}
{if $event.event_parent_id > 0}
  {* this is a child event... we can display a link to the parent *}
  <div class="information">{$mod->Lang('info_about_child_event')}</div>
  <div>
    {* note, this does not generate a pretty url to the event at this time,  but the canonical URL stuff in the detail view should resolve any problems *}
    <a href="{module_action_url module=CGCalendar action=default display=event event_id=$event.event_parent_id}">{$mod->Lang('view_parent_event')}</a>
  </div>
{else}
  {* this is a parent/orphan event *}
  {if $event.event_recur_period != '' && $event.event_recur_period != 'none' }
    <div class="information">{$mod->Lang('info_about_parent_event')}</div>
    <div>
       {$period=$lang.recur_period[$event.event_recur_period]|strtolower}
       {$datestr=$event.event_date_recur_end|date_format:'%d %b %Y'}
       {if $event.event_recur_nevents > 0}
         {$mod->Lang('cal_recur_str',$period,$datestr,$event.event_recur_nevents)}
       {else}
         {$mod->Lang('cal_recur_str2',$period,$datestr)}
       {/if}
    </div>
  {/if}
{/if}

{if $event.event_details !="" && $event.event_details != "<br />"}
	<div class="calendar-details"><span class="calendar-details-title">{$lang.details}: </span>{eval var=$event.event_details}</div>
{/if}

{* Display custom fields
   There are two ways to address custom fields
   1) {$event.fields.fieldname}
   2) {foreach $event.fields as $fieldname => $fieldrec}
        {$fieldname}:&nbsp;{$fieldrec.field_value}
      {/foreach}
   You may want to use the former method with file upload or attachment fields.
   Note:  The fieldrec array contains all of the information about the field, including its options, and raw_value (for dropdown or similar fields)
   Note:  values in file attachment fields are all relative to the upload_url config entry (which is by default <root>/uploads).  (v2.5+)
*}
{if isset($event.fields)}
  <div class="calendar-fields">
    {foreach $event.fields as $fieldname => $fieldrec}
      {$fieldname}:&nbsp;{$fieldrec.field_value}<br/>
    {/foreach}
  </div>
{/if}

{if isset($edit_url)}
  {* only available if logged in to FEU... but since FEU is not a dependency we cannot use the feu smarty plugins in a stock template *}
  <div class="calendar-edit">
    <a href="{$edit_url}"><img src="{$mod->GetModuleURLPath()}/images/calendar_edit.png" alt=""/> {$mod->Lang('edit')}</a>
    <a href="{module_action_url module=CGCalendar action=delete_event event_id=$event.event_id}"><img src="{$mod->GetModuleURLPath()}/images/calendar_delete.png" alt=""/> {$mod->Lang('delete')}</a>
  </div>
{/if}
</div>
