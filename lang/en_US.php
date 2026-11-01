<?php
# Simple Language file for the Calendar module.
# Copyright (c) 2004 by Rob Allen <rob@akrabat.com>
# Copyright (c) 2005 by Robert Campbell <calguy1000@cmsmadesimple.org>
$lang['dropdownfromudt'] = 'Dropdown from UDT';
$lang['imageattachment'] = 'Image Attachment';
$lang['err_exported_nomatches'] = 'Sorry, we did not find any  events maching your criteria to export';
$lang['export_delimiter'] = 'Field delimiter';
$lang['info_export_events'] = 'This action will generate an ASCII CSV file that contains information about the events';
$lang['export_category'] = 'Export events that are in this category';
$lang['export_datesel'] = 'Export events that were';
$lang['export_datesel_created'] = 'Created on or after';
$lang['export_datesel_modified'] = 'Modified on or after';
$lang['export_datesel_start'] = 'Start on or after';
$lang['export_from_date'] = 'Date';
$lang['cal_export_events'] = 'Export events';
$lang['warn_import'] = '<strong>Warning:</strong> This is a simple import functionality.  There is no checking for duplicate events, knowledge of parent and child relationships,  or updating of existing events.  It is entirely possible that you could generate duplicate events, collisions, and disconnected recurring events.  Use with caution.';
$lang['hdr_pastlist'] = 'Past Events';
$lang['filter_all'] = 'All';
$lang['filter_draft'] = 'Draft';
$lang['filter_published'] = 'Published';
$lang['display'] = 'Display';
$lang['dflt_status_newevents'] = 'Default status for new events';
$lang['desc2'] = 'Description';
$lang['status_draft'] = 'This event is not yet published for frontend display.';
$lang['status'] = 'Status';
$lang['published'] = 'Published';
$lang['draft'] = 'Draft';
$lang['confirm_add_event_here'] = 'Create new event on this date';
$lang['deleted_n'] = 'Deleted %d events';
$lang['msg_noexpiredevents'] = 'There are no expired events to remove';
$lang['cal_delete_expired'] = 'Delete Expired Events';
$lang['prompt_feedit_mastergroup'] = 'FEU Group that is allowed to edit any users events';
$lang['cal_recur_str'] = 'This event recurs %s until %s to a maximum of %d times';
$lang['cal_recur_str2'] = 'This event recurs %s until %s';
$lang['info_about_parent_event'] = 'This event recurs';
$lang['info_about_child_event'] = 'This event is actually a recurrance of another event in our database.  You can view information about the parent event by clicking on the link below.';;
$lang['view_parent_event'] = 'View parent event';
$lang['hdr_daylist'] = 'Events for the day of';
$lang['hdr_yearlist'] = 'Events for the Year';
$lang['hdr_weeklist'] = 'Events for Week %d of %d';
$lang['hdr_upcominglist'] = 'Upcoming Events';
$lang['hdr_monthlist'] = 'Events for the Month of ';
$lang['cal_today'] = 'Today';
$lang['msg_settings_changed'] = 'Settings changed';
$lang['title_adminnav_settings'] = 'CGCalendar Settings';
$lang['desc_adminnav_settings'] = 'Manage Settings for the CGCalendar Module';
$lang['tpltype_searchform'] = 'Search Form';
$lang['tpltype_searchresult'] = 'Search Result View';
$lang['tpltype_fullcalendar'] = 'fullCalendar View';
$lang['tpltype_deleteevent'] = 'Delete Event Form';
$lang['tpltype_editevent'] = 'Edit Event Form';
$lang['tpltype_event'] = 'Event View';
$lang['tpltype_eventlist'] = 'Event List View';
$lang['tpltype_myevents'] = 'MyEvents List View';
$lang['tpltype_CGCalendar'] = 'CGCalendar';
$lang['deleteevent_warn_children'] = '<strong>Warning!</strong>  This event appears to recur on multiple dates.  Deleting this event will delete it, and all of the other recurring dates.  Proceed with caution.';
$lang['deleteevent_warn_child'] = '<strong>Warning!</strong>  This event appears to be the child of a recurring event.  Deleting this object will only delete this recurrance, and not all recurrances of this event.  Proceed with caution.';
$lang['prompt_ddopts'] = 'Dropdown Options';
$lang['info_ddopts'] = 'Some information is required for a dropdown field.  Enter one option per line.  Values can be separated from labels with the = character.  i.e:  f=Female';
$lang['ok'] = 'Ok';
$lang['title_edit_ddopts'] = 'Edit dropdown options';
$lang['dropdown'] = 'Dropdown';
$lang['checkbox'] = 'Checkbox';
$lang['holiday_colors'] = 'Holiday hilite colors';
$lang['prompt_ical_holidays'] = 'ICAL feed for holidays';
$lang['info_ical_holidays'] = 'This is used on the fullcalendar views to hilite public holidays. There is no further interaction with the events in this ical feed.';
$lang['allday'] = 'All Day';
$lang['dayview'] = 'Day View';
$lang['edit'] = 'Edit';
$lang['error_fetching_events'] = 'Oops... something happened when we were talking to the server, please try again';
$lang['noresults'] = 'Sorry, no events matching your criteria were found';
$lang['help_find_when'] = 'Past and future events are always relative to the actual date';
$lang['reorder'] = 'Reorder';
$lang['dflt_colors'] = 'Default Colors';
$lang['edit_category'] = 'Edit category';
$lang['fgcolor'] = 'Foreground Color';
$lang['bgcolor'] = 'Background Color';
$lang['help_find_showchildren'] = 'Indicates if child events should be included in search results?';
$lang['help_find_event_keyword'] = 'Enter one or more keywords to search by.  The event title, summary, and detail fields will be searched for the matching words.';
$lang['info_find_results'] = 'The first 10 matches of your query are displayed below.<br/>Clicking on the event title will allow you to edit the event.<br/>Click on the view icon to navigate the calendar to the approximate date of the event <em>(depending on the view)</em>';
$lang['goto_date'] = 'Go to this date';
$lang['info_find_events'] = 'Search for matching events.  Up to 10 matching events will be returned.';
$lang['results'] = 'Results';
$lang['keywords'] = 'Keywords';
$lang['find_showchildren'] = 'Show Children';
$lang['any'] = 'Any';
$lang['past'] = 'Past';
$lang['future'] = 'Future';
$lang['find_when'] = 'When';
$lang['find_parentsonly'] = 'Parents Only';
$lang['find'] = 'Find...';
$lang['editparent'] = 'Edit parent';
$lang['help_eventstab'] = '<p>The events tab provides a simple way to browse events by date.  The selected date is saved <em>(in a cookie)</em> for two days.  This view also provides simple editing and deletion functionality.</p><br/><p>In the month view dragging an event will change the start day <em>(but not the time).</em>.  Resizing the event will change the events end date.</p><br/><p>In the week, and day views the start date/time of the event can be changed by dragging the event.  The end date/time of the event can be changed by resizing it.  Additionally, these views support the ability to quickly change an event to an all day event by dragging it into the &quot;All Day&quot; area at the top.</p><br/><p>No checks for conflicts are performed when dragging events.</p>';
$lang['prompt_removefromparent'] = 'Remove this event from the parent event';
$lang['info_removefromparent'] = 'If selected, this particular event will not be effected by any changes in the parent event.';
$lang['info_edit_parentevent'] = '<strong>Note:</strong> This event recurs on one or more different dates.  Click the option below update child events with the details of this event.';
$lang['info_edit_childevent'] = '<strong>Note:</strong> This event is the child of another event.  Changes made here will effect only this particular child.  To effect all recurrances you should adjust the parent event.';
$lang['allday_event'] = 'This is an allday event';
$lang['note'] = 'Note';
$lang['title_parent_event'] = 'This event recurs, and contains child events';
$lang['start'] = 'Start';
$lang['end'] = 'End';
$lang['event_action'] = 'Event Action';
$lang['info_event_action'] = 'Select the action to perform on this event.<br/>Editing the event will redirect to a new page.';
$lang['error_deleteparentevent'] = 'Sorry, but you cannot delete a parent event';
$lang['child'] = 'Child';
$lang['repeats'] = 'repeats';
$lang['error_eventnotfound'] = 'Event not found';
$lang['error_unknown'] = 'Unknown error';
$lang['info_cal_filter_by'] = 'When searching <em>(filtering)</em> by words in the title, date scrolling is not available';
$lang['msg_nodates'] = 'Title search applied';
$lang['title_recurring_event'] = 'This is a recurring event with children';
$lang['title_child_event'] = 'This is a recurrance of a parent event';
$lang['error_somedeleted'] = 'Some of the events were deleted.  However, due to permissions some were not';
$lang['error_nonedeleted'] = 'Due to permissions or other errors, none of the selected events were deleted';
$lang['error_deletefailed'] = 'Event deletion failed';
$lang['fileattachment'] = 'File Attachment';
$lang['prompt_general_settings'] = 'General Settings';
$lang['prompt_editing_settings'] = 'Event Editing Settings';
$lang['prompt_feedit_settings'] = 'Frontend Editing Settings';
$lang['prev_month'] = 'Go to the previous month';
$lang['next_month'] = 'Go to the next month';
$lang['info_customfields_tab'] = 'Custom fields can be reordered by dragging and dropping rows.  Only text fields are searchable, and only text areas can have WYSIWYG fields attached. <strong style="color: red;">Note:</strong> WYSIWYG fields will not be shown for textarea fields on the frontend.';
$lang['msg_categories_updated'] = 'Categories updated';
$lang['msg_fieldsupdated'] = 'Field definitions updated';
$lang['wysiwyg'] = 'WYSIWYG';
$lang['info_reorder_categories'] = 'Category order can be defined by dragging and dropping rows';
$lang['error_categorynamerow'] = 'The name has been erased for a valid category at row:';
$lang['confirm_nocategories'] = 'We detected no valid categories.  Are you sure that this is what you want to do?';
$lang['add_field'] = 'Add field';
$lang['add_category'] = 'Add category';
$lang['prompt_firstdayofweek'] = 'First day of the week';
$lang['edit_event'] = 'Edit this event';
$lang['edit_parent_event'] = 'Edit the parent event';
$lang['title_child_event'] = 'This is a child of a parent recurring event';
$lang['select_period'] = 'Period';
$lang['show_filter'] = 'Show filter';
$lang['show_filter_applied'] = 'Show filter <em>(applied)</em>';
$lang['reset'] = 'Reset';
$lang['cal_current'] = 'This period';
$lang['cal_from'] = 'From';
$lang['error_feu_loggedin'] = 'The user is not logged in to FEU.  Adding or editing events is not permitted';
$lang['prompt_friendlyname'] = 'Friendly Name for this Module in Admin Navigation';
$lang['info_friendlyname'] = 'Note: You may need to clear the site cache for this preference to have effect.  If no value is supplied a default will be used.';
$lang['use_wysiwyg'] = 'Use Wysiwyg';
$lang['error_insufficientparams'] = 'Insufficient or invalid parameters';
$lang['column_delimiter'] = 'Import file column delimiter';
$lang['import_completed'] = 'Import Complete';
$lang['num_imported'] = 'Number of events created: %d';
$lang['import_lines'] = 'Number of lines processed from file: %d';
$lang['errors'] = 'Errors';
$lang['error_csvfirstlineinvalidfield'] = 'Field: %s is not recognized, and is not a custom field';
$lang['info_csv_format'] = <<<EOT
<h4>CSV File Format Information.</h4>
<ul>
  <li>General Information:
   <ul>
     <li>The file must be an ASCII file with unix (preferred), dos, or mac line endings.</li>
     <li>Comments start with // any characters after this on a line will be ignored (including within quotes.</li>
     <li>Empty lines are ignored</li>
     <li>Field delimiters are user controllable, however a delimiter can be used as a value within a field if the field is delimited by double quotes.</li>
     <li>Rows are delimited by newline chars (as above).</li>
   </ul>
  </li>
  <li>The Field Map:
   <ul>
    <li>The first line defines the order of the fields, and what &quot;custom fields&quot; are defined in the file. Data can start on the second non-empty line, or after.</li>
    <li>Theres are the required fields: start time, end time, title, summary, and description.  Without defining these fields in the header the import will not proceed.  Required field names are not case sensitive.</li>
    <li>There is one optional column:: &quot;category&quot;.  If specified it can contain a ; separated list of category names that the event will belong to.</li>
   </ul>
  </li>
  <li>The Data Rows:
   <ul>
    <li>The start time, title, and description fields must not be empty.</li>
