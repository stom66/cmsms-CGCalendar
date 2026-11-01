{cgjs_require cssfile='lib/fullcalendar/fullcalendar.min.css'}
{cgjs_require jsfile='lib/fullcalendar/lib/moment.min.js'}
{cgjs_require jsfile='lib/fullcalendar/fullcalendar.js'}
{cgjs_require cssfile='lib/colorPicker/colorPicker.css'}
{cgjs_require jsfile='lib/colorPicker/jquery.colorPicker.min.js'}
{cgcss_add}
.fc-event {
  min-height: 20px;
}
.fc-event-icons {
  text-align: right;
  float: right;
  position: absolute;
  bottom: 0;
  right: 0;
}
.information {
  display: block;
}
.error {
  display: block;
}
.fc-event-container a {
  color: white;
}
.text_right {
  text-align: right;
}
.form_row {
  padding: 2px 0;
  height: auto;
}
.form_prompt {
  text-align: right;
}
.form_input {
}
a.goto_date {
  cursor: pointer; cursor: hand;
}
#find_results_panel table {
  width: 100%;
}
#find_results_panel th {
  padding-right: 1em;
}
#find_results_panel td {
  padding-right: 1em;
}
#find_results_none {
  width: 95%;
}
{/cgcss_add}

{module_action_link action='admin_ajax_fetchevents' jsfriendly=1 assign=fetch_url}

<script>
String.prototype.addSlashes = function()
{
   //no need to do (str+'') anymore because 'this' can only be a string
   return this.replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');
}

String.prototype.decode = function() {
   var txt = $('<div/>').html(this).text();
   return txt;
}

function closeDialog(sel) {
  try {
    if( $(sel).dialog('isOpen') ) $(sel).dialog('close');
  } catch( err ) {
    // nothing.
  }
}
function formatDate(date,show_time) {
  var locale = window.navigator.userLanguage || window.navigator.language;
  date.locale(locale);
  var out = date.format('L');
  if( show_time ) out = date.format('L LT');
  return out;
}
function createCookie(name, value, days) {
    var expires;

    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    } else {
        expires = "";
    }
    document.cookie = escape(name) + "=" + escape(JSON.stringify(value)) + expires + "; path=/";
}
function readCookie(name) {
    var nameEQ = escape(name) + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0) {
	  var t = unescape(c.substring(nameEQ.length, c.length));
	  t = jQuery.parseJSON(t);
	  return t;
	}
    }
    return null;
}
function eraseCookie(name) {
    createCookie(name, "", -1);
}

