<?php
$lang['error_feu_loggedin'] = 'K&auml;ytt&auml;j&auml; ei ole kirjautunut (FEU). Tapahtumien lis&auml;&auml;minen tai muokkaaminen ei ole sallittua';
$lang['prompt_friendlyname'] = 'Friendly Name for this Module in Admin Navigation';
$lang['info_friendlyname'] = 'Note: You may need to clear the site cache for this preference to have effect.  If no value is supplied a default will be used.';
$lang['use_wysiwyg'] = 'K&auml;yt&auml; Wysiwygi&auml;';
$lang['error_insufficientparams'] = 'Puutteelliset parametrit';
$lang['column_delimiter'] = 'Import file column delimiter';
$lang['import_completed'] = 'Tuonti suoritettu';
$lang['num_imported'] = 'Number of events created: %d';
$lang['import_lines'] = 'Number of lines processed from file: %d';
$lang['errors'] = 'Errorit';
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
$lang['delete'] = 'Poista';
$lang['cd_company'] = 'Yhti&ouml; Hakemisto Yhti&ouml;';
$lang['dflt_urlprefix'] = 'URL-osoitteiden oletus etuliite';
$lang['dflt_alldayevent'] = 'Uudet tapahtumat ovat oletuksena &quot;Koko p&auml;iv&auml;n&quot;-tapahtumia';
$lang['dflt_starttime'] = 'Uusien tapahtumien oletus alkamisaika';
$lang['info_dflt_starttime'] = '<em>(p&auml;tee ainoastaan, jos uudet tapahtumat eiv&auml;t ole &amp;quot;Koko p&auml;iv&auml;n&quot;-tapahtumia)</em>';
$lang['search_type'] = 'Hakutyyppi';
$lang['search_any'] = 'N&auml;yt&auml; tulokset, jotka t&auml;sm&auml;&auml;v&auml;t mihin tahansa sanaan';
$lang['search_all'] = 'N&auml;yt&auml; tulokset, jotka t&auml;sm&auml;&auml;v&auml;t kaikkiin sanoihin';
$lang['info_customfields'] = '<span style=&quot;color: red;&quot;>Huomaa:</span> &quot;Tiedostonl&auml;hetys&quot;-kentt&auml; ei ole haettavissa.';
$lang['searchable'] = 'Haettavissa';
$lang['help_param_editeventtemplate'] = 'Koskee ainoastaan myevents-toimintoa, t&auml;m&auml; parametri sallii m&auml;&auml;ritell&auml; oletuksesta poikkeavan sivupohjan tapahtumien muokkaamiseen, kun k&auml;ytt&auml;j&auml; luo tai muokkaa tapahtumaa.';
$lang['help_param_editpage'] = 'Koskee ainoastaan myevents-toimintoa, t&auml;m&auml; parametri sallii toisen sivun m&auml;&auml;rittelemisen lomakkeelle.';
$lang['help_param_myeventstemplate'] = 'Koskee ainoastaan myevents-toimintoa, t&auml;m&auml; sallii oletuksesta poikkeavan sivupohjan m&auml;&auml;rittelemisen.';
$lang['cal_edit_event'] = 'Muokkaa kalenteritapahtumaa';
$lang['error_dberror'] = 'Tietokantavirhe';
$lang['submit'] = 'L&auml;het&auml;';
$lang['info_sysdflt_editevent_template'] = 'J&auml;rjestelm&auml;n oletus &quot;Muokkaa tapahtumaa&quot;-sivupohja';
$lang['info_sysdflt_myevents_template'] = 'J&auml;rjestelm&auml;n oletus &quot;Omat tapahtumat&quot;-sivupohja';
$lang['prompt_feedit_group'] = 'T&auml;m&auml;n ryhm&auml;n FrontEndUsers-moduulin j&auml;senet voivat lis&auml;t&auml; tapahtumia.';
$lang['prompt_feedit_page'] = 'Oletussivu julkisten sivujen muokkauslomakkeelle.';
$lang['none'] = 'Ei mit&auml;&auml;n';
$lang['owner'] = 'Omistaja';
$lang['cal_editevent_templates'] = 'Tapahtuman muokkaus -lomake';
$lang['info_editevent_template_tab'] = 'Saatavilla olevien sivupohjien lista tapahtuman muokkauslomakkeelle.';
$lang['editeventtemplate_addedit'] = 'Lis&auml;&auml;/muokkaa tapahtuman muokkaus -lomakkeen sivupohjaa';
$lang['cal_myevents_templates'] = 'Omat tapahtumat -lista';
$lang['info_myevents_template_tab'] = 'Saatavilla olevien sivupohjien lista, joilla voi n&auml;ytt&auml;&auml; k&auml;ytt&auml;j&auml;n tapahtumien listan';
$lang['myeventstemplate_addedit'] = 'Lis&auml;&auml; tai poista &quot;Omat tapahtumat&quot; raportin sivupohjaa';
$lang['areyousure_removeconflicting'] = 'T&auml;m&auml; toiminta poistaa tapahtumat, jotka ovat ristiriidassa sellaisten tapahtumien kanssa, jotka eiv&auml;t voi olla p&auml;&auml;llekk&auml;in. Oletko varma, ett&auml; haluat tehd&auml; t&auml;m&auml;n?';
$lang['filter_conflicting'] = 'Suodata p&auml;&auml;llekk&auml;in menev&auml;t tapahtumat';
$lang['overlap_action_remove'] = 'Poista p&auml;&auml;llekk&auml;in menev&auml;t tapahtumat hiljaisesti';
$lang['overlap_action_error'] = 'N&auml;yt&auml; virhe';
$lang['prompt_overlap_action'] = 'Mit&auml; tapahtuu, kun tapahtuman lis&auml;yksess&auml; tai muokkauksessa huomataan p&auml;&auml;llekk&auml;isyys';
$lang['error_event_conflict'] = 'Tapahtuman p&auml;iv&auml;m&auml;&auml;r&auml;t menev&auml;t p&auml;&auml;llekk&auml;in sellaisen tapahtuman kanssa, jonka p&auml;&auml;lle ei voi laittaa toista tapahtumaa';
$lang['cal_info_overlap'] = 'Koskee ainoastaan tapahtumia, joilla on loppumisaika (tai koko p&auml;iv&auml;n kest&auml;vi&auml; tapahtumia)';
$lang['cal_overlap'] = 'Salli uusien tapahtumien menn&auml; p&auml;&auml;llekk&auml;in t&auml;m&auml;n kanssa';
$lang['prompt_overlap_policy'] = 'Tapahtuman p&auml;&auml;llekk&auml;isyys k&auml;yt&auml;nt&ouml;';
$lang['policy_all'] = 'Kaikki tapahtumat voivat menn&auml; p&auml;&auml;llekk&auml;in';
$lang['policy_none'] = 'Tapahtumat eiv&auml;t voi menn&auml; p&auml;&auml;llekk&auml;in';
$lang['policy_individual'] = 'Valitaan tapahtumakohtaisesti';
$lang['lbl_back'] = 'Takaisin';
$lang['lbl_templates'] = 'Sivupohjat';
$lang['textarea'] = 'Tekstikentt&auml;';
$lang['error_calendar_incompatible'] = 'Huomasimme, ett&auml; my&ouml;s kalenterimoduuli on asennettuna. T&auml;m&auml; moduuli ei ole sen kanssa yhteensopiva eik&auml; ne voi olla yht&auml;aikaa asennettuna. Ole hyv&auml; ja poista kalenterimoduuli ensin.';
$lang['error_event_not_found'] = 'Joko tapahtuman tunnistetta ei ole tietokannassa, tai tietokannassa on useampi tapahtuma t&auml;ll&auml; tunnisteella! (%d)';
$lang['first_page'] = '<<';
$lang['prev_page'] = '<';
$lang['next_page'] = '>';
$lang['last_page'] = '>>';
$lang['page_of'] = 'Sivu %d / &amp;d';
$lang['search_results'] = 'Kalenterin hakutulokset';
$lang['search_words'] = 'Hakusanat';
$lang['no_results_found'] = 'Ei tuloksia';
$lang['frequency'] = 'Frekvenssi';
$lang['specified_date'] = 'K&auml;yt&auml; tietty&auml; p&auml;iv&auml;m&auml;&auml;r&auml;&auml;';
$lang['first_sunday'] = 'Kuukauden ensimm&auml;inen sunnuntai';
$lang['first_monday'] = 'Kuukauden ensimm&auml;inen maanantai';
$lang['first_tuesday'] = 'Kuukauden ensimm&auml;inen tiistai';
$lang['first_wednesday'] = 'Kuukauden ensimm&auml;inen keskiviikko';
$lang['first_thursday'] = 'Kuukauden ensimm&auml;inen torstai';
$lang['first_friday'] = 'Kuukauden ensimm&auml;inen perjantai';
$lang['first_saturday'] = 'Kuukauden ensimm&auml;inen lauantai';
$lang['second_sunday'] = 'Kuukauden toinen sunnuntai';
$lang['second_monday'] = 'Kuukauden toinen maanantai';
$lang['second_tuesday'] = 'Kuukauden toinen tiistai';
$lang['second_wednesday'] = 'Kuukauden toinen keskiviikko';
$lang['second_thursday'] = 'Kuukauden toinen torstai';
$lang['second_friday'] = 'Kuukauden toinen perjantai';
$lang['second_saturday'] = 'Kuukauden toinen lauantai';
$lang['third_sunday'] = 'Kuukauden kolmas sunnuntai';
$lang['third_monday'] = 'Kuukauden kolmas maanantai';
$lang['third_tuesday'] = 'Kuukauden kolmas tiistai';
$lang['third_wednesday'] = 'Kuukauden kolmas keskiviikko';
$lang['third_thursday'] = 'Kuukauden kolmas torstai';
$lang['third_friday'] = 'Kuukauden kolmas perjantai';
$lang['third_saturday'] = 'Kuukauden kolmas lauanatai';
$lang['fourth_sunday'] = 'Kuukauden nelj&auml;s sunnuntai';
$lang['fourth_monday'] = 'Kuukauden nelj&auml;s maanantai';
$lang['fourth_tuesday'] = 'Kuukauden nelj&auml;s tiistai';
$lang['fourth_wednesday'] = 'Kuukauden nelj&auml;s keskiviikko';
$lang['fourth_thursday'] = 'Kuukauden nelj&auml;s torstai';
$lang['fourth_friday'] = 'Kuukauden nelj&auml;s perjantai';
$lang['fourth_saturday'] = 'Kuukauden nelj&auml;s lauantai';
$lang['last_sunday'] = 'Kuukauden viimeinen sunnuntai';
$lang['last_monday'] = 'Kuukauden viimeinen maanantai';
$lang['last_tuesday'] = 'Kuukauden viimeinen tiistai';
$lang['last_wednesday'] = 'Kuukauden viimeinen keskiviikko';
$lang['last_thursday'] = 'Kuukauden viimeinen torstai';
$lang['last_friday'] = 'Kuukauden viimeinen perjantai';
$lang['last_saturday'] = 'Kuukauden viimeinen lauantai';
$lang['error_invalid_recur_monthly_freq'] = 'Virheellinen frekvenssi... et voi m&auml;&auml;ritell&auml; sek&auml; tietty&auml; p&auml;iv&auml;m&auml;&auml;r&auml;&auml; JA muita arvoja';
$lang['searchresulttemplate_addedit'] = 'Lis&auml;&auml;/muokkaa hakutulosten sivupohjaa';
$lang['info_searchresult_template_tab'] = 'Saatavilla oleva lista hakutulosten sivupohjista';
$lang['info_sysdflt_searchresult_template'] = 'J&auml;rjestelm&auml;n oletussivupohja hakutuloksille';
$lang['cal_search_result_templates'] = 'Hakutulokset';
$lang['error_query_failed'] = 'Tietokannasta haku ep&auml;onnistui';
$lang['error_search_invalid_dates'] = 'Virheelliset p&auml;iv&auml;m&auml;&auml;r&auml;t';
$lang['text'] = 'Teksti';
$lang['search'] = 'Haku';
$lang['category'] = 'Kategoria';
$lang['info_sysdflt_search_template'] = 'J&auml;rjestelm&auml;n oletussivupohja hakulomakkeelle';
$lang['searchtemplate_addedit'] = 'Lis&auml;&auml;/muokkaa hakulomakkeen sivupohjaa';
$lang['info_search_template_tab'] = 'Saatavilla oleva lista hakulomakkeen sivupohjista';
$lang['cal_search_form_templates'] = 'Hakulomake';
$lang['max'] = 'max.';
$lang['times'] = 'kertaa';
$lang['on'] = 'on ';
$lang['areyousure_uninstall'] = 'Oletko varma, ett&auml; haluat tehd&auml; t&auml;m&auml;n? Jatkaminen poistaa kaikki t&auml;h&auml;n moduuliin liittyv&auml;t tiedot.';
$lang['post_uninstall'] = 'Kalenterimoduuli on poistettu onnistuneesti ja t&auml;h&auml;n moduuliin liittyv&auml;t sivupohjat on poistettu.';
$lang['fieldupdated'] = 'Kentt&auml; p&auml;ivitetty';
$lang['every'] = 'Joka';
$lang['or'] = 'tai';
$lang['update_children'] = 'P&auml;ivit&auml; alatapahtumat';
$lang['error_nocategory'] = 'Sinun t&auml;ytyy valita ainakin yksi kategoria';
$lang['error_nothingselected'] = 'Ei valittu mit&auml;&auml;n poistettavaksi';
$lang['error_eventoverlap'] = 'Antamasi parametrit toistuvalle tapahtumalla aiheuttaisi p&auml;&auml;llekk&auml;isen tapahtuman.';
$lang['recurs'] = 'Toistuu';
$lang['error_invalid_dates'] = 'Yksi tai useampi annettu arvo oli virheellinen';
$lang['sunday'] = 'sunnuntai';
$lang['monday'] = 'maanantai';
$lang['tuesday'] = 'tiistai';
$lang['wednesday'] = 'keskiviikko';
$lang['thursday'] = 'torstai';
$lang['friday'] = 'perjantai';
$lang['saturday'] = 'lauantai';
$lang['abbr_sunday'] = 'su';
$lang['abbr_monday'] = 'ma';
$lang['abbr_tuesday'] = 'ti';
$lang['abbr_wednesday'] = 'ke';
$lang['abbr_thursday'] = 'to';
$lang['abbr_friday'] = 'pe';
$lang['abbr_saturday'] = 'la';
$lang['weekdays'] = 'Viikonp&auml;iv&auml;t';
$lang['plural_daily'] = 'P&auml;iv&auml;&auml;';
$lang['plural_weekly'] = 'Viikkoa';
$lang['plural_monthly'] = 'Kuukautta';
$lang['plural_yearly'] = 'Vuotta';
$lang['no'] = 'Ei';
$lang['daily'] = 'P&auml;ivitt&auml;in';
$lang['weekly'] = 'Viikottain';
$lang['monthly'] = 'Kuukausittain';
$lang['yearly'] = 'Vuosittain';
$lang['interval'] = 'Toista joka';
$lang['cal_recur_details'] = 'Toistuvan tapahtuman tiedot';
$lang['unlimited'] = 'Rajoittamaton';
$lang['max_recur_events'] = 'Alatapahtumien maksimim&auml;&auml;r&auml;';
$lang['use_to_date'] = 'M&auml;&auml;rittele toinen loppumisp&auml;iv&auml;m&auml;&auml;r&auml;?';
$lang['cancel'] = 'Peruuta';
$lang['show_child_events'] = 'N&auml;yt&auml; alatapahtumat <em>(voi aiheuttaa eritt&auml;in pitk&auml;n listan)</em>';
$lang['n/a'] = 'ei mit&auml;&auml;n';
$lang['recurs_until'] = 'Toista kunnes';
$lang['parent'] = 'P&auml;&auml;tapahtuma';
$lang['recur_period'] = 'Toistumisaika';
$lang['filter'] = 'Suodata';
$lang['start_date'] = 'Alkamisp&auml;iv&auml;m&auml;&auml;r&auml;';
$lang['end_date'] = 'Loppumisp&auml;iv&auml;m&auml;&auml;r&auml;';
$lang['info_upcominglist_template_tab'] = 'Saatavilla oleva lista sivupohjista tuleville tapahtumille';
$lang['upcominglisttemplate_addedit'] = 'Lis&auml;&auml;/muokkaa tulevien tapahtumien n&auml;ytt&auml;misen sivupohjia';
$lang['info_sysdflt_upcominglist_template'] = 'J&auml;rjestelm&auml;n oletussivupohja tulevien tapahtumien n&auml;ytt&auml;miseksi';
$lang['info_list_template_tab'] = 'Saatavilla oleva lista sivupohjista tapahtumien listan n&auml;ytt&auml;miseksi';
$lang['listtemplate_addedit'] = 'Lis&auml;&auml;/muokkaa sivupohjaa tapahtumalistan n&auml;ytt&auml;miseksi';
$lang['info_sysdflt_list_template'] = 'J&auml;rjestelm&auml;n oletussivupohja tapahtumalistalle';
$lang['info_event_template_tab'] = 'Saatavilla olevien sivupohjien lista tapahtumien n&auml;ytt&auml;miseksi';
$lang['eventtemplate_addedit'] = 'Lis&auml;&auml;/muokkaa sivupohjaa tapahtuman n&auml;ytt&auml;miseksi';
$lang['info_sysdflt_event_template'] = 'J&auml;rjestelm&auml;n oletus sivupohja tapahtuman n&auml;ytt&auml;miseksi';
$lang['info_calendar_template_tab'] = 'Saatavilla oleva lista sivupohjista kalenterin n&auml;ytt&auml;miseksi';
$lang['calendartemplate_addedit'] = 'Lis&auml;&auml;/muokkaa sivupohjia kalenterin n&auml;ytt&auml;miseksi';
$lang['info_sysdflt_calendar_template'] = 'J&auml;rjestelm&auml;n oletussivupohja kalenterin n&auml;ytt&auml;miseksi';
$lang['info_sysdflt_template'] = 'T&auml;m&auml; lomake m&auml;&auml;rittelee sivupohjan, jota k&auml;ytet&auml;&auml;n, kun klikkaat &quot;Uusi sivupohja&quot;-painiketta v&auml;lilehdess&auml;. T&auml;m&auml;n arvon muuttamiselle ei ole vaikutusta heti t&auml;m&auml;n moduulin luomille sivuille.';
$lang['eventdesc-EventAdded'] = 'Kutsutaan kalenteritapahtuman lis&auml;&auml;misen j&auml;lkeen';
$lang['eventdesc-EventEdited'] = 'Kutsutaan kalenteritapahtuman muutoksen j&auml;lkeen';
$lang['eventdesc-EventDeleted'] = 'Kutsutaan tapahtuman poiston j&auml;lkeen';
$lang['eventdesc-CategoryAdded'] = 'Kutsutaan kategorian lis&auml;&auml;misen j&auml;lkeen';
$lang['eventdesc-CategoryEdited'] = 'Kutsutaan kategorian muutoksen j&auml;lkeen';
$lang['eventdesc-CategoryDeleted'] = 'Kutsutaan kategorian poiston j&auml;lkeen';
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
$lang['msg_eventadded'] = 'Tapahtuma lis&auml;tty';
$lang['error_noeventname'] = 'Tapahtumalla t&auml;ytyy olla nimi';
$lang['error_noupload'] = 'Virhe: Palvelimelle siirretty&auml; tiedostoa ei l&ouml;ytynyt';
$lang['error_invalidfilename'] = 'Virhe: Tiedoston siirtoa ei voida hyv&auml;ksy&auml;';
$lang['error_problemwithupload'] = 'Virhe: Tiedoston siirrossa tapahtui virhe';
$lang['error_filecopyfailed'] = 'Virhe: Tiedoston kopioinnissa lopulliseen sijaintiinsa tapahtui virhe';
$lang['error_fileexists'] = 'Virhe: Samanniminen tiedosto on jo olemassa';
$lang['error_csvfilenotfound'] = 'Virhe:  CSV-tiedostoa ei voitu l&ouml;yt&auml;&auml;';
$lang['error_cantopenfile'] = 'Virhe: Tiedostoa ei voida avata';
$lang['error_categoryexists'] = 'Virhe: Kategoria nimell&auml; %s on jo olemassa';
$lang['cal_id'] = 'ID';
$lang['cal_calendar'] = 'Kalenteri';
$lang['cal_default_templates'] = 'Oletukset';
$lang['cal_description'] = 'Monipuolinen moduuli, jonka avulla voit n&auml;ytt&auml;&auml; tapahtumia useissa eri muodoissa.';
$lang['cal_addevent'] = 'Lis&auml;&auml; tapahtuma';
$lang['cal_import_events'] = 'Tuo tapahtumia';
$lang['cal_events'] = 'Tapahtumat';
$lang['cal_categories'] = 'Kategoriat';
$lang['cal_calendar_template'] = 'Kalenteri';
$lang['cal_list_template'] = 'Tapahtumalista';
$lang['cal_upcominglist_template'] = 'Tulevat tapahtumat';
$lang['cal_event_template'] = 'Tapahtuma';
$lang['cal_settings'] = 'Asetukset';
$lang['cal_prev'] = '&laquo; Edellinen';
$lang['cal_next'] = 'Seuraava &raquo;';
$lang['cal_categories_updated'] = 'Kategoriat p&auml;ivitetty';
$lang['cal_settings_updated'] = 'Asetukset p&auml;ivitetty';
$lang['cal_add_event'] = 'Lis&auml;&auml; tapahtuma';
$lang['cal_edit'] = 'Muokkaa';
$lang['cal_delete'] = 'Poista';
$lang['cal_areyousure'] = 'Oletko varma?';
$lang['cal_update_template'] = 'P&auml;ivit&auml; pohja';
$lang['cal_sunday'] = 'sunnuntai';
$lang['cal_monday'] = 'maanantai';
$lang['cal_tuesday'] = 'tiistai';
$lang['cal_wednesday'] = 'keskiviikko';
$lang['cal_thursday'] = 'torstai';
$lang['cal_friday'] = 'perjantai';
$lang['cal_saturday'] = 'lauantai';
$lang['cal_sun'] = 'su';
$lang['cal_mon'] = 'ma';
$lang['cal_tues'] = 'ti';
$lang['cal_wed'] = 'ke';
$lang['cal_thurs'] = 'to';
$lang['cal_fri'] = 'pe';
$lang['cal_sat'] = 'la';
$lang['cal_january'] = 'tammikuu';
$lang['cal_february'] = 'helmikuu';
$lang['cal_march'] = 'maaliskuu';
$lang['cal_april'] = 'huhtikuu';
$lang['cal_may'] = 'toukokuu';
$lang['cal_june'] = 'kes&auml;kuu';
$lang['cal_july'] = 'hein&auml;kuu';
$lang['cal_august'] = 'elokuu';
$lang['cal_september'] = 'syyskuu';
$lang['cal_october'] = 'lokakuu';
$lang['cal_november'] = 'marraskuu';
$lang['cal_december'] = 'joulukuu';
$lang['cal_add'] = 'Lis&auml;&auml;';
$lang['cal_update'] = 'P&auml;ivit&auml;';
$lang['cal_event'] = 'Tapahtuma';
$lang['cal_date'] = 'P&auml;iv&auml;';
$lang['cal_summary'] = 'Tiivistelm&auml;';
$lang['cal_details'] = 'Lis&auml;tiedot';
$lang['cal_more'] = 'Lis&auml;&auml; &raquo;';
$lang['cal_return'] = 'Palaa';
$lang['cal_to'] = ' - ';
$lang['cal_past_events'] = 'Menneet tapahtumat';
$lang['cal_upcoming_events'] = 'Tulevat tapahtumat';
$lang['cal_any_category'] = 'Kaikki kategoriat';
$lang['cal_show_only_events_in'] = 'N&auml;yt&auml; vain ';
$lang['cal_filter_by'] = 'Suodata otsikko';
$lang['cal_go'] = 'Mene';
$lang['cal_title'] = 'Otsikko';
$lang['cal_fromdate'] = 'Mist&auml;';
$lang['cal_todate'] = 'Mihin';
$lang['cal_update_categories'] = 'P&auml;ivit&auml; kategoriat';
$lang['cal_language'] = 'Kieli';
$lang['cal_updatesettings'] = 'P&auml;ivit&auml; asetukset';
$lang['cal_use_twelve_hour_clock'] = 'K&auml;ytet&auml;&auml;nk&ouml; pudotusvalikoissa 12-tunnin kelloa?';
$lang['cal_default_category'] = 'Oletuskategoria';
$lang['cal_update_fields'] = 'P&auml;ivit&auml; kent&auml;t';
$lang['force_category'] = 'Pakota v&auml;hint&auml;&auml;n yksi kategoria';
$lang['showpastyears'] = 'Kuinka monta vuotta menneisyyteen on sallittua';
$lang['showfutureyears'] = 'Kuinka monta vuotta tulevaisuuteen on sallittua';
$lang['hidesummary'] = 'Piilotetaanko yhteenvetokentt&auml;';
$lang['hidecontent'] = 'Piilotetaanko sis&auml;lt&ouml;kentt&auml;';
$lang['category_reminder'] = 'Valitse tapahtumalle yksi tai useampia kategoria';
$lang['module_example_stylesheet'] = 'CSS-esimerkki kalenterille';
$lang['error_permission'] = 'Tarvitset asiaankuluvat oikeudet (%s) t&auml;h&auml;n toiminnallisuuteen';
$lang['install_postmessage'] = 'Varmista, ett&auml; lis&auml;&auml;t oikeuden &quot;Muokata kalenteria&quot; niille k&auml;ytt&auml;jille, jotka yll&auml;pit&auml;v&auml;t kalenteritapahtumia.';
$lang['deletetagged'] = 'Poista valitut tapahtumat';
$lang['confirmdeletetagged'] = 'Haluatko varmasti poistaa n&auml;m&auml; tapahtumat?';
$lang['taggeddeleted'] = 'Valitut tapahtumat poistettu';
$lang['templates'] = 'Sivupohjat';
$lang['template_help'] = 'Sivupohjien ohje';
$lang['file_templates'] = 'Tiedostopohjat';
$lang['addtemplate'] = 'Lis&auml;&auml; sivupohja';
$lang['template'] = 'Sivupohja';
$lang['templatenametext'] = 'Sivupohjan nimi';
$lang['edittemplate'] = 'Muokkaa sivupohjaa';
$lang['deletetemplate'] = 'Poista sivupohja';
$lang['newtemplate'] = 'Uuden sivupohjan nimi';
$lang['templatenameexists'] = 'Virhe: Samanniminen sivupohja on jo olemassa. Valitse toinen nimi';
$lang['templateimported'] = 'Pohja tuotiin onnistuneesti tietokantaan ja on nyt asennettu k&auml;ytett&auml;v&auml;ksi.';
$lang['view_default_stylesheet'] = 'N&auml;yt&auml; oletustyylisivu (CSS)';
$lang['filename'] = 'Tiedostonimi';
$lang['importtemplate'] = 'Tuo sivupohja';
$lang['template_deleted'] = 'Sivupohja poistettu onnistuneesti tietokannasta';
$lang['updatetemplatesuccess'] = 'Sivupohja p&auml;ivitetty onnistuneesti';
$lang['updatetemplatefailure'] = 'Sivupohjan p&auml;ivityksess&auml; tapahtui virhe';
$lang['settingssaved'] = 'Asetukset tallennettu onnistuneesti';
$lang['categorydeleted'] = 'Kategoria poistettu';
$lang['categoryupdated'] = 'Kategoria p&auml;ivitetty';
$lang['categoryadded'] = 'Kategoria lis&auml;tty';
$lang['eventdeleted'] = 'Tapahtuma poistettu';
$lang['eventupdated'] = 'Tapahtuma p&auml;ivitetty';
$lang['default_page_error'] = 'Virhe: Sinun t&auml;ytyy asettaa oletussivu, jotta voit k&auml;ytt&auml;&auml; kalenteria sievien URL-osoitteiden ollessa p&auml;&auml;ll&auml;. Mene kalenterin asetuksiin ja valitse oletussivu.';
$lang['time_at'] = 'klo';
$lang['type'] = 'Tyyppi';
$lang['name'] = 'Nimi';
$lang['order'] = 'J&auml;rjestys';
$lang['fieldadded'] = 'Kentt&auml; lis&auml;tty onnistuneesti';
$lang['fielddeleted'] = 'Kentt&auml; poistettu';
$lang['fields'] = 'Mukautetut kent&auml;t';
$lang['textfield'] = 'Tekstikentt&auml;';
$lang['uploadfield'] = 'Tiedostonlatauskentt&auml;';
$lang['description'] = '<p>Calendar is a module for displaying events on your page. When the
		module is installed, a Calendar admin page is added to the content menu
		that will allow you to manage your events.</p>';
