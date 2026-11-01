{cgjs_require jsfile='lib/colorPicker/jquery.colorPicker.min.js'}
{cgjs_add}
$(function(){
    $(':input.colorPicker').colorPicker();
});
{/cgjs_add}

{$startform}
<div class="pageoverflow">
  <p class="pageinput">{$submit}</p>
</div>

<fieldset>
  <legend>{$mod->Lang('prompt_general_settings')}:</legend>
  <div class="pageoverflow">
    <p class="pagetext">{$mod->Lang('prompt_friendlyname')}:</p>
    <p class="pageinput">
      <input type="text" name="{$actionid}friendlyname" size="40" maxlength="255" value="{$friendlyname}"/>
      <br/>{$mod->Lang('info_friendlyname')}
    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">{$defaultcalendarpage_text}:</p>
    <p class="pageinput">{$defaultcalendarpage_input}</p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">{$mod->Lang('dflt_status_newevents')}:</p>
    <p class="pageinput">
      {$tmp=['D'=>$mod->Lang('draft'), 'P'=>$mod->Lang('published')]}
      <select name="{$actionid}dflt_status">
        {html_options options=$tmp selected=$dflt_status}
      </select>
    </p>
  </div>
  <div class="pageoverflow">
    <p class="pagetext">{$mod->Lang('dflt_urlprefix')}:</p>
    <p class="pageinput">
      <input type="text" size="20" name="{$actionid}url_prefix" value="{$url_prefix}" />
    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">{$mod->Lang('dflt_colors')}:</p>
    <p class="pageinput">
      <label><input type="text" class="colorPicker" size="7" name="{$actionid}dflt_fgcolor" value="{$dflt_fgcolor}" /> {$mod->Lang('fgcolor')}</label>
      <label><input type="text" class="colorPicker" size="7" name="{$actionid}dflt_bgcolor" value="{$dflt_bgcolor}" /> {$mod->Lang('bgcolor')}</label>
    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">{$mod->Lang('prompt_ical_holidays')}:</p>
    <p class="pageinput">
      <input type="text" name="{$actionid}ical_holidays" value="{$ical_holidays}" size="80"/>
      <br/>{$mod->Lang('info_ical_holidays')}
    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">{$mod->Lang('holiday_colors')}:</p>
    <p class="pageinput">
      <label><input type="text" class="colorPicker" size="7" name="{$actionid}holiday_fgcolor" value="{$holiday_fgcolor}" /> {$mod->Lang('fgcolor')}</label>
      <label><input type="text" class="colorPicker" size="7" name="{$actionid}holiday_bgcolor" value="{$holiday_bgcolor}" /> {$mod->Lang('bgcolor')}</label>
    </p>
  </div>
</fieldset>

<fieldset>
  <legend>{$mod->Lang('prompt_editing_settings')}:</legend>
  <div class="pageoverflow">
    <p class="pagetext">{$twelvehourtext}:</p>
    <p class="pageinput">{$twelvehourinput}</p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">{$mod->Lang('prompt_firstdayofweek')}:</p>
    <p class="pageinput">
      <select name="{$actionid}firstdayofweek">
      {html_options options=$dayoptions selected=$firstdayofweek}
      </select>
    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">{$mod->Lang('prompt_overlap_policy')}:</p>
    <p class="pageinput">
      <select name="{$actionid}overlap_policy">
      {html_options options=$overlap_policies selected=$overlap_policy}
      </select>
    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">{$mod->Lang('prompt_overlap_action')}:</p>
    <p class="pageinput">
      <select name="{$actionid}overlap_action">
      {html_options options=$overlap_actions selected=$overlap_action}
      </select>
    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">{$forcecattext}:</p>
    <p class="pageinput">{$forcecatinput}</p>
  </div>

  {if isset($defcattext)}
  <div class="pageoverflow">
    <p class="pagetext">{$defcattext}:</p>
    <p class="pageinput">{$defcatinput}</p>
  </div>
  {/if}

  <div class="pageoverflow">
    <p class="pagetext">{$pastyearstext}:</p>
    <p class="pageinput">{$pastyearsinput}</p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">{$futureyearstext}:</p>
    <p class="pageinput">{$futureyearsinput}</p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">{$hidesummarytext}:</p>
    <p class="pageinput">{$hidesummaryinput}</p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">{$hidecontenttext}:</p>
    <p class="pageinput">{$hidecontentinput}</p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">{$mod->Lang('dflt_alldayevent')}:</p>
    <p class="pageinput">
      {cge_yesno_options prefix=$actionid name='dflt_alldayevent' selected=$dflt_alldayevent}
    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">{$mod->Lang('dflt_starttime')}:</p>
    <p class="pageinput">
      {capture assign='tmp'}{$actionid}dflt_starttime_{/capture}
      {html_select_time prefix=$tmp display_seconds=false time=$dflt_starttime minute_interval=15}
      <br/>
      {$mod->Lang('info_dflt_starttime')}
    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">{$uploaddirectory_text}:</p>
    <p class="pageinput">{$uploaddirectory_input}</p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">{$uploadfiletypes_text}:</p>
    <p class="pageinput">{$uploadfiletypes_input}</p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">{$uploadunique_text}:</p>
    <p class="pageinput">{$uploadunique_input}</p>
  </div>
</fieldset>

{if isset($grouplist)}
<fieldset>
  <legend>{$mod->Lang('prompt_feedit_settings')}:</legend>

  <div class="pageoverflow">
    <p class="pagetext">{$mod->lang('prompt_feedit_group')}:</p>
    <p class="pageinput">
      <select name="{$actionid}frontend_group">
        {html_options options=$grouplist selected=$frontend_group}
      </select>
    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">{$mod->lang('prompt_feedit_mastergroup')}:</p>
    <p class="pageinput">
      <select name="{$actionid}frontend_mastergroup">
        {html_options options=$grouplist selected=$frontend_mastergroup}
      </select>
    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">{$mod->Lang('prompt_feedit_page')}:</p>
    <p class="pagetext">
      <input type="text" name="{$actionid}frontend_redirectpage" size="50" maxlength="255" value="{$frontend_redirectpage}"/>
    </p>
  </div>
</fieldset>
{/if}
{$endform}