{* CGCalendar list view template *}

{* this template uses jquery and ajax for navigation *}
{if !isset($actionparams.cal_origparams)}
<script type="text/javascript">
$(document).ready(function(){
  $(document).on('click','a.calendar-nav, a.calendar-cur',function(ev){
     // this loads the events via ajax.
     ev.preventDefault();
     var url = $(this).attr('href')+'&showtemplate=false';
     url = url.replace(/amp;/g,'');
     $(this).closest('.calendar-list').load(url);
     return false;
  });
});
</script>
{/if}

<div class="calendar-list">
  {* a header *}
  <div class="calendar-list-hdr">
    {if $listtype == 'day'}
       <h3>{$mod->Lang('hdr_daylist')}: {$start_ts|date_format:'%d %b %Y'}</h3>
    {elseif $listtype == 'week'}
       <h3>{$start_ts|date_format:'%d %b %Y'} {$lang.to} {$end_ts|date_format:'%d %b %Y'}</h3>
    {elseif $listtype == 'month'}
       <h3>{$mod->Lang('hdr_monthlist')} {$start_ts|date_format:'%b %Y'}</h3>
    {elseif $listtype == 'year'}
       <h3>{$mod->Lang('hdr_yearlist')} {$year}</h3>
    {elseif $listtype == 'upcominglist'}
       <h3>{$mod->Lang('hdr_upcominglist')}</h3>
    {elseif $listtype == 'pastlist'}
       <h3>{$mod->Lang('hdr_pastlist')}</h3>
    {/if}
  </div>

  {* a navigation, some views do not have navigation *}
  {if $nav}
  <div class="calendar-list-nav">
    {if isset($nav.prev_url)}
       <span class="calendar-prev"><a class="calendar-nav" href="{$nav.prev_url}">{$lang.prev}</a></span>
    {/if}
    {if isset($nav.home_url)}
       <span class="calendar-cur"><a class="calendar-cur" href="{$nav.home_url}">{$lang.today}</a></span>
    {/if}
    {if isset($nav.prev_url)}
       <span class="calendar-next"><a class="calendar-nav" href="{$nav.next_url}">{$lang.next}</a></span>
    {/if}
  </div>
  {/if}

  {* the events themselves *}
  {foreach from=$events key=key item=event}
    <div class="calendar-event">
      {if $event.fgcolor != '' && $event.bgcolor != ''}
        <h2 style="color: {$event.fgcolor}; background-color: {$event.bgcolor};">{$event.event_title}</h2>
      {else}
        <h2>{$event.event_title}</h2>
      {/if}

      {if $event.event_date_start == $event.event_date_end || $event.event_date_end == 0}
        {* event has no end date *}
        <div class="calendar-date-from"><span class="calendar-date-title">{$lang.date}: </span>{$event.event_date_start|date_format:"%x"}</div>
      {else}
        {if $event.event_date_start|date_format:"%d%m%Y" == $event.event_date_end|date_format:"%d%m%Y"}
	   {* event starts and ends on the same day *}
           <div class="calendar-date-from"><span class="calendar-date-title">{$lang.date}: </span>{$event.event_date_start|date_format:"%x %H:%M"} {$lang.to} {$event.event_date_end|date_format:"%H:%M"}</div>
        {else}
	   {* event staarts and ends on different dates *}
           <div class="calendar-date-from"><span class="calendar-date-title">{$lang.date}: </span>{$event.event_date_start|date_format:"%x %H:%M"} {$lang.to} {$event.event_date_end|date_format:"%x %H:%M"}</div>
        {/if}
      {/if}

      {$details=$event.event_details|strip_tags|trim}
      {if $event.event_summary != '' && $details != ''}
        <div class="calendar-summary"><span class="calendar-summary-title">{$lang.summary}: </span>{$event.event_summary}</div>
      {/if}
      {* optionally display detail information
      {if $details != ''}
        <div class="calendar-details"><span class="calendar-details-title">{$lang.details}: </span>{$event.event_details}</div>
      {/if}
      *}
      <a href="{$event.url}">{$mod->Lang('cal_more')}</a>
    </div>
  {/foreach}
</div>
