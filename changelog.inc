<h3>Future changes</h3>
<p>In upcoming versions of this module we will be:</p>
	<ul>
	<li>Utilize caching when loading events to improve performance (not loading the same event multiple times in some views)</li>
	<li>Improving pretty url support</li>
	<li>Further OOPifying the code to allow for future expansion.</li>
	<li>Allow filtering by custom fields... i.e: a dropdown value so that you can filter by a resource.</li>
	</ul>

<h3>History</h3>
  <ul>
  <li>Version 2.6
	<ul>
	<li>Reformat the admin editing form a bit.</li>
	<li>Adds a new "Dropdown from UDT" field type.</li>
	</ul>
  </li>
  <li>Version 2.5.1
	<ul>
	<li>Fixes wrt including the final day on recurring events.</li>
	<li>Now include the first detected category name in the event data provided by ajax to fullcalendar.</li>
	<li>Fix for display of multi-day events in FullCalendar.</li>
	</ul>
  </li>

  <li>Version 2.5
    <ul>
      <li>Now use the CMSMS FilePicker in the admin for file attachment fields.  This field no longer uses the upload-directory preference.</li>
      <li>Adds a new image attachment field type. Usable in the admin editor only.</li>
    </ul>
  </li>
  <li>Version 2.4.9
    <ul>
      <li>More improvements to the edit-event template layout.</li>
      <li>Now assume that the add-event links and dayclick events are in UTC.</li>
    </ul>
  </li>
  <li>Version 2.4.8
    <ul>
      <li>Improvements to the edit-event template layout.</li>
      <li>Fixes off-by-one error in the dayclick stuff.</li>
      <li>Fixes to the upgrade routine wrt templates.</li>
    </ul>
  </li>
  <li>Version 2.4.3
	<ul>
	<li>Fix an issue with frontend fullcalendar displays.  You may need to create new fullcalendar view templates after resetting the default fullcalendar template to factory defaults.</li>
	</ul>
  </li>
  <li>Version 2.4.3
     <ul>
       <li>Adds the EventEditedPre CMSMS Event that is sent prior to an event being modified.</li>
       <li>Adds the ability to export events to a simple CSV file.</li>
       <li>Minor improvements to import functionality.</li>
       <li>Fixes to admin view of FullCalendar display if no holidays defined.</li>
     </ul>
  </li>
  <li>Version 2.3
     <ul>
       <li>Adds ability to save an event as Draft (D) or Published (P).
           <p> Draft events are never added to the search module.
           On upgrade all events are marked as 'Published'.</p>
       </li>
       <li>Improves interface with the Search module.</li>
       <li>Upgrade FullCalendar to the latest version (3.1.x)</li>
       <li>Adds the year list view to the admin interface FullCalendar implementation.</li>
     </ul>
  </li>
  <li>Version 2.2
     <ul>
     <li>Minor bug fixes.</li>
     <li>Adds ability to click on a date on the fullcalendar view to add an event on that date.</li>
     </ul>
  </li>
  <li>Version 2.1.4
     <ul>
     <li>Minor bug fixes to summary views.</li>
     <li>More fixes wrt upgrading from the 1.x series.</li>
     </ul>
  </li>
  <li>Version 2.1.3
	<p class="warning"><strong>Warning:</strong> In future versions I will be converting all MyISAM tables to InnoDB on upgrade.  You will lose full text search capabilities if you are not running MySQL 5.6 or later.</p>
	<ul>
	<li>Bug fixes.</li>
	</ul>
  <li>Version 2.1.3
	<ul>
	  <li>Bug fixes.</li>
	</ul>
	</li>
  <li>Version 2.1.2
     	<ul>
	<li>Fix problem on new installs with default templates not being specified.</li>
	<li>Fix problem with category= not working in various displays.</li>
        <li>Fix problems with upgrade from 1.x
        </li>
	</ul>
  </li>
	<li>Version 2.1
	<p class="warning"><strong>Warning:</strong> If your host is not using MySQL 5.6 or beyond the full text search capabilities will not operate on new installs.</p>
	<ul>
	<li>Adds the ability for members of a special FEU group to edit or delete any event from the frontend.</li>
	<li>Aads the ability to bulk delete expired events.</li>
	<li>New installs use InnoDB instead of MyISAM which allows for data integrity.</li>
	</ul>
  </li>

  <li>Version 2.0
	<p class="warning"><strong>Warning:</strong> This version marks a major change for the CGCalendar module.  This version requires CMSMS 2.1.2 or beyond, and integrates with the Design Manager.  Also, some functionality has been removed or changed.  Upgrading will require creating new templates, and adjusting their styles, and potentially adjusting the way the module is called.</p>
	<p class="warning"><strong>Warning:</strong> CMSMS 2.x requires that all templates be uniquely named.  During the upgrade process we ensure that all CGCalendar templates are uniquely named upon conversion.  However, we cannot and do not edit your calls to the CGCalendar module.  Therefore, you may need to adjust your calls to CGCalendar to adjust a manually specified template name.</p>
	<ul>
	<li>Removes the previously deprecated calendar view.</li>
	<li>Migrates all template functionality into the CMSMS 2.0 Layout scheme.  All templates for CGCalendar are now available in DesignManager.</li>
	<li>Moves all settings controls into their own navigation item in the admin console.</li>
	<li>Removes the upcominglist template type, and replaces it with the standard list template type.  <strong>Note:</strong> This may cause compatibility issues.  Pastlist and upcominglist views no longer support pagination.</li>
	<li>Adds support for a day view.</li>
	<li>Unifies all summary views (year, month, day, upcominglist, pastlist) to use the same eventlist templates. The format of this template has changed significantly.</li>
	<li>No longer pass the eventtemplate parameter from the summary view into detail urls to potentially allow for pretty urls in detail (event) views.  Use the {cge_module_hint} tag instead.</li>
	<li>No longer use the use_session parameters for list views.  The default template allows for scrolling by ajax.</li>
	<li>No longer use the displayforday parameter for pastlist or upcominglist.  Upcominglist automatically includes events for the current day, as does pastlist.</li>
	<li>No longer use the unique_only parameters for the pastlist and upcominglist views as the event name cannot be assumed to be a key.  Post filtering in the list template may be a valid solution for this if on a particular site it can be assumed that the event name is unique.</li>
	</ul>
  </li>

  <li>Version 1.16.x
     <p><strong>Warning:</strong> Most frontend templates have changed in this version.  Event display templates have different functionality for accessing custom fields.  Please see the examples, and re-style your websites after upgrading.</p>
     <ul>
       <li>Added checkbox and dropdown custom field types.</li>
       <li>Added the ability to manipulate recurring events from the frontend interface.</li>
       <li>Rewites the frontend delete action.</li>
       <li>rewrite the frontend add/edit event action.</li>
       <li>Modify default event template to allow editing the event if logged in to FEU.</li>
       <li>Now fields and categories are preserved and working properly when editing child events in the frontend or admin.</li>
       <li>Numerous minor bug fixes and code optimizations.</li>
       <li>Make ready plans to remove template code entirely in favor of DesignManager.</li>
     </ul>
  </li>

  <li>Version 1.15.x
	<p><strong>Note:</strong>  We now create a separate smarty scope for each visible template to prevent smarty variable name collision issues, and for memory optimization purposes.  This may cause problems when trying to export variables to other templates, or when trying to use a variable created in another template.</p>
	<p><strong>Note:</strong> The new default action for the CGCalendar module is the fullcalendar view.  This view requires jquery and jquery-ui to operate.  Therefore your site may break on upgrade.  To force the old <em>(deprecated)</em> calednar view, change your call to <code>{CGCalendar display=calendar}</code></p>
     <p><strong>Note:</strong>  Fixed errors in many of the display templates.</p>
	<ul>
	<li>Version 1.15
	<ul>
	   <li>Introduces separate smarty scopes for all frontend, and some backend displays.</li>
	   <li>Upgrades fullcalendar to the latest released version.</li>
	   <li>Adds the fullcalendar action to the frontend, which is now the default action. (requires jquery and jquery-ui)</li>
           <li>Adds support for an ical feed to hilite holidays in fullcalendar views.</li>
	   <li>Numerous bug fixes.</li>
	</ul>
	</li>
	<li>Version 1.15.1
	<ul>
	   <li>Fixes error with event display in admin and frontend if there were no holidays.</li>
	</ul>
	</li>
	<li>Version 1.15.2
	<ul>
	   <li>Fixes javascript errors with date formats in fullcalendar in the admin panel.</li>
	</ul>
	<li>Version 1.15.2
	<ul>
	   <li>Adds missing code from upgrade routine to create a new system default and initial fullcalendar template.</li>
	</ul>
	</li>
        <li>Version 1.15.3
        <ul>
           <li>Adds missing upgrade routine to populate sample fullcalendar template.</li>
        </ul>
        <li>Version 1.15.4
        <ul>
	   <li>Category param now works for the fullcalendar action.</li>
	   <li>Fixes an issue with weekly recurring events.</li>
	   <li>minor UI changes in the add event template.</li>
	   <li>minor bug fixes.</li>
        </ul>
	</li>
        <li>Version 1.15.5
          <ul>
            <li>Fixes probloem with changing field names.</li>
          </ul>
        </li>
        <li>Version 1.15.6
          <ul>
            <li>Fixes probloem with displaying child events in fullcalendar view <em>(Note: fullcalendar template needs to be reset to factory settings)</em></li>
            <li>Adds sample date formatting to fullcalendar</li>
          </ul>
        </li>
        <li>Version 1.15.8
          <ul>
            <li>Enhance the image upload directory behavior so that paths relative to the root or uploads directory can be specified.</li>
          </ul>
        </li>
     </ul>
  </li>
  <li>Version 1.14.x
  <p><strong>Note:</strong> - The 1.14 series introduces a great deal of improvements.  It also fixes a lot of outstanding issues.  There will probably be template incompatibilities.</p>
  <p><strong>Note:</strong> - Future releases in this series will be replacing the frontend calendar view with fullcalendar.</p>
    <ul>
	<li>Version 1.14.4
          <ul>
	    <li>fixes to entities with respect to the frontend form.  Now it is possible to submit HTML via all text areas.  HTML is sanitized before being stored.  Text input fields are also sanitized on frontend forms.  <strong>Note:</strong>This may introduce a compatibility problem.</li>
            <li>Now requires PHP 5.4.3+</li>
          </ul>
	</li>


	<li>Version 1.14.3
	<p>The 1.14.x series stops using the hardcoded month and day names in the lang file and began using locale specified date names.  If you are encountering an 'off by one' problem in the CGCalendar display templates you will want to fix up your frontend templates.  Restoring frontend templates to factory defaults should solve the issue.  Essentially, you should not use the {$month_names} or {$day_names} array (they are one based).  And instead should use the {$event.event_date_start} and {$event.event_date_end} smarty variables, and the date_format modifier to format dates appropriately.</p>
	<ul>
	  <li>Fixes to the upcoming list template.</li>
	</ul>
	</li>

	<li>Version 1.14.2
	<ul>
	<li>Restores import link (oops).</li>
	</ul>
	</li>

	<li>Version 1.14.2
	<p><strong>Note:</strong>  in the near future (depending upon sponsorship) I plan to replace the calendar view with an ajaxy calendar (fullcalendar).   This will probably break
     compatibility.  You are forewarned.</p>
	<ul>
	<li>Bug fixes.</li>
	<li>Revise list template to use date_format instead of hardcoded month/day stuff. (still have to do calendar template)</li>
	<li>Revised upcoming list action to call the template even if there are no events.</li>
	<li>Revised the event loader to figure out a color for event (based on category colors).</lI>
	<li>Revise the list, and calendar templates to show some ajaxy stuff, and the use of colors.</li>
	</ul>
	</li>

	<li>Version 1.14.1
	<ul>
	<li>Bug fixes.</li>
	<li>Week and day names are now generated by the locale, not from lang strings.</li>
	</ul>
	</li>

	<li>Version 1.14
	<ul>
	<li>Implements fullcalendar for the primary event listing.
	<ul>
  	  <li>Drag and drop events to move them to different dates, or adjust end time (ajax)</li>
	  <li>Delete events via ajax<li>
	</ul>
	</li>
	<li>Adds the All day event flag</li>
	<li>Associates colors with categories (sexy in the fullcalendar view)... not yet available in the frontend.</li>
	<li>Rework to the add/edit event form (admin)</li>
	<li>Now depends on CGSimpleSmarty.</li>
	<li>bug fixes.</li>
	</ul>
	</li>
    </ul>
  </li>