$lang['defaultcalendarpage'] = 'Kalenterin sis&auml;lt&auml;v&auml; oletussivu. T&auml;m&auml;n sivun tulee sis&auml;lt&auml;&auml; Smarty-tagi, joka kutsuu kalenteria. T&auml;llainen vaaditaan, jos k&auml;yt&auml;t kaunistettuja URLeja.';
$lang['uploaddirectory'] = 'Hakemisto palvelimelle siirretyille tiedostoille';
$lang['uploadfiletypes'] = 'Sallitut tiedostotyypit';
$lang['uploadunique'] = 'Varmista uniikit tiedostonimet';
$lang['thumb_width'] = 'Pienoiskuvan leveys';
$lang['thumb_height'] = 'Pienoiskuvan korkeus';
$lang['large_width'] = 'T&auml;ysikokoisen kuvan leveys';
$lang['large_height'] = 'T&auml;ysikokoisen kuvan korkeus';
$lang['cal_recur_period'] = 'Toistuu';
$lang['cal_recur_until'] = 'Toistuu kunnes';
$lang['help_template'] = '<p><b>Print Author Name:</b></p>
<code>Author: {$event.authorname}</code>
';
$lang['utma'] = '156861353.1474777839.1362079466.1362079466.1362079466.1';
$lang['utmc'] = '156861353';
$lang['utmz'] = '156861353.1362079466.1.1.utmccn=(direct)|utmcsr=(direct)|utmcmd=(none)';
$lang['utmb'] = '156861353';
?>