<li>Start and End Times:</li>
  <ul>
   <li>Start time processing is flexible.  You can specify either  YYYY-MM-DD HH:MM format, or many formats as defined by the strtotime function, including relative times.  If a relative time is supplied it is assumed to be relative to the current time.</li>
   <li>End time processing is flexible, and may be empty.  You can specify either an absolute time (in the same format as the start time) or a relative time.  If a relative time is specified, it is assumed to be relative to the start time.   i.e: &quot;+4 hours&quot; would indicate 4 hours from the start time column.</li>
   <li>If the end time colume is the string &quot;All day&quot; <em>(case insensitive)</em> it will assume an all day event from the start time.</li>
   <li>If the end time column is empty, then no end time value will be created in the database.</li>
  </ul>
</li>
<li>Categories
  <ul>
   <li>If specified, the category column can contain a list (separated by ;) of category names for which the event should belong.</li>
   <li>Category names are case sensitive.</li>
   <li>Categories are not created if they do not exist.</li>
  </ul>
</li>
<li>Custom Fields:
  <ul>
    <li>Custom field names (even in the first line that determines the field mapping) are case sensitive.</li>
    <li>Cutom fields are not created if specified in the CSV file.  Unknown custom field columns will be ignored.</li>
    <li>Custom fields of type &quot;file&quot; cannot be imported.  The entire import operation will fail.</li>
  </ul>
  </li>
  </ul>
