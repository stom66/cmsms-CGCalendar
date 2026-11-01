<h3>{$mod->Lang('cal_export_events')}</h3>
<div class="information">{$mod->Lang('info_export_events')}</div>

{form_start}
<div class="c_full cf">
  <label class="grid_2">{$mod->Lang('export_datesel')}</label>
  <div class="grid_9">
    <label class="grid_12"><input type="radio" name="{$actionid}datesel" value="created" {if $settings->datesel=='created'}checked{/if}> {$mod->Lang('export_datesel_created')}</label>
    <label class="grid_12"><input type="radio" name="{$actionid}datesel" value="modified" {if $settings->datesel=='modified'}checked{/if}> {$mod->Lang('export_datesel_modified')}</label>
    <label class="grid_12"><input type="radio" name="{$actionid}datesel" value="start" {if $settings->datesel=='start'}checked{/if}> {$mod->Lang('export_datesel_start')}</label>
  </div>
</div>
<div class="c_full cf">
    <label class="grid_2">{$mod->Lang('export_from_date')}:</label>
    <div class="grid_9">
        <input class="grid_12" type="date" placeholder="YYYY-MM-DD" name="{$actionid}fromdate" value="{$settings->fromdate|date_format:'%Y-%m-%d'}"/>
    </div>
</div>
<div class="c_full cf">
    <label class="grid_2">{$mod->Lang('export_category')}:</label>
    <div class="grid_9">
        <select name="{$actionid}category" class="grid_12">
           <option value="">{$mod->Lang('any')}</option>
           {html_options options=$categories selected=$settings->category}
        </select>
    </div>
</div>
<div class="c_full cf">
    <label class="grid_2">*{$mod->Lang('export_delimiter')}:</label>
    <div class="grid_9">
        <input class="grid_12" type="text" placeholder="delimiter" name="{$actionid}delimiter" value="{$settings->delimiter}" required maxlength="2"/>
    </div>
</div>

<hr/>
<div class="c_full cf">
  <input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>
  <input type="submit" name="{$actionid}cancel" value="{$mod->Lang('cancel')}" formnovalidate/>
</div>
{form_end}