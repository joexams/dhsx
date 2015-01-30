<?php defined('IN_G') or exit('No permission resources.'); ?>
<?php 

$weburl1 = WEB_URL.INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=player_list&sid='.$data['sid'].'&cid='.$data['cid'].'&title='.$data["title"];
$weburl2 = WEB_URL.INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=faction&sid='.$data['sid'].'&cid='.$data['cid'].'&title='.$data["title"];
$weburl3 = WEB_URL.INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=arena&sid='.$data['sid'].'&cid='.$data['cid'].'&title='.$data["title"];
$weburl4 = WEB_URL.INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=gamelog&sid='.$data['sid'].'&cid='.$data['cid'].'&title='.$data["title"];
$weburl5 = WEB_URL.INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=gamewar&sid='.$data['sid'].'&cid='.$data['cid'].'&title='.$data["title"];
$weburl6 = WEB_URL.INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=gamereport&sid='.$data['sid'].'&cid='.$data['cid'].'&title='.$data["title"];
$url1 = WEB_URL.INDEX.'#app=4&cpp=35&url='.urlencode($weburl1);
$url2 = WEB_URL.INDEX.'#app=4&cpp=35&url='.urlencode($weburl2);
$url3 = WEB_URL.INDEX.'#app=4&cpp=35&url='.urlencode($weburl3);
$url4 = WEB_URL.INDEX.'#app=4&cpp=35&url='.urlencode($weburl4);
$url5 = WEB_URL.INDEX.'#app=4&cpp=35&url='.urlencode($weburl5);
$url6 = WEB_URL.INDEX.'#app=4&cpp=35&url='.urlencode($weburl6);

?>
<script type="text/javascript">
$(function(){
    $('#btnShowTips').on('click', function(){
        if ($(this).parent().hasClass('pop')) {
            $(this).parent().removeClass('pop');
        }else {
            $(this).parent().addClass('pop');
        }
    });
    $('#btnShowTips').on('mouseout', function(){
        $(this).parent().removeClass('pop');
    });
});
</script>
<div class="speed_result">
    <div class="mod_tab_title first_level_tab">
    	<ul>
    		<li<?php echo ROUTE_V=='player_list' ? ' class="current"' : '' ?>><a href="<?php echo $url1 ?>" class="othermenu"><span><?php echo Lang('player_list') ?></span></a></li>
    		<li<?php echo ROUTE_V=='faction' ? ' class="current"' : '' ?>><a href="<?php echo $url2 ?>" rel="<?php echo INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=faction&sid='.$data['sid'].'&cid='.$data['cid'].'' ?>" class="othermenu"><span><?php echo Lang('faction') ?></span></a></li>
    		<li<?php echo ROUTE_V=='arena' ? ' class="current"' : '' ?>><a href="<?php echo $url3 ?>" rel="<?php echo INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=arena&sid='.$data['sid'].'&cid='.$data['cid'].'' ?>" class="othermenu"><span><?php echo Lang('arena') ?></span></a></li>
    		<li<?php echo ROUTE_V=='gamelog' ? ' class="current"' : '' ?>><a href="<?php echo $url4 ?>" rel="<?php echo INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=gamelog&sid='.$data['sid'].'&cid='.$data['cid'].'' ?>" class="othermenu"><span><?php echo Lang('game_log') ?></span></a></li>
    		<li<?php echo ROUTE_V=='gamereport' ? ' class="current"' : '' ?>><a href="<?php echo $url6 ?>" rel="<?php echo INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=gamereport&sid='.$data['sid'].'&cid='.$data['cid'].'' ?>" class="othermenu"><span>数据报表</span></a></li>
    		<li<?php echo ROUTE_V=='gamewar' ? ' class="current"' : '' ?>><a href="<?php echo $url5 ?>" rel="<?php echo INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=gamewar&sid='.$data['sid'].'&cid='.$data['cid'].'' ?>" class="othermenu"><span>群仙会</span></a></li>
    	</ul>
    	<div class="toolbar_opt cf">
    	    <div class="tool_list more">
                <div id="div_pop_table" class="">
                    <button id="btnShowTips" class="btn_thin1"><i class="i_data"></i>切换服务器</button>
                    <div class="pop_wrap" id="wrap_pop_table">
                        <div class="pop_cont" style="width: 600px;">
                            <h5>您希望切换到的服务器：</h5>
                            <ul class="list" id="tblItemList"></ul>
                         </div>
                     </div>
                </div>
    	    </div>
    	</div>
    </div>
</div>