<li>Version 1.13.x
  <ul>
    <li>Adds two new permissions (Add Calendar Events, Edit My Calendar Events).</li>
    <li>Minor cleanup of the admin add event template.</li>
    <li>Minor tweaking to the event list template.</li>
  </ul>
</li>
<li>Version 1.12.x - November, 2013
   <p><strong>Note:</strong> In the future I will be moving images into their own directory, and cleaning up preferences more.  This will probably cause compatibility problems in the future.</p>
  <ul>
    <li>Removed all image cropping on upload.
       <p><strong>Note:</strong> This could cause compatibility problem with displaying images in your existing templates.</p>
    </li>
    <li>Adds the file attachment field type.</li>
    <li>Code cleanup and bug fixes.</li>
  </ul>
</li>
<li>Version 1.11.x - October, 2013
  <p><strong>Note:</strong> There probably will be compatibility problems for people upgrading to CGCalendar 1.11.x and beyond from
  previous versions.  Particularly related to the dropped parameters <em>(see below)</em> and some of the inline fixes and changes.
  You will need to do testing on your CGCalendar installs and fix any calls to CGCalendar, and also make some changes to templates.</p>
  <ul>
    <li>Bug fixes.</li>
    <li>Dropped the column parameters (this is a design related item, not related to business logic and should be done in templates.).</li>
    <li>Dropped the returnlink parameters as they didnt work properly.  This should be done in the templates.  (history.go(-1) or something).</li>
    <li>Dropped the first-day-of-week parameter, and replaced it with a preference.</li>
    <li>All navigation links on prototype templates now use non-inline URLS.</li>
    <li>Added display=weeklist</li>
    <li>Refactored the event display in the admin panel.
      <ul>
        <li>Events are now displayed only for a specified month and year.</li>
        <li>Easy selection of month and year, and scrolling between months.</li>
        <li>The ability to edit a parent event from a child event.</li>
        <li>Filters are stored in the user session.</li>
      </ul>
    </li>
    <li>Refactored and checked all parameters passed to navigation links in the individual vieews</li>
    <li>All navigation views have in_ and ni_ variants to allow selecting an inline, or non inline variant of the navigation link.  Though the ni_ prefixed URLS may infact be inline links depending upon the use_session stuff.</li>
    <li>Day view URLS from the calendar view now have ni_ and in_ variants.</li>
    <li>The inline parameter is deprecated, and will be dropped at the next major version.</li>
    <li>Refactored all event views
      <ul>
        <li>All event views use the cgc_event_loader class.</li>
        <li>Optimize custom field, and category loading.</li>
        <li>Custom fields and categories are now copied from the parent event if not specified for the child event.</li>
        <li>All links/URLs generated to events use pretty URLS (if enabled) and are non inline.</li>
        <li>author field may be present for each event that has an author... but authorname (which was inconsistently applied) has been dropped.  Use a smarty plugin to extract the author information and display it.</li>
        <li>use_session now automatically turns on inline mode.. and will use inline mode for even ni_ links/urls</li>
        <li>use_session value is only used as a prefix for the session key.  The session key is unique to all calls of CGCalendar.</li>
        <li>field output is now consistent with field ordering (by default).</li>
      </ul>
    </li>
    <li>Refactored the admin categories tab.
      <ul>
        <li>Adds drag and drop reordering.</li>
      </ul>
    </li>
    <li>Refactored the admin fields tab.
      <ul>
        <li>Adds drag and drop reordering.</li>
        <li>Adds the ability to specify wether a text area field has a wysiwyg (admin interface only).</li>
      </ul>
    </li>
    <li>More</li>
  </ul>
