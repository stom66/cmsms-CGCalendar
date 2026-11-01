{* delete event template *}
<h3>Delete Event</h3>

{if $event.event_recur_period != '' && $event.event_recur_period != 'none'}
  <div class="row warning">
    <p>{$mod->Lang('deleteevent_warn_children')}</p>
  </div>
{elseif $event.event_parent_id > 0}
  <div class="row warning">
    <p>{$mod->Lang('deleteevent_warn_child')}</p>
  </div>
{/if}

{$formstart}
<fieldset>
  <legend>{$mod->Lang('cal_details')}:</legend>
  <div class="row">
    <p class="row_prompt">{$mod->Lang('cal_title')}:<p>
    <p class="row_input">{$event.event_title}</p>
  </div>
  <div class="row">
    <p class="row_prompt">{$mod->Lang('cal_summary')}:<p>
    <p class="row_input">{$event.event_summary}</p>
  </div>
</fieldset>

<div class="row">
  <input type="submit" name="{$actionid}cal_submit" value="{$mod->Lang('delete')}"/>
  <input type="submit" name="{$actionid}cal_cancel" value="{$mod->Lang('cancel')}"/>
</div>
{$formend}