</li>
<li>A sample csv file exists in the modules source code directory.</li>
</ul>
EOT;
$lang['prompt_csv_file'] = 'Select Import File';
$lang['delete'] = 'Delete';
$lang['cd_company'] = 'Company Directory Company';
$lang['dflt_urlprefix'] = 'Default URL prefix for Pretty URLS';
$lang['dflt_alldayevent'] = 'New events are &quot;All Day&quot; events by default';
$lang['dflt_starttime'] = 'Default start time for new events';
$lang['info_dflt_starttime'] = '<em>(applies only if new events are not &quot;All Day&quot; events.)</em>';
$lang['search_type'] = 'Search Type';
$lang['search_any'] = 'Return results matching any word';
$lang['search_all'] = 'Return results matching all words';
$lang['searchable'] = 'Searchable';
$lang['help_param_deleteeventtemplate'] = 'Applicable only to the delete_event action, this parameter allows specifying a non default delete event template to use for that action.';
$lang['help_param_editeventtemplate'] = 'Applicable only to the myevents action, this parameter allows specifying a non default edit event template to use when a user creates or edits an event';
$lang['help_param_editpage'] = 'Applicable only to the myevents action, this parameter allows specifying a different page for the edit form.';
$lang['help_param_myeventstemplate'] = 'Applicable only to the myevents action, this parameter allows specifying a non default template to use';
$lang['cal_edit_event'] = 'Edit Calendar Event';
$lang['error_dberror'] = 'Database Error';
$lang['submit'] = 'Submit';
$lang['prompt_feedit_group'] = 'Members of this FEU group can add events';
$lang['prompt_feedit_page'] = 'Default page for the frontend edit form.';
$lang['none'] = 'None';
$lang['owner'] = 'Owner';
$lang['areyousure_removeconflicting'] = 'This action will remove any events that conflict with non-overlappable events.  Are you sure you want to do this?';
$lang['filter_conflicting'] = 'Filter Conflicting Events';
$lang['overlap_action_remove'] = 'Silently remove conflicting events';
$lang['overlap_action_error'] = 'Display an error';
$lang['prompt_overlap_action'] = 'What should happen when a conflict is detected during editing or adding of an event';
$lang['error_event_conflict'] = 'The event values specified overlap with an existing event that does not allow conflicts';
$lang['cal_info_overlap'] = 'Applies only to events with an end time (or all day events)';
$lang['cal_overlap'] = 'Allow new events to overlap with this one';
$lang['prompt_overlap_policy'] = 'Event Overlap Policy';
$lang['policy_all'] = 'All Events can overlap';
$lang['policy_none'] = 'No Events can overlap';
$lang['policy_individual'] = 'Decided on an event by event basis';
$lang['lbl_back'] = 'Back';
$lang['textarea'] = 'Text Area';
$lang['error_calendar_incompatible'] = 'We have detected that the Calendar module is also installed..  This module is not compatible with the Calendar module and cannot co-exist with it on the same install.  Please uninstall the Calendar module';
$lang['error_event_not_found'] = 'Either event_id is not in the database, or there is more than one event with this id! (%d)';
$lang['first_page'] = '&lt;&lt;';
$lang['prev_page'] = '&lt;';
$lang['next_page'] = '&gt;';
$lang['last_page'] = '&gt;&gt;';
$lang['page_of'] = 'Page %d of %d';
$lang['search_results'] = 'Calendar Search Results';
$lang['search_words'] = 'Search Words';
$lang['no_results_found'] = 'No displayable events found for this period.';
$lang['frequency'] = 'Frequency';
$lang['specified_date'] = 'Use the specified date';
$lang['first_sunday'] = 'First Sunday of the Month';
$lang['first_monday'] = 'First Monday of the Month';
$lang['first_tuesday'] = 'First Tuesday of the Month';
$lang['first_wednesday'] = 'First Wednesday of the Month';
$lang['first_thursday'] = 'First Thursday of the Month';
$lang['first_friday'] = 'First Friday of the Month';
$lang['first_saturday'] = 'First Saturday of the Month';
$lang['second_sunday'] = 'Second Sunday of the Month';
$lang['second_monday'] = 'Second Monday of the Month';
$lang['second_tuesday'] = 'Second Tuesday of the Month';
$lang['second_wednesday'] = 'Second Wednesday of the Month';
$lang['second_thursday'] = 'Second Thursday of the Month';
$lang['second_friday'] = 'Second Friday of the Month';
$lang['second_saturday'] = 'Second Saturday of the Month';
$lang['third_sunday'] = 'Third Sunday of the Month';
$lang['third_monday'] = 'Third Monday of the Month';
$lang['third_tuesday'] = 'Third Tuesday of the Month';
$lang['third_wednesday'] = 'Third Wednesday of the Month';
$lang['third_thursday'] = 'Third Thursday of the Month';
$lang['third_friday'] = 'Third Friday of the Month';
$lang['third_saturday'] = 'Third Saturday of the Month';
$lang['fourth_sunday'] = 'Fourth Sunday of the Month';
$lang['fourth_monday'] = 'Fourth Monday of the Month';
$lang['fourth_tuesday'] = 'Fourth Tuesday of the Month';
$lang['fourth_wednesday'] = 'Fourth Wednesday of the Month';
$lang['fourth_thursday'] = 'Fourth Thursday of the Month';
$lang['fourth_friday'] = 'Fourth Friday of the Month';
$lang['fourth_saturday'] = 'Fourth Saturday of the Month';
$lang['last_sunday'] = 'Last Sunday of the Month';
$lang['last_monday'] = 'Last Monday of the Month';
$lang['last_tuesday'] = 'Last Tuesday of the Month';
$lang['last_wednesday'] = 'Last Wednesday of the Month';
$lang['last_thursday'] = 'Last Thursday of the Month';
$lang['last_friday'] = 'Last Friday of the Month';
$lang['last_saturday'] = 'Last Saturday of the Month';