$(function(){
  var dfltCalendarView = (readCookie('cgcalendar_curview')) ? readCookie('cgcalendar_curview') : 'month';
  // restore our selected date from a cookie.
  var cur_date = new Date();
  var datestrings = {$datestrings};
  var tmp = readCookie('cgcalendar_date');
  if( tmp != null ) cur_date = new Date(tmp);

  var _holidays = null;
  $.ajax({
     url: '{module_action_url action=ajax_fetchholidays forajax=1}'
  }).done(function(data){
     if( typeof data != 'undefined' && data != null && data.length > 0 ) {
        _holidays = data;
     }
  $('#calendarview').fullCalendar({
    dayRender: function( date, cell ) {
       // get holiday events for today.
       if( ! _holidays instanceof Array ) return;
       if( _holidays == null || _holidays.length == 0 ) return;
       var _tst1 = date.format('YYYY-MM-DD');
       _holidays.forEach(function(entry){
          if( _tst1 == entry.start ) {
             cell.addClass('holiday');
	     cell.css('backgroundColor','{$holiday_bgcolor}').css('color','{$holiday_fgcolor}');
	     cell.html(entry.title);
	  }
       });
    },
    dayClick: function( date, jsEvent, view ) {
       var url = '{module_action_link action=admin_add_event jsfriendly=1}';
       var st = {$dflt_starttime};
       date.time( st[0]+':'+st[1] );  // adjust time to our default start/end time.
       start = date.toISOString(); // assume UTC/Zulu time.
       url += '&{$actionid}start='+start;
       if( confirm('{$mod->Lang('confirm_add_event_here')}') ) {
           window.location.href = url;
       }
    },
    events: {
       url: '{$fetch_url}&showtemplate=false&t='+Math.random(),
       type: 'GET',
       data: function() {
         var o = {};
         o['{$actionid}filter'] = $('#filter').val();
         o['{$actionid}showchildren'] = 1;
         return o;
       },
       error: function(data) {
         alert('error ' + data);
         alert('{$mod->Lang('error_fetching_events')|cms_escape}');
       }
    },
    defaultDate: cur_date,
    header: {
      left: 'prev,next,today',
      center: 'title',
      right: 'listYear,month,agendaWeek,agendaDay'
    },
    eventColor: '{$dflt_bgcolor}',
    eventTextColor: '{$dflt_fgcolor}',
    allDayText: '{$mod->Lang('allday')|cms_escape}',
    monthNames: datestrings.monthNames,
    monthNamesShort: datestrings.monthNamesShort,
    dayNames: datestrings.dayNames,
    dayNamesShort: datestrings.dayNamesShort,
    buttonText: {
       listYear: 'Year',
       today: '{$text_today|cms_escape}'.decode(),
       week: '{$text_week|cms_escape}'.decode(),
       month: '{$text_month|cms_escape}'.decode(),
       day: '{$text_day|cms_escape}'.decode()
    },
    firstDay: {$firstdayofweek},
    defaultView: dfltCalendarView,
    defaultEventMinutes: 15,
    slotMinutes: 15,
    editable: true,
    viewRender: function( view, element ) {
      cur_date = $('#calendarview').fullCalendar('getDate');
      createCookie('cgcalendar_date',cur_date,7);
      createCookie('cgcalendar_curview',view.name,7);
    },
    viewDestroy: function(view) {
        closeDialog('#eventpreview');
	closeDialog('#find_panel');
	closeDialog('#find_results_panel');
    },

    eventClick: function(event, jsEvent, view) {
       $('#event_start').html(formatDate(event.start,!event.allDay));
       $('#event_desc').html(event.summary);
       $('#eventpreview .draft_block').hide();
       $('#eventpreview .parent_block').hide();
       $('#eventpreview .child_block').hide();
       if( event.child ) {
         $('#eventpreview .child_block').show();
       }
       else if( event.recurs ) {
         $('#eventpreview .child_block').hide();
         $('#eventpreview .parent_block').show();
       }

       if( event.status != 'P' ) $('#eventpreview .draft_block').show();

       if( event.allDay ) {
         $('#eventpreview .end_block').hide();
	 $('#eventpreview .allday_block').show();
       }
       else {
         $('#eventpreview .allday_block').hide();
         if( event.end ) {
           $('#eventpreview .end_block').show();
           $('#event_end').html(formatDate(event.end,!event.allDay));
	 }
	 else {
           $('#eventpreview .end_block').hide();
	 }
       }

       var tmp_buttons = [{
         text: '{$mod->Lang('cal_edit')}',
	 click: function() {
           var url = '{module_action_link action=admin_add_event jsfriendly=1}';
           url += '&{$actionid}event_id='+event._id
           location.href = url;
	 }
       }];

       if( event.child ) {
         tmp_buttons.push({
           text: '{$mod->Lang('editparent')}',
	   click: function() {
             var url = '{module_action_link action=admin_add_event event_id=xx jsfriendly=1}';
             url += '&{$actionid}event_id='+event.child
             location.href = url;
	   }
         });
       }

       tmp_buttons.push({
         text: '{$mod->Lang('delete')}',
         click: function() {
             var cont = this;
	     $.ajax({
	         type: 'POST',
                 url: '{module_action_link action=admin_ajax_deleteevent jsfriendly=1}&showtemplate=false',
     	         dataType: 'json',
 	         data: {
  	      	    '{$actionid}event_id': event._id,
	     	    '{$actionid}delete_children': 1 // todo, make this an option
	         }
             }).done(function(data) {
	         if( data.status == 0 ) {
   	             alert(data.msg);
	         } else {
	             // make sure it's not visible
	             $('#calendarview').fullCalendar('refetchEvents');
      	         }
	         closeDialog(cont);
   	     }).fail(function(jqXHR, textStatus, errorThrown) {
	         alert('fail '+textStatus);
	         closeDialog(cont);
             });
	 }
       });

       tmp_buttons.push({
         text: '{$mod->Lang('cancel')}',
	 click: function() {
	   $(this).dialog('close');
	 }
       });

       $('#eventpreview').dialog({
         title: event.title,
	 position: {
	   my: 'center bottom',
	   at: 'top center',
	   of: jsEvent
	 },
         width: 400,
         height: 200,
	 buttons: tmp_buttons
       });
    },

    eventResize: function(ev, dayDelta, minuteDelta, revertFunc ) {
      // an event time has been extended
      $.ajax({
        type: 'POST',
	url:  '{module_action_link action=admin_ajax_editevent jsfriendly=1}&showtemplate=false',
	dataType: 'json',
	data: {
	  '{$actionid}event_id': ev.id,
	  '{$actionid}dayDelta': dayDelta,
	  '{$actionid}minuteDelta': minuteDelta,
	},
	success: function( data ) {
	  if( data.status == 0 ) {
	      alert(data.msg);
	      revertFunc();
	  }
	},
	fail: function( data ) {
	  alert('fail');
	  revertFunc();
	}
      });
    },

    eventDrop: function(ev, dayDelta, minuteDelta, allDay, revertFunc) {
      // an event has been moved
      $.ajax({
        type: 'POST',
	url:  '{module_action_link action=admin_ajax_editevent jsfriendly=1}&showtemplate=false',
	dataType: 'json',
	data: {
	  '{$actionid}event_id': ev.id,
	  '{$actionid}dayDelta': dayDelta,
	  '{$actionid}minuteDelta': minuteDelta,
	  '{$actionid}allDay': allDay ? 1 : 0,
	  '{$actionid}move': 1
	},
	success: function( data ) {
	  if( data.status == 0 ) {
	      alert(data.msg);
	      revertFunc();
	  }
	},
	fail: function( data ) {
	  alert('fail');
	  revertFunc();
	}
      });
    }
  });

  });  // fetch holidays done...

  $('#filter').change(function(){
     $('#calendarview').fullCalendar('refetchEvents');
  });

  $('a#find_event').click(function(){
    var t = readCookie('cgcal_find');
    if( t != null ) {
      for( var k in t ) {
        var s = '#find_panel :input[name="' + k + '"]';
	$(s).val(t[k]);
      }
    }
    $('#find_panel').dialog({
      width: 375,
      buttons: [
        {
	  text: '{$mod->Lang('submit')}',
	  click: function() {
            // get the data
	    var data = {};
	    var f = 0;
	    $('#find_panel :input').each(function(){
	       var n = $(this).attr('name');
	       var v = $(this).val();
	       if( v ) {
	         data[n] = v;
		 f = 1;
	       }
	    })
	    // save it in a cookie
	    if( f ) createCookie('cgcal_find',data,7);

	    // close the dialog
	    $(this).dialog('close');

	    $.ajax({
	       url: '{$fetch_url}&showtemplate=false',
	       type: 'GET',
               cache: false,
	       dataType: 'json',
	       data: {
		 '{$actionid}keyword': data.keyword,
		 '{$actionid}category': data.category,
	         '{$actionid}showchildren': data.showchildren,
		 '{$actionid}when': data.when,
		 '{$actionid}limit': 10
	       },
	       success: function( data ) {
	         if( typeof data.status != 'undefined' && data.status == 0 ) {
		   alert('fetch: '+data.msg);
		 } else {
		   // got 0 or more results
		   if( data.length == 0 ) {
		     $('#find_results_none').show();
		     $('#find_results_area').hide();
		   }
		   else {
		     $('#find_results_none').hide();
		     $('#find_results_area').show();
		     $('#find_results_data').html('');

		     $.each(data, function(idx,row) {
		       // display each of the results of the row.
		       var s_date = moment(row.start);
		       var e_date = null;
		       if( row.end ) e_date = moment(row.end);

		       var out = $('<tr></tr>');
		       out.append('<td><a class="edit_event" href="'+row.edit_url+'" title="{$mod->Lang('edit_event')|cms_escape}">'+row.title+'</a></td>');
		       out.append('<td>' + formatDate(s_date,!row.allDay) + '</td>');
		       if( e_date ) {
		         out.append('<td>' + formatDate(e_date,!row.allDay) + '</td>');
		       } else {
		         out.append('<td></td>');
		       }
	               out.append('<td><a class="goto_date" data-date="'+s_date.toString()+'">{cgimage image='icons/system/view.gif' alt=$mod->Lang('goto_date')}</a></td>');
	               out.append('<td><a class="edit_event" href="'+row.edit_url+'">{cgimage class='edit_event' image='icons/system/edit.gif' alt=$mod->Lang('edit_event')}</a></td>');
		       $('#find_results_data').append(out);
		     });
		   } /* else */
		 }
	       }
	    });

	    // open our results dialog
	    $('#find_results_panel').dialog({
	      width: 'auto'
	    });

	  }
	},
        {
	  text: '{$mod->Lang('cancel')}',
	  click: function() {
	    $(this).dialog('close');
	  }
	}
      ]
    });
  });

  $(document).on('click','a.goto_date',function(){
    closeDialog('#find_results_panel');
    var date = $(this).data('date');
    if( !date ) return false;
    date = new Date(date);
    $('#calendarview').fullCalendar('gotoDate',date);
  })

  $('#page_tabs > div').click(function(){
    $('#calendarview').fullCalendar('render');
    closeDialog('#eventpreview');
    closeDialog('#find_panel');
    closeDialog('#find_results_panel');
  });
});
</script>

