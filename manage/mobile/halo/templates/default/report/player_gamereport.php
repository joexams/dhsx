<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function(){
	$('#ajax-link-area').on('click', 'a.link', function(){
		var url = $(this).attr('data');
		$('#ajax-link-area a.active').removeClass('active');
		$(this).addClass('active');
		goReport(url);
	});

	$('#ajax-link-area a.link').eq(0).click();
});

function goReport(url)
{
	Ha.common.ajax('<?php echo INDEX; ?>'+url, 'html', 'cid=<?php echo $data['cid'] ?>&sid=<?php echo $data['sid'] ?>', 'get', 'server_report', function(data){
		$('#server_report').html(data);
	}, 1);
}
</script>
<h2><span id="tt"><?php echo Lang('玩家数据'); ?>：
<a href="#app=4&cpp=35&url=<?php echo urlencode(WEB_URL.INDEX.'?m=report&c=player&v=init'); ?>"><?php echo Lang('game_server') ?></a>
<span>&gt;</span><?php echo $data['title']; ?>
<span>&gt;</span><?php echo Lang('数据报表'); ?></span></h2>
<div class="container">
	<?php include template('report', 'player_top'); ?>
	<div class="toolbar">
		<div class="tool_date cf">
			<div class="control cf" id="ajax-link-area">
				<div class="frm_cont">
					<ul>
						<li>
						<?php 
						foreach ($menulist_sub as $key => $value){							
						?>
						<a href="javascript:;" class="link <?php echo ROUTE_V==$value['v'] ? ' active' : '' ?>" data="?m=<?php echo $value['m']?>&c=<?php echo $value['c']?>&v=<?php echo $value['v']?>"><?php echo $value['mname']?></a>
						<?php
						}
						?>
						</li>
					</ul>
				</div>
			</div>
	</div>		
</div>

<div>
<style type="text/css">
#server_report .container{
	padding: 0px;
}
</style>
<div id="server_report">
    
</div>