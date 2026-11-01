<script type="text/javascript">
$(document).ready(function(){
  var category_refresh_url = '{module_action_link action='admin_ajax_getcategories' jsfriendly=1}&showtemplate=false';
  $('#categorytable').load(category_refresh_url);

  $('#reorder_mode_button').click(function(){
    $('#category_options').hide();
    $('#reorder_mode').show();
    $('tbody#categorylist').sortable({
      items: '> tr',
      cursor: 'move'
    })
  });
  $('#reorder_mode_cancel').click(function(){
    $('#reorder_mode').hide();
    $('#category_options').show();
    $('tbody#categorylist').sortable('destroy');
    // ajax call to restore the category list
    $('#categorytable').load(category_refresh_url);
  });
  $('#reorder_mode_submit').click(function(){
    $('#reorder_mode').hide();
    $('#category_options').show();
    // ajax call to submit the category list
    $.ajax({
      type: 'POST',
      url: '{module_action_link action=admin_ajax_ordercategories jsfriendly=1}&showtemplate=false',
      data: $('tbody#categorylist').sortable('serialize'),
      success: function( data ) {
        // ajax call to restore the category list
        $('#categorytable').load(category_refresh_url);
      },
      error: function(jqXHR, textStatus, errorThrown) {
        alert('fail '+textStatus);
        // ajax call to restore the category list
        $('#categorytable').load(category_refresh_url);
      }
    });
  });
});
</script>

<div id="category_options" class="pageoptions">
  <a href="{module_action_link action='admin_addedit_category' urlonly=1}">{admin_icon icon='newobject.gif' alt=$mod->Lang('add_category') class='systemicon'} {$mod->Lang('add_category')}</a>
  <a id="reorder_mode_button">{admin_icon icon='reorder.gif' alt=$mod->Lang('reorder')} {$mod->Lang('reorder')}</a>
</div>

<div id="reorder_mode" style="display: none;">
  <div class="information" style="display: block;">{$mod->Lang('info_reorder_categories')}</div>
  <a id="reorder_mode_cancel">{admin_icon icon='false.gif' alt=$mod->Lang('cancel')} {$mod->Lang('cancel')}</a>&nbsp;
  <a id="reorder_mode_submit">{admin_icon icon='true.gif' alt=$mod->Lang('submit')} {$mod->Lang('submit')}</a>
</div>

<div id="categorytable"></div>
