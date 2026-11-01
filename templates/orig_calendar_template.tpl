{if !isset($smarty.get.nojs)}
<script type="text/javascript">{jsmin}
// the jsmin plugin is included with CGExtensions.
if( typeof jQuery != 'undefined' ) {
  $(document).ready(function(){
    $(document).on('click','a.calendar-nav',function(ev){
      // allow paginating through months via ajax.
      ev.preventDefault();
      var url = $(this).attr('href')+'&nojs=1&showtemplate=false';
      url = url.replace(/amp;/g,'');
      $('#cal-calendar').load(url);
    });

    if( jQuery().dialog ) {
      $(document).on('click','a.calendar-daylist',function(ev){
        // demonstrate viewing a day list in a popup dialog
        // and mixing smarty and javascript code.
        // uses jquery ui dialog... but could just as easily use fancybox etc.
        // you could add parameters here for changing the template from the default, etc, or even filter by category.
        ev.preventDefault();
        var day = new Date( $(this).data('day') * 1000 );
        var m = day.getMonth()+1;
        var d = day.getDate();
        var y = day.getFullYear();
        var url = '{module_action_link module=CGCalendar display=list day=DDDD  month=MMMM year=YYYY jsfriendly=1}&showtemplate=false';
        url = url.replace('MMMM',m).replace('DDDD',d).replace('YYYY',y);
        $('#cal-dayview').load(url,function(data){
          $('#cal-dayview').dialog({
            title: '{$mod->Lang('dayview')}'
          })
        });
      });
    }
  })
} // jquery test
{/jsmin}</script>
{/if}

{strip}
{if !isset($smarty.get.nojs)}
<div style="display: none;">{* a simple wrapper *}
  <div id="cal-dayview"></div>
</div>
{/if}

<table class="calendar" id="cal-calendar">
<caption class="calendar-month">
   <span class="calendar-prev"><a class="calendar-nav" href="{$navigation.prev}">&laquo;</a></span>
     <span class="calendar-lbl">{$date|date_format:'%b %Y'}</span>
   <span class="calendar-next"><a class="calendar-nav" href="{$navigation.next}">&raquo;</a></span>
</caption>
<tbody><tr>
{foreach from=$day_names item=day key=key}
<th class="cal-dayhdr cal-{$day_short_names[$key]|strtolower}" abbr="{$day}">{$day_short_names[$key]}</th>
{/foreach}</tr>

<tr>
{* initial empty days *}
{if $first_of_month_weekday_number > 0}
<td colspan="{$first_of_month_weekday_number}">&nbsp;</td>
{/if}

{* iterate over the days of this month *}
{assign var=weekday value=$first_of_month_weekday_number}
{foreach from=$days item=day key=key}
{if $weekday == 7}
  {assign var=weekday value=0}
</tr>
<tr>
{/if}
<td {if isset($day.class)}class="{$day.class} cal-day cal-{$day_short_names[$weekday]|strtolower}"{/if}>
{if isset($day.events.0)}<a class="calendar-daylist" data-day="{$day.date}" href="{$day.ni_url}">{$key}</a>{* by default use the non inline (replace content tag) version of the URL *}
<ul>
{foreach from=$day.events item=event}
<li>
  {* some simple smarty logic to use the foreground and background colors assigned to an event *}
  {$style=''}{if $event.bgcolor}{$style="style=\"color: {$event.fgcolor}; background-color: {$event.bgcolor}\""}{/if}
  <a class="calendar-event" href="{$event.url}" {$style}>{$event.event_title|summarize:20}</a></li>
{/foreach}
</ul>
{else}{$key}{/if}
</td>
{math assign=weekday equation="x + 1" x=$weekday}
{/foreach}

{* remaining empty days *}
{if $weekday != 7}
<td colspan="{math equation="7-x" x=$weekday}">&nbsp;</td>
{/if}


</tr>
</tbody></table>

{/strip}
