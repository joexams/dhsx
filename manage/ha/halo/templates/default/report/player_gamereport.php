<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var reportUrl = {
	0 : "<?php echo INDEX; ?>?m=report&c=data&v=servertotal",
	1 : "<?php echo INDEX; ?>?m=report&c=data&v=channel",
	2 : "<?php echo INDEX; ?>?m=report&c=pay&v=log",
	3 : "<?php echo INDEX; ?>?m=report&c=pay&v=ranking",
	4 : "<?php echo INDEX; ?>?m=report&c=pay&v=consume",
	5 : "<?php echo INDEX; ?>?m=report&c=data&v=lossnewer",
	6 : "<?php echo INDEX; ?>?m=report&c=data&v=asset",
	7 : "<?php echo INDEX; ?>?m=report&c=data&v=item",
	8 : "<?php echo INDEX; ?>?m=report&c=data&v=fate",
	9 : "<?php echo INDEX; ?>?m=report&c=consume&v=total",
	10 : "<?php echo INDEX; ?>?m=report&c=data&v=role",
	11 : "<?php echo INDEX; ?>?m=report&c=data&v=power",
	12 : "<?php echo INDEX; ?>?m=report&c=globalstat&v=mission",
	13 : "<?php echo INDEX; ?>?m=report&c=globalstat&v=level",
	14 : "<?php echo INDEX; ?>?m=report&c=globalstat&v=vip",
	15 : "<?php echo INDEX; ?>?m=report&c=data&v=lossrate",
	16 : "<?php echo INDEX; ?>?m=report&c=data&v=losslevel",
	17 : "<?php echo INDEX; ?>?m=report&c=data&v=stay",
	18 : "<?php echo INDEX; ?>?m=report&c=data&v=townonline"
};
$(function(){
	$('#ajax-link-area').on('click', 'a.link', function(){
		var index = $(this).attr('data');
		$('#ajax-link-area a.active').removeClass('active');
		$(this).addClass('active');
		goReport(index);
	});

	$('#ajax-link-area a.link').eq(0).click();
});

function goReport(index)
{
	Ha.common.ajax(reportUrl[index], 'html', 'cid=<?php echo $data['cid'] ?>&sid=<?php echo $data['sid'] ?>', 'get', 'server_report', function(data){
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
							<a href="javascript:;" class="link active" data="0">日报表</a>
							<a href="javascript:;" class="link" data="1">角色创建/渠道统计</a>
							<a href="javascript:;" class="link" data="2">充值记录</a>
							<a href="javascript:;" class="link" data="3">充值排行</a>
							<a href="javascript:;" class="link" data="4">消费充值比率</a>
							<a href="javascript:;" class="link" data="5">新入游戏流失</a>
							<a href="javascript:;" class="link" data="6">财富统计</a>
							<a href="javascript:;" class="link" data="7">装备统计</a>
							<a href="javascript:;" class="link" data="8">命格统计</a>
							<a href="javascript:;" class="link" data="9">消费统计</a>
							<a href="javascript:;" class="link" data="10">伙伴统计</a>
							<a href="javascript:;" class="link" data="11">体力统计</a>
							<a href="javascript:;" class="link" data="12">关卡报表</a>
						</li>
						<li>
							<a href="javascript:;" class="link" data="13">玩家等级分布</a>
							<a href="javascript:;" class="link" data="14">玩家VIP等级分布</a>
							<a href="javascript:;" class="link" data="15">登录分级流失</a>
							<a href="javascript:;" class="link" data="16">分级流失率</a>
							<a href="javascript:;" class="link" data="17">新用户留存</a>
							<a href="javascript:;" class="link" data="18">当前城镇在线人数</a>
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