</li>
<li>Version 1.10.2 - February, 2013
  <ul>
    <li>Bug fixes.</li>
  </ul>
</li>
<li>Version 1.10 - August 2012
  <ul>
    <li>Adds a toggle to use wysiwygs for textarea custom fields.</li>
    <li>Adds a preference for the friendly name.</li>
  </ul>
</li>
<li>Version: 1.9.7 - July 2012
  <ul>
   <li>Numerous minor bug fixes.</li>
   <li>Move help out of lang file.</li>
  </ul>
</li>
<li>Version: 1.9.6 - April 2012
  <ul>
   <li>Numerous minor bug fixes.</li>
   <li>Fix javascript issue in edit event.</li>
  </ul>
</li>
<li>Version: 1.9.4 - March, 2012
  <ul>
   <li>Numerous minor bug fixes.</li>
   <li>Fixed notices in default templates.</li>
  </ul>
</li>
<li>Version: 1.9.1 - March, 2012
  <ul>
   <li>Added ability to specify import column delimiter</li>
   <li>Minor bug fixes.</li>
  </ul>
</li>
<li>Version: 1.9 - January, 2012
  <ul>
   <li>Improved event import capabilities.</li>
  </ul>
</li>
<li>Version: 1.8.2 - October, 2011
  <ul>
   <li>Minor event name fix.</li>
  </ul>
