{* orig fullcalendar view *}
{* This template utilizes fullcalendar (http://fullcalendar.io) an interactive javascript calendar *}
{* this action requires that:
   a: jquery and jquery-ui be enabled in the page header.  You can use jQueryTools, or {cms_jquery} for this.
   b: {cgjs_render} be called in the page header, after the inclusion of jquery and jquery ui.
*}
{cgjs_require cssfile='lib/fullcalendar/fullcalendar.min.css'}
{cgjs_require jsfile='lib/fullcalendar/lib/moment.min.js'}
{cgjs_require jsfile='lib/fullcalendar/fullcalendar.js'}
{cgjs_add nominify=1}
{*module_action_url action=ajax_fetchevents forajax=1 assign='fetch_url'*}
{$actionid='cntnt01'}
function entity_decode(encoded) {
   return $('<textarea/>').html(encoded).text();
}
$(document).ready(function(){
  var _holidays = null;
  var dateStrings = {$datestringsjson};
  $.ajax({
     url: '{module_action_url action=ajax_fetchholidays forajax=1}'
  }).done(function(data){
     if( typeof data != 'undefined' && data != null && data.length > 0 ) {
        _holidays = data;
     }

   $('#fullcalendar').fullCalendar({
     dayRender: function( date, cell ) {
       // get holiday events for today.
       if( typeof _holidays == 'string' ) return;
       if( !_holidays || ! _holidays instanceof Array ) return;
       var _tst1 = date.format('YYYY-MM-DD');
       _holidays.forEach(function(entry){
          if( _tst1 == entry.start ) {
             cell.addClass('holiday');
	     cell.css('backgroundColor','{$holiday_bgcolor}').css('color','{$holiday_fgcolor}');
	     cell.html(entry.title);
	  }
       });
     },
     events: {
       url:  '{$fetch_url}',
       type: 'GET',
       data: {
         '{$actionid}showchildren': 1,
	 '{$actionid}detailpage': '{$detailpage}',
	 '{$actionid}eventtemplate': '{$actionparams.eventtemplate|default:''}',
	 '{$actionid}editpage': '{$actionparams.editpage|default:''}',
	 '{$actionid}editeventtemplate': '{$actionparams.editeventtemplate|default:''}',
       },
       error: function() {
         alert('{$mod->Lang('error_fetching_events')|cms_escape}');
       }
     },
     header: {
       left: 'prev,next,today',
       center: 'title',
       right: 'month,agendaWeek,agendaDay'
     },
     defaultEventMinutes: 15,
     slotMinutes: 15,
     allDayText: '{$mod->Lang('allday')|cms_escape}',
     eventColor: '{$dflt_bgcolor}',
     eventTextColor: '{$dflt_fgcolor}',
     monthNames: dateStrings.monthNames,
     monthNamesShort: dateStrings.monthNamesShort,
     dayNames: dateStrings.dayNames,
     dayNamesShort: dateStrings.dayNamesShort,
     firstDay: {$firstdayofweek},
     views: {
        month: { columnFormat: 'ddd' },
        week: { columnFormat: 'ddd d/w' },
        day: { columnFormat: 'dddd d/M' },
     },
     buttonText: {
       today: entity_decode('{$text_today|cms_escape}'),
       week: '{$text_week|cms_escape}',
       month: '{$text_month|cms_escape}',
       day: '{$text_day|cms_escape}'
     },
     eventClick: function(event, jsEvent, view) {
       // woot, we can go to a detail view.
       window.location = event.detail_url;
     }
   });

   });
});
{/cgjs_add}

<div id="fullcalendar"></div>
