<h3>{$mod->Lang('cal_import_events')}</h3>
<div class="warning">{$mod->Lang('warn_import')}</div>

{if isset($formstart)}
{$formstart}
<p class="pagetext">{$mod->Lang('info_csv_format')}:</p>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('column_delimiter')}:</p>
  <p class="pageinput">
    <input type="text" name="{$actionid}delimiter" value="{$import_delimiter}" size="3" maxlength="3"/>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('prompt_csv_file')}:</p>
  <p class="pageinput">
    <input type="file" name="{$actionid}csv_file" size="50"/>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext"></p>
  <p class="pageinput">
    <input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>
    <input type="submit" name="{$actionid}cancel" value="{$mod->Lang('cancel')}"/>
  </p>
</div>
{$formend}
{else}
<h4>{$mod->Lang('import_completed')}:</h4>
<p>{$mod->Lang('num_imported',$imported)}</p>
<p>{$mod->Lang('import_lines',$linenum)}</p>
{if isset($errors)}
  <h4 style="color: red;">{$mod->Lang('errors')}:</h4>
  <ul style="border: 1px solid red;">
  {foreach from=$errors item='error'}
    <ul>{$error}</ul>
  {/foreach}
  </ul>
{/if}
{/if}