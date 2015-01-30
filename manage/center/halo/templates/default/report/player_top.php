<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
    $('#btnShowTips').on('mouseover', function(){
    	$(this).parent().addClass('pop');
    });
    $('#wrap_common').on('click', function(){
    	$("#div_pop_table").removeClass('pop');
    });
    $('#other_serverlisttpl').tmpl(getServerByCid(<?php echo $data['cid'] ?>)).appendTo('#tblItemList');
});
</script>
<script type="text/template" id="other_serverlisttpl">
<li><a href="#app=4&cpp=35&url=${encodeurl('<?php echo ROUTE_M; ?>', '<?php echo ROUTE_V; ?>', '<?php echo ROUTE_C ?>','&sid=')}${sid}%26cid%3D${cid}%26title%3D${name}" class="playerinfo" data-cid="${cid}" data-sid="${sid}"><span>${name}</span></a>
</script>
<div class="speed_result">
    <div class="mod_tab_title first_level_tab">
    	<ul>
    	<?php 
    		foreach ($menulist as $key => $value){
    		$weburl = WEB_URL.INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v='.$value['v'].'&sid='.$data['sid'].'&cid='.$data['cid'].'&title='.$data["title"];
    		$url = WEB_URL.INDEX.'#app=4&cpp=35&url='.urlencode($weburl);
    	?>
			  <li<?php echo ROUTE_V==$value['v'] ? ' class="current"' : '' ?>><a href="<?php echo $url ?>" class="othermenu"><span><?php echo $value['mname'] ?></span></a></li>  	
    	<?php
    		}
    	?>
    		
    	</ul>
    	<div class="toolbar_opt cf">
    	    <div class="tool_list more">
                <div id="div_pop_table" class="">
                    <button id="btnShowTips" class="btn_thin1"><i class="i_data"></i>切换服务器</button>
                    <div class="pop_wrap" id="wrap_pop_table">
                        <div class="pop_cont" style="width: 600px;">
                            <h5>您希望切换到的服务器：</h5>
                            <ul class="list" id="tblItemList">
                            </ul>
                         </div>
                     </div>
                </div>
    	    </div>
    	</div>
    </div>
</div>