$lang['error_invalid_recur_monthly_freq'] = 'Invalid frequency.... you cannot specify both the specified date AND other values';
$lang['error_query_failed'] = 'Database Query Failed';
$lang['error_search_invalid_dates'] = 'Invalid Search Dates Specified';
$lang['text'] = 'Text';
$lang['search'] = 'Search';
$lang['category'] = 'Category';
$lang['max'] = 'max';
$lang['times'] = 'times';
$lang['on'] = 'on';
$lang['areyousure_uninstall'] = 'Are you sure you want to do this?  Continuing will permanenently erase all data associated with this module.';
$lang['post_uninstall'] = 'The Calendar module has been uninstalled and all event and template information associated with this module has been removed';
$lang['fieldupdated'] = 'Field Updated';
$lang['every'] = 'Every';
$lang['or'] = 'or';
$lang['update_children'] = 'Update Child Events';
$lang['error_nocategory'] = 'You must select at least one category';
$lang['error_nothingselected'] = 'Nothing has been selected to delete';
$lang['error_eventoverlap'] = 'The recuring event parameters you have specified would result in overlapping events.';
$lang['recurs'] = 'Recurs';
$lang['error_invalid_dates'] = 'One or more of the date values entered were invalid';
$lang['sunday'] = 'Sunday';
$lang['monday'] = 'Monday';
$lang['tuesday'] = 'Tuesday';
$lang['wednesday'] = 'Wednesday';
$lang['thursday'] = 'Thursday';
$lang['friday'] = 'Friday';
$lang['saturday'] = 'Saturday';