<div class="pageoptions" style="margin-bottom: 0.5em">
  <a id="add_event" href="{module_action_link action=admin_add_event urlonly=1}">{cgimage image='icons/system/newobject.gif' alt=$mod->Lang('cal_add_event') class='systemicon'} {$mod->Lang('cal_add_event')}...</a>&nbsp;&nbsp;
  {if $can_manage}
  <a id="import_events" href="{module_action_url action=admin_import}">{cgimage image='icons/system/import.gif' alt=$mod->Lang('cal_import_events') class='systemicon'} {$mod->Lang('cal_import_events')}</a>&nbsp;&nbsp;
  <a id="export_events" href="{module_action_url action=admin_export}">{cgimage image='icons/system/export.gif' alt=$mod->Lang('cal_export_events') class='systemicon'} {$mod->Lang('cal_export_events')}</a>&nbsp;&nbsp;
  <a href="{module_action_url action=admin_remove_expired}">{cgimage image='icons/system/delete.gif' alt=$mod->Lang('cal_delete_expired') class='systemicon'} {$mod->Lang('cal_delete_expired')}</a>&nbsp;&nbsp;
  {/if}
  <a id="find_event">{cgimage image='icons/system/view.gif' alt=$mod->Lang('find')} {$mod->Lang('find')}</a>
  <div style="float: right; text-align: right;">
      {cge_helptag key='eventstab'}
  </div>
