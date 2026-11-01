{if isset($categories) && count($categories)}
<table class="pagetable">
  <thead>
    <tr>
      <th>{$mod->Lang('name')}</th>
      <th></th>
      <th class="pageicon"></th>
      <th class="pageicon"></th>
    </tr>
  </thead>
  <tbody id="categorylist">
  {foreach $categories as $cat}
    {module_action_link action=admin_addedit_category catid=$cat.category_id urlonly=1 assign='edit_url'}
    {module_action_link action=admin_delete_category catid=$cat.category_id urlonly=1 assign='delete_url'}
    <tr id="cat_{$cat.category_id}">
      <td>
        <a href="{$edit_url}" title="{$mod->Lang('edit')}">{$cat.category_name}</a>
      </td>
      <td>
         <span style="padding: 2px; {if $cat.category_bgcolor}background-color: {$cat.category_bgcolor};{/if} {if $cat.category_fgcolor}color: {$cat.category_fgcolor};{/if}">Sample Text</span>
      </td>
      <td><a href="{$edit_url}" title="{$mod->Lang('edit')}">{cgimage image='icons/system/edit.gif' class='systemicon' alt=$mod->Lang('edit')}</a></td>
      <td><a href="{$delete_url}" title="{$mod->Lang('delete')}">{cgimage image='icons/system/delete.gif' class='systemicon' alt=$mod->Lang('delete')}</a></td>
    </tr>
  {/foreach}
  </tbody>
</table>
{/if}