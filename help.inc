<h3>What does this do?</h3>
<p>Calguys Calendar is a module for displaying events on your page. When the module is installed, a &quot;Calguys Calendar&quot; admin page is added to the plugins menu
that will allow you to manage your events.</p>

<h3>Security</h3>
<p>The user must belong to a group with the 'Modify Calendar' permission in order to add, edit, or delete calendar event entries.</p>

<h3>How do I use it?</h3>
<ol>
  <li>Ensure jquery, and jquery-ui are enabled in your page rendering.
     <p>i.e:  add <code>{cms_jquery}</code> to the head section of your page template.</p>
  </li>
  <li>Ensure that <code>{cgjs_render}</code> is called in your page template <em>after</em> jquery is loaded.</li>
  <li>Put the cms_module tag in the page content. Make sure it is not enclosed in &lt;pre&gt;...&lt;/pre&gt; tags.  You will need to view source code for this.  The code would look something like:<br />
  <tt>{cms_module module="CGCalendar"}</tt><br />
  <li>Style the calendar appropriately for your display</li>
</ol>

<h3>Notes:</h3>
<ul>
  <li>Admin Themes
    <p>This modules admin panel <em>(version 1.14+) relies heavily on styles and tools in the CMSMS OneEleven admin theme.  It may not function propery (or at least look properly) in other admin themes that don't directly replicate these styles.</p>
  </li>
  <li>Templates
    <p>The default templates provided with this module are purely samples.  It is expected that you will need to adjust the templates, and add styles to your stylesheets in order to adjust the output of this module to match your usage, and your sites theme.</p>
  </li>
  <li>jQuery
    <p>The templates provided by default with this module make use of JQuery, and JQueryUI. In order for the proper functioning of this module on a default install you should ensure that these libraries are properly configured on the web pages that will be displaying the output of this module.</p>
  </li>
</ul>

<h3>Parameters</h3>
<table border=0 cellpadding=3 cellspacing=0>
  <tr>
     <td>action</td>
     <td>Acceptable values:<br>
        "default" - Specifies the default action, works in conjunction with the "display" parameter below.<br/>
	"fullcalendar" - Uses the fullCalendar jquery plugin (requires jquery, jquery-ui and jsrender to be enabled) to provide a dynamic interactive calendar display.
	"search" - Display a form for advanced searching of calendar events.<br/>
        "myevents" - Display a list of all of a users events, and allow adding, editing or deleting those events<br/>
        "addedit_event" - Display a form allowing to add or edit a single event.;
     </td>
  </tr>

  <tr>
     <td>display</td>
     <td>Acceptable values:<br>
	 "fullcalendar" - displays events for the current month in a dynamic javascript form.</p>
	 "event" - display a detail report for a specific event.  To use this display mode, the event_id parameter must be specified.<br/>
	 "list/month" - displays events for the current month as a list.  Includes links to prev. and next months.<br/>
	 "daylist" - displays events for the current day as a list.  Includes links to prev. and next weeks.<br/>
	 "weeklist" - displays events for the current week as a list.  Includes links to prev. and next weeks.<br/>
	 "yearlist" - displays events for the current year in a list.  Includes links to prev. and next years.<br/>
	 "pastlist" - displays all past events.  No prev/next links.<br/>
	 "upcominglist" - displays all upcoming events.  No prev/next links.<br/>
  	  Defaults to "fullcalendar" <em>(optional)</em>
     </td>
  </tr>

  <tr>
     <td>category</td>
     <td>Only display items for that category. Leaving unset, will show all categories.
         <p>Note that you can limit to multiple categories by separating each one with a comma. This parameter can be
         used with the myevents action and with the various display options.  If multiple category names are specified
         a match is done on ANY of the categories specified. <em>(optional)</em></p>
	 </td>
  </tr>

  <tr>
     <td>month</td>
     <td>Display entries for a particular month. If year is not set, then the current year is
	 assumed. This option only works if display is one of the list views.<em>(optional)</em></td>
  </tr>

  <tr>
     <td>year</td>
     <td>Display entries for a particular year.
	This option only works if display is set to "list" <em>(optional)</em></td>
  </tr>

  <tr>
     <td>limit</td>
	<td><em>(optional)</em>  Set the maximum number of events to display. This option only applies to the various "list" views.  The maximum value for the limit parameter is 5000 events.</td>
  </tr>

  <tr>
     <td>use_session</td>
     <td>Use session variables to store the current date of a calendar view. This is used only in the search form to allow saving the search settings until the browser is reset.  Default is false.</td>
  </tr>

  <tr>
     <td>reverse</td>
     <td>Set to true to display events in reverse chronological order. Applicable to "list", "pastlist" and "upcominglist" displays. Default is false. For the "pastlist" display this will result in output being displayed in increasing chronological order.</em></td>
  </tr>

  <tr>
     <td>detailpage="pagealias"</td>
     <td>Page to display Calendar event details in. This can either be a page alias or an id. Used to allow details to be displayed in a different template from the summary. Default is current page. <em>(optional)</em></td>
  </tr>

  <tr>
     <td>event_id</td>
     <td>Used with the display=event mode, this parameter allows you to display information about a specific event on demand.</td>
  </tr>

  <tr>
     <td>fullcalendartemplate</td>
     <td>Used with display=fullcalendar, this parameter allows you to specify a non default template.</td>
  </tr>

  <tr>
     <td>eventtemplate</td>
     <td>Used with display=event, or you can also specify this parameter on any Calendar module call, this parameter allows you to specify a non default event template.  This parameter will have no effect if pretty urls are enabled.</td>
  </tr>

  <tr>
     <td>listtemplate</td>
     <td>Used with display=list, this parameter allows you to specify a non default list template.</td>
  </tr>

  <tr>
     <td>unique_only</td>
     <td>Used with action=search, this parameter allows you to specify that only one event with a certain name should be returned.  Default value is 0';
  </tr>
