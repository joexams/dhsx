<?php 
if(!defined('IN_UCTIME')){	exit('Access Denied');}
switch (ReqStr('action')){
	
	case 'day': 
		day_list(); 
		break;
	case 'day_setting':
		day_setting();
		break;

	case 'TeamBuyingGiftType': TeamBuyingGiftType(); break;
	case 'TeamBuyinginfo': TeamBuyinginfo(); break;
	case 'TeamBuyingAwardType': TeamBuyingAwardType(); break;
	case 'QuizGameQuestion': QuizGameQuestion(); break;

	case 'SetQuizGameQuestion': SetQuizGameQuestion(); break;
	case 'SetTeamBuyingGiftType': SetTeamBuyingGiftType(); break;
	case 'SetTeamBuyinginfo': SetTeamBuyinginfo(); break;
	case 'SetTeamBuyingAwardType': SetTeamBuyingAwardType(); break;
	
	case 'SetBoZongZi': SetBoZongZi(); break;
	case 'BoZongZi': BoZongZi(); break;
	
	case 'SetAwardType': SetAwardType(); break;
	case 'AwardType': AwardType(); break;
	
	case 'ConsumptionDrawItems': ConsumptionDrawItems(); break;
	case 'SetConsumptionDrawItems': SetConsumptionDrawItems(); break;
	
	case 'QiXiQuizBase': QiXiQuizBase();break;
	case 'SetQiXiQuizBase': SetQiXiQuizBase();break;
	
	case 'SetAwardLevelZone': SetAwardLevelZone(); break;
	case 'AwardLevelZone': AwardLevelZone(); break;
}

/**
 * 奖励等级区间
 */ 
function AwardLevelZone() {
	global $db;

	$zone_list = globalDataList('award_level_zone');
	include template('t_award_level_zone');
}


/**
 * 设置奖励等级区间
 */ 
