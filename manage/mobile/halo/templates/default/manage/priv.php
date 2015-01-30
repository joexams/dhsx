<?php defined('IN_G') or exit('No permission resources.');?>
<script type="text/javascript" src="static/js/jquery.treetable-3.0.0.js"></script>
<script type="text/javascript" src="static/js/jquery.treetable-ajax-persist.js"></script>
<script type="text/javascript" src="static/js/persist-min.js"></script>
<script type="text/javascript">
$(function(){
	$("#menutree").agikiTreeTable({persist: true, persistStoreName: "files", indent: 20});

  $('#post_submit').on('submit', function(e){
    e.preventDefault();
    var obj = $(this);
    var url = '<?php echo INDEX; ?>?m=manage&c=priv&v=setting';
    Ha.common.ajax(url, 'json', obj.serialize(), 'post');
    $.dialog({id:'dialog_priv'}).close();
  });
});

function checknode(obj)
{
  var chk = $("input[type='checkbox']");
  var count = chk.length;
  var num = chk.index(obj);
  var level_top = level_bottom =  chk.eq(num).attr('level')
  for (var i=num; i>=0; i--)
  {
          var le = chk.eq(i).attr('level');
          if(eval(le) < eval(level_top)) 
          {
              chk.eq(i).attr("checked",'checked');
              var level_top = level_top-1;
          }
  }
  for (var j=num+1; j<count; j++)
  {
          var le = chk.eq(j).attr('level');
          if(chk.eq(num).attr("checked")=='checked') {
              if(eval(le) > eval(level_bottom)) chk.eq(j).attr("checked",'checked');
              else if(eval(le) == eval(level_bottom)) break;
          }
          else {
              if(eval(le) > eval(level_bottom)) chk.eq(j).attr("checked",false);
              else if(eval(le) == eval(level_bottom)) break;
          }
  }
}
</script>
<div class="container">
<form name="post_submit" id="post_submit" method="post">
<div class="column cf">
	<table id="menutree" class="treetable">
	<tbody>
		<?php echo $categorys ?>
	</tbody>
	</table>
</div>
<div class="float_footer">    
	<div class="frm_btn"> 
    <input type="hidden" name="doSubmit" value="1">
	<input type="hidden" name="bid" id="bid" value="<?php echo $data['info']['bid'] ?>">
    <input type="submit" id="btnsubmit" class="btn_sbm" value="<?php echo Lang('submit'); ?>">
    <input type="hidden" name="doSubmit" value="1" />
    <input type="hidden" name="roleid" value="<?php echo $roleid; ?>">
    <input type="hidden" name="userid" value="<?php echo $userid; ?>">
	<input type="button" id="btnreset" class="btn_rst" value="<?php echo Lang('close'); ?>" onclick="$.dialog({id:'dialog_priv'}).close();">
	</div>
</div>
</form>
</div>