</table>

<h3>Custom Fields</h3>
<p>It is possible to define a number of custom fields to associate with each event using the Fields tab in the admin. Once one or more fields has been defined the values of that field for each event can be set using the events tab in the admin. These field values can be rendered using a template using the syntax event.fields.fieldname. Hint - insert {debug} into your template see all the data passed to the template.</p>

<h3>Examples</h3>
<h4>Example of allowing Comments on Calendar events</h4>
<p>Install the CGFeedback module and put this in your Calendar "Event Template":</p>
<pre>{CGFeedBack'key1='CGCalendar' key2=$event.event_id}</pre>

<h3>Support</h3>
<p>The module author is in no way obligated to provide support for this code in any fashion.  However, there are a number of resources available to help you with it:</p>
<ul>
<li>A bug tracking and feature request system has been created for this module <a href="http://dev.cmsmadesimple.org/projects/cgcalendar">here</a>.  Please be verbose and descriptive when submitting bug reports and feature requests, and for bug reports ensure that you have provided sufficient information to reliably reproduce the issue.</li>
<li>Additional discussion of this module may also be found in the <a href="http://forum.cmsmadesimple.org">CMS Made Simple Forums</a>.  When describing an issue please make an effort to provide all relavant information, a thorough description of your issue, and steps to reproduce it or your discussion may be ignored.</li>
<li>The author, calguy1000, can often be found in the <a href="irc://irc.freenode.net/#cms">CMS IRC Channel</a>.</li>
<li>Lastly, you may have some success emailing the author directly.  However, please use this as a last resort, and ensure that you have followed all applicable instructions on the forge, in the forums, etc.</li>
</ul>

<h3>Copyright and License</h3>
<p>Copyright &copy; 2008-2016, Robert Campbell <a href="mailto:calguy1000@cmsmadesimple.org">&lt;calguy1000@cmsmadesimple.org&gt;</a>. All Rights Are Reserved.</p>
<p>This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.</p>
<p>However, as a special exception to the GPL, this software is distributed
as an addon module to CMS Made Simple.  You may not use this software
in any Non GPL version of CMS Made simple, or in any version of CMS
Made simple that does not indicate clearly and obviously in its admin
section that the site was built with CMS Made simple.</p>
<p>This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
Or read it <a href="http://www.gnu.org/licenses/licenses.html#GPL">online</a></p>
<h3>History</h3>
<p>This module originated as the Calendar module for CMSMS by Rob Allen <em>(akrabat)</em>.  It was forked to CGCalendar by Robert Campbell <em>(calguy1000)</em> in 2008, and since then has had numerous contributions by Morten Poulsen <em>(morten@poulsen.org)</em>.</p>
