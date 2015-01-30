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

//	$('#get_search_submit').on('submit', function(e){
//		e.preventDefault();
//
//		$('#selectOrder').click();
//	});

	$('#selectOrder').on('click', 'a.logtype', function(){
		var typeid = $(this).attr('data-id'), key = $(this).attr('data-key');
		if (typeid > 0) {
			$('.active', $('#selectOrder')).removeClass('active');
			$(this).addClass('active');

			if (key != ''){
		            var url = '<?php echo INDEX; ?>?m=report&c=player&v=record';
		            var queryData = $('#get_search_submit').serialize()+'&sid=<?php echo $data['sid'] ?>&version=<?php echo $data['version']; ?>&key='+key;
		            $('.active', $('#selectOrder')).removeClass('active');
		            $(this).addClass('active');
					Ha.common.ajax(url, 'html', queryData, 'get', 'container', function(data){
						$('#infoarea').empty().html(data).show();
					}, 1);
			}
			// recordNum = 0;
			// getlogcoinList(1);
		}
		return false;
	});
});
</script>

<h2><span id="tt"><?php echo Lang('玩家数据'); ?>：
<a href="#app=4&cpp=35&url=<?php echo urlencode(WEB_URL.INDEX.'?m=report&c=player&v=init'); ?>"><?php echo Lang('game_server') ?></a>
<span>&gt;</span><?php echo $data['title']; ?>
<span>&gt;</span><?php echo Lang('game_log'); ?></span></h2>
<div class="container" id="container">
	<?php include template('report', 'player_top'); ?>
	
	<div class="toolbar cf">
		<div class="tool_date cf">
		<form id="get_search_submit" action="" method="get" name="form">
		<div class="title cf">	
			<div class="tool_group">
				<label><?php echo Lang('player'); ?>：<input type="text" class="ipt_txt_l" name="playername"></label>
				<!--<input type="submit" class="btn_sbm" value="查询" id="query"> 
				<input type="reset" class="btn_rst" value="重置" id="reset">-->
			</div>
		</div>
        <div class="control">
        	<div class="frm_cont">
				<ul>
					<li name="condition" id="selectOrder">
						<?php foreach($menulist_sub as $block){ ?>
					    <a href="javascript:;" class="logtype" data-id="<?php echo $block['mid']; ?>" data-key="<?php  $dataarr = explode("=",$block['data']); echo $dataarr[1]?$dataarr[1]:'pay'; ?>"><?php echo $block['mname']; ?></a>
					    <?php } ?>
					</li>
				</ul>
			</div>
		</div>
		</form>
		</div>
    </div>

	<div id="infoarea">

	</div>
</div>