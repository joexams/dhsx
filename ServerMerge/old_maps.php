<?php
// 2013-06-08-01
$DATA_MAPS = array( //array(表, array(array(外部字段, 引用表, 引用表的字段)), array(需要修改结构的字段) 或 "log"是否记录表)
//	array('player', 
//			array( //引用外部表的字段
//				array('id', 'player', 'id'),
//				array('main_role_id', 'player_role', 'id')
//			), 
//			
//			//对名称字段结构需要特殊处理, array(字段名, 新长度, 新ISNULL, 新注释, 是否一定要加后缀)
//			array(
//				array('id', 'player', 'id'), //唯一键条件
//				array('username', 120, "NOT NULL", "玩家用户名", true),
//				array('nickname', 40, "NOT NULL", "玩家昵称", true),
//				array('lower_case_nickname', 40, "NOT NULL", "玩家小写昵称", true),
//				array('lower_case_username', 120, "NULL", "玩家小写用户名", true)
//			)
//		),
//	array('player_role', 
//			array(
//				array('id', 'player_role', 'id'),
//				array('player_id', 'player', 'id'),
//				array('trans_player_role_id', 'player_role', 'id'),
//				array('be_trans_player_role_id', 'player_role', 'id')
//			)
//		),
//	array('player_faction', 
//			array(
//				array('id', 'player_faction', 'id'),
//				array('player_id', 'player', 'id')
//			), 
//			
//			//对名称字段结构需要特殊处理
//			array(
//				array('id', 'player_faction', 'id'), //唯一键条件
//				array('name', 40, "NOT NULL", "帮派名称"),
//				array('master_name', 40, "NOT NULL", "帮主名称", true)
//			)
//		),
//	array('player_fate', 
//			array(
//				array('id', 'player_fate', 'id'),
//				array('player_id', 'player', 'id'),
//				array('player_role_id', 'player_role', 'id')
//			)
//		),
//	array('player_world_war', 
//			array(
//				array('player_id', 'player', 'id')
//			)
//		),
//	array('player_item', 
//			array(
//				array('id', 'player_item', 'id'),
//				array('player_id', 'player', 'id'),
//				array('player_role_id', 'player_role', 'id')
//			)
//		),
//	array('player_new_back_times_res', 
//			array(
//				array('id', 'player_new_back_times_res', 'id'),
//				array('player_id', 'player', 'id')
//			)
//		),
//	array('player_item_deploy_start', 
//			array(
//				array('id', 'player_item_deploy_start', 'id'),
//				array('player_id', 'player', 'id')
//			)
//		),
//	array('player_deploy_start', 
//			array(
//				array('id', 'player_deploy_start', 'id'),
//				array('player_id', 'player', 'id')
//			)
//		),
//	array('player_enhance_weapon_info', 
//			array(
//				array('player_id', 'player', 'id')
//			)
//		),
//	array('player_enhance_weapon', 
//			array(
//				array('player_role', 'player_role', 'id'),
//				array('player_id', 'player', 'id')
//			)
//		),
//	array('player_new_treasure_hunt', 
//			array(
//				array('player_id', 'player', 'id')
//			)
//		),
//	array('player_jun_activity', 
//			array(
//				array('player_id', 'player', 'id')
//			)
//		),
//	array('player_june_activity', 
//			array(
//				array('player_id', 'player', 'id')
//			)
//		),
//	array('player_self_info', 
//			array(
//				array('player_id', 'player', 'id')
//			)
//		),
//	array('player_consumption_draw', 
//			array(
//				array('player_id', 'player', 'id')
//			)
//		),
//		
//	array('player_fish_flag', 
//			array(
//				array('player_id', 'player', 'id')
//			)
//		),
//	array('player_library', 
//			array(
//				array('player_id', 'player', 'id')
//			)
//		),
//	array('player_library_level', 
//			array(
//				array('player_id', 'player', 'id')
//			)
//		),
//	array('player_xianling_tree_data', 
//			array(
//				array('player_id', 'player', 'id')
//			)
//		),
//	array('player_nine_regions_challenge_record', 
//			array(
//				array('player_id', 'player', 'id')
//			)
//		),
//	array('player_jin_xian_gift', 
//			array(
//				array('player_id', 'player', 'id')
//			)
//		),
//	array('player_nine_regions_hidden', 
//			array(
//				array('player_id', 'player', 'id')
//			)
//		),
//	array('player_pcmgr_sign_in', 
//			array(
//				array('player_id', 'player', 'id')
//			)
//		),
//	array('player_server_login_bu_chang', 
//			array(
//				array('player_id', 'player', 'id')
//			)
//		),
//	array('player_server_award', 
//			array(
//				array('player_id', 'player', 'id')
//			)
//		),
//	array('player_item_attribute_stone', 
//			array(
//				array('id', 'player_item_attribute_stone', 'id'),
//				array('player_id', 'player', 'id')
//			)
//		),
//	array('player_gift', 
//			array(
//				array('id', 'player_gift', 'id'),
//				array('player_id', 'player', 'id'),
//				array('player_item_id', 'player_item', 'id')
//			)
//		),
//	array('player_war_report', 
//			array(
//				array('id', 'player_war_report', 'id')
//			),
//			"clear"
//		),
//		
//	array('player_faction_job', 
//			array(
//				array('id', 'player_faction_job', 'id'),
//				array('player_id', 'player', 'id'),
//				array('faction_id', 'player_faction', 'id')
//			)
//		),
//	array('player_faction_member', 
//			array(
//				array('id', 'player_faction_member', 'id'),
//				array('player_id', 'player', 'id'),
//				array('faction_id', 'player_faction', 'id')
//			)
//		),
//	array('player_faction_notice', 
//			array(
//				array('id', 'player_faction_notice', 'id'),
//				array('player_id', 'player', 'id'),
//				array('faction_id', 'player_faction', 'id')
//			)
//		),
//	array('player_faction_request', 
//			array(
//				array('id', 'player_faction_request', 'id'),
//				array('player_id', 'player', 'id'),
//				array('faction_id', 'player_faction', 'id')
//			)
//		),
//	array('player_farmland', 
//			array(
//				array('id', 'player_farmland', 'id'),
//				array('player_id', 'player', 'id'),
//				array('player_role_id', 'player_role', 'id')
//			)
//		),
//	array('player_send_flower_log', 
//			array(
//				array('id', 'player_send_flower_log', 'id'),
//				array('player_id', 'player', 'id'),
//				array('from_player_id', 'player', 'id')
//			)
//		),
//	array('player_chat_shield', 
//			array(
//				array('id', 'player_chat_shield', 'id'),
//				array('player_id', 'player', 'id'),
//				array('target_id', 'player', 'id')
//			)
//		),
//	array('player_soul', 
//			array(
//				array('id', 'player_soul', 'id'),
//				array('player_id', 'player', 'id')
//			)
//		),
//	array('player_appoint_fate', 
//			array(
//				array('id', 'player_appoint_fate', 'id'),
//				array('player_id', 'player', 'id')
//			)
//		),
//	
//	array('player_against_wallows_info', 
//			array(
//				array('player_id', 'player', 'id')
//			)
//		),
//	array('player_mission_box', 
//			array(
//				array('player_id', 'player', 'id')
//			)
//		),
//	array('player_special_award', 
//			array(
//				array('player_id', 'player', 'id')
//			)
//		),
//	array('player_at_last_pos', 
//			array(
//				array('player_id', 'player', 'id')
//			)
//		),
//	array('player_cd_time', 
//			array(
//				array('player_id', 'player', 'id')
//			)
//		),
//		
//	array('player_halloween', 
//			array(
//				array('player_id', 'player', 'id')
//			)
//		),
//		
//	array('player_pk', 
//			array(
//				array('player_id', 'player', 'id')
//			)
//		),
		
	array('player_charge_record', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_chat_record', 
			array(
				array('player_id', 'player', 'id'),
				array('to_id', 'player', 'id')
			)
		),
	array('player_coin_change_record', 
			array(
				array('player_id', 'player', 'id')
			), 
			"log"
		),
	array('player_coin_tree_count_log', 
			array(
				array('player_id', 'player', 'id')
			), 
			"log"
		),
	array('player_data', 
			array(
				array('player_id', 'player', 'id'),
				array('follow_role_id', 'player_role', 'id'),
				array('mounted_player_item', 'player_item', 'id'),
				array('player_clothes_item_id', 'player_item', 'id')
			)
		),
	array('player_assistant', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_day_quest', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_defeat_world_boss_record', 
			array(
				array('player_id', 'player', 'id')
			), 
			"log"
		),
	array('player_delay_notify_message', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_deploy_grid', 
			array(
				array('player_id', 'player', 'id'),
				array('player_role_id', 'player_role', 'id')
			)
		),
	array('player_faction_banquet', 
			array(
				array('player_id', 'player', 'id'),
				array('faction_id', 'player_faction', 'id')
			)
		),
	array('player_faction_banquet_member', 
			array(
				array('player_id', 'player', 'id'),
				array('faction_id', 'player_faction', 'id')
			)
		),
	array('player_faction_boss_defeat', 
			array(
				array('player_id', 'player', 'id'),
				array('faction_id', 'player_faction', 'id')
			)
		),
	array('player_faction_contribution', 
			array(
				array('player_id', 'player', 'id'),
				array('faction_id', 'player_faction', 'id')
			)
		),
	array('player_faction_incense_log', 
			array(
				array('player_id', 'player', 'id'),
				array('faction_id', 'player_faction', 'id')
			)
		),
	array('player_faction_seal_satan_member', 
			array(
				array('player_id', 'player', 'id'),
				array('faction_id', 'player_faction', 'id')
			)
		),
	array('player_faction_war_gift', 
			array(
				array('player_id', 'player', 'id'),
				array('faction_id', 'player_faction', 'id')
			)
		),
	array('player_faction_war_member', 
			array(
				array('player_id', 'player', 'id'),
				array('faction_id', 'player_faction', 'id')
			)
		),
	array('player_fame_log', 
			array(
				array('player_id', 'player', 'id')
			), 
			"log"
		),
	array('player_farmland_data', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_farmland_log', 
			array(
				array('player_id', 'player', 'id'),
				array('player_role_id', 'player_role', 'id')
			), 
			"log"
		),
	array('player_fate_log', 
			array(
				array('player_id', 'player', 'id'),
				array('player_fate_id', 'player_fate', 'id'),
			), 
			"log"
		),
	array('player_fate_npc', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_fight_boss_combat', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_friends', 
			array(
				array('player_id', 'player', 'id'),
				array('friend_id', 'player', 'id')
			)
		),
	array('player_attribute_stone_skill', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_herbs', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_herbs_seed', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_ingot_change_record', 
			array(
			    array('id', 'player_ingot_change_record', 'id'),
				array('player_id', 'player', 'id')
			)
		),
		
	array('player_faction_contribution_record', 
			array(
			    array('player_id', 'player', 'id'),
				array('faction_id', 'player_faction', 'id')
			)
		),
		
	array('player_item_change_record', 
			array(
				array('player_id', 'player', 'id'),
				array('player_item_id', 'player_item', 'id')
			), 
			"log"
		),
	array('player_item_change_record2', 
			array(
				array('player_id', 'player', 'id'),
				array('player_item_id', 'player_item', 'id')
			), 
			"log"
		),
	array('player_faction_contribution_log', 
			array(
				array('player_id', 'player', 'id'),
				array('faction_id', 'player_faction', 'id')
			), 
			"log"
		),
	array('player_item_soul', 
			array(
				array('player_id', 'player', 'id'),
				array('player_item_id', 'player_item', 'id'),
				array('player_soul_id_location_1','player_soul','id'),
				array('player_soul_id_location_2','player_soul','id'),
				array('player_soul_id_location_3','player_soul','id'),
				array('player_soul_id_location_4','player_soul','id'),
				array('player_soul_id_location_5','player_soul','id'),
				array('player_soul_id_location_6','player_soul','id')
			)
		),
	array('player_join_faction_boss', 
			array(
				array('player_id', 'player', 'id'),
				array('faction_id', 'player_faction', 'id')
			)
		),
		
	array('player_faction_leave_info', 
			array(
				array('player_id', 'player', 'id'),
				array('faction_id', 'player_faction', 'id')
			)
		),
		
	array('player_faction_golden_room_record', 
			array(
				array('player_id', 'player', 'id'),
				array('faction_id', 'player_faction', 'id')
			)
		),
		
	array('player_pair_practice', 
			array(
				array('player_id', 'player', 'id'),
				array('invite_player_id', 'player', 'id')
			)
		),
		
	array('player_all_mounts', 
			array(
				array('player_id', 'player', 'id')
			)
		),
		
	array('player_mounts', 
			array(
				array('player_id', 'player', 'id')
			)
		),
		
	array('player_faction_golden_room_log', 
			array(
				array('player_id', 'player', 'id'),
				array('faction_id', 'player_faction', 'id')
			), "log"
		),
		
	array('player_join_faction_war_record', 
			array(
				array('player_id', 'player', 'id')
			), "log"
		),
	array('player_key', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_xianhua_tree_data', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_hero_war', 
			array(
				array('player_id', 'player', 'id')
			)
		),
		
	array('player_sort', 
			array(
				array('player_id', 'player', 'id')
			)
		),
		
	array('player_paid_user_vip', 
			array(
				array('player_id', 'player', 'id')
			)
		),
		
	array('player_last_harvest_role', 
			array(
				array('player_id', 'player', 'id'),
				array('player_role_id', 'player_role', 'id')
			)
		),
	array('player_last_pos', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_listener_count', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_lucky_shop_item', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_lucky_shop_record', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_mission_practice', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_mission_record', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_monster_team_strategy', 
			array(
				array('player_id', 'player', 'id'),
				array('player_war_report_id', 'player_war_report', 'id')
			),
			"clear"
		),
	array('player_online_gift', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_pcmgr', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_qplus', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_qq_game', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_qq_vip', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_order_execute_record', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_power_log', 
			array(
				array('player_id', 'player', 'id')
			), 
			"log"
		),
	array('player_quest', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_quest_monster', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_race_record', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_research', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_role_data', 
			array(
				array('player_id', 'player', 'id'),
				array('player_role_id', 'player_role', 'id')
			)
		),
	array('player_role_elixir', 
			array(
				array('player_id', 'player', 'id'),
				array('player_role_id', 'player_role', 'id')
			)
		),
	array('player_role_exp_log', 
			array(
				array('player_id', 'player', 'id'),
				array('player_role_id', 'player_role', 'id')
			), 
			"log"
		),
	array('player_send_flower_data', 
			array(
				array('player_id', 'player', 'id'),
				array('max_send_flower_player_id', 'player', 'id')
			)
		),
	array('player_send_flower_record', 
			array(
				array('player_id', 'player', 'id'),
				array('from_player_id', 'player', 'id')
			)
		),
	array('player_skill_log', 
			array(
				array('player_id', 'player', 'id')
			), 
			"log"
		),
	array('player_item_attribute_stone_log', 
			array(
				array('player_id', 'player', 'id')
			), 
			"log"
		),
	array('player_super_sport', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_super_sport_award', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_super_sport_ranking', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_super_sport_report', 
			array(
				array('player_id', 'player', 'id'),
				array('attack_player_id', 'player', 'id'),
				array('defense_player_id', 'player', 'id'),
				array('win_player_id', 'player', 'id'),
				array('player_war_report_id', 'player_war_report', 'id')
			),
			"clear"
		),
	array('player_take_bible', 
			array(
				array('player_id', 'player', 'id'),
				array('protect_player_id', 'player', 'id'),
				array('apply_for_protect', 'player', 'id'),
				array('last_rob_player', 'player', 'id')
			)
		),
	array('player_take_bible_log', 
			array(
				array('player_id', 'player', 'id'),
				array('be_rob_player_id', 'player', 'id')
			), 
			"log"
		),
	array('player_take_bible_record', 
			array(
				array('player_id', 'player', 'id'),
				array('protect_player_id', 'player', 'id')
			)
		),
	array('player_tower', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_tower_layer', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_trace', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_travel_event', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_wb_last_pos', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_world_boss_defeat', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_flower_count_log', 
			array(
				array('player_id', 'player', 'id'),
				array('from_player_id', 'player', 'id')
			), 
			"log"
		),
	array('player_role_fate', 
			array(
				array('player_role_id', 'player_role', 'id'),
				array('player_fate_id', 'player_fate', 'id')
			)
		),
	array('player_faction_boss_state', 
			array(
				array('faction_id', 'player_faction', 'id')
			)
		),
	array('player_faction_war_group', 
			array(
				array('faction_id', 'player_faction', 'id'),
				array('signer', 'player', 'id')
			)
		),
	array('player_faction_join_faction_war_record', 
			array(
				array('faction_id', 'player_faction', 'id')
			), 
			"log"
		),
	array('player_achievement_data', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_gift_detail', 
			array(
				array('id','player_gift_detail','id'),
				array('player_gift_id', 'player_gift', 'id')
			)
		),
	array('player_gift_fate_detail', 
			array(
				array('id','player_gift_fate_detail','id'),
				array('player_gift_id', 'player_gift', 'id')
			)
		),
	array('player_gift_soul_detail', 
			array(
				array('id','player_gift_soul_detail','id'),
				array('player_gift_id', 'player_gift', 'id')
			)
		),
		
	array('player_league_war_apply', 
			array(
				array('id','player_league_war_apply','id'),
				array('faction_id', 'player_faction', 'id'),
				array('player_id','player','id')
			)
		),
		
	array('player_soul_change_record',
			array(
				array('id','player_soul_change_record','id'),
				array('player_soul_id','player_soul','id'),
				array('player_id','player','id')
			),
			"log"
		),
	array('player_soul_change_record2',
			array(
				array('id','player_soul_change_record','id'),
				array('player_soul_id','player_soul','id'),
				array('player_id','player','id')
			),
			"log"
		),
	array('player_soul_stone_change_record',
			array(
				array('id','player_soul_stone_change_record','id'),
				array('player_soul_id','player_soul','id'),
				array('player_id','player','id')
			),
			"log"
		),
	array('player_soul_data',
			array(
				array('player_id','player','id')
			)
		),		
	array('player_boss_robot',
			array(
				array('player_id','player','id')
			)
		),
    array('player_hero_mission', 
			array(
				array('player_id', 'player', 'id')
			)
		),
    array('player_hero_mission_practice', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_auto_camp_war', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_award_role', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_blue_gift', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_back_times_record', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_item_data', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_achievement', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_achievement_milestone', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_activity_data', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_role_spirit_state', 
			array(
				array('player_id', 'player', 'id'),
				array('player_role_id', 'player_role', 'id')
			)
		),
	array('player_state_point', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_roll_data', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_day_quest_data', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_state_point_change_record', 
			array(
				array('id', 'player_state_point_change_record', 'id'),
				array('player_id', 'player', 'id'),
				array('player_role_id', 'player_role', 'id')
			),
			"log"
		),
	array('player_elixir_log', 
			array(
				array('player_id', 'player', 'id'),
				array('player_role_id', 'player_role', 'id'),
				array('player_item_id', 'player_item', 'id')
			),
			"log"
		),
	array('player_peach_data', 
			array(
				array('player_id', 'player', 'id')
			)
		),
		
	array('player_qzone', 
			array(
				array('player_id', 'player', 'id')
			)
		),
		
	array('player_tgc_item', 
			array(
				array('player_id', 'player', 'id')
			)
		),
		
	array('player_yellow_gift', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_mars_incense_log', 
			array(
				array('id', 'player_mars_incense_log', 'id'),
				array('player_id', 'player', 'id')
			)
		),
	array('player_peach_record', 
			array(
				array('id', 'player_peach_record', 'id'),
				array('player_id', 'player', 'id')
			),
			"log"
		),
	array('player_worship_mars_data', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_roll_digital', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_fate_data', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_consume_alert_set', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_auto_heroes_war', 
			array(
				array('player_id', 'player', 'id')
			)
		),
	array('player_continue_reward', 
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_zodiac_data',
			array(
				array('player_id', 'player', 'id')
			)
		),    
	
	array('player_qq_friend_info',
			array(
				array('player_id', 'player', 'id')
			)
		),
	
	array('player_qq_friend',
			array(
				array('player_id', 'player', 'id')
			)
		),
	
	array('player_pet_animal',
			array(
				array('player_id', 'player', 'id')
			)
	),
	
	array('player_pet',
			array(
				array('player_id', 'player', 'id')
			)
	),
	
	array('player_pet_animal_record',
			array(
				array('player_id', 'player', 'id')
			),
			"log"
	),
	
	
	array('player_abnormal_record',
			array(
				array('player_id', 'player', 'id')
			)
	),
	
	array('player_week_ranking_record',
			array(
				array('player_id', 'player', 'id')
			)
	),
	
	array('player_furnace_data',
			array(
			    array('player_id', 'player', 'id')
			)
	),
	
	array('player_role_favor_value',
			array(
			    array('player_id', 'player', 'id')
			)
	),
	
	array('player_role_favor_record',
			array(
			    array('id', 'player_role_favor_record', 'id'),
			    array('player_id', 'player', 'id')
			),
			"log"
	),
	
	
	array('player_week_ranking_award_data',
			array(
			    array('player_id', 'player', 'id')
			)
	),
	
	array('player_follow_setting',
			array(
			    array('player_id', 'player', 'id')
			)
	),
	
	array('player_adventure_record',
			array(
			    array('player_id', 'player', 'id')
			)
	),
	
	array('player_duan_wu',
			array(
			    array('player_id', 'player', 'id')
			)
	),
	
	array('player_plant',
			array(
			    array('player_id', 'player', 'id')
			)
	),
	
	array('player_3366_gift',
			array(
			    array('player_id', 'player', 'id')
			)
	),
	
	array('player_fate_log2', 
			array(
				array('player_id', 'player', 'id'),
				array('player_fate_id', 'player_fate', 'id'),
			), 
			"log"
	),
	
	array('player_finger_guess', 
			array(
				array('player_id', 'player', 'id')
			)
	),
	
	array('player_nine_regions_info', 
			array(
				array('player_id', 'player', 'id')
			)
	),
	
	array('player_special_partner', 
			array(
				array('player_id', 'player', 'id')
			)
	),
	
	array('player_special_partner_mission', 
			array(
				array('player_id', 'player', 'id')
			)
	),
	
	array('player_nine_regions_log', 
			array(
				array('id', 'player_nine_regions_log', 'id'),
				array('player_id', 'player', 'id')
			), 
			"log"
	),
	
	array('player_enhance_item', 
			array(
				array('player_id', 'player', 'id')
			)
	),
	
	array('player_quiz_game', 
			array(
				array('player_id', 'player', 'id')
			)
	),
	
	array('player_sign_in_info', 
			array(
				array('player_id', 'player', 'id')
			)
	),
	
	array('player_sign_in_data', 
			array(
				array('player_id', 'player', 'id')
			)
	),
	
	array('player_sign_in', 
			array(
				array('id', 'player_sign_in', 'id'),
				array('player_id', 'player', 'id')
			)
	),
	array('player_quiz_game_score', 
			array(
				array('player_id', 'player', 'id')
			)
	),
	
	array('player_run_business', 
			array(
				array('player_id', 'player', 'id')
			)
	),
	
	array('player_faction_quest', 
			array(
				array('player_id', 'player', 'id')
			)
	),
	
	array('player_red_envelopes', 
			array(
				array('player_id', 'player', 'id')
			)
	),
	
	array('player_faction_gift_record', 
			array(
				array('player_id', 'player', 'id')
			)
	),
	
	array('player_fund_info', 
			array(
				array('player_id', 'player', 'id')
			)
	),
	
	array('player_vitality', 
			array(
				array('player_id', 'player', 'id')
			)
	),
	
	array('player_xinyue_vip_data', 
			array(
				array('player_id', 'player', 'id')
			)
	),
	
	array('player_faction_flags', 
			array(
				array('player_id', 'player', 'id')
			)
	),
	
	array('player_title', 
			array(
				array('player_id', 'player', 'id')
			)
	),
	
	array('player_take_bible_enemy', 
			array(
				array('player_id', 'player', 'id')
			)
	),
	
	array('player_lobby', 
			array(
				array('player_id', 'player', 'id')
			)
	),
	
	array('player_rulai_incense', 
			array(
				array('player_id', 'player', 'id')
			)
	),
	
	array('player_target_info', 
			array(
				array('player_id', 'player', 'id')
			)
	),
	
	array('player_treasure_hunt_info',
			array(
				array('player_id', 'player', 'id')
			)
	),
	array('player_warning',
			array(
				array('player_id', 'player', 'id')
			)
	),
	
	array('player_treasure_hunt_award',
			array(
				array('player_id', 'player', 'id')
			)
	),
	
	array('player_golden_eggs',
			array(
				array('player_id', 'player', 'id')
			)
	),
	
	array('player_marry_info',
			array(
				array('id', 'player_marry_info', 'id'),
				array('m_player_id', 'player', 'id'),
				array('f_player_id', 'player', 'id'),
				array('current_cruise_id', 'player_marry_cruise', 'id'),
				array('current_banquet_id', 'player_marry_banquet', 'id')
			)
	),
	
	array('player_marry',
			array(
				array('player_id', 'player', 'id'),
				array('marry_id', 'player_marry_info', 'id'),
				array('spouse_id', 'player', 'id'),
				array('target_player_id', 'player', 'id')
			)
	),
	
	array('player_marry_cruise',
			array(
				array('id', 'player_marry_cruise', 'id'),
				array('player_id', 'player_marry_info', 'id')
			)
	),
	
	array('player_marry_cruise_gift',
			array(
				array('player_id', 'player', 'id')
			)
	),
	
	array('player_marry_cruise_gift_info',
			array(
				array('player_id', 'player_marry_cruise', 'id')
			)
	),
	
	array('player_marry_banquet',
			array(
				array('id', 'player_marry_banquet', 'id'),
				array('player_id', 'player_marry_info', 'id')
			)
	),
	
	array('player_marry_banquet_gift',
			array(
				array('player_id', 'player', 'id')
			)
	),
	
	array('player_marry_skill',
			array(
				array('player_id', 'player_marry_info', 'id')
			)
	),
	
	array('player_cruise_gift',
			array(
				array('id', 'player', 'id'),
				array('player_id', 'player_marry_cruise', 'id')
			)
	),
	
	array('player_banquet_eat',
			array(
				array('id', 'player', 'id'),
				array('player_id', 'player_marry_banquet', 'id')
			)
	),
	
	array('player_banquet_eat_guest',
			array(
				array('player_id', 'player_marry_banquet', 'id'),
				array('guest_id', 'player', 'id')
			)
	),
	
	array('player_banquet_gift',
			array(
				array('id', 'player', 'id'),
				array('player_id', 'player_marry_banquet', 'id')
			)
	),
	
	array('player_banquet_gift_guest',
			array(
				array('player_id', 'player_marry_banquet', 'id'),
				array('guest_id', 'player', 'id')
			)
	),
	
	array('player_marry_favor_log',
			array(
				array('id', 'player_marry_favor_log', 'id'),
				array('marry_id', 'player_marry_info', 'id')
			),
			"log"
	),

	array('player_take_bible_protection', 
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_july_activity', 
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_consumption_draw_new', 
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_refined_array', 
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_pearl_log', 
			array(
				array('player_id', 'player', 'id')
			),
			"log"
		),

	array('player_sky_war_record',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_league_war_data',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_email_detail',
			array(
				array('id', 'player', 'id')
			)
		),

	array('player_challenge_partner',
			array(
				array('id', 'player_challenge_partner', 'id'),
				array('player_id', 'player', 'id')
			)
		),

	array('player_war_page',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_passivity_stunt',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_rose_tree_data',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_offer_reward',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_ba_xian_ling',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_ba_xian_ling_log',
			array(
				array('player_id', 'player', 'id')
			),
			"log"
		),

	array('player_immortal_art',
			array(
				array('id', 'player_immortal_art', 'id'),
				array('player_id', 'player', 'id')
			)
		),

	array('player_offer_reward_record',
			array(
				array('id', 'player_offer_reward_record', 'id'),
				array('player_id', 'player', 'id')
			)
		),

	array('player_baxian_info',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_consume_jifen_log',
			array(
				array('player_id', 'player', 'id')
			),
			"log"
		),

	array('player_dragonball',
			array(
				array('id', 'player_dragonball', 'id'),
				array('player_id', 'player', 'id')
			)
		),

	array('player_dragonball_log',
			array(
				array('id', 'player_dragonball_log', 'id'),
				array('player_id', 'player', 'id')
			),
			"log"
		),

	array('player_dragonball_record',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_agate',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_dragonball_backup',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_ling_yun_log',
			array(
				array('id', 'player_ling_yun_log', 'id'),
				array('player_id', 'player', 'id')
			),
			"log"
		),

	array('player_war_page_inspirelist',
			array(
				array('id', 'player_war_page_inspirelist', 'id'),
				array('player_id', 'player', 'id')
			)
		),

	array('player_war_page_final',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_red_rabbit',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_stake_bible',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_stake_bible_enemy',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_stake_bible_protection',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_stake_bible_record',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_stake_bible_rob_record',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_baxian_hire',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_run_business_town',
			array(
				array('id', 'player_run_business_town', 'id'),
				array('player_id', 'player', 'id')
			)
		),

	array('player_run_business_package',
			array(
				array('id', 'player_run_business_package', 'id'),
				array('player_id', 'player', 'id')
			)
		),

	array('player_thanksgiving_day',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_unite_minds',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_banquet_material',
			array(
				array('faction_id', 'player_faction', 'id'),
				array('id', 'player_banquet_material', 'id')
			)
		),

	array('player_blood_pet_chanllenge',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_blood_pet',
			array(
				array('id', 'player_blood_pet', 'id'),
				array('player_id', 'player', 'id'),
				array('player_role_id', 'player_role', 'id')
			)
		),

	array('player_toast',
			array(
				array('id', 'player_toast', 'id'),
				array('player_id', 'player', 'id')
			)
		),

	array('player_entertain',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_blood_pet_log',
			array(
				array('player_id', 'player', 'id')
			),
			"log"
		),

	array('player_christmas_deer',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_christmas_deer_gift',
			array(
				array('id', 'player_christmas_deer_gift', 'id'),
				array('player_id', 'player', 'id')
			)
		),

	array('player_christmas_snow',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_halo_role',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_halo_role_record',
			array(
				array('player_id', 'player', 'id'),
				array('role_id', 'player_role', 'id')
			)
		),

	array('player_farm_activity',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_farm_activity_complete',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_st_practice_room_exp_log',
			array(
				array('id', 'player_st_practice_room_exp_log', 'id')
			),
			"log"
		),

	array('player_gold_body',
			array(
				array('player_id', 'player_role', 'id')
			)
		),

	array('player_role_mounts',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_st_practice_room_record',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_circlewar_data',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_neidan_log',
			array(
				array('player_id', 'player', 'id')
			),
			"log"
		),

	array('player_valentines_day',
			array(
				array('marry_id', 'player_marry_info', 'id')
			)
		),

	array('player_circlewar_level_data',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_new_year_bag',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_turn_lamp',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_dragon_egg_log',
			array(
				array('player_id', 'player', 'id')
			),
			"log"
		),

	array('player_sky_war_data',
			array(
				array('player_id', 'player', 'id')
			)
		),

	array('player_blood_pet_chip',
			array(
				array('id', 'player_blood_pet_chip', 'id'),
				array('player_id', 'player', 'id')
			)
		),

	array('player_blood_pet_chip_log',
			array(
				array('player_id', 'player', 'id')
			),
			"log"
		),
	//以下仅复制库1数据	
	array('player_mars'),
	array('player_faction_war_state'),
	array('player_affiche'),
	array('player_bug'),
	array('world_boss_state'),
	array('player_server_data'),
	array('player_baxian_random')
);


//竞技场排名字段, array(表, 排名字段, array(此表主键字段集))
$RANK_MAPS = array(
	array('player_super_sport', 'last_ranking', array('player_id')),
	array('player_super_sport_award', 'ranking', array('ranking')),
	array('player_super_sport_ranking', 'ranking', array('player_id')),
	array('player_super_sport_report', 'old_ranking', array('player_id', 'player_war_report_id')),
	array('player_super_sport_report', 'new_ranking', array('player_id', 'player_war_report_id'))
);
?>
