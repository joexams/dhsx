<?php 
if(!defined('IN_UCTIME'))
{
	exit('Access Denied');
}

switch (ReqStr('action'))
{
    case 'AttributeStone': AttributeStone();break;
    case 'WeekRanking': WeekRanking();break;
	case 'WeekRankingAward': WeekRankingAward();break;
	case 'ConsumeAlertSetType': ConsumeAlertSetType();break;
	case 'GoldOil': GoldOil();break;	
	case 'YellowGiftAward': YellowGiftAward();break;	
	case 'RollCake': RollCake();break;	
	case 'RollCount': RollCount();break;	
	case 'SpiritState': SpiritState();break;	
	case 'Achievement': Achievement();break;	
	case 'AchievementTag': AchievementTag();break;
	case 'Horse': Horse();break;	
	case 'Track': Track();break;
	case 'IngotChangeType': IngotChangeType();break;
	case 'RulaiAttr': RulaiAttr(); break;
	case 'RulaiIncenseAttr': RulaiIncenseAttr(); break;

	case 'DelayNotifyMessageTemplate': DelayNotifyMessageTemplate();break;
	case 'HorseRacesInspireMessage': HorseRacesInspireMessage();break;
	case 'GameFunction': GameFunction();break;
	case 'ChangeMoneyIngot': ChangeMoneyIngot();break;
	case 'ChangeMoneyCoins': ChangeMoneyCoins();break;
	case 'FameLevelData': FameLevelData();break;
	case 'FameLevelForRole': FameLevelForRole();break;
	case 'VipRequire': VipRequire();break;
	case 'TravelEvent': TravelEvent();break;
	case 'OnlineGift': OnlineGift();break;
	case 'PracticeLevel': PracticeLevel();break;

	case 'SetHerbs': SetHerbs();break;
	case 'SetHorse': SetHorse();break;
	case 'SetTrack': SetTrack();break;
	case 'SetIngotChangeType': SetIngotChangeType();break;
	case 'SetHorseRacesInspireMessage': SetHorseRacesInspireMessage();break;
	case 'SetDelayNotifyMessageTemplate': SetDelayNotifyMessageTemplate();break;
	case 'SetHerbsLevel': SetHerbsLevel();break;
	case 'SetGameFunction': SetGameFunction();break;
	case 'SetChangeMoneyIngot': SetChangeMoneyIngot();break;
	case 'SetChangeMoneyCoins': SetChangeMoneyCoins();break;
	case 'SetFameLevelData': SetFameLevelData();break;
	case 'SetFameLevelForRole': SetFameLevelForRole();break;
	case 'SetVipRequire': SetVipRequire();break;
	case 'SetTravelEvent': SetTravelEvent();break;
	case 'SetTravelEventAnswer': SetTravelEventAnswer();break;
	case 'SetOnlineGift': SetOnlineGift();break;
	case 'SetPracticeLevel': SetPracticeLevel();break;
	case 'SetAchievement': SetAchievement();break;	
	case 'SetAchievementTag': SetAchievementTag();break;
	case 'SetSpiritState': SetSpiritState();break;
	case 'SetSpiritStateRequire': SetSpiritStateRequire();break;
	case 'SetRollCake': SetRollCake();break;	
	case 'SetRollCount': SetRollCount();break;	
	case 'SetYellowGiftAward': SetYellowGiftAward();break;
	case 'SetGoldOil': SetGoldOil();break;	
	case 'SetCallGoldOilData': SetCallGoldOilData();break;	
	case 'SetConsumeAlertSetType': SetConsumeAlertSetType();break;
	case 'SetWeekRanking': SetWeekRanking();break;
	case 'SetWeekRankingAward': SetWeekRankingAward();break;
	case 'SetWeekRankingDayAward': SetWeekRankingDayAward();break;
	case 'SetAttributeStone': SetAttributeStone();break;

	case 'SetRulaiAttr': SetRulaiAttr(); break;
	case 'SetRulaiIncenseAttr': SetRulaiIncenseAttr(); break;
	default:  Herbs();
}

//--------------------------------------------------------------------------------------------如来属性表
function RulaiAttr() {
	global $db,$page; 
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;	

	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		rulai_attr
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			rulai_attr
		order by 
			level asc
		limit 
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=other&action=RulaiAttr");	

	}	
	include_once template('t_rulai_attr');
}

//--------------------------------------------------------------------------------------------如来上香属性表
function RulaiIncenseAttr() {
	global $db,$page; 
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;
	
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		rulai_incense_attr
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			rulai_incense_attr
		limit 
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=other&action=RulaiIncenseAttr");	

	}	
	include_once template('t_rulai_incense_attr');
}


