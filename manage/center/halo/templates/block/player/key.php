<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
var typeflag = 0, extendlist;
function getkeyList(){
	var query = "<?php echo INDEX; ?>?m=server&c=get&v=player_info",
	data = "sid=<?php echo $data['sid'] ?>&id=<?php echo $data['id'] ?>&key=key";
		$.ajax({
			dataType: "json",
			url: query,
			data: data,
			success: function(data){
				if (data.status == 1){
					$('#info_list').html(data.msg);
				}else {
					if (data.type != undefined){
						extendlist = data.type;
					}
					var str = '';
						var selected = '';
					var tagkey = '';
					if (data.count > 0){
						tagkey = 'key_functionlock';
						$('#'+tagkey).html(data.list.function);
						tagkey = 'key_function_playedlock';
						$('#'+tagkey).html(data.list.function_played);
						
						tagkey = 'key_mission_videolock';
						$('#'+tagkey).val(data.list.mission_video);
						tagkey = 'key_upgrade_queue_numberlock';
						$('#'+tagkey).val(data.list.upgrade_queue_number);
			
						tagkey = '';
						for(var key in extendlist) {
							str ='';
							selected = '';
							tagkey = 'key_'+key+'lock';
							if (typeof data.list[key] != 'undefined') {
								$('#'+tagkey).html(data.list[key]);
								for(var skey in extendlist[key]) {
if (key == 'quest') {
									selected = extendlist[key][skey].lock == data.list[key] ? 'selected="selected"' : '';
									str += '<option value="'+extendlist[key][skey].lock+'" '+selected+'>'+extendlist[key][skey].title+'</option>';
}else if (key == 'pack_grid' || key == 'role_equi' || key == 'warehouse') {
									selected = extendlist[key][skey].unlock_level == data.list[key] ? 'selected="selected"' : '';
									str += '<option value="'+extendlist[key][skey].unlock_level+'" '+selected+'>'+extendlist[key][skey].name+'</option>';

}else {
									selected = extendlist[key][skey].lock == data.list[key] ? 'selected="selected"' : '';
									str += '<option value="'+extendlist[key][skey].lock+'" '+selected+'>'+extendlist[key][skey].name+'</option>';

}
								}
								$('#key_'+key+'list').append(str);

							}
						}
					}
				}
			}
		});
	return false;
}

$(document).ready(function(){
	if (dialog != undefined){
		dialog.title("<?php echo Lang('player').urldecode($_GET['title']); ?>"+'->玩家权值');
	}
	getkeyList();

	extendlist = null;
});
</script>
<div id="bgwrap">
	<div class="content">
		<table class="global" width="100%" cellpadding="0" cellspacing="0">
			  <tr>
			    <th colspan="2">解锁权值情况</th>
			  </tr>
			  <tr>
			    <td>城镇</td>
				<td>
				<select name="town" id="key_townlist">
				 <option class="select">选择</option>
				</select>
				<span class="graytitle" id="key_townlock"></span>
				</td>
			  </tr>
			  <tr>
			    <td>任务</td>
				<td>
				<select name="quest" id="key_questlist">
				 <option class="select">选择</option>
				</select>
				<span class="graytitle" id="key_questlock"></span>
				</td>	
			  </tr>
			  <tr>	
			    <td>副本</td>
				<td>
				<select name="section" id="key_sectionlist">
				 <option class="select">选择</option>
				</select>
				<span class="graytitle" id="key_sectionlock"></span>
				</td>	
			  </tr>
			  <tr>	
			    <td>剧情</td>
				<td>
				<select name="mission" id="key_missionlist">
				 <option class="select">选择</option>
				</select>
				<span class="graytitle" id="key_missionlock"></span>
				</td>	
			  </tr>
			  
			  <tr>	
			    <td>背包格子</td>
				<td>
				<select name="pack_grid" id="key_pack_gridlist>
				 <option class="select">选择</option>
				</select>
				<span class="graytitle" id="key_pack_gridlock"></span>
				</td>	
			  </tr>
			  <tr>	
			    <td>装备位置</td>
				<td>
				<select name="role_equi" id="key_role_equilist">
				 <option class="select">选择</option>
				</select>
				<span class="graytitle" id="key_role_equilock"></span>
				</td>	
			  </tr>
			  <tr>	
			    <td>仓库格子</td>
				<td>
				<select name="warehouse" id="key_warehouselist">
				 <option class="select">选择</option>
				</select>
				<span class="graytitle" id="key_warehouselock"></span>
				</td>	
			  </tr>
			  
			  <tr>	
			    <td>功能开放</td>
				<td>
				<select name="function" id="key_game_functionlist">
				 <option class="select">选择</option>
				</select>
				<span class="graytitle" id="key_functionlock"></span>
				</td>	
			  </tr> 
			  <tr>	
			    <td>功能开放播放</td>
				<td>
				<select name="function_played" >
				 <option class="select">选择</option>
				</select>
				<span class="graytitle" id="key_function_playedlock"></span>
				</td>	
			  </tr>  
			  <tr>	
			    <td>招募等级</td>
				<td>
				<select name="role" id="key_rolelist">
				 <option class="select">选择</option>
				</select>
				<span class="graytitle" id="key_rolelock"></span>
				</td>	
			  </tr>      
			  <tr>	
			    <td>剧情动画播放锁</td>
				<td><input name="mission_video" id="key_mission_videolock" type="text" value="-"  size="10"/></td>	
			  </tr>    
			  <tr>	
			    <td>附增的强化队列个数</td>
				<td><input name="upgrade_queue_number" id="key_upgrade_queue_numberlock" type="text" value="-"  size="10"/></td>	
			  </tr>  
		</table>
	</div>
</div>