$lang['abbr_sunday'] = 'Su';
$lang['abbr_monday'] = 'Mo';
$lang['abbr_tuesday'] = 'Tu';
$lang['abbr_wednesday'] = 'We';
$lang['abbr_thursday'] = 'Th';
$lang['abbr_friday'] = 'Fr';
$lang['abbr_saturday'] = 'Sa';

$lang['week'] = 'Week';
$lang['month'] = 'Month';
$lang['day'] = 'Day';
$lang['today'] = 'Today';
$lang['weekdays'] = 'Weekdays';
$lang['plural_none'] = 'None';
$lang['plural_daily'] = 'Days';
$lang['plural_weekly'] = 'Weeks';
$lang['plural_monthly'] = 'Months';
$lang['plural_yearly'] = 'Years';
$lang['no'] = 'No';
$lang['yes'] = 'Yes';
$lang['daily'] = 'Daily';
$lang['weekly'] = 'Weekly';
$lang['monthly'] = 'Monthly';
$lang['yearly'] = 'Yearly';
$lang['interval'] = 'Repeat every';
$lang['cal_recur_details'] = 'Recurring Event Details';
$lang['unlimited'] = 'Unlimited';
$lang['max_recur_events'] = 'Maximum number of child events';
$lang['use_to_date'] = 'Specify a different end date/time?';
$lang['cancel'] = 'Cancel';
$lang['show_child_events'] = 'Show Child Events <em>(may result in very long display)</em>';
$lang['n/a'] = 'n/a';
$lang['recurs_until'] = 'Recur Until';
$lang['parent'] = 'Parent';
$lang['recur_period'] = 'Recur Period';
$lang['filter'] = 'Filter';
$lang['start_date'] = 'Start Date';
$lang['end_date'] = 'End Date';