function SetRulaiAttr() {
	global $db; 
	global $level_old,$level_del,$level,$name,$experience,$lift_fame_prob_1,$lift_fame_prob_2,$lift_fame_prob_3; 
	global $level_n,$name_n,$experience_n,$lift_fame_prob_1_n,$lift_fame_prob_2_n,$lift_fame_prob_3_n; 
	//-----------------更新-------------------------------------------
	if ($level_old && $level && $name)
	{
		$id_num = count($level_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($level_old[$i] && $level[$i])
			{

				$db->query("
				update 
					rulai_attr 
				set 
					`level`='$level[$i]',
					`name`='$name[$i]',
					`experience`='$experience[$i]',
					`lift_fame_prob_1`='$lift_fame_prob_1[$i]',
					`lift_fame_prob_2`='$lift_fame_prob_2[$i]',
					`lift_fame_prob_3`='$lift_fame_prob_3[$i]'
				where 
					level = '$level_old[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($level_n && $name_n)
	{
	
		$query = $db->query("
		insert into 
			rulai_attr
			(`level`,`name`,`experience`,`lift_fame_prob_1`,`lift_fame_prob_2`,`lift_fame_prob_3`) 
		values 
			('$level_n','$name_n','$experience_n','$lift_fame_prob_1_n','$lift_fame_prob_2_n','$lift_fame_prob_3_n')
		") ;	
		if($query)
		{
			$msg .= " 增加成功！";
		}
		else
		{
			$msg .= ' <strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}		
	//----------------------删除--------------------------------------
	if ($level_del)
	{
		$delidNum = count($level_del);
		for ($i=0;$i<$delidNum;$i++)	{
			$idArr = explode(',',$level_del[$i]);
			$db->query("delete from rulai_attr where level = '$idArr[0]'");
		}
		$msg .= " 删除成功！";

	}
	
	showMsg($msg,'','','greentext');
}


//--------------------------------------------------------------------------------------------如来上香属性表
function SetRulaiIncenseAttr() {
	global $db; 
	global $id_old,$id_del,$name,$skill,$ingot,$fame,$incense,$vip_level; 
	global $name_n,$skill_n,$ingot_n,$fame_n,$incense_n,$vip_level_n; 
	//-----------------更新-------------------------------------------
	if ($id_old && $name)
	{
		$id_num = count($id_old);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id_old[$i] && $name[$i])
			{
				$db->query("
				update 
					rulai_incense_attr 
				set 
					`name`='$name[$i]',
					`skill`='$skill[$i]',
					`ingot`='$ingot[$i]',
					`fame`='$fame[$i]',
					`incense`='$incense[$i]',
					`vip_level`='$vip_level[$i]'
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
			rulai_incense_attr
			(`name`,`skill`,`ingot`,`fame`,`incense`,`vip_level`) 
		values 
			('$name_n','$skill_n','$ingot_n','$fame_n','$incense_n','$vip_level_n')
		") ;	
		if($query)
		{
			$msg .= " 增加成功！";
		}
		else
		{
			$msg .= ' <strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}		
	//----------------------删除--------------------------------------
	if ($id_del)
	{
		$delidNum = count($id_del);
		for ($i=0;$i<$delidNum;$i++)	{
			$idArr = explode(',',$id_del[$i]);
			$db->query("delete from rulai_incense_attr where id = '$idArr[0]'");
		}
		$msg .= " 删除成功！";

	}
	
	showMsg($msg,'','','greentext');
}

//--------------------------------------------------------------------------------------------仙石属性表

function  AttributeStone() 
{
	global $db,$page; 
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;	
	//------------------------------------------------------------
	$war_attribute_type_list = globalDataList('war_attribute_type');	
	$item_list = globalDataList('item','type_id=22000');
	$src_item_list = globalDataList('item','type_id=25000');
	$book_item_list = globalDataList('item','type_id=24000');

	
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		attribute_stone
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			attribute_stone
		order by 
			lv asc
		limit 
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=other&action=AttributeStone");	

	}	
	include_once template('t_attribute_stone');
}
//--------------------------------------------------------------------------------------------周排行

function  WeekRanking() 
{
	global $db,$page; 
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;	
	//------------------------------------------------------------

		
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		week_ranking
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			week_ranking
		order by 
			id asc
		limit 
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query))
		{
			$rs['desc_url'] = urlencode($rs['desc']);
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=other&action=WeekRanking");	

	}	
	include_once template('t_week_ranking');
}

//--------------------------------------------------------------------------------------------周排行奖励

function  WeekRankingAward() 
{
	global $db,$page; 
	$pageNum = 20;
	$start_num = ($page-1)*$pageNum;	
	//------------------------------------------------------------

		
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		week_ranking_award
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			week_ranking_award
		order by 
			rank asc
		limit 
			$start_num,$pageNum
		");	
		while($rs = $db->fetch_array($query))
		{
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=other&action=WeekRankingAward");	

	}	
	include_once template('t_week_ranking_award');
}

//--------------------------------------------------------------------------------------------消费提醒类型

function  ConsumeAlertSetType() 
{
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;
	$gold_oil_list = globalDataList('gold_oil');	
	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		consume_alert_set_type 
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			A.*
		from 
			consume_alert_set_type A
		order by 
			A.id asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=other&action=ConsumeAlertSetType");	
	}	
	include_once template('t_consume_alert_set_type');
}

//--------------------------------------------------------------------------------------------金油数据

function  GoldOil() 
{
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;
	$item_list = globalDataList('item','type_id=15000');	
	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		gold_oil 
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			A.*,
			B.name as item_name
		from 
			gold_oil A
			left join item B on A.item_id = B.id
		order by 
			A.item_id asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$rs['name_url'] = urlencode($rs['name']);
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=other&action=GoldOil");	
	}	
	include_once template('t_gold_oil');
}
//--------------------------------------------------------------------------------------------黄钻奖励

function  YellowGiftAward() 
{
	global $db,$page; 
	$type=ReqNum('type');
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;
	$set_type = "where `type` = '$type'";
	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		yellow_gift_award
		$set_type
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			*
		from 
			yellow_gift_award
			$set_type
		order by 
			id asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=other&action=YellowGiftAward&type=$type");	
	}	
	include_once template('t_yellow_gift_award');
}
//--------------------------------------------------------------------------------------------博饼表

function  RollCake() 
{
	global $db,$page; 
	$tag=ReqNum('tag');
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;

	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		roll_cake
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			*
		from 
			roll_cake
		order by 
			id asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=other&action=RollCake");	
	}	
	include_once template('t_roll_cake');
}
//--------------------------------------------------------------------------------------------点数表

function  RollCount() 
{
	global $db,$page; 
	$tag=ReqNum('tag');
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;

	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		roll_count
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			*
		from 
			roll_count
		order by 
			number asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=other&action=RollCount");	
	}	
	include_once template('t_roll_count');
}
//--------------------------------------------------------------------------------------------渡劫境界

function  SpiritState() 
{
	global $db,$page; 
	$tag=ReqNum('tag');
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;

	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		spirit_state
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			*
		from 
			spirit_state
		order by 
			id asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$rs['name_url'] = urlencode($rs['name']);
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=other&action=SpiritState");	
	}	
	include_once template('t_spirit_state');
}
//--------------------------------------------------------------------------------------------成就

function  Achievement() 
{
	global $db,$page; 
	$tag=ReqNum('tag');
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;
	$achievement_tag_list = globalDataList('achievement_tag','type = 1');//TAG
	if($tag){
		$set_tag = "where tag = '$tag'";
	}
	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		achievement
		$set_tag
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			*
		from 
			achievement
			$set_tag
		order by 
		    tag asc,
			sort_order asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=other&action=Achievement&tag=$tag");	
	}	
	include_once template('t_achievement');
}
//--------------------------------------------------------------------------------------------成就标签

function  AchievementTag() 
{
	global $db,$page; 
	$type=ReqNum('type');
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;
	$achievement_tag_list = globalDataList('achievement_tag','type = 0');//TAG
	if(!$type){
		$type = 0;
	}
	
	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		achievement_tag
	where 
		type = '$type'
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			*
		from 
			achievement_tag
		where 
			type = '$type'
		order by 
			id asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=other&action=AchievementTag&type=$type");	
	}	
	include_once template('t_achievement_tag');
}
//--------------------------------------------------------------------------------------------草药

function  Herbs() 
{
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;
	$herbs_type=ReqNum('herbs_type');
	$herbs_type_list = globalDataList('herbs_type');//类型
	
	if ($herbs_type)
	{
		$set_type = "where A.herbs_type = '$herbs_type'";
	}
	
	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		herbs A
		left join herbs_type B on A.herbs_type = B.id
		$set_type
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			A.*,
			B.type_name
		from 
			herbs A
			left join herbs_type B on A.herbs_type = B.id
			$set_type
		order by 
			A.id asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=other&herbs_type=$herbs_type");	
	}	
	include_once template('t_herbs');
}


//--------------------------------------------------------------------------------------------招财进宝元宝设定

function  ChangeMoneyIngot() 
{
	global $db,$page; 
	$pageNum = 100;
	$start_num = ($page-1)*$pageNum;		
	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		change_money_ingot
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			*
		from 
			change_money_ingot
		order by 
			count asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=other&action=ChangeMoneyIngot");	
	}	
	include_once template('t_change_money_ingot');
}


//--------------------------------------------------------------------------------------------招财进宝玩家等级获得铜钱设定

function  ChangeMoneyCoins() 
{
	global $db,$page; 
	$pageNum = 100;
	$start_num = ($page-1)*$pageNum;		
	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		change_money_coins
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			*
		from 
			change_money_coins
		order by 
			level asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=other&action=ChangeMoneyCoins");	
	}	
	include_once template('t_change_money_coins');
}


//--------------------------------------------------------------------------------------------元包记录类型

function  IngotChangeType() 
{
	global $db,$page; 
	$pageNum = 100;
	$start_num = ($page-1)*$pageNum;	
	$type=ReqNum('type');	
	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		ingot_change_type
	where 
		type = '$type'
		
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			*
		from 
			ingot_change_type
		where 
			type = '$type'
		order by 
			id asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=other&action=IngotChangeType&type=$type");	
	}	
	include_once template('t_ingot_change_type');
}
//--------------------------------------------------------------------------------------------神兽

function  Horse() 
{
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;		
	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		horse
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			*
		from 
			horse
		order by 
			id asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=other&action=Horse");	
	}	
	include_once template('t_horse');
}

//--------------------------------------------------------------------------------------------神兽赛道

function  Track() 
{
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;		
	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		track
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			*
		from 
			track
		order by 
			id asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=other&action=Track");	
	}	
	include_once template('t_track');
}


//--------------------------------------------------------------------------------------------延迟通知信息(未做)

function  DelayNotifyMessageTemplate() 
{
	global $db,$page; 
	
	$type=ReqNum('type');
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;	

	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		delay_notify_message_template
	where 
		`type` = '$type'	
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			*
		from 
			delay_notify_message_template
		where 
			`type` = '$type'
		order by 
			id asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=other&action=DelayNotifyMessageTemplate&type=$type");	
	}	
	include_once template('t_delay_notify_message_template');
}