</li>
<li>Version: 1.7.5 - August, 2011
  <ul>
  <li>Ads first day, and last day stuff to the event in the calendar view.</li>
  </ul>
</li>
<li>Version: 1.7.3 - July, 2011
  <ul>
  <li>More Fixes to the list view.</li>
  </ul>
</li>
<li>Version: 1.7.2 - July, 2011
  <ul>
  <li>Fixes to the list view.</li>
  </ul>
</li>
<li>Version: 1.7.1 - July, 2011
  <ul>
  <li>The unique_only parameter now defaults to &quot;off&quot; for all views.</li>
  <li>Adds the displayforday parameter to the upcominglist view.</li>
  </ul>
</li>
<li>Version: 1.7 - June, 2011
  <ul>
  <li>Adds the ability to filter the &quot;myevents&quot; view by a category.</li>
  </ul>
</li>
<li>Version: 1.6.1 - April, 2011
  <ul>
    <li>Remove debug statements.</li>
  </ul>
</li>
<li>Version: 1.6 - April, 2011
  <ul>
    <li>Adds a company directory field type.</li>
    <li>Bug Fixes.</li>
  </ul>
</li>
<li>Version: 1.5.7 - September 2010
  <ul>
    <li>More fixes to displaycalendar and displayupcominglist.</li>
  </ul>
