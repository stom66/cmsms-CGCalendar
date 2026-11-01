<?php
$lang['error_feu_loggedin'] = 'The user is not logged in to FEU.  Adding or editing events is not permitted';
$lang['prompt_friendlyname'] = 'Friendly Name for this Module in Admin Navigation';
$lang['info_friendlyname'] = 'Note: You may need to clear the site cache for this preference to have effect.  If no value is supplied a default will be used.';
$lang['use_wysiwyg'] = 'Wysiwyg Edit&ouml;r&uuml; Kullan';
$lang['error_insufficientparams'] = 'Insufficient Parameters';
$lang['column_delimiter'] = 'Import file column delimiter';
$lang['import_completed'] = 'Import Complete';
$lang['num_imported'] = 'Number of events created: %d';
$lang['import_lines'] = 'Number of lines processed from file: %d';
$lang['errors'] = 'Hatalar';
$lang['error_csvfirstlineinvalidfield'] = 'Field: %s is not recognized, and is not a custom field';
$lang['info_csv_format'] = '<h4>CSV File Format Information.</h4>
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
</ul>';
$lang['prompt_csv_file'] = 'Select Import File';
$lang['delete'] = 'Sil';
$lang['cd_company'] = 'Company Directory Company';
$lang['dflt_urlprefix'] = 'Default URL prefix for Pretty URLS';
$lang['dflt_alldayevent'] = 'New events are &quot;All Day&quot; events by default';
$lang['dflt_starttime'] = 'Default start time for new events';
$lang['info_dflt_starttime'] = '<em>(applies only if new events are not &quot;All Day&quot; events.)</em>';
$lang['search_type'] = 'Search Type';
$lang['search_any'] = 'Return results matching any word';
$lang['search_all'] = 'Return results matching all words';
$lang['info_customfields'] = '<span style=&quot;color: red;&quot;>Note:</span> Only text fields are searchable.  Clicking the chechbox on other field types will have no effect.';
$lang['searchable'] = 'Aranabilir';
$lang['help_param_editeventtemplate'] = 'Applicable only to the myevents action, this parameter allows specifying a non default edit event template to use when a user creates or edits an event';
$lang['help_param_editpage'] = 'Applicable only to the myevents action, this parameter allows specifying a different page for the edit form.';
$lang['help_param_myeventstemplate'] = 'Applicable only to the myevents action, this parameter allows specifying a non default template to use';
$lang['cal_edit_event'] = 'Takvim Etkinliğini D&uuml;zenle';
$lang['error_dberror'] = 'Database Error';
$lang['submit'] = 'G&ouml;nder';
$lang['info_sysdflt_editevent_template'] = 'System Default &quot;Edit Event&quot; Template';
$lang['info_sysdflt_myevents_template'] = 'System Default &quot;My Events&quot; Template';
$lang['prompt_feedit_group'] = 'Members of this FEU group can add events';
$lang['prompt_feedit_page'] = 'Default page for the frontend edit form.';
$lang['none'] = 'None';
$lang['owner'] = 'Sahip';
$lang['cal_editevent_templates'] = 'Edit Event Form Templates';
$lang['info_editevent_template_tab'] = 'The list of templates that are available for the edit event form';
$lang['editeventtemplate_addedit'] = 'Add/Edit an Edit Event Form Template';
$lang['cal_myevents_templates'] = 'My Events List Templates';
$lang['info_myevents_template_tab'] = 'The list of templates that are available to display a list of user events';
$lang['myeventstemplate_addedit'] = 'Add/Edit a &quot;My Events&quot; report template';
$lang['areyousure_removeconflicting'] = 'This action will remove any events that conflict with non-overlappable events.  Are you sure you want to do this?';
$lang['filter_conflicting'] = '&Ccedil;akışanları Filtrele';
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
$lang['lbl_back'] = 'Geri';
$lang['lbl_templates'] = 'Templates';
$lang['textarea'] = 'Text Area';
$lang['error_calendar_incompatible'] = 'We have detected that the Calendar module is also installed..  This module is not compatible with the Calendar module and cannot co-exist with it on the same install.  Please uninstall the Calendar module';
$lang['error_event_not_found'] = 'Either event_id is not in the database, or there is more than one event with this id! (%d)';
$lang['first_page'] = '<<';
$lang['prev_page'] = '<';
$lang['next_page'] = '>';
$lang['last_page'] = '>>';
$lang['page_of'] = 'Page %d of %d';
$lang['search_results'] = 'Calendar Search Results';
$lang['search_words'] = 'Search Words';
$lang['no_results_found'] = 'No results found matching the query specified';
$lang['frequency'] = 'Frekans';
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
$lang['searchresulttemplate_addedit'] = 'Add/Edit a Search Result Template';
$lang['info_searchresult_template_tab'] = 'The list of templates that are available for the search results display';
$lang['info_sysdflt_searchresult_template'] = 'System Default Search Result Template';
$lang['cal_search_result_templates'] = 'Search Result Templates';
$lang['error_query_failed'] = 'Database Query Failed';
$lang['error_search_invalid_dates'] = 'Invalid Search Dates Specified';
$lang['text'] = 'Text';
$lang['search'] = 'Arama';
$lang['category'] = 'Kategori';
$lang['info_sysdflt_search_template'] = 'System Default Search Form Template';
$lang['searchtemplate_addedit'] = 'Add/Edit a Search Form Template';
$lang['info_search_template_tab'] = 'The list of templates that are available for the search form';
$lang['cal_search_form_templates'] = 'Search Form Templates';
$lang['max'] = 'max';
$lang['times'] = 'kere';
$lang['on'] = 'on';
$lang['areyousure_uninstall'] = 'Are you sure you want to do this?  Continuing will permanenently erase all data associated with this module.';
$lang['post_uninstall'] = 'The Calendar module has been uninstalled and all event and template information associated with this module has been removed';
$lang['fieldupdated'] = 'Field Updated';
$lang['every'] = 'Her';
$lang['or'] = 'yada';
$lang['update_children'] = 'Update Child Events';
$lang['error_nocategory'] = 'You must select at least one category';
$lang['error_nothingselected'] = 'Nothing has been selected to delete';
$lang['error_eventoverlap'] = 'The recuring event parameters you have specified would result in overlapping events.';
$lang['recurs'] = 'Yineleme';
$lang['error_invalid_dates'] = 'One or more of the date values entered were invalid';
$lang['sunday'] = 'Pazar';
$lang['monday'] = 'Pazartesi';
$lang['tuesday'] = 'Salı';
$lang['wednesday'] = '&Ccedil;arşamba';
$lang['thursday'] = 'Perşembme';
$lang['friday'] = 'Cuma';
$lang['saturday'] = 'Cumartesi';
$lang['abbr_sunday'] = 'Pz';
$lang['abbr_monday'] = 'Pt';
$lang['abbr_tuesday'] = 'Sa';
$lang['abbr_wednesday'] = '&Ccedil;a';
$lang['abbr_thursday'] = 'Pe';
$lang['abbr_friday'] = 'Cu';
$lang['abbr_saturday'] = 'Ct';
$lang['weekdays'] = 'Hafta i&ccedil;i';
$lang['plural_daily'] = 'G&uuml;nler';
$lang['plural_weekly'] = 'Haftalar';
$lang['plural_monthly'] = 'Aylar';
$lang['plural_yearly'] = 'Yıllar';
$lang['no'] = 'Hayır';
$lang['daily'] = 'G&uuml;nl&uuml;k';
$lang['weekly'] = 'Haftalık';
$lang['monthly'] = 'Aylık';
$lang['yearly'] = 'Yıllık';
$lang['interval'] = 'Tekrarla';
$lang['cal_recur_details'] = 'Yinelenen Etkinlik Detayları';
$lang['unlimited'] = 'Limitsiz';
$lang['max_recur_events'] = 'Maximum number of child events';
$lang['use_to_date'] = 'Bitiş g&uuml;n&uuml; belirleyin';
$lang['cancel'] = 'Vazge&ccedil;';
$lang['show_child_events'] = 'Show Child Events <em>(may result in very long display)</em>';
$lang['n/a'] = 'n/a';
$lang['recurs_until'] = 'Recur Until';
$lang['parent'] = '&Uuml;st';
$lang['recur_period'] = 'Yineleme Periyodu';
$lang['filter'] = 'Filtre';
$lang['start_date'] = 'Başlama Tarihi';
$lang['end_date'] = 'Bitiş Tarihi';
$lang['info_upcominglist_template_tab'] = 'The list of templates that are available for upcominglist views';
$lang['upcominglisttemplate_addedit'] = 'Add/Edit an Upcominglist Display Template';
$lang['info_sysdflt_upcominglist_template'] = 'System Default Upcominglist Template';
$lang['info_list_template_tab'] = 'The list of templates that are available for list views';
$lang['listtemplate_addedit'] = 'Add/Edit an List Display Template';
$lang['info_sysdflt_list_template'] = 'System Default List Template';
$lang['info_event_template_tab'] = 'The list of templates that are available for event views';
$lang['eventtemplate_addedit'] = 'Add/Edit an Event Display Template';
$lang['info_sysdflt_event_template'] = 'System Default Event Template';
$lang['info_calendar_template_tab'] = 'The list of templates that are available for calendar views';
$lang['calendartemplate_addedit'] = 'Add/Edit a Calendar Display Template';
$lang['info_sysdflt_calendar_template'] = 'System Default Calendar Template';
$lang['info_sysdflt_template'] = 'This form defines the template that will be used when you click &quot;New Template&quot; in the appropriate tab.  Changing this value will have no immediate effect on the displays generated from this module.';
$lang['eventdesc-EventAdded'] = 'Called after adding a calendar event';
$lang['eventdesc-EventEdited'] = 'Called after editing an existing calendar event';
$lang['eventdesc-EventDeleted'] = 'Called after an event has been deleted';
$lang['eventdesc-CategoryAdded'] = 'Called after a category is created';
$lang['eventdesc-CategoryEdited'] = 'Called after a category is edited';
$lang['eventdesc-CategoryDeleted'] = 'Called after a category is deleted';
$lang['eventhelp-EventAdded'] = '<p>Sent when a calendar event is added.</p>
<h4>Parameters</h4>
<ul>
<li>\&quot;event_title\&quot; - Event Title</li>
<li>\&quot;event_summary\&quot; - Summary Text</li>
<li>\&quot;event_details\&quot; - Detailed Description</li>
<li>\&quot;event_date_start\&quot; - The event start date/time</li>
<li>\&quot;event_date_end\&quot; - The event end date/time</li>
<li>\&quot;event_created_by\&quot; - The userid of the author</li>
<li>\&quot;event_id\&quot; - The event id</li>
</ul>
';
$lang['eventhelp-EventEdited'] = '<p>Sent when a calendar event is edited.</p>
<h4>Parameters</h4>
<ul>
<li>\&quot;event_title\&quot; - Event Title</li>
<li>\&quot;event_summary\&quot; - Summary Text</li>
<li>\&quot;event_details\&quot; - Detailed Description</li>
<li>\&quot;event_date_start\&quot; - The event start date/time</li>
<li>\&quot;event_date_end\&quot; - The event end date/time</li>
<li>\&quot;event_created_by\&quot; - The userid of the author</li>
<li>\&quot;event_id\&quot; - The event id</li>
</ul>
';
$lang['eventhelp-EventDeleted'] = '<p>Sent when a calendar event is deleted.</p>
<h4>Parameters</h4>
<ul>
<li>\&quot;event_id\&quot; - The event id</li>
</ul>
';
$lang['eventhelp-CategoryAdded'] = '<p>Sent when a calendar category is added.</p>
<h4>Parameters</h4>
<ul>
<li>\&quot;category_id\&quot; - The category id</li>
</ul>
';
$lang['eventhelp-CategoryEdited'] = '<p>Sent when a calendar category is edited.</p>
<h4>Parameters</h4>
<ul>
<li>\&quot;category_id\&quot; - The category id</li>
<li>\&quot;category_name\&quot; - The category name</li>
<li>\&quot;category_order\&quot; - The category sort order</li>
</ul>
';
$lang['eventhelp-CategoryDeleted'] = '<p>Sent when a calendar category is deleted.</p>
<h4>Parameters</h4>
<ul>
<li>\&quot;category_id\&quot; - The category id</li>
<li>\&quot;category_name\&quot; - The category name</li>
<li>\&quot;category_order\&quot; - The category sort order</li>
</ul>
';
$lang['error_csv_insert'] = 'Error importing event from csv at line %d';
$lang['error_csvfirstlinefldnotfound'] = 'A required field: %s, could not be found in the csv file header line';
$lang['error_csvinvalidfirstline'] = 'Could not find a valid first line of the CSV file';
$lang['msg_eventadded'] = 'Event Added';
$lang['error_noeventname'] = 'A name for the event is required';
$lang['error_noupload'] = 'ERROR: No uploaded file found';
$lang['error_invalidfilename'] = 'ERROR: The file uploaded cannot be accepted';
$lang['error_problemwithupload'] = 'ERROR: A problem occurred trying to upload the file';
$lang['error_filecopyfailed'] = 'ERROR: A Problem occurred when copying the file to its final destination';
$lang['error_fileexists'] = 'ERROR: A file by that name already exists';
$lang['error_csvfilenotfound'] = 'ERROR: Could not find the CSV File';
$lang['error_cantopenfile'] = 'ERROR: Cannot open file';
$lang['error_categoryexists'] = 'ERROR: A category with the name %s already exists';
$lang['cal_id'] = 'Id';
$lang['cal_calendar'] = 'Takvim';
$lang['cal_default_templates'] = 'Default Templates';
$lang['cal_description'] = 'A&ccedil;ıklama';
$lang['cal_addevent'] = 'Etkinlik Ekle';
$lang['cal_import_events'] = 'Import Et';
$lang['cal_events'] = 'Etkinlikler';
$lang['cal_categories'] = 'Kategoriler';
$lang['cal_calendar_template'] = 'Calendar Templates';
$lang['cal_list_template'] = 'List Templates';
$lang['cal_upcominglist_template'] = 'Upcoming Templates';
$lang['cal_event_template'] = 'Event Templates';
$lang['cal_settings'] = 'Ayarlar';
$lang['cal_prev'] = '&laquo; &Ouml;nceki';
$lang['cal_next'] = 'Sonraki &raquo;';
$lang['cal_categories_updated'] = 'Categories Updated';
$lang['cal_settings_updated'] = 'Settings Updated';
$lang['cal_add_event'] = 'Etkinlik Ekle';
$lang['cal_edit'] = 'D&uuml;zenle';
$lang['cal_delete'] = 'Sil';
$lang['cal_areyousure'] = 'Silmek istediğinize emin misiniz?';
$lang['cal_update_template'] = 'Update Template';
$lang['cal_sunday'] = 'Pazar';
$lang['cal_monday'] = 'Pazartesi';
$lang['cal_tuesday'] = 'Salı';
$lang['cal_wednesday'] = '&Ccedil;arşamba';
$lang['cal_thursday'] = 'Perşembe';
$lang['cal_friday'] = 'Cuma';
$lang['cal_saturday'] = 'Cumartesi';
$lang['cal_sun'] = 'Paz';
$lang['cal_mon'] = 'Pts';
$lang['cal_tues'] = 'Sal';
$lang['cal_wed'] = '&Ccedil;ar';
$lang['cal_thurs'] = 'Per';
$lang['cal_fri'] = 'Cum';
$lang['cal_sat'] = 'Cts';
$lang['cal_january'] = 'Ocak';
$lang['cal_february'] = 'Şubat';
$lang['cal_march'] = 'Mart';
$lang['cal_april'] = 'Nisan';
$lang['cal_may'] = 'Mayıs';
$lang['cal_june'] = 'Haziran';
$lang['cal_july'] = 'Temmuz';
$lang['cal_august'] = 'Ağustos';
$lang['cal_september'] = 'Eyl&uuml;l';
$lang['cal_october'] = 'Ekim';
$lang['cal_november'] = 'Kasım';
$lang['cal_december'] = 'Aralık';
$lang['cal_add'] = 'Ekle';
$lang['cal_update'] = 'G&uuml;ncelle';
$lang['cal_event'] = 'Etkinlik';
$lang['cal_date'] = 'Tarih';
$lang['cal_summary'] = '&Ouml;zet';
$lang['cal_details'] = 'Ayrıntılar';
$lang['cal_more'] = 'devamı >>';
$lang['cal_return'] = 'Geri d&ouml;n';
$lang['cal_to'] = 'to';
$lang['cal_past_events'] = 'Ge&ccedil;miş Etkinlikler';
$lang['cal_upcoming_events'] = 'Yaklaşan Etkinlikler';
$lang['cal_any_category'] = 'Herhangi bir kategori';
$lang['cal_show_only_events_in'] = 'Show Only Events In';
$lang['cal_filter_by'] = 'Filter Title By <em>(regex)</em>';
$lang['cal_go'] = 'Git';
$lang['cal_title'] = 'Başlık';
$lang['cal_fromdate'] = 'Başlama Tarihi';
$lang['cal_todate'] = 'Bitiş Tarihi';
$lang['cal_update_categories'] = 'Kategorileri g&uuml;ncelle';
$lang['cal_language'] = 'Dil';
$lang['cal_updatesettings'] = 'Ayarları G&uuml;ncelle';
$lang['cal_use_twelve_hour_clock'] = 'Use twelve hour clock on hour drop-downs?';
$lang['cal_default_category'] = 'Varsayılan Kategori';
$lang['cal_update_fields'] = 'Update Fields';
$lang['force_category'] = 'Force at least one category';
$lang['showpastyears'] = 'How many years in the past is allowed';
$lang['showfutureyears'] = 'How many year into the future is allowed';
$lang['hidesummary'] = 'Should the summary field be hidden';
$lang['hidecontent'] = 'Should the content field be hidden';
$lang['category_reminder'] = 'Please check one or more categories for this event';
$lang['module_example_stylesheet'] = 'Calendar CSS example';
$lang['error_permission'] = 'You need the appropriate permission (%s) to access this functionality';
$lang['install_postmessage'] = 'Make sure to set the &quot;Modify Calendar&quot; permission on users who will be administering calendar events.';
$lang['deletetagged'] = 'Delete tagged events';
$lang['confirmdeletetagged'] = 'Are you sure these events should be deleted?';
$lang['taggeddeleted'] = 'Tagged events successfully deleted';
$lang['templates'] = 'Templates';
$lang['template_help'] = 'Template Help';
$lang['file_templates'] = 'File Templates';
$lang['addtemplate'] = 'Add Template';
$lang['template'] = 'Template';
$lang['templatenametext'] = 'Template Name';
$lang['edittemplate'] = 'Edit Template';
$lang['deletetemplate'] = 'Delete Template';
$lang['newtemplate'] = 'New Template Name';
$lang['templatenameexists'] = 'Error: A template with that name already exists. Please choose a different name.';
$lang['templateimported'] = 'The template was sucessfully imported into the database and is now installed for use.';
$lang['view_default_stylesheet'] = 'View Default Stylesheet';
$lang['filename'] = 'Filename';
$lang['importtemplate'] = 'Import Template';
$lang['template_deleted'] = 'The template was successfully deleted from the database.';
$lang['updatetemplatesuccess'] = 'The template was updated successfully';
$lang['updatetemplatefailure'] = 'An error occurred updating the template';
$lang['settingssaved'] = 'Settings was saved successfully';
$lang['categorydeleted'] = 'Category deleted';
$lang['categoryupdated'] = 'Category updated';
$lang['categoryadded'] = 'Category added';
$lang['eventdeleted'] = 'The event was deleted';
$lang['eventupdated'] = 'The event was updated';
$lang['default_page_error'] = 'Error: You must set a default page in order to use Calendar with pretty URLs turned on. Please go to the Calendar settings tab and choose one.';
$lang['time_at'] = 'at';
$lang['type'] = 'Type';
$lang['name'] = 'İsim';
$lang['order'] = 'Sıralama';
$lang['fieldadded'] = 'Field Successfully Added';
$lang['fielddeleted'] = 'Field Deleted';
$lang['fields'] = 'Ekstra Elanlar';
$lang['textfield'] = 'Text Field';
$lang['uploadfield'] = 'File upload field';
$lang['description'] = '<p>Calendar is a module for displaying events on your page. When the
		module is installed, a Calendar admin page is added to the content menu
		that will allow you to manage your events.</p>';
$lang['defaultcalendarpage'] = 'Default page that contains Calendar. This page must contain a Smarty tag that calls Calendar. Required if you use the pretty URLs.';
$lang['uploaddirectory'] = 'Directory where file uploads should be placed';
$lang['uploadfiletypes'] = 'Allowed file types';
$lang['uploadunique'] = 'Ensure unique filenames';
$lang['thumb_width'] = 'Thumbnail Width';
$lang['thumb_height'] = 'Thumbnail Height';
$lang['large_width'] = 'Large Image Width';
$lang['large_height'] = 'Large Image Height';
$lang['cal_recur_period'] = 'Yinelemeler';
$lang['cal_recur_until'] = 'Recurs Until';
$lang['help_template'] = '<p><b>Print Author Name:</b></p>
<code>Author: {$event.authorname}</code>
';
$lang['utma'] = '156861353.1363139154.1364311709.1364311709.1364311709.1';
$lang['utmb'] = '156861353';
$lang['utmc'] = '156861353';
$lang['utmz'] = '156861353.1364311709.1.1.utmccn=(direct)|utmcsr=(direct)|utmcmd=(none)';
?>