//--------------------------------------------------------------------------------------------神兽鼓舞

function  HorseRacesInspireMessage() 
{
	global $db,$page; 
	$pageNum = 30;
	$start_num = ($page-1)*$pageNum;		
	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		horse_races_inspire_message
	"),0);	
	if($num)
	{			
		$query = $db->query("
		select 
			*
		from 
			horse_races_inspire_message
		order by 
			id asc
		limit 
			$start_num,$pageNum			
		");
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=other&action=HorseRacesInspireMessage");	
	}	
	include_once template('t_horse_races_inspire_message');
}
//--------------------------------------------------------------------------------------------游戏功能

function  GameFunction() 
{
	global $db,$page; 
	$name=ReqStr('name');
	$pageNum = 50;
	$start_num = ($page-1)*$pageNum;		
	
	//------------------------------------------------------------
	$num = $db->result($db->query("
	select 
		count(*) 
	from 
		game_function
	"),0);	
	if($num)
	{		
		$query = $db->query("
		select 
			*
		from 
			game_function
		order by 
			`lock` asc,
			id asc
		limit 
			$start_num,$pageNum			
		");


		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=other&action=GameFunction");	
	}	
	include_once template('t_game_function');
}

//------------------------------------------------------角色声望等级数据
function FameLevelForRole() 
{

	global $db,$page;
	$pageNum = 20; 
	$start_num = ($page-1)*$pageNum;
	//------------------------------------------------------------
	$num = $db->result($db->query("
		select 
			count(*) 
		from 
			fame_level_for_role				
		"),0); //获得总条数
	if($num){			
		$query = $db->query("
		select 
			*
		from 
			fame_level_for_role 
		order by 
			level asc
		limit 
			$start_num,$pageNum				
		");
			
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=other&action=FameLevelForRole");	
	}
	include_once template('t_fame_level_for_role');

}

//------------------------------------------------------声望等级数据
function FameLevelData() 
{

	global $db,$page;
	$id=ReqNum('id');
	$name=ReqStr('name');
	$name_url = urlencode($name); 
	$msg=ReqStr('msg','htm');	
	$winid=ReqStr('winid');	
	
	$monster_list = globalDataList('monster','role_job_id > 0');//怪物	
	$role_list = globalDataList('role');//招募
	$item_list = globalDataList('item','type_id=10001');//气血包

	$pageNum = 20; 
	$start_num = ($page-1)*$pageNum;
	//------------------------------------------------------------
	$num = $db->result($db->query("
		select 
			count(*) 
		from 
			fame_level_data				
		"),0); //获得总条数
	if($num){			
		$query = $db->query("
		select 
			*
		from 
			fame_level_data 
		order by 
			level asc
		limit 
			$start_num,$pageNum				
		");
			
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=other&action=FameLevelData");	
	}
	include_once template('t_fame_level_data');

}


//------------------------------------------------------仙旅奇缘事件
function TravelEvent() 
{

	global $db,$page;

	$pageNum = 20; 
	$start_num = ($page-1)*$pageNum;
	//------------------------------------------------------------
	$num = $db->result($db->query("
		select 
			count(*) 
		from 
			travel_event				
		"),0); //获得总条数
	if($num){			
		$query = $db->query("
		select 
			*
		from 
			travel_event 
		order by 
			id asc
		limit 
			$start_num,$pageNum				
		");
			
		while($rs = $db->fetch_array($query))
		{	
			$rs['name_url'] = urlencode('事件'.$rs['id']);
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=other&action=TravelEvent");	
	}
	include_once template('t_travel_event');

}
//------------------------------------------------------新手在线奖励
function OnlineGift() 
{

	global $db,$page;

	$pageNum = 20; 
	$start_num = ($page-1)*$pageNum;
	$item_list = globalDataList('item','','require_level desc,type_id asc');//物品
	//------------------------------------------------------------
	$num = $db->result($db->query("
		select 
			count(*) 
		from 
			online_gift				
		"),0); //获得总条数
	if($num){			
		$query = $db->query("
		select 
			*
		from 
			online_gift 
		order by 
			id asc
		limit 
			$start_num,$pageNum				
		");
			
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=other&action=OnlineGift");	
	}
	include_once template('t_online_gift');

}
//------------------------------------------------------挂机练功等级数据
function PracticeLevel() 
{

	global $db,$page;

	$pageNum = 50; 
	$start_num = ($page-1)*$pageNum;
	//------------------------------------------------------------
	$num = $db->result($db->query("
		select 
			count(*) 
		from 
			practice_level				
		"),0); //获得总条数
	if($num){			
		$query = $db->query("
		select 
			*
		from 
			practice_level 
		order by 
			player_level asc
		limit 
			$start_num,$pageNum				
		");
			
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=other&action=PracticeLevel");	
	}
	include_once template('t_practice_level');

}
//------------------------------------------------------vip等级要求
function VipRequire() 
{

	global $db,$page;

	$pageNum = 100; 
	$start_num = ($page-1)*$pageNum;
	//------------------------------------------------------------
	$num = $db->result($db->query("
		select 
			count(*) 
		from 
			vip_require				
		"),0); //获得总条数
	if($num){			
		$query = $db->query("
		select 
			*
		from 
			vip_require 
		order by 
			vip_level asc
		limit 
			$start_num,$pageNum				
		");
			
		while($rs = $db->fetch_array($query))
		{	
			$list_array[] =  $rs;
		}
		$list_array_pages = multi($num,$pageNum,$page,"t.php?in=other&action=VipRequire");	
	}
	include_once template('t_vip_require');

}
//--------------------------------------------------------------------------------------------批量设置草药
function  SetHerbs() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$sign = ReqArray('sign');
	$ripe_time = ReqArray('ripe_time');
	$experience = ReqArray('experience');
	$star_level = ReqArray('star_level');
	$lock = ReqArray('lock');
	$herbs_type = ReqArray('herbs_type');
	$coin= ReqArray('coin');

	$herbs_type_n = ReqNum('herbs_type_n');
	$name_n = ReqStr('name_n');
	$sign_n = ReqStr('sign_n');
	$ripe_time_n = ReqNum('ripe_time_n');
	$experience_n = ReqNum('experience_n');
	$star_level_n = ReqNum('star_level_n');
	$lock_n= ReqNum('lock_n');
	$coin_n= ReqNum('coin_n');
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $name[$i] && $sign[$i])
			{

				$db->query("
				update 
					herbs 
				set 
					`name`='$name[$i]',
					`sign`='$sign[$i]',
					`ripe_time`='$ripe_time[$i]',
					`experience`='$experience[$i]',
					`star_level`='$star_level[$i]',
					`lock`='$lock[$i]',
					`herbs_type`='$herbs_type[$i]',
					`coin`='$coin[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($name_n && $sign_n)
	{
	
		$query = $db->query("
		insert into 
			herbs
			(`name`,`sign`,`ripe_time`,`experience`,`star_level`,`lock`,`herbs_type`,`coin`) 
		values 
			('$name_n','$sign_n','$ripe_time_n','$experience_n','$star_level_n','$lock_n','$herbs_type_n','$coin_n')
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
		$db->query("delete from herbs where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');	
}
//--------------------------------------------------------------------------------------------批量元宝类型
function  SetIngotChangeType() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$sign = ReqArray('sign');
	$type = ReqArray('type');
	
	$name_n = ReqStr('name_n');
	$sign_n = ReqStr('sign_n');
	$type_n = ReqNum('type_n');
		
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $name[$i] && $sign[$i])
			{

				$db->query("
				update 
					ingot_change_type 
				set 
					`name`='$name[$i]',
					`sign`='$sign[$i]',
					`type`='$type[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($name_n && $sign_n)
	{
	
		$query = $db->query("
		insert into 
			ingot_change_type
			(`name`,`sign`,`type`) 
		values 
			('$name_n','$sign_n','$type_n')
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
		$db->query("delete from ingot_change_type where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');	
}

//--------------------------------------------------------------------------------------------批量设神兽
function  SetHorse() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$sign = ReqArray('sign');

	
	$name_n = ReqStr('name_n');
	$sign_n = ReqStr('sign_n');

		
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $name[$i] && $sign[$i])
			{

				$db->query("
				update 
					horse 
				set 
					`name`='$name[$i]',
					`sign`='$sign[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($name_n && $sign_n)
	{
	
		$query = $db->query("
		insert into 
			horse
			(`name`,`sign`) 
		values 
			('$name_n','$sign_n')
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
		$db->query("delete from horse where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');	
}

//--------------------------------------------------------------------------------------------批量设神兽赛道
function  SetTrack() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$track_name = ReqArray('track_name');
	$track_sign = ReqArray('track_sign');

	
	$track_name_n = ReqStr('track_name_n');
	$track_sign_n = ReqStr('track_sign_n');

		
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $track_name[$i] && $track_sign[$i])
			{

				$db->query("
				update 
					track 
				set 
					`track_name`='$track_name[$i]',
					`track_sign`='$track_sign[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($track_name_n && $track_sign_n)
	{
	
		$query = $db->query("
		insert into 
			track
			(`track_name`,`track_sign`) 
		values 
			('$track_name_n','$track_sign_n')
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
		$db->query("delete from track where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');	
}

//--------------------------------------------------------------------------------------------批量设神兽鼓舞
function  SetHorseRacesInspireMessage() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$inspire_message = ReqArray('inspire_message');
	$inspire_message_n = ReqStr('inspire_message_n');

		
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $inspire_message[$i])
			{

				$db->query("
				update 
					horse_races_inspire_message 
				set 
					`inspire_message`='$inspire_message[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($inspire_message_n )
	{
	
		$query = $db->query("
		insert into 
			horse_races_inspire_message
			(`inspire_message`) 
		values 
			('$inspire_message_n')
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
		$db->query("delete from horse_races_inspire_message where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');	
}



//--------------------------------------------------------------------------------------------批量设延迟通知信息
function  SetDelayNotifyMessageTemplate() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$message_sign = ReqArray('message_sign');
	$template_message = ReqArray('template_message');
	$type = ReqArray('type');
	
	$message_sign_n = ReqStr('message_sign_n');
	$template_message_n = ReqStr('template_message_n');
	$type_n = ReqNum('type_n');
		
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $message_sign[$i])
			{

				$db->query("
				update 
					delay_notify_message_template 
				set 
					`message_sign`='$message_sign[$i]',
					`template_message`='$template_message[$i]',
					`type`='$type[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($message_sign_n)
	{
	
		$query = $db->query("
		insert into 
			delay_notify_message_template
			(`message_sign`,`template_message`,`type`) 
		values 
			('$message_sign_n','$template_message_n','$type_n')
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
		$db->query("delete from delay_notify_message_template where id in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');	
}
//--------------------------------------------------------------------------------------------批量设置草药星级
function  SetHerbsLevel() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$star_level = ReqArray('star_level');
	$upgrade_pbt = ReqArray('upgrade_pbt');
	$keep_pbt = ReqArray('keep_pbt');
	$demote_pbt = ReqArray('demote_pbt');
	
	$star_level_n = ReqNum('star_level_n');
	$upgrade_pbt_n = ReqNum('upgrade_pbt_n');
	$keep_pbt_n = ReqNum('keep_pbt_n');
	$demote_pbt_n = ReqNum('demote_pbt_n');
		
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
					herbs_level
				set 
					`upgrade_pbt`='$upgrade_pbt[$i]',
					`keep_pbt`='$keep_pbt[$i]',
					`demote_pbt`='$demote_pbt[$i]'
				where 
					star_level = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($star_level_n)
	{
	
		$query = $db->query("
		insert into 
			herbs_level
			(`star_level`,`upgrade_pbt`,`keep_pbt`,`demote_pbt`) 
		values 
			('$star_level_n','$upgrade_pbt_n','$keep_pbt_n','$demote_pbt_n')
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
		$db->query("delete from herbs_level where star_level in ($id_arr)");
		$msg .= "<br />删除成功！";
		
	}	
	showMsg($msg,'','','greentext');	
}


//--------------------------------------------------------------------------------------------批量设置功能
function  SetGameFunction() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$sign = ReqArray('sign');
	$lock = ReqArray('lock');
	
	$name_n = ReqStr('name_n');
	$sign_n = ReqStr('sign_n');
	$lock_n = ReqNum('lock_n');
	
	//----------------------删除--------------------------------------
	if ($id_del)
	{
	
		$id_arr = implode(",",$id_del);
		$db->query("delete from game_function where id in ($id_arr)");
		$msg = "删除成功！";
	}	
	
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $name[$i] && $sign[$i])
			{

				$db->query("
				update 
					game_function 
				set 
					`name`='$name[$i]',
					`sign`='$sign[$i]',
					`lock`='$lock[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg .= "<br />更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($name_n && $sign_n)
	{
	
		$query = $db->query("
		insert into 
			game_function
			(`name`,`sign`,`lock`) 
		values 
			('$name_n','$sign_n','$lock_n')
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
			
	showMsg($msg,'','','greentext');	
}


//--------------------------------------------------------------------------------------------批量设置招财进宝元宝设定
function  SetChangeMoneyIngot() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('count');
	$ingot = ReqArray('ingot');
	
	$count_n = ReqNum('count_n');
	$ingot_n = ReqNum('ingot_n');
	
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $ingot[$i])
			{

				$db->query("
				update 
					change_money_ingot 
				set 
					`ingot`='$ingot[$i]'
				where 
					count = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($count_n && $ingot_n)
	{
	
		$query = $db->query("
		insert into 
			change_money_ingot
			(`count`,`ingot`) 
		values 
			('$count_n','$ingot_n')
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
		$db->query("delete from change_money_ingot where count in ($id_arr)");
		
		$msg .= "<br />删除成功！";
	}
	showMsg($msg,'','','greentext');	

}

//--------------------------------------------------------------------------------------------批量设置招财进宝元玩家等级获得铜钱设定
function  SetChangeMoneyCoins() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('level');
	$coins = ReqArray('coins');
	
	$level_n = ReqNum('level_n');
	$coins_n = ReqNum('coins_n');
	
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $coins[$i])
			{

				$db->query("
				update 
					change_money_coins 
				set 
					`coins`='$coins[$i]'
				where 
					level = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($level_n && $coins_n)
	{
	
		$query = $db->query("
		insert into 
			change_money_coins
			(`level`,`coins`) 
		values 
			('$level_n','$coins_n')
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
		$db->query("delete from change_money_coins where level in ($id_arr)");
		
		$msg .= "<br />删除成功！";
	}
	showMsg($msg,'','','greentext');	

}

//--------------------------------------------------------------------------------------------批量设置角色声望等级数据
function  SetFameLevelForRole() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$level = ReqArray('level');
	//$level = ReqArray('level');
	$require_fame = ReqArray('require_fame');

	
	$level_n = ReqNum('level_n');
	$require_fame_n = ReqNum('require_fame_n');

	
	//-----------------更新-------------------------------------------
	if ($level)
	{
	
		$id_num = count($level);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($level[$i])
			{

				$db->query("
				update 
					fame_level_for_role 
				set 
					`require_fame`='$require_fame[$i]'
				where 
					level = '$level[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($level_n)
	{
	
		$query = $db->query("
		insert into 
			fame_level_for_role
			(
			`level`,
			`require_fame`
			) 
		values 
			(
			'$level_n',
			'$require_fame_n'
			)
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
		$db->query("delete from fame_level_for_role where level in ($id_arr)");
		
		$msg .= "<br />删除成功！";
	}
	showMsg($msg,'','','greentext');	

}
//--------------------------------------------------------------------------------------------批量设置声望等级数据
function  SetFameLevelData() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$level = ReqArray('level');
	$name = ReqArray('name');
	$require_fame = ReqArray('require_fame');

	
	$level_n = ReqNum('level_n');
	$name_n = ReqStr('name_n');
	$require_fame_n = ReqNum('require_fame_n');

	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $name[$i])
			{

				$db->query("
				update 
					fame_level_data 
				set 
					`level`='$level[$i]',
					`name`='$name[$i]',
					`require_fame`='$require_fame[$i]'
				where 
					id = '$id[$i]'
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
			fame_level_data
			(
			`level`,
			`name`,
			`require_fame`
			) 
		values 
			(
			'$level_n',
			'$name_n',
			'$require_fame_n'
			)
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
		$db->query("delete from fame_level_data where id in ($id_arr)");
		
		$msg .= "<br />删除成功！";
	}
	showMsg($msg,'','','greentext');	

}

//--------------------------------------------------------------------------------------------批量设置vip等级要求
function  SetVipRequire() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$money = ReqArray('money');
	
	$vip_level_n = ReqNum('vip_level_n');
	$money_n = ReqNum('money_n');
	
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $money[$i])
			{

				$db->query("
				update 
					vip_require 
				set 
					`money`='$money[$i]'
				where 
					vip_level = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($vip_level_n)
	{
	
		$query = $db->query("
		insert into 
			vip_require
			(`vip_level`,`money`) 
		values 
			('$vip_level_n','$money_n')
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
		$db->query("delete from vip_require where vip_level in ($id_arr)");
		
		$msg .= "<br />删除成功！";
	}
	showMsg($msg,'','','greentext');	

}

//--------------------------------------------------------------------------------------------批量设置仙旅奇缘事件
function  SetTravelEvent() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$event = ReqArray('event');
	$id_n = ReqNum('id_n');
	$event_n = ReqNum('event_n');
	
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $event[$i])
			{

				$db->query("
				update 
					travel_event 
				set 
					`event`='$event[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($id_n && $event_n)
	{
	
		$query = $db->query("
		insert into 
			travel_event
			(`id`,`event`) 
		values 
			('$id_n','$event_n')
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
		$db->query("delete from travel_event_answer where event_id in ($id_arr)");
		$db->query("delete from travel_event where id in ($id_arr)");
		$msg .= "<br />删除成功！";
	}
	showMsg($msg,'','','greentext');	

}

//--------------------------------------------------------------------------------------------批量设置仙旅奇缘事件答案
function  SetTravelEventAnswer() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$event_answer_sign = ReqArray('event_answer_sign');
	$event_answer = ReqArray('event_answer');
	$award = ReqArray('award');
	$coin = ReqArray('coin');
	$exp = ReqArray('exp');
	$fame = ReqArray('fame');
	$power = ReqArray('power');
	$skill = ReqArray('skill');
	
	$event_id = ReqNum('event_id');
	$event_answer_sign_n = ReqStr('event_answer_sign_n');
	$event_answer_n = ReqStr('event_answer_n');
	$award_n = ReqStr('award_n');
	$coin_n = ReqNum('coin_n');
	$exp_n = ReqNum('exp_n');
	$fame_n = ReqNum('fame_n');
	$power_n = ReqNum('power_n');
	$skill_n = ReqNum('skill_n');
	
	$url = ReqStr('url','htm');
	$winid = ReqStr('winid');
	
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $event_answer_sign[$i] && $event_answer[$i])
			{

				$db->query("
				update 
					travel_event_answer 
				set 
					`event_answer_sign`='$event_answer_sign[$i]',
					`event_answer`='$event_answer[$i]',
					`award`='$award[$i]',
					`coin`='$coin[$i]',
					`exp`='$exp[$i]',
					`fame`='$fame[$i]',
					`power`='$power[$i]',
					`skill`='$skill[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($event_id  && $event_answer_sign_n && $event_answer_n)
	{
	
		$query = $db->query("
		insert into 
			travel_event_answer
			(`event_answer_sign`,`event_answer`,`award`,`coin`,`exp`,`fame`,`power`,`skill`,`event_id`) 
		values 
			('$event_answer_sign_n','$event_answer_n','$award_n','$coin_n','$exp_n','$fame_n','$power_n','$skill_n','$event_id')
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
		$db->query("delete from travel_event_answer where id in ($id_arr)");
		
		$msg .= "<br />删除成功！";
	}
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);

}


//--------------------------------------------------------------------------------------------批量设置新手在线奖励
function  SetOnlineGift() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$timestamp = ReqArray('timestamp');
	$type = ReqArray('type');
	$target_id = ReqArray('target_id');
	$value = ReqArray('value');
	
	$timestamp_n = ReqNum('timestamp_n');
	$type_n = ReqNum('type_n');
	$target_id_n = ReqNum('target_id_n');
	$value_n = ReqNum('value_n');
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $timestamp[$i] && $value[$i])
			{
				$timestamp[$i] = $timestamp[$i]*60*1000;
				$db->query("
				update 
					online_gift 
				set 
					`timestamp`='$timestamp[$i]',
					`type`='$type[$i]',
					`target_id`='$target_id[$i]',
					`value`='$value[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($timestamp_n && $value_n)
	{
		$timestamp_n = $timestamp_n*60*1000;
		$query = $db->query("
		insert into 
			online_gift
			(`timestamp`,`type`,`target_id`,`value`) 
		values 
			('$timestamp_n','$type_n','$target_id_n','$value_n')
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
		$db->query("delete from online_gift where id in ($id_arr)");
		$msg .= "<br />删除成功！";
	}
	showMsg($msg,'','','greentext');	

}


//--------------------------------------------------------------------------------------------批量设置挂机练功等级
function  SetPracticeLevel() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$experience = ReqArray('experience');
	
	$player_level_n = ReqNum('player_level_n');
	$experience_n = ReqNum('experience_n');
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $experience[$i])
			{
				$db->query("
				update 
					practice_level 
				set 
					`experience`='$experience[$i]'
				where 
					player_level = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($player_level_n && $experience_n)
	{
		$query = $db->query("
		insert into 
			practice_level
			(`player_level`,`experience`) 
		values 
			('$player_level_n','$experience_n')
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
		$db->query("delete from practice_level where player_level in ($id_arr)");
		$msg .= "<br />删除成功！";
	}
	showMsg($msg,'','','greentext');	

}

//--------------------------------------------------------------------------------------------批量设置成就
function  SetAchievement() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$sign = ReqArray('sign');
	$name = ReqArray('name');
	$content = ReqArray('content');
	$total = ReqArray('total');
	$points = ReqArray('points');
	$special_award = ReqArray('special_award');
	$sort_order = ReqArray('sort_order');
	$tag = ReqArray('tag');
	$time_type = ReqArray('time_type');


	$sign_n = ReqStr('sign_n');
	$name_n = ReqStr('name_n');
	$content_n = ReqStr('content_n');
	$total_n = ReqNum('total_n');
	$points_n = ReqNum('points_n');
	$special_award_n = ReqStr('special_award_n');
	$sort_order_n = ReqNum('sort_order_n');
	$tag_n = ReqNum('tag_n');
	$time_type_n = ReqNum('time_type_n');
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $name[$i] && $sign[$i] && $points[$i] && $tag[$i])
			{
				$db->query("
				update 
					achievement 
				set 
					`sign`='$sign[$i]',
					`name`='$name[$i]',
					`content`='$content[$i]',
					`total`='$total[$i]',
					`points`='$points[$i]',
					`special_award`='$special_award[$i]',
					`sort_order`='$sort_order[$i]',
					`tag`='$tag[$i]',
					`time_type`='$time_type[$i]'
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($name_n && $sign_n && $points_n && $tag_n)
	{
		$query = $db->query("
		insert into 
			achievement
			(`sort_order`,`sign`,`name`,`content`,`total`,`points`,`special_award`,`tag`,`time_type`) 
		values 
			('$sort_order_n','$sign_n','$name_n','$content_n','$total_n','$points_n','$special_award_n','$tag_n','$time_type_n')
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
		$db->query("delete from achievement where id in ($id_arr)");
		$msg .= "<br />删除成功！";
	}
	showMsg($msg,'','','greentext');	

}

//--------------------------------------------------------------------------------------------批量设置成就标签
function  SetAchievementTag() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$type = ReqArray('type');
	$parent_id = ReqArray('parent_id');

	$name_n = ReqStr('name_n');
	$type_n = ReqStr('type_n');
	$parent_id_n = ReqNum('parent_id_n');

	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $name[$i])
			{
				$db->query("
				update 
					achievement_tag
				set 
					`type`='$type[$i]',
					`name`='$name[$i]',
					`parent_id`='$parent_id[$i]'
				where 
					id = '$id[$i]'
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
			achievement_tag
			(`type`,`name`,`parent_id` )
		values 
			('$type_n','$name_n','$parent_id_n')
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
		$db->query("delete from achievement where tag in ($id_arr)");
		$db->query("delete from achievement_tag where id in ($id_arr)");
		$msg .= "<br />删除成功！";
	}
	showMsg($msg,'','','greentext');	

}

//--------------------------------------------------------------------------------------------批量设置渡劫境界
function  SetSpiritState() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');

	$name_n = ReqStr('name_n');

	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $name[$i])
			{
				$db->query("
				update 
					spirit_state
				set 
					`name`='$name[$i]'
				where 
					id = '$id[$i]'
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
			spirit_state
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
		$db->query("delete from spirit_state_require where spirit_state_id in ($id_arr)");
		$db->query("delete from spirit_state where id in ($id_arr)");
		$msg .= "<br />删除成功！";
	}
	showMsg($msg,'','','greentext');	

}


//--------------------------------------------------------------------------------------------批量设置渡劫境界等级需求
function  SetSpiritStateRequire() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$spirit_state_id = ReqArray('spirit_state_id');
	$level = ReqArray('level');
	$role_level = ReqArray('role_level');
	$state_point = ReqArray('state_point');
	$multiple_mission_id = ReqArray('multiple_mission_id');
	$health = ReqArray('health');
	$monster_team_id = ReqArray('monster_team_id');
	
	$spirit_state_id_n = ReqNum('spirit_state_id_n');
	$level_n = ReqNum('level_n');
	$role_level_n = ReqNum('role_level_n');
	$state_point_n = ReqNum('state_point_n');
	$multiple_mission_id_n = ReqNum('multiple_mission_id_n');
	$health_n = ReqNum('health_n');
	$monster_team_id_n = ReqNum('monster_team_id_n');

	$url = ReqStr('url','htm');
	$winid=ReqStr('winid');	
	
	//-----------------更新-------------------------------------------
	if ($spirit_state_id)
	{
	
		$id_num = count($spirit_state_id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($spirit_state_id[$i] && $level[$i])
			{

				$db->query("
				update 
					spirit_state_require 
				set 
					`multiple_mission_id`='$multiple_mission_id[$i]',
					`role_level`='$role_level[$i]',
					`state_point`='$state_point[$i]',
					`health`='$health[$i]',
					`monster_team_id`='$monster_team_id[$i]'
					
				where 
					spirit_state_id = '$spirit_state_id[$i]'
				and 
					level = '$level[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($spirit_state_id_n && $level_n)
	{
	
		$query = $db->query("
		insert into 
			spirit_state_require
			(`spirit_state_id`,`level`,`role_level`,`state_point`,`health`,`multiple_mission_id`,`monster_team_id`) 
		values 
			('$spirit_state_id_n','$level_n','$role_level_n','$state_point_n','$health_n','$multiple_mission_id_n','$monster_team_id_n')
		") ;	
		if($query)
		{
			$msg .= " 增加成功！";
		}
		else
		{
			$msg .= ' <strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}		
	//----------------------删除--------------------------------------
	if ($id_del)
	{
		$delidNum = count($id_del);
		for ($i=0;$i<=$delidNum;$i++)	{
			$idArr = explode(',',$id_del[$i]);
			$db->query("delete from spirit_state_require where spirit_state_id = '$idArr[0]' and level = '$idArr[1]'");
		}
		$msg .= " 删除成功！";		
	}	
	
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);

}

//--------------------------------------------------------------------------------------------批量设置点数
function  SetRollCount() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$number = ReqArray('number');
	$text = ReqArray('text');
	$picture = ReqArray('picture');
	
	$number_n = ReqNum('number_n');
	$text_n = ReqStr('text_n');
	$picture_n = ReqStr('picture_n');

	
	//-----------------更新-------------------------------------------
	if ($number)
	{
	
		$id_num = count($number);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($number[$i] && $text[$i])
			{

				$db->query("
				update 
					roll_count 
				set 
					`text`='$text[$i]',
					`picture`='$picture[$i]'
				where 
					`number` = '$number[$i]'
				");
			}
			
		}
		$msg = "<br />更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($number_n && $text_n)
	{
	
		$query = $db->query("
		insert into 
			roll_count
			(`number`,`text`,`picture`) 
		values 
			('$number_n','$text_n','$picture_n')
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
		$db->query("delete from roll_count where `number` in ($id_arr)");
		$msg .= "<br />删除成功！";
	}

	
	showMsg($msg,'','','greentext');	

}

//--------------------------------------------------------------------------------------------批量设置博饼
function  SetRollCake() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$nick_name = ReqArray('nick_name');
	$skill = ReqArray('skill');
	$state_point = ReqArray('state_point');
	$coin = ReqArray('coin');

	$id_n = ReqNum('id_n');
	$name_n = ReqStr('name_n');
	$nick_name_n = ReqStr('nick_name_n');
	$skill_n = ReqNum('skill_n');
	$state_point_n = ReqNum('state_point_n');
	$coin_n = ReqNum('coin_n');
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $name[$i])
			{

				$db->query("
				update 
					roll_cake 
				set 
					`name`='$name[$i]',
					`nick_name`='$nick_name[$i]',
					`skill`='$skill[$i]',
					`state_point`='$state_point[$i]',
					`coin`='$coin[$i]'
				where 
					`id` = '$id[$i]'
				");
			}
			
		}
		$msg = "<br />更新成功！";
	}
	//-----------------增加记录-------------------------------------------
	if ($id_n && $name_n)
	{
	
		$query = $db->query("
		insert into 
			roll_cake
			(`id`,`name`,`nick_name`,`skill`,`state_point`,`coin`) 
		values 
			('$id_n','$name_n','$nick_name_n','$skill_n','$state_point_n','$coin_n')
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
		$db->query("delete from roll_cake where `id` in ($id_arr)");
		$msg .= "<br />删除成功！";
	}

	
	showMsg($msg,'','','greentext');	

}


//--------------------------------------------------------------------------------------------批量设置黄钻奖励
function  SetYellowGiftAward() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$type = ReqArray('type');
	$lv = ReqArray('lv');
	$yellow_lv = ReqArray('yellow_lv');
	$coin = ReqArray('coin');
	$fame = ReqArray('fame');
	$skill = ReqArray('skill');

	$id_n = ReqNum('id_n');
	$type_n = ReqNum('type_n');
	$lv_n = ReqNum('lv_n');
	$yellow_lv_n = ReqNum('yellow_lv_n');
	$coin_n = ReqNum('coin_n');
	$fame_n = ReqNum('fame_n');
	$skill_n = ReqNum('skill_n');
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
					yellow_gift_award 
				set 
					`type`='$type[$i]',
					`lv`='$lv[$i]',
					`yellow_lv`='$yellow_lv[$i]',
					`coin`='$coin[$i]',
					`fame`='$fame[$i]',
					`skill`='$skill[$i]'
				where 
					`id` = '$id[$i]'
				");
			}
			
		}
		$msg = "<br />更新成功！";
	}
	//-----------------增加记录-------------------------------------------
	if ($lv_n && $yellow_lv_n)
	{
	
		$query = $db->query("
		insert into 
			yellow_gift_award
			(`type`,`lv`,`yellow_lv`,`coin`,`fame`,`skill`) 
		values 
			('$type_n','$lv_n','$yellow_lv_n','$coin_n','$fame_n','$skill_n')
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
		$db->query("delete from yellow_gift_award where `id` in ($id_arr)");
		$msg .= "<br />删除成功！";
	}

	
	showMsg($msg,'','','greentext');	

}
//--------------------------------------------------------------------------------------------批量设置金油数据
function  SetGoldOil() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$item_id = ReqArray('item_id');
	$name = ReqArray('name');
	$item_lv = ReqArray('item_lv');
	$oil_lv = ReqArray('oil_lv');
	$need_item_id = ReqArray('need_item_id');
	$use_coin = ReqArray('use_coin');
	$use_state_point = ReqArray('use_state_point');
	$get_state_point = ReqArray('get_state_point');
	$use_ingot = ReqArray('use_ingot');

	$item_id_n = ReqNum('item_id_n');
	$name_n = ReqStr('name_n');
	$item_lv_n = ReqNum('item_lv_n');
	$oil_lv_n = ReqNum('oil_lv_n');
	$need_item_id_n = ReqNum('need_item_id_n');
	$use_coin_n = ReqNum('use_coin_n');
	$use_state_point_n = ReqNum('use_state_point_n');
	$get_state_point_n = ReqNum('get_state_point_n');
	$use_ingot_n = ReqNum('use_ingot');

	//-----------------更新-------------------------------------------
	if ($item_id)
	{
	
		$id_num = count($item_id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($item_id[$i])
			{

				$db->query("
				update 
					gold_oil 
				set 
					`name`='$name[$i]',
					`item_lv`='$item_lv[$i]',
					`oil_lv`='$oil_lv[$i]',
					`need_item_id`='$need_item_id[$i]',
					`use_coin`='$use_coin[$i]',
					`use_state_point`='$use_state_point[$i]',
					`get_state_point`='$get_state_point[$i]',
					`use_ingot`='$use_ingot[$i]'					
				where 
					`item_id` = '$item_id[$i]'
				");
			}
			
		}
		$msg = "<br />更新成功！";
	}
	//-----------------增加记录-------------------------------------------
	if ($item_id_n && $name_n)
	{
	
		$query = $db->query("
		insert into 
			gold_oil
			(`item_id`,`name`,`item_lv`,`oil_lv`,`need_item_id`,`use_coin`,`use_state_point`,`get_state_point`,`use_ingot`) 
		values 
			('$item_id_n','$name_n','$item_lv_n','$oil_lv_n','$need_item_id_n','$use_coin_n','$use_state_point_n','$get_state_point_n','$use_ingot_n')
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
		$db->query("delete from gold_oil where `item_id` in ($id_arr)");
		$msg .= "<br />删除成功！";
	}

	
	showMsg($msg,'','','greentext');	

}


//--------------------------------------------------------------------------------------------批量设置金油提升装备数据
function  SetCallGoldOilData() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$gold_oil_item_id = ReqArray('gold_oil_item_id');
	$item_type = ReqArray('item_type');
	$item_type_old = ReqArray('item_type_old');
	$item_attack_up = ReqArray('item_attack_up');
	$item_defense_up = ReqArray('item_defense_up');
	$item_stunt_attack_up = ReqArray('item_stunt_attack_up');
	$item_stunt_defense_up = ReqArray('item_stunt_defense_up');
	$item_magic_attack_up = ReqArray('item_magic_attack_up');
	$item_magic_defense_up = ReqArray('item_magic_defense_up');
	$item_health_up = ReqArray('item_health_up');
	$attack_type_old = ReqArray('attack_type_old');
	$attack_type = ReqArray('attack_type');
	$item_speed_up = ReqArray('item_speed_up');

	$gold_oil_item_id_n = ReqNum('gold_oil_item_id_n');
	$item_type_n = ReqNum('item_type_n');
	$item_attack_up_n = ReqNum('item_attack_up_n');
	$item_defense_up_n = ReqNum('item_defense_up_n');
	$item_stunt_attack_up_n = ReqNum('item_stunt_attack_up_n');
	$item_stunt_defense_up_n = ReqNum('item_stunt_defense_up_n');
	$item_magic_attack_up_n = ReqNum('item_magic_attack_up_n');
	$item_magic_defense_up_n = ReqNum('item_magic_defense_up_n');
	$item_health_up_n = ReqNum('item_health_up_n');
	$attack_type_n = ReqNum('attack_type_n');
	$item_speed_up_n =ReqNum('item_speed_up_n');

	$url = ReqStr('url','htm');
	$winid=ReqStr('winid');	
	
	//-----------------更新-------------------------------------------
	if ($gold_oil_item_id)
	{
	
		$id_num = count($gold_oil_item_id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($gold_oil_item_id[$i] && $item_type[$i] && $item_type_old[$i])
			{

				$db->query("
				update 
					gold_oil_data 
				set 
					`item_type`='$item_type[$i]',
					`item_attack_up`='$item_attack_up[$i]',
					`item_defense_up`='$item_defense_up[$i]',
					`item_stunt_attack_up`='$item_stunt_attack_up[$i]',
					`item_stunt_defense_up`='$item_stunt_defense_up[$i]',
					`item_magic_attack_up`='$item_magic_attack_up[$i]',
					`item_magic_defense_up`='$item_magic_defense_up[$i]',
					`item_health_up`='$item_health_up[$i]',
					`attack_type`='$attack_type[$i]',
					`item_speed_up`='$item_speed_up[$i]'
				where 
					gold_oil_item_id = '$gold_oil_item_id[$i]'
				and 
					item_type = '$item_type_old[$i]'
				and 
					attack_type = '$attack_type_old[$i]'					
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($gold_oil_item_id_n && $item_type_n)
	{
	
		$query = $db->query("
		insert into 
			gold_oil_data
			(`gold_oil_item_id`,`item_type`,`item_attack_up`,`item_defense_up`,`item_stunt_attack_up`,`item_stunt_defense_up`,`item_magic_attack_up`,`item_magic_defense_up`,`item_health_up`,`attack_type`,`item_speed_up`) 
		values 
			('$gold_oil_item_id_n','$item_type_n','$item_attack_up_n','$item_defense_up_n','$item_stunt_attack_up_n','$item_stunt_defense_up_n','$item_magic_attack_up_n','$item_magic_defense_up_n','$item_health_up_n','$attack_type_n','$item_speed_up_n')
		") ;	
		if($query)
		{
			$msg .= " 增加成功！";
		}
		else
		{
			$msg .= ' <strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}		
	//----------------------删除--------------------------------------
	if ($id_del)
	{
		$delidNum = count($id_del);
		for ($i=0;$i<=$delidNum;$i++)	{
			$idArr = explode(',',$id_del[$i]);
			$db->query("delete from gold_oil_data where gold_oil_item_id = '$idArr[0]' and item_type = '$idArr[1]' and attack_type = '$idArr[2]'");
		}
		$msg .= " 删除成功！";		
	}	
	
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);

}

//--------------------------------------------------------------------------------------------批量设置消费提醒类型
function  SetConsumeAlertSetType() 
{
	global $db; 
	$id_del = ReqArray('id_del');
	$id = ReqArray('id');
	$name = ReqArray('name');
	$description = ReqArray('description');
	$vip = ReqArray('vip');

	$name_n = ReqStr('name_n');
	$description_n = ReqStr('description_n');
	$vip_n = ReqNum('vip_n');

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
					consume_alert_set_type 
				set 
					`name`='$name[$i]',
					`description`='$description[$i]',
					`vip`='$vip[$i]'				
					
				where 
					`id` = '$id[$i]'
				");
			}
			
		}
		$msg = "<br />更新成功！";
	}
	//-----------------增加记录-------------------------------------------
	if ($name_n)
	{
	
		$query = $db->query("
		insert into 
			consume_alert_set_type
			(`name`,`description`,`vip`) 
		values 
			('$name_n','$description_n','$vip_n')
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
		$db->query("delete from consume_alert_set_type where `id` in ($id_arr)");
		$msg .= "<br />删除成功！";
	}

	
	showMsg($msg,'','','greentext');	

}

//--------------------------------------------------------------------------------------------批量设置周排行
function  SetWeekRanking() 
{
	global $db; 
	global $id,$id_del,$desc,$sign,$lock; 
	global $desc_n,$sign_n,$lock_n; 
	
	//-----------------更新-------------------------------------------
	if ($id)
	{
	
		$id_num = count($id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($id[$i] && $sign[$i] && $desc[$i])
			{

				$db->query("
				update 
					week_ranking 
				set 
					`desc`='$desc[$i]',
					`sign`='$sign[$i]',
					`lock`='$lock[$i]'				
				where 
					id = '$id[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($sign_n && $desc_n  )
	{
	
		$query = $db->query("
		insert into 
			week_ranking
			(`desc`,`sign`,`lock`) 
		values 
			('$desc_n','$sign_n','$lock_n')
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
		$db->query("delete from week_ranking where `id` in ($id_arr)");
		$msg .= "<br />删除成功！";
	}	
	
	showMsg($msg,'','','greentext');	

}


//--------------------------------------------------------------------------------------------批量设置周排行奖励
function  SetWeekRankingAward() 
{
	global $db; 
	global $week_ranking_id,$id_del,$rank,$item_id; 
	global $week_ranking_id_n,$rank_n,$item_id_n; 
	$url = ReqStr('url','htm');
	$winid=ReqStr('winid');		
	//-----------------更新-------------------------------------------
	if ($week_ranking_id)
	{
	
		$id_num = count($week_ranking_id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($week_ranking_id[$i] && $rank[$i] && $item_id[$i])
			{

				$db->query("
				update 
					week_ranking_award 
				set 
					`item_id`='$item_id[$i]'			
				where 
					week_ranking_id = '$week_ranking_id[$i]'
					and rank = '$rank[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($week_ranking_id_n && $rank_n  && $item_id_n)
	{
	
		$query = $db->query("
		insert into 
			week_ranking_award
			(`week_ranking_id`,`rank`,`item_id`) 
		values 
			('$week_ranking_id_n','$rank_n','$item_id_n')
		") ;	
		if($query)
		{
			$msg .= " 增加成功！";
		}
		else
		{
			$msg .= ' <strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}		
	//----------------------删除--------------------------------------
	if ($id_del)
	{
		$delidNum = count($id_del);
		for ($i=0;$i<$delidNum;$i++)	{
			$idArr = explode(',',$id_del[$i]);
			$db->query("delete from week_ranking_award where week_ranking_id = '$idArr[0]' and `rank` = '$idArr[1]'");
		}
		$msg .= " 删除成功！";

	}	
	
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);

}


//--------------------------------------------------------------------------------------------批量设置周排日行奖励
function  SetWeekRankingDayAward() 
{
	global $db; 
	global $week_ranking_id,$id_del,$rank,$coin,$fame; 
	global $week_ranking_id_n,$rank_n,$coin_n,$fame_n; 
	$url = ReqStr('url','htm');
	$winid=ReqStr('winid');		
	//-----------------更新-------------------------------------------
	if ($week_ranking_id)
	{
	
		$id_num = count($week_ranking_id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($week_ranking_id[$i] && $rank[$i])
			{

				$db->query("
				update 
					week_ranking_day_award 
				set 
					`coin`='$coin[$i]',
					`fame`='$fame[$i]'
				where 
					week_ranking_id = '$week_ranking_id[$i]'
					and rank = '$rank[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($week_ranking_id_n && $rank_n)
	{
	
		$query = $db->query("
		insert into 
			week_ranking_day_award
			(`week_ranking_id`,`rank`,`coin`,`fame`) 
		values 
			('$week_ranking_id_n','$rank_n','$coin_n','$fame_n')
		") ;	
		if($query)
		{
			$msg .= " 增加成功！";
		}
		else
		{
			$msg .= ' <strong class="redtext">增加失败，可能因为您输入了重复的数据！</strong>';
		}
	}		
	//----------------------删除--------------------------------------
	if ($id_del)
	{
		$delidNum = count($id_del);
		for ($i=0;$i<$delidNum;$i++)	{
			$idArr = explode(',',$id_del[$i]);
			$db->query("delete from week_ranking_day_award where week_ranking_id = '$idArr[0]' and `rank` = '$idArr[1]'");
		}
		$msg .= " 删除成功！";

	}	
	
	$msg = urlencode($msg);		
	showMsg($msg,$url.'&msg='.$msg,'ajax','greentext',$winid);

}

//--------------------------------------------------------------------------------------------批量设置仙石属性
function  SetAttributeStone() 
{
	global $db; 
	global $item_id,$item_id_old,$lv_old,$id_del,$item_id,$lv,$war_attribute_type_id,$value,$need_item_id,$need_item_count,$src_item_id,$book_id,$change_need_coin,$out_need_coin,$merge_need_coin; 
	global $item_id_n,$lv_n,$war_attribute_type_id_n,$value_n,$need_item_id_n,$need_item_count_n,$src_item_id_n,$book_id_n,$change_need_coin_n,$out_need_coin_n,$merge_need_coin_n;

	//-----------------更新-------------------------------------------
	if ($item_id)
	{
	
		$id_num = count($item_id);

		for ($i=0;$i<=$id_num;$i++)	
		{
			if ($item_id[$i])
			{

				$db->query("
				update 
					attribute_stone 
				set 
					`item_id`='$item_id[$i]',
					`lv`='$lv[$i]',
					`war_attribute_type_id`='$war_attribute_type_id[$i]',
					`value`='$value[$i]',
					`need_item_id`='$need_item_id[$i]',
					`need_item_count`='$need_item_count[$i]',
					`src_item_id`='$src_item_id[$i]',
					`book_id`='$book_id[$i]',
					`change_need_coin`='$change_need_coin[$i]',
					`out_need_coin`='$out_need_coin[$i]',
					`merge_need_coin`='$merge_need_coin[$i]'
				where 
					item_id = '$item_id_old[$i]'
					and lv = '$lv_old[$i]'
				");
			}
			
		}
		$msg = "更新成功！";
	}
		
	//-----------------增加记录-------------------------------------------
	if ($item_id_n)
	{
	
		$query = $db->query("
		insert into 
			attribute_stone
			(`item_id`,`lv`,`war_attribute_type_id`,`value`,`need_item_id`,`need_item_count`,`src_item_id`,`book_id`,`change_need_coin`,`out_need_coin`,`merge_need_coin`) 
		values 
			('$item_id_n','$lv_n','$war_attribute_type_id_n','$value_n','$need_item_id_n','$need_item_count_n','$src_item_id_n','$book_id_n','change_need_coin_n','out_need_coin_n','merge_need_coin_n')
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
		$delidNum = count($id_del);
		for ($i=0;$i<$delidNum;$i++)	{
			$idArr = explode(',',$id_del[$i]);
			$db->query("delete from attribute_stone where item_id = '$idArr[0]' and `lv` = '$idArr[1]'");
		}
		$msg .= "<br />删除成功！";

	}	
	
	showMsg($msg,'','','greentext');	

}


?>