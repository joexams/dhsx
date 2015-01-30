<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
if (typeof dialog != 'undefined'){
	delete dialog;
}
$(function(){
	// $('.dash1').on('click', 'a.othermenu', function(){
	// 	var obj = $(this), url = obj.attr('rel');
	// 	if (url != ''){
	// 		url = url + '&title=<?php echo $data["title"] ?>';
	// 		$('#container').load(url,function(response,status){});
	// 	}
	// });

	$('.b_lib_nav').on('click', 'a.logtype', function(){
		var typeid = $(this).attr('data-id'), key = $(this).attr('data-key');
		if (typeid > 0) {
			$('.hover').removeClass('hover');
			$(this).parent('li').addClass('hover');

			if (key != ''){
				var url = '<?php echo INDEX; ?>?m=report&c=player&v=record&sid=<?php echo $data['sid'] ?>&id=<?php echo $data['id']; ?>&version=<?php echo $data['version']; ?>&key='+key;
				$('#logarea').load(url,function(response,status){});
				$('#logarea').show();
			}
			// recordNum = 0;
			// getlogcoinList(1);
		}
		return false;
	});
});
</script>

<div id="bgwrap">
	<div class="locationbq">
        <div class="locLeft">
            <a href="#app=5&cpp=24&url=<?php echo urlencode(WEB_URL.INDEX.'?m=report&c=player&v=init'); ?>"><?php echo Lang('game_server') ?></a>
            <span>&gt;</span><?php echo $data['title']; ?>
            <span>&gt;</span><?php echo Lang('game_log'); ?>
        </div>
        <div class="logo"></div>
    </div>
	<ul class="dash1">
		<li class="fade_hover"><a href="<?php echo $url1 ?>" rel="<?php echo INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=detail_list&sid='.$data['sid'].'&cid='.$data['cid'].'' ?>" class="othermenu"><span><?php echo Lang('player_list') ?></span></a></li>
		<li class="fade_hover"><a href="<?php echo $url2 ?>" rel="<?php echo INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=faction&sid='.$data['sid'].'&cid='.$data['cid'].'' ?>" class="othermenu"><span><?php echo Lang('faction') ?></span></a></li>
		<li class="fade_hover bubble"><a href="<?php echo $url3 ?>" rel="<?php echo INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=arena&sid='.$data['sid'].'&cid='.$data['cid'].'' ?>" class="othermenu"><span><?php echo Lang('arena') ?></span></a></li>
		<li class="fade_hover selected"><a href="javascript:;"><span><?php echo Lang('game_log'); ?></span></a></li>
		<li class="fade_hover"><a href="<?php echo $url5 ?>" rel="<?php echo INDEX.'?m='.ROUTE_M.'&c='.ROUTE_C.'&v=gamewar&sid='.$data['sid'].'&cid='.$data['cid'].'' ?>" class="othermenu"><span>群仙会</span></a></li>
	</ul>
	<br class="clear">
    <div class="onecolumn">
		<div class="header">
			<h2><?php echo Lang('select_search_type'); ?></h2>
			<ul class="second_level_tab"></ul>
		</div>
	</div>
	<br class="clear">
    <ul class="b_lib_nav">
        <?php foreach($blocklist as $block){ ?>
        <li><a href="javascript:;" class="logtype" data-id="<?php echo $block['bid']; ?>" data-key="<?php echo $block['key']; ?>"><?php echo $block['bname']; ?></a><span></span></li>
        <?php } ?>
    </ul>
	<br class="clear">
	<br class="clear">
	<div class="content" id="logarea" style="display:none;border: 2px solid rgb(230, 121, 28); padding: 10px 0px;">
		
	</div>
</div>