# Language: en_US
$lang['eventdesc-EventAdded'] = 'Sent after adding a calendar event';
$lang['eventdesc-EventEdited'] = 'Sent after editing an existing calendar event';
$lang['eventdesc-EventEditedPre'] = 'Sent prior to editing an existing calendar event';
$lang['eventdesc-EventDeleted'] = 'Sent after an event has been deleted';
$lang['eventdesc-CategoryAdded'] = 'Sent after a category is created';
$lang['eventdesc-CategoryEdited'] = 'Sent after a category is edited';
$lang['eventdesc-CategoryDeleted'] = 'Sent after a category is deleted';

$lang['eventhelp-EventAdded'] = '
<p>Sent when a calendar event is added.</p>
<h4>Parameters</h4>
<ul>
<li>\"event\" - (array) The entire event record</li>
<li>\"event_title\" - Event Title</li>
<li>\"event_summary\" - Summary Text</li>
<li>\"event_details\" - Detailed Description</li>
<li>\"event_date_start\" - The event start date/time</li>
<li>\"event_date_end\" - The event end date/time</li>
<li>\"event_created_by\" - The userid of the author</li>
<li>\"event_id\" - The event id</li>
</ul>
';
$lang['eventhelp-EventEditedPre'] = '
<p>Sent prior to an existing event being edited.</p>
<h4>Parameters:</h4>
<ul>
<li>\"event\" - (array) The entire event record</li>
<li>\"event_id\" - The event id</li>
</ul>
';
$lang['eventhelp-EventEdited'] = '
<p>Sent when a calendar event is edited.</p>
<h4>Parameters</h4>
<ul>
<li>\"event\" - (array) The entire event record</li>
<li>\"event_title\" - Event Title</li>
<li>\"event_summary\" - Summary Text</li>
<li>\"event_details\" - Detailed Description</li>
<li>\"event_date_start\" - The event start date/time</li>
<li>\"event_date_end\" - The event end date/time</li>
<li>\"event_created_by\" - The userid of the author</li>
<li>\"event_id\" - The event id</li>
</ul>
';
$lang['eventhelp-EventDeleted'] = '
<p>Sent when a calendar event is deleted.</p>
<h4>Parameters</h4>
<ul>
<li>\"event_id\" - The event id</li>
</ul>
';
$lang['eventhelp-CategoryAdded'] = '
<p>Sent when a calendar category is added.</p>
<h4>Parameters</h4>
<ul>
<li>\"category_id\" - The category id</li>
</ul>
';
$lang['eventhelp-CategoryEdited'] = '
<p>Sent when a calendar category is edited.</p>
<h4>Parameters</h4>
<ul>
<li>\"category_id\" - The category id</li>
<li>\"category_name\" - The category name</li>
<li>\"category_order\" - The category sort order</li>
</ul>
';
$lang['eventhelp-CategoryDeleted'] = '
<p>Sent when a calendar category is deleted.</p>
<h4>Parameters</h4>
<ul>
<li>\"category_id\" - The category id</li>
<li>\"category_name\" - The category name</li>
<li>\"category_order\" - The category sort order</li>
</ul>
';

