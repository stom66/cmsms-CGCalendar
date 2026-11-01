{cgjs_require cssfile='lib/colorPicker/colorPicker.css'}
{cgjs_require jsfile='lib/colorPicker/jquery.colorPicker.min.js'}
{cgjs_add}
$(document).ready(function(){
  function update_color() {
    var bgcolor = $('#bgcolor').val();
    var fgcolor = $('#fgcolor').val();
    if( bgcolor && fgcolor ) {
      $('.testlabel').css('background-color', bgcolor ).css('color', fgcolor);
    }
  }
  update_color();
  $('#bgcolor,#fgcolor').change(function(){
    update_color();
  })
  $('input.colorPicker').colorPicker();
  $(document).on('click','#cancel',function(){
    $(':input').removeAttr('required');
  })
});
{/cgjs_add}

{if $category.id > 0}
<h3>{$mod->Lang('edit_category')}</h3>
{else}
<h3>{$mod->Lang('add_category')}</h3>
{/if}

{$formstart}
<div class="pageoverflow">
  <p class="pageinput">
    <input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>
    <input id="cancel" type="submit" name="{$actionid}cancel" value="{$mod->Lang('cancel')}"/>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext">*{$mod->Lang('name')}:</p>
  <p class="pageinput">
    <input type="text" name="{$actionid}name" value="{$category.name|default:''}" size="40" required/>
    &nbsp;<span class="testlabel" style="padding: 5px;">Sample</span>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext">*{$mod->Lang('bgcolor')}:</p>
  <p class="pageinput">
    <input id="bgcolor" type="text" class="colorPicker" name="{$actionid}bgcolor" value="{$category.bgcolor|default:''}" size="7"/>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext">*{$mod->Lang('fgcolor')}:</p>
  <p class="pageinput">
    <input id="fgcolor" type="text" class="colorPicker" name="{$actionid}fgcolor" value="{$category.fgcolor|default:''}" size="7"/>
  </p>
</div>
{$formend}