</li>
<li>Version: 1.5.6 - September 2010
  <ul>
    <li>Fixes to upcoming list pagination.</li>
    <li>Fixes to unique only param in upcoming list.</li>
  </ul>
</li>
<li>Version: 1.5.5 - August 2010
  <ul>
    <li>Now Require CGExtensions 1.19.4</li>
    <li>Adds a preference for the URL prefix.</li>
    <li>Adds a preference to default new events to all day events.</li>
    <li>Adds a preference for the default start time for non-allday events.</li>
  </ul>
</li>
<li>Version: 1.5.4 - August 2010
  <ul>
    <li>Bug fixes.</li>
  </ul>
</li>
<li>Version: 1.5.3 - July 2010
  <ul>
    <li>Minor bug fix.</li>
  </ul>
</li>
<li>Version: 1.5.2 - April 2010
  <ul>
    <li>Remove leftover debug statements.</li>
  </ul>
</li>
<li>Version: 1.5.1 - April 2010
  <ul>
    <li>Bug fixes.</li>
  </ul>
</li>
<li>Version: 1.5 - April 2010
  <ul>
    <li>Adds the ability to perform search on custom field values.</li>
  </ul>
</li>
<li>Version: 1.4 - April 2010
  <ul>
    <li>Bug fixes wrt deleting events.</li>
    <li>Adds the ability for logged in frontend users to add, edit, or delete calendar events.</li>
  </ul>
</li>
<li>Version: 1.3.1 - March 2010
  <ul>
    <li>Minor bugfix in installer.</li>
  </ul>