</div>
<div class="pageoptions" style="margin-bottom: 0.5em;">
    <label>{$mod->Lang('display')}: <select id="filter">
       {html_options options=$filter_opts}
    </select></label>
</div>
<hr/>

{* this is where the fullcalendar goes *}
<div id="calendarview"></div>

{* other stuff *}

{* the popup dialog when clicking on an event *}
<div id="eventpreview" style="display: none;" title="{$mod->Lang('event_action')}">
  <div class="c_full allday_block">
    <strong>{$mod->Lang('allday_event')}</strong>
  </div>
  <div class="c_full start_block">
    <p class="grid_3 text_right">{$mod->Lang('start')}:</p>
    <p id="event_start" class="grid_9 cf"></p>
  </div>
  <div class="c_full end_block">
    <p class="grid_3 text_right">{$mod->Lang('end')}:</p>
    <p id="event_end" class="grid_9 cf"></p>
  </div>
  <div class="c_full draft_block" style="color: orange;">
    <span>{$mod->Lang('status_draft')}</span>
  </div>
  <div class="c_full child_block">
    <p class="grid_3 text_right" style="color: red;">{$mod->Lang('note')}:</p>
    <p class="grid_9 cf">{$mod->Lang('title_child_event')}</p>
  </div>
  <div class="parent_block">
    <div class="c_full parent_block">
      <p class="grid_3 text_right" style="color: red;">{$mod->Lang('note')}:</p>
      <p class="grid_9 cf">{$mod->Lang('title_parent_event')}</p>
    </div>
  </div>
  <div class="c_full desc_block">
    <p class="grid_3 text_right">{$mod->Lang('desc2')}:</p>
    <p id="event_desc" class="grid_9 cf"></p>
  </div>