$lang['error_csv_insert'] = 'Error importing event from csv at line %d';
$lang['error_csvfirstlinefldnotfound'] = 'A required field: %s, could not be found in the csv file header line';
$lang['error_csvinvalidfirstline'] = 'Could not find a valid first line of the CSV file';
$lang['msg_eventadded'] = 'Event Added';
$lang['error_noeventname'] = 'A title for the event is required';
$lang['error_noupload'] = 'ERROR: No uploaded file found';
$lang['error_invalidfilename'] = 'ERROR: The file uploaded cannot be accepted';
$lang['error_problemwithupload'] = 'ERROR: A problem occurred trying to upload the file';
$lang['error_filecopyfailed'] = 'ERROR: A Problem occurred when copying the file to its final destination';
$lang['error_fileexists'] = 'ERROR: A file by that name already exists';
$lang['error_csvfilenotfound'] = 'ERROR: Could not find the CSV File';
$lang['error_cantopenfile'] = 'ERROR: Cannot open file';
$lang['error_categoryexists'] = 'ERROR: A category with the name %s already exists';

$lang['cal_id'] = 'Id';
$lang['cal_calendar'] = 'Calguys Calendar';
$lang['cal_description'] = 'Add, edit and remove events';
$lang['cal_addevent'] = 'Add event';
$lang['cal_import_events'] = 'Import events';
$lang['cal_events'] = 'Events';
$lang['cal_categories'] = 'Categories';
$lang['cal_settings'] = 'Settings';
$lang['cal_prev'] = '&laquo; Prev';
$lang['cal_next'] = 'Next &raquo;';

$lang['cal_categories_updated'] = 'Categories updated';
$lang['cal_settings_updated'] = 'Settings updated';
$lang['cal_add_event'] = 'Add event';
$lang['cal_edit'] = 'Edit this event';
$lang['cal_delete'] = 'Delete this event';
$lang['cal_areyousure'] = 'Are you sure you want to delete this item?';

$lang['cal_sunday'] = 'Sunday';
$lang['cal_monday'] = 'Monday';
$lang['cal_tuesday'] = 'Tuesday';
$lang['cal_wednesday'] = 'Wednesday';
$lang['cal_thursday'] = 'Thursday';
$lang['cal_friday'] = 'Friday';
$lang['cal_saturday'] = 'Saturday';
$lang['cal_sun'] = 'Sun';
$lang['cal_mon'] = 'Mon';
$lang['cal_tues'] = 'Tue';
$lang['cal_wed'] = 'Wed';
$lang['cal_thurs'] = 'Thu';
$lang['cal_fri'] = 'Fri';
$lang['cal_sat'] = 'Sat';