</li>
<li>Version: 1.3 - December 2009
  <ul>
    <li>Adds the concept of all day events.</li>
    <li>Adds the ability to allow events to overlap or not.</li>
    <li>Adds the ability to remove all conflicting/overlapping events.</li>
    <li>Various bug fixes.</li>
  </ul>
</li>
<li>Version: 1.2.5 - September 2009
  <ul>
    <li>Minor bug fixes.</li>
  </ul>
</li>

<li>Version: 1.2.4 - September 2009
  <ul>
    <li>Adds support for textarea fields.</li>
    <li>Minor bug fixes.</li>
  </ul>
</li>
<li>Version: 1.2.3 - September 2009
  <ul>
    <li>Fixes stupid errors with the installer.</li>
    <li>Adds unique_only parameter.</li>
    <li>Adds basic ical support in event views.(Ted)</li>
    <li>Minor bug fixes.</li>
  </ul>
</li>
<li>Version: 1.2.2 - May 2009
  <ul>
    <li>Fixes edge case problem with dates recurring monthly on the Nth weekday</li>
    <li>Fixes upcominglist view.</li>
  </ul>
</li>
<li>Version: 1.2.1 - May 2009
  <ul>
    <li>Fixes problem with dates recurring monthly on the Nth weekday</li>
  </ul>
</li>
<li>Version: 1.2 - April 2009
  <ul>
    <li>Adds pagination to upcoming list view.</li>
    <li>Various bug fixes.</li>
  </ul>
</li>
<li>Version: 1.1 - March 2009
 <ul>
   <li>Minor bug fixes.</li>
   <li>Add enhanced monthly recuring event capabilities.</li>
   <li>Add search form.</li>
 </ul>
</li>
<li><p>Version: 1.0</p>
<p>Fork version 0.8.2 of the Calendar module, and completely re-do event adding, editing, recurring events, and templates.  This module is not comptible with previous versions of the Calendar module</p>
</li>
<li><p>Version: 0.8.2</p>
  <p>Fix to pretty urls.</p>
</li>
<li><p>Version: 0.8.1</p>
  <p>Minor fix to search integration when updating an event.</p>
  <p>Add categories information to the list display.</p>
</li>
<li><p>Version: 0.8</p>
  <p>Adds search capabilities</p>
</li>
<li><p>Version: 0.7.14</p>
  <p>Fixes a serious issue that could result in thousands of needless calendar entries being inserted into the table.</p>
</li>
<li><p>Version: 0.7.13</p>
  <p>Fixes friendly urls for detail articles</p>
</li>
<li><p>Version: 0.7.12</p>
  <p>Fixes file upload fields</p>
  <p>Fixes issues with references to the Album module</p>
  <p>Adds recurring events</p>
</li>
<li><p>Version: 0.7.11</p>
  <p>Fixed a bug introduced by brining in the categories, whereby the join did not work on earlier versions on mySQL. (codepoet)</p>
  <p>Added a list of categories that each event is associated with to the data available to the template. (codepoet)</p>
  <p>Fixed a bug introduced in 0.7.10 that prevents a clean install from working, because of an extra comma on the end of the field list for table cms_module_ecalander_event_field_values (codepoet)</p>
</li>
<li><p>Version: 0.7.10 - calguy1000</p>
<p>Added support for file uploads in custom fields</p>
<p>Added the ability to supply \'next\' as the event_id in the event display, to display the next upcoming event.</p>
</li>
<li><p>Version: 0.7.9</p>
  <p>Added custom fields that can be associated with each event.</p>
</li>

<li><p>Version: 0.7.8</p>
  <p>Fixed a detailpage/returnlink bug... again...</p>
</li>

<li><p>Version: 0.7.7</p>
  <p>Added a default category</p>
  <p>Added an option to allow an event to be forced a member of at least 1 category</p>
  <p>Added options of how many years in the past and future you should be able to select</p>
  <p>Progress towards more uptodate API-usage, using templates, actions etc.</p>
  <p>Numerous bugfixes</p>
</li>

<li><p>Version: 0.7.6</p>
  <p>Added column style output for upcominglist and pastlist</p>
  <p>Moved example stylesheet to the stylesheetlist</p>
  <p>Added tagging of events and deletions of tagged ones</p>
  <p>Reworked for use of external actionfiles</p>
  <p>Improved tab-functionaliy and actionmessages</p>