</div>

<div id="find_panel" style="display: none;" title="{$mod->Lang('find')}">
  <div class="information">{$mod->Lang('info_find_events')}</div>
  <div class="c_full form_row">
    <p class="grid_3 form_prompt">{$mod->Lang('keywords')}:</p>
    <p class="grid_9 form_input">
      <input type="text" name="keyword" maxlength="255" value=""/>
      {cge_helptag key='find_title'}
    </p>
    <div class="clearb"></div>
  </div>

  {if isset($categories)}
  <div class="c_full">
    <p class="grid_3 form_prompt">{$mod->Lang('category')}:</p>
    <p class="grid_9 form_input">
      <select name="category">
      {html_options options=$categories}
      </select>
    </p>
    <div class="clearb"></div>
  </div>
  {/if}

  <div class="c_full">
    <p class="grid_3 form_prompt">{$mod->Lang('find_showchildren')}:</p>
    <p class="grid_9 form_input">
      {cge_yesno_options name='showchildren'}
      {cge_helptag key='find_showchildren'}
    </p>
    <div class="clearb"></div>
  </div>

  <div class="c_full">
    <p class="grid_3 form_prompt">{$mod->Lang('find_when')}:</p>
    <p class="grid_9 form_input">
      <select name="when">
        <option value="any">{$mod->Lang('any')}</option>
        <option value="past">{$mod->Lang('past')}</option>
        <option value="future">{$mod->Lang('future')}</option>
      </select>&nbsp;{cge_helptag key='hfind_when'}
    </p>
  </div>
</div>

<div id="find_results_panel" style="display: none;" title="{$mod->Lang('results')}...">
  <div id="find_results_none" class="red">{$mod->Lang('noresults')}</div>
  <div id="find_results_area">
    <div class="information">{$mod->Lang('info_find_results')}</div>
    <table class="ui-widget" id="find_results_table">
      <thead class="ui-widget-header">
        <th>{$mod->Lang('cal_title')}</th>
        <th>{$mod->Lang('start')}</th>
        <th>{$mod->Lang('end')}</th>
        <th></th>
        <th></th>
      </thead>
      <tbody id="find_results_data"></tbody>
    </table>
  </div>
</div>

{cge_helphandler}
<div style="display: none;">
  {cge_helpcontent key='eventstab' text=$mod->Lang('help_eventstab')}
  {cge_helpcontent key='find_title' text=$mod->Lang('help_find_event_keyword')}
  {cge_helpcontent key='find_showchildren' text=$mod->Lang('help_find_showchildren')}
  {cge_helpcontent key='hfind_when' text=$mod->Lang('help_find_when')}
</div>