$lang['cal_january'] = 'January';
$lang['cal_february'] = 'February';
$lang['cal_march'] = 'March';
$lang['cal_april'] = 'April';
$lang['cal_may'] = 'May';
$lang['cal_june'] = 'June';
$lang['cal_july'] = 'July';
$lang['cal_august'] = 'August';
$lang['cal_september'] = 'September';
$lang['cal_october'] = 'October';
$lang['cal_november'] = 'November';
$lang['cal_december'] = 'December';

$lang['cal_add'] = 'Add';
$lang['cal_update'] = 'Update';
$lang['cal_event'] = 'Event';
$lang['cal_date'] = 'Date';
$lang['cal_summary'] = 'Summary';
$lang['cal_details'] = 'Details';
$lang['cal_more'] = 'more >>';
$lang['cal_return'] = 'Return';
$lang['cal_to'] = 'to';
$lang['cal_past_events'] = 'Past events';
$lang['cal_upcoming_events'] = 'Upcoming events';
$lang['cal_any_category'] = 'Any category';
$lang['cal_show_only_events_in'] = 'Show only events in';
$lang['cal_filter_by'] = 'Search title <em>(regex)</em>';
$lang['cal_go'] = 'Go';
$lang['cal_title'] = 'Title';
$lang['cal_fromdate'] = 'Start Date';
$lang['cal_todate'] = 'To Date';
$lang['cal_summary'] = 'Summary';
$lang['cal_description'] = 'A full featured, and flexible module to allow displaying information about events in numerous formats.';
$lang['cal_update_categories'] = 'Update Categories';
$lang['cal_language'] = 'Language';
$lang['cal_updatesettings'] = 'Update Settings';
$lang['cal_use_twelve_hour_clock'] = 'Use twelve hour clock on hour drop-downs?';
$lang['cal_default_category'] = 'Default Category';
$lang['cal_update_fields'] = 'Update Fields';
$lang['force_category']='Force at least one category';
$lang['showpastyears']='How many years in the past is allowed';
$lang['showfutureyears']='How many year into the future is allowed';
$lang['hidesummary']='Should the summary field be hidden';
$lang['hidecontent']='Should the content field be hidden';

$lang['category_reminder']='Please check one or more categories for this event';
$lang["module_example_stylesheet"]="Calguys Calendar CSS example";

$lang['error_permission'] = 'You do not have the appropriate permission to access this functionality (or you may not own the event you are trying to modify)';
$lang['install_postmessage'] = 'Make sure to set the "Modify Calendar" permission on users who will be administering calendar events.';

$lang["deletetagged"]="Delete tagged events";
$lang["confirmdeletetagged"]="Are you sure these events should be deleted?";
$lang["taggeddeleted"]="Tagged events successfully deleted";
$lang['templates'] = 'Templates';
$lang['template'] = 'Template';
$lang['view_default_stylesheet'] = 'View Default Stylesheet';
$lang['filename'] = 'Filename';
$lang["settingssaved"]="Settings were saved successfully";
$lang["categorydeleted"]="Category deleted";
$lang["categoryupdated"]="Category updated";
$lang["categoryadded"]="Category added";
$lang["eventdeleted"]="Event(s) deleted successfully";
$lang["eventupdated"]="The event was updated";
$lang['default_page_error'] = 'Error: You must set a default page in order to use Calendar with pretty URLs turned on. Please go to the Calendar settings tab and choose one.';
$lang["time_at"]="at";
$lang['type']='Type';
$lang["name"]="Name";
$lang["order"]="Order";

$lang['fieldadded'] = 'Field Successfully Added';
$lang['fielddeleted'] = 'Field Deleted';
$lang['fields'] = 'Custom Fields';
$lang['textfield'] = 'Text Field';
$lang['uploadfield'] = 'File upload field';
$lang["description"]="CGCalendar is a module for displaying and managing events on your website";
$lang['defaultcalendarpage'] = 'Default event view page';
$lang['uploaddirectory'] = 'Directory where file uploads should be placed';
$lang['uploadfiletypes'] = 'Allowed file types';
$lang['uploadunique'] = 'Ensure unique filenames';

$lang['cal_recur_period'] = 'Recurs';
$lang['cal_recur_until'] = 'Recurs Until';


$lang['help_template'] = '
<p><b>Print Author Name:</b></p>
<code>Author: {$event.authorname}</code>
';
?>