</li>

<li><p>Version: 0.7.5</p>
  <p>Fixed a lot of openbasedir-issues</p>

</li>

<li><p>Version: 0.7.4</p>
  <p>Fixed hardcoded length of title input field</p>
  <p>Fixed some missing localization</p>
  <p>Removed support for pretty urls (it was not working, as some parameters were missing, and putting them in would have made the urls unpretty anyways</p>

  <p>Updated permissions model.  Now need \'Modify Templates\' permission to modify the templates, and \'Modify Site Preferences\' permission to adjust the preferences.</p>

</li>
<li><p>Version: 0.7.3</p>
    <p>Added Minimum CMS Version</p>
    <p>Added the pastlist option</p>
</li>
<li><p>Version: 0.7.2</p>
    <p>CMS 1.0 compatiblity</p>
    <p>Split out into numerous files for better memory efficiency</p>
    <p>Introduced references where necessary to improve memory efficiency</p>
    <p>Added functions to allow for talking with this module via other modules</p>
    <p>Added events</p>
    <p>Improved security and reduced memory footprint</p>
</li>
<li><p>Version: 0.7 stable</p>
    <p>Fixed problem with broken month list on 31st of each month;</p>
    <p>Fixed problem with Calendar showing in the menu for unauthorized admin users.</p>
</li>
<li><p>Version: 0.7beta3</p>
    <p>Added the yearlist view option to list all events in the year. Acts like the list option.</p>
</li>
<li><p>Version: 0.7beta2</p>
    <p>Added the reverse parameter to show events in reverse chronological order.</p>
</li>
<li><p>Version: 0.7beta1</p>
    <p>Added navigation in the list view, fixed problem with events not showing up in the list view.</p>
</li>
<li><p>Version: 0.7alpha2</p>
    <p><ul>
       <li>Bug fix: include last day of the month!</li>
       <li>Bug fix: template use of $table_id should have {} around it.</li>
       <li>Include icon for content page.</li>
       <li>Whitespace changes - trailing spaces and tabs/space tidy up.</li>
       <li>Make default templates XHTML 1.0 Transitional compliant.</li>
    </ul></p>
</li>
<li><p>Version: 0.7alpha1</p>
    <p>Complete rewrite to fit into 0.10.x better.</p>
    <p>Support language for day and month names</p>
    <p>Use smarty templates for controlling the display.</p>
    <p>Rewrite the SQL used to select the events to hopefully be more maintainable.</p>
    <p><strong>Note</strong> that the language files are not fully updated for this version!</p>
</li>
<li><p>Version: 0.6.1</p>
    <p>More fixes for multi-day event on calendar. Added new parameter "time_format" so we can make the display of a single day event with a start and stop time look better. Change de_DE\'s "to" to "bis" as per Mordran\'s post on the boards.</p>
</li>
<li><p>Version: 0.6</p>
   <p>Fix event display so that if the end date is not set, we don\'t display "to".</p>
   <p>Filter by category when displaying an upcominglist.</p>
   <p>Fix End date setting that was off by one.</p>
   <p>Fix odd behaviour of year when transitioning from Jan to Dec or Dec to Jan in the calendar view.</p>
   <p>Support mutli-day events in the calendar view.</p>
</li>
<li><p>Version: 0.5</p>
    <p>Fix the drop down list for end date year. Fix DE translation of "Return" (thanks Piratos!).</p>
    <p>Fix spacing around "to" (thanks Greg!). Add Danish and Dutch translations courtesy of board members esmann and dont.</p>
</li>
<li><p>Version: 0.4</p>
    <p>Support for language translations. Default to a NULL end date. Improved the help information.</p>
    <p>Display upcoming events in the correct order! Other minor bug fixes.</p>
</li>
<li><p>Version: 0.3</p>
    <p>Initial support for "From" and "To" dates for events. Ability to filter admin list of events by category.</p>
</li>
<li><p>Version: 0.2</p>
    <p>New display option: "upcominglist". Also many bugfixes!</p>
</li>
<li><p>Version: 0.1</p>
    <p>Initial release.</p>
</li>
</ul>