function SetAwardLevelZone() {
	global $db; 
	global $id_old,$id_del,$min_lv,$max_lv; 
	global $min_lv_n,$max_lv_n; 
	
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id_old[$i])
			{

				$db->query("
				update 
					award_level_zone 
				set 
					`min_lv`='$min_lv[$i]',
					`max_lv`='$max_lv[$i]'
				where 
					id = '$id_old[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($min_lv_n && $max_lv_n)
	{
	
		$query = $db->query("
		insert into award_level_zone
			(`min_lv`,`max_lv`) 
		values 
			('$min_lv_n','$max_lv_n')
		") ;
		if($query)
		{
			$msg .= "<br />增加成功！";
		}
		else
		{
			$msg .= '<br /><strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}		
	//----------------------删除--------------------------------------
	if ($id_del)
	{
		$id_arr = implode(",",$id_del);
		$db->query("delete from award_level_zone where id in ($id_arr)");
		$msg .= "<br />删除成功！";

	}
	
	showMsg($msg,'','','greentext');
}

//---------------------------------------------------------------七夕活动题目库
function SetQiXiQuizBase() {
	global $db; 
	global $id_del,$id,$question,$answer_a,$answer_b,$answer_right; 
	global $question_n,$answer_a_n,$answer_b_n,$answer_right_n; 
	

	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i])
			{

				$db->query("
				update 
					st_qi_xi_quiz_base 
				set 
					`question`='$question[$i]',
					`answer_a`='$answer_a[$i]',
					`answer_b`='$answer_b[$i]',
					`answer_right`='$answer_right[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($question_n)
	{
	
		$query = $db->query("
		insert into 
			st_qi_xi_quiz_base
			(question,answer_a,answer_b,answer_right) 
		values 
			('$question_n','$answer_a_n','$answer_b_n','$answer_right_n')
		") ;	
		if($query)
		{
			$msg .= "<br />增加成功！";
		}
		else
		{
			$msg .= '<br /><strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}		
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from st_qi_xi_quiz_base where id in ($id_arr)");
		$msg .= "<br />删除成功！";
	}	
	
	showMsg($msg,'','','greentext');	
}

function QiXiQuizBase() {
	global $db,$page; 
	$pageNum = 10;
	$start_num = ($page-1)*$pageNum;	
	//------------------------------------------------------------
		
	$num = $db->result($db->query("
	select 
		count(1) 
	from 
		st_qi_xi_quiz_base
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			st_qi_xi_quiz_base
		order by 
			id asc
		limit 
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=active&action=QiXiQuizBase");	

	}
	include_once template('t_qi_xi_quiz_base');
}

/**
 * 玩家活动奖励
 */ 
function AwardType() {
	global $db,$page;
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;

	$item_list = globalDataList('item');
	$type_list = globalDataList('day_type');
	$zone_list = globalDataList('award_level_zone');
	foreach ($item_list as $key => $value) {
		$item_list[$key]['sign'] = ucfirst(substr($value['sign'], 0, 1));
	}
	$num = $db->result($db->query("
	select 
		count(1) 
	from 
		award_type
	"),0);	
	$query = $db->query("SELECT * FROM award_type order by 
			id asc
		limit 
			$start_num,$pageNum");
	$list_array = array();
	if($db->num_rows($query)){	
		while($rs = $db->fetch_array($query)){	
			$list_array[] =  $rs;
		}
	}
	$list_array_pages = multi($num,$pageNum,$page,"t.php?in=active&action=AwardType");	
	include template('t_award_type');
}


/**
 * 玩家活动奖励类型
 */ 
function SetAwardType() {
	global $db; 
	global $id_old,$id_del,$name,$day_type_id,$skill,$fame,$coins,$item_id,$item_number, $lv,$probability,$level_zone_id,$need_jifen,$fate,$ling_shi; 
	global $name_n,$day_type_id_n,$skill_n,$fame_n,$coins_n,$item_id_n,$item_number_n, $lv_n,$probability_n,$level_zone_id_n,$need_jifen_n,$fate_n,$ling_shi_n; 

	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id_old[$i])
			{

				$db->query("
				update 
					award_type 
				set 
					`name`='$name[$i]',
					`day_type_id`='$day_type_id[$i]',
					`skill`='$skill[$i]',
					`fame`='$fame[$i]',
					`coins`='$coins[$i]',
					`item_id`='$item_id[$i]',
					`item_number`='$item_number[$i]',
					`lv`='$lv[$i]',
					`probability`='$probability[$i]',
					`level_zone_id`='$level_zone_id[$i]',
					`need_jifen`='$need_jifen[$i]',
					`fate`='$fate[$i]',
					`ling_shi`='$ling_shi[$i]'
				where 
					id = '$id_old[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($name_n)
	{
	
		$query = $db->query("
		insert into award_type
			(`name`,`day_type_id`,`skill`,`fame`,`coins`,`item_id`,`item_number`, `lv`,`probability`,`level_zone_id`,`need_jifen`,`fate`,`ling_shi`) 
		values 
			('$name_n','$day_type_id_n','$skill_n','$fame_n','$coins_n','$item_id_n','$item_number_n', '$lv_n','$probability_n','$level_zone_id_n','$need_jifen_n','$fate_n','$ling_shi_n')
		") ;
		if($query)
		{
			$msg .= "<br />增加成功！";
		}
		else
		{
			$msg .= '<br /><strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}		
	//----------------------删除--------------------------------------
	if ($id_del)
	{
		$id_arr = implode(",",$id_del);
		$db->query("delete from award_type where id in ($id_arr)");
		$msg .= "<br />删除成功！";

	}	
	
	showMsg($msg,'','','greentext');
}

//-------------------------------------------------------------------------------------------6月消耗抽奖物品表
function ConsumptionDrawItems()
{
	global $db,$page; 
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;

	$item_list = globalDataList('item');//装备类型
	
	foreach ($item_list as $key => $value) {
		$item_list[$key]['sign'] = ucfirst(substr($value['sign'], 0, 1));
	}
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		consumption_draw_items
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			consumption_draw_items
		limit 
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=active&action=BoZongZi");	

	}	
	include_once template('t_consumption_draw_items');
}

function SetConsumptionDrawItems()
{
	global $db; 
	global $id_old,$id_del,$state_point,$coin,$fame,$skill,$items_id,$items_count,$frame; 
	global $state_point_n,$coin_n,$fame_n,$skill_n,$items_id_n,$items_count_n,$frame_n;
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id_old[$i])
			{

				$db->query("
				update 
					consumption_draw_items 
				set 
					`state_point`='$state_point[$i]',
					`coin`='$coin[$i]',
					`fame`='$fame[$i]',
					`skill`='$skill[$i]',
					`items_id`='$items_id[$i]',
					`frame`='$frame[$i]',
					`items_count`='$items_count[$i]'
				where 
					id = '$id_old[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($state_point_n || $coin_n || $fame_n || $skill_n || $items_id_n || $items_count_n || $frame_n)
	{
	
		$query = $db->query("
		insert into 
			consumption_draw_items
			(`state_point`,`coin`,`fame`,`skill`,`items_id`,`items_count`,`frame`) 
		values 
			('$state_point_n','$coin_n','$fame_n','$skill_n','$items_id_n','$items_count_n','$frame_n')
		") ;
		if($query)
		{
			$msg .= "<br />增加成功！";
		}
		else
		{
			$msg .= '<br /><strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}		
	//----------------------删除--------------------------------------
	if ($id_del)
	{
		$id_arr = implode(",",$id_del);
		$db->query("delete from consumption_draw_items where id in ($id_arr)");
		$msg .= "<br />删除成功！";

	}	
	
	showMsg($msg,'','','greentext');
}
//-------------------------------------------------------------------------------------------剥粽子
function BoZongZi()
{
	global $db,$page; 
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;

	$item_list = globalDataList('item');//装备类型
	
	foreach ($item_list as $key => $value) {
		$item_list[$key]['sign'] = ucfirst(substr($value['sign'], 0, 1));
	}
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		bo_zong_zi
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			bo_zong_zi
		limit 
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=active&action=BoZongZi");	

	}	
	include_once template('t_bo_zong_zi');
}

function SetBoZongZi()
{
	global $db; 
	global $id_old,$id_del,$type,$max,$name,$state_point,$coin,$fame,$skill,$item_id,$item_count; 
	global $type_n,$max_n,$name_n,$state_point_n,$coin_n,$fame_n,$skill_n,$item_id_n,$item_count_n;
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id_old[$i])
			{

				$db->query("
				update 
					bo_zong_zi 
				set 
					`type`='$type[$i]',
					`max`='$max[$i]',
					`name`='$name[$i]',
					`state_point`='$state_point[$i]',
					`coin`='$coin[$i]',
					`fame`='$fame[$i]',
					`skill`='$skill[$i]',
					`item_id`='$item_id[$i]',
					`item_count`='$item_count[$i]'
				where 
					id = '$id_old[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($name_n)
	{
	
		$query = $db->query("
		insert into 
			bo_zong_zi
			(`type`,`max`,`name`,`state_point`,`coin`,`fame`,`skill`,`item_id`,`item_count`) 
		values 
			('$type_n','$max_n','$name_n','$state_point_n','$coin_n','$fame_n','$skill_n','$item_id_n','$item_count_n')
		") ;
		if($query)
		{
			$msg .= "<br />增加成功！";
		}
		else
		{
			$msg .= '<br /><strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}		
	//----------------------删除--------------------------------------
	if ($id_del)
	{
		$id_arr = implode(",",$id_del);
		$db->query("delete from bo_zong_zi where id in ($id_arr)");
		$msg .= "<br />删除成功！";

	}	
	
	showMsg($msg,'','','greentext');
}

//-------------------------------------------------------------------------------------------猜谜题库表
function QuizGameQuestion() {
	global $db,$page; 
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;

	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		quiz_game_question
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			quiz_game_question
		limit 
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=active&action=QuizGameQuestion");	

	}	
	include_once template('t_quiz_game_question');
}

/**
 * 猜谜题库表
 */ 
function SetQuizGameQuestion() {
	global $db; 
	global $id_old,$id_del,$question,$answer_1,$answer_2,$answer_3,$answer_4,$answer; 
	global $question_n,$answer_1_n,$answer_2_n,$answer_3_n,$answer_4_n,$answer_n;
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id_old[$i])
			{

				$db->query("
				update 
					quiz_game_question 
				set 
					`question`='$question[$i]',
					`answer_1`='$answer_1[$i]',
					`answer_2`='$answer_2[$i]',
					`answer_3`='$answer_3[$i]',
					`answer_4`='$answer_4[$i]',
					`answer`='$answer[$i]'
				where 
					id = '$id_old[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($question_n)
	{
	
		$query = $db->query("
		insert into 
			quiz_game_question
			(`question`,`answer_1`,`answer_2`,`answer_3`,`answer_4`,`answer`) 
		values 
			('$question_n','$answer_1_n','$answer_2_n','$answer_3_n','$answer_4_n','$answer_n')
		") ;
		if($query)
		{
			$msg .= "<br />增加成功！";
		}
		else
		{
			$msg .= '<br /><strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}		
	//----------------------删除--------------------------------------
	if ($id_del)
	{
		$id_arr = implode(",",$id_del);
		$db->query("delete from quiz_game_question where id in ($id_arr)");
		$msg .= "<br />删除成功！";

	}	
	
	showMsg($msg,'','','greentext');
}


/**
 * 定期活动列表
 */ 
function day_list(){
	global $db,$page; 
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;

	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		day_type
	"),0);	
	$query = $db->query("SELECT * FROM day_type limit $start_num,$pageNum");
	$list_array = array();
	if($db->num_rows($query)){	
		while($rs = $db->fetch_array($query)){	
			$list_array[] =  $rs;
		}
	}
	$list_array_pages = multi($num,$pageNum,$page,"t.php?in=active&action=day");
	include template('t_active_day');
}
/**
 * 添加、修改定期活动
 */ 
function day_setting(){
	global $db;

	$id      = ReqArray('id');
	$sign    = ReqArray('sign');
	$name    = ReqArray('name');
	$id_del  = ReqArray('id_del');
	$is_open = ReqArray('is_open');

	$sign_n = ReqStr('sign_n');
	$name_n = ReqStr('name_n');
	$is_open_n = isset($_POST['is_open_n']) ? intval($_POST['is_open_n']) : 0;

	//-----------------更新-------------------------------------------
	if ($id){
		$id_num = count($id);
		if ($id_num > 0){
			foreach ($id as $key => $value) {
				$db->query("UPDATE day_type SET `sign`='$sign[$value]',	`name`='$name[$value]',	`is_open`='$is_open[$value]' WHERE id = '$value';");
			}
			$msg = "更新成功！";
		}
	}
	//----------------增加------------------
	if (!empty($name_n)){
		$sql = "INSERT INTO day_type (sign, name, is_open) VALUES ('$sign_n', '$name_n', $is_open_n);";
		$query = $db->query($sql);
		$msg = $query ? '增加记录成功' : '增加记录失败';
	}

	//----------------------删除--------------------------------------
	if ($id_del){
		if (count($id_del) > 0){
			$id_arr = implode(",",$id_del);
			$db->query("DELETE FROM day_type WHERE id IN ($id_arr)");
			$msg .= "<br />删除成功！";
		}
	}	
	showMsg($msg,'','','greentext');	
}
/**
 * 删除定期活动
 */ 
function day_del(){
	global $db;
	$sign_n = ReqStr('sign_n');
	$name_n = ReqStr('name_n');
	$is_open_n = isset($_POST['is_open_n']) ? intval($_POST['is_open_n']) : 0;

	if (!empty($name_n)){
		$sql = "INSERT INTO day_type (sign, name, is_open) VALUES ('$sign_n', '$name_n', $is_open_n);";
		$query = $db->query($sql);
		if ($query){
			$msg = '增加记录成功';
		}else {
			$msg = '增加记录失败';
		}
	}
	showMsg($msg,'','','greentext');	
}


function TeamBuyingGiftType() {
	global $db;

	$query = $db->query('SELECT * FROM team_buying_gift_type');
	$list_array = array();
	if($db->num_rows($query)){	
		while($rs = $db->fetch_array($query)){	
			$list_array[] =  $rs;
		}
	}

	include template('t_team_buying_gift_type');
}

/**
 * 玩家团购信息
 */ 
function TeamBuyingAwardType() {
	global $db;

	$query = $db->query('SELECT * FROM team_buying_award_type');
	$list_array = array();
	if($db->num_rows($query)){	
		while($rs = $db->fetch_array($query)){	
			$list_array[] =  $rs;
		}
	}
	include template('t_team_buying_award_type');
}

/**
 * 玩家团购信息
 */ 
function TeamBuyinginfo() {
	global $db,$page; 
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;

	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		team_buying_info
	"),0);	
	$item_list = globalDataList('item');//任务物品
	$award_list = globalDataList('team_buying_award_type');//奖励类型
	$gift_list = globalDataList('team_buying_gift_type');//奖励类型

	$query = $db->query("SELECT * FROM team_buying_info limit $start_num,$pageNum");
	$list_array = array();
	if($db->num_rows($query)){	
		while($rs = $db->fetch_array($query)){	
			$list_array[] =  $rs;
		}
	}
	$list_array_pages = multi($num,$pageNum,$page,"t.php?in=active&action=TeamBuyinginfo");
	include template('t_team_buying_info');
}


/**
 * 玩家团购奖励类型
 */ 
function SetTeamBuyingAwardType() {
	global $db; 
	global $id_old,$id_del,$name; 
	global $name_n;
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
	
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id_old[$i])
			{

				$db->query("
				update 
					team_buying_award_type 
				set 
					`name`='$name[$i]'
				where 
					id = '$id_old[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($name_n)
	{
	
		$query = $db->query("
		insert into 
			team_buying_award_type
			(`name`) 
		values 
			('$name_n')
		") ;
		if($query)
		{
			$msg .= "<br />增加成功！";
		}
		else
		{
			$msg .= '<br /><strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}		
	//----------------------删除--------------------------------------
	if ($id_del)
	{
		$id_arr = implode(",",$id_del);
		$db->query("delete from team_buying_award_type where id in ($id_arr)");
		$msg .= "<br />删除成功！";

	}	
	
	showMsg($msg,'','','greentext');
}

/**
 * 玩家团购奖励类型
 */ 
function SetTeamBuyingGiftType() {
	global $db; 
	global $id_old,$id_del,$name,$price,$discount,$spare,$jifen,$limit_amount; 
	global $name_n,$price_n,$discount_n,$spare_n,$jifen_n,$limit_amount_n; 
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
		$id_num = count($id_old);
		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id_old[$i])
			{

				$db->query("
				update 
					team_buying_gift_type 
				set 
					`name`='$name[$i]',
					`price`='$price[$i]',
					`discount`='$discount[$i]',
					`spare`='$spare[$i]',
					`jifen`='$jifen[$i]',
					`limit_amount`='$limit_amount[$i]'
				where 
					id = '$id_old[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($name_n)
	{
	
		$query = $db->query("
		insert into 
			team_buying_gift_type
			(`name`,`price`,`discount`,`spare`,`jifen`,`limit_amount`) 
		values 
			('$name_n','$price_n','$discount_n','$spare_n','$jifen_n','$limit_amount_n')
		") ;
		if($query)
		{
			$msg .= "<br />增加成功！";
		}
		else
		{
			$msg .= '<br /><strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}		
	//----------------------删除--------------------------------------
	if ($id_del)
	{
		$id_arr = implode(",",$id_del);
		$db->query("delete from team_buying_gift_type where id in ($id_arr)");
		$msg .= "<br />删除成功！";

	}	
	
	showMsg($msg,'','','greentext');
}

/**
 * 玩家团购信息
 */ 
function SetTeamBuyinginfo() {
	global $db; 
	global $id_old,$id_del,$gift_id,$award_id,$item_id,$amount; 
	global $gift_id_n,$award_id_n,$item_id_n,$amount_n; 
	//-----------------更新-------------------------------------------
	if ($id_old)
	{
		$id_num = count($id_old);
		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id_old[$i])
			{
				$db->query("
				update 
					team_buying_info 
				set 
					`gift_id`='$gift_id[$i]',
					`award_id`='$award_id[$i]',
					`item_id`='$item_id[$i]',
					`amount`='$amount[$i]'
				where 
					id = '$id_old[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($gift_id_n)
	{
	
		$query = $db->query("
		insert into 
			team_buying_info
			(`gift_id`,`award_id`,`item_id`,`amount`) 
		values 
			('$gift_id_n','$award_id_n','$item_id_n','$amount_n')
		") ;
		if($query)
		{
			$msg .= "<br />增加成功！";
		}
		else
		{
			$msg .= '<br /><strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}		
	//----------------------删除--------------------------------------
	if ($id_del)
	{
		$id_arr = implode(",",$id_del);
		$db->query("delete from team_buying_info where id in ($id_arr)");
		$msg .= "<br />删除成功！";

	}	
	
	showMsg($msg,'','','greentext');
}