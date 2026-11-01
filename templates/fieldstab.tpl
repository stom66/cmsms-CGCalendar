<script>
function update_field_row($row) {
  var type = $('select.fld_type',$row).val()
  if( type != 0 && type != 2 ) {
    $('.fld_searchable',$row).hide()
  } else {
    $('.fld_searchable',$row).show()
  }

  if( type != 6 ) {
    $('.fld_btn_ddopts',$row).hide()
  } else {
    $('.fld_btn_ddopts',$row).show()
  }

  if( type != 2 ) {
    $('.fld_wysiwyg',$row).hide()
  } else {
    $('.fld_wysiwyg',$row).show()
  }

  if( type != 8 ) {
    $('.fld_udtlist',$row).hide()
  } else {
    $('.fld_udtlist',$row).show()
  }
}
$(function(){
  $('#cgcal_fields > tbody').sortable()
  $('#cgcal_fields > tbody tr').each(function(idx,el){
    if( $(el).attr('id') == 'fld_skeleton' ) return
    update_field_row(el)
  })
  $(document).on('change','select.fld_type',function(){
    update_field_row($(this).closest('tr'))
  })
  $(document).on('change','select.fld_udtlist',function(){
     var val = $(this).val()
     var row = $(this).closest('tr')
     $('.fld_ddopts',row).val(val)
  })
  $(document).on('click','#fld_add',function(ev){
    ev.preventDefault();
    var row = $('tr#fld_skeleton').clone()
    update_field_row(row)
    $(row).show().removeAttr('id').appendTo('#cgcal_fields > tbody')
  })
  $(document).on('click','#fld_update',function(){
    $('#cgcal_fields > tbody tr').each(function(idx,$el){
      var oldname = $('input.fld_id',$el).val()
      var newname = $('input.fld_name',$el).val()
      if( oldname != '' && newname == '' ) {
        alert('got emptied name')
      }
    });
    $('tr#field_skeleton').remove()
  })
  $(document).on('click','.fld_btn_ddopts',function(ev){
     ev.preventDefault()
     var row = $(this).closest('tr')
     var text = $('.fld_ddopts',row).val()
     $('#input_ddopts').val(text)
     $('#dlg_edit_ddopts').dialog({
        modal: true,
	width: 'auto',
	buttons: [
           {
	      text: '{$mod->Lang('ok')}',
	      click: function() {
	         $('.fld_ddopts',row).val($('#input_ddopts').val());
		 $(this).dialog('close');
	      },
	   },
           {
	      text: '{$mod->Lang('cancel')}',
	      click: function(){
		 $(this).dialog('close');
	      }
	   },
	]
     })
  })
  $(document).on('click', '.fld_delete', function(ev){
    if( !confirm('{$mod->Lang('cal_areyousure')}') ) return false
    $(this).closest('tr').remove()
  })
})
</script>

<div class="information">{$mod->Lang('info_customfields_tab')}</div>
{$formstart}

<table class="pagetable" id="cgcal_fields">
  <thead>
    <th>{$nametext}</th>
    <th>{$typetext}</th>
    <th>{$mod->Lang('searchable')}</th>
    <th>{$mod->Lang('wysiwyg')}</th>
    <th class="pageicon"></th>
  </thead>
  <tbody>
    {if isset($fields)}
    {foreach from=$fields item='entry'}
      <tr>
        <td>
          <input class="fld_id" type="hidden" name="{$actionid}field_ids[]" value="{$entry.field_name}"/>
          <input class="fld_name" type="text" name="{$actionid}field_names[]" value="{$entry.field_name}" maxlength="255"/>
        </td>
        <td>
	   <select class="fld_type" name="{$actionid}field_types[]">{html_options options=$types selected=$entry.field_type}</select>
	   <input class="fld_ddopts" type="hidden" name="{$actionid}field_ddopts[]" value="{$entry.field_extra.ddopts|default:''}"/>
           <button class="fld_btn_ddopts" title="{$mod->Lang('title_edit_ddopts')}">...</button>
	   <select class="fld_udtlist">{html_options options=$usertags selected=$entry.field_extra.ddopts}</select>
        </td>
        <td><select class="fld_searchable" name="{$actionid}field_searchable[]">{cge_yesno_options selected=$entry.field_extra.searchable}</select></td>
        <td><select class="fld_wysiwyg" name="{$actionid}field_wysiwyg[]">{cge_yesno_options selected=$entry.field_extra.wysiwyg}</select></td>
	<td>{cgimage image='icons/system/delete.gif' class="fld_delete"}</td>
      </tr>
    {/foreach}
    {/if}
    <tr id="fld_skeleton" style="display: none;">
      <td>
        <input class="fld_id" name="{$actionid}field_ids[]" type="hidden" value=""/>
        <input class="fld_name" name="{$actionid}field_names[]" type="text" value="" maxlength="255"/>
      </td>
      <td>
        <select class="fld_type" name="{$actionid}field_types[]">{html_options options=$types}</select>
        <input class="fld_ddopts" type="hidden" name="{$actionid}field_ddopts[]" value="{$entry.field_ddopts|default:''}"/>
        <button class="fld_btn_ddopts" title="{$mod->Lang('title_edit_ddopts')}">...</button>
	<select class="fld_udtlist">{html_options options=$usertags}</select>
      </td>
      <td>
        <select class="fld_searchable" name="{$actionid}field_searchable[]">{cge_yesno_options}</select>
      </td>
      <td>
        <select class="fld_wysiwyg" name="{$actionid}field_wysiwyg[]">{cge_yesno_options}</select>
      </td>
      <td>{cgimage image='icons/system/delete.gif' class="fld_delete"}</td>
    </tr>
  </tbody>
</table>
<div class="pageoverflow">
  <p class="pagetext"></p>
  <p class="pageinput">
    <input type="submit" id="fld_add" value="{$mod->Lang('add_field')}"/>
    <input type="submit" id="fld_update" name="{$actionid}submit" value="{$mod->Lang('cal_update_fields')}"/>
  </p>
</div>
<div id="dlg_edit_ddopts" title="{$mod->Lang('title_edit_ddopts')}" style="display: none;">
  <div class="pageoverflow">
    <p class="pagetext">* {$mod->Lang('prompt_ddopts')}:</p>
    <p class="pageinput">
      <textarea id="input_ddopts" rows="8" cols="30"></textarea>
      <br/>{$mod->Lang('info_ddopts')}
    </p>
  </div>
</div>
{$formend}