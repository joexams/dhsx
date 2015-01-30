-module(war_demo).
-include("../../server-new/include/game.hrl").
-include("../../server-new/include/gen/game_db.hrl").
-export([start/1, rank/1, pk/1, get_war_param/1]).
-define(MAGIC_JOB, [?RJ_SHUSHI, ?RJ_FANGSHI, ?RJ_FASHU]).
-define(STUNT_TYPE, [
    {?RS_YUJIANSHU, "御剑术", ""},
    {?RS_MENGJI, "猛击", ""},
    {?RS_BENGJING, "崩劲", ""},
    {?RS_FEIYUJIAN, "飞羽箭", ""},
    {?RS_JIANLIUYUN, "剑流云", ",被(眩晕)"},
    {?RS_LUOXINGSHI, "落星式", ""},
    {?RS_XUANYINJIAN, "玄阴十二剑，众生皆我灭", ""},
    {?RS_GUANGMANGWANZHANG, "光芒万丈", ""},
    {?RS_SANMEIZHENHUO, "三昧真火", ""},
    {?RS_TIANSHUANGQUAN, "天霜拳", ",被(眩晕)"},
    {?RS_SHIFANGJIESHA, "十方皆杀", ""},
    {?RS_AXIULUOBAWANGQUAN, "阿修罗霸王拳", ""},
    {?RS_TIANXUANWUYIN, "天玄五音", ""},
    {?RS_JINGLEISHAN, "惊雷闪", "被(眩晕)"},
    {?RS_MENGPOTANG, "孟婆汤", ",被(眩晕)"},
    {?RS_GUIYUHUANSHEN, "鬼狱还神", ""},
    {?RS_ERDUANJI, "二段击", ""},
    {?RS_TIANSHIFUFA, "天师符法", ""},
    {?RS_LEIZHOU, "雷咒", "被(眩晕)"},
    {?RS_FENGZHOU, "风咒", "被(眩晕)"},
    {?RS_HUOYANZHOU, "火炎咒", ""},
    {?RS_BINGJINGZHOU, "冰晶咒", "被(眩晕)"},
    {?RS_JUHUN, "拘魂", ""},
    {?RS_ZIBAO, "自爆", ""},
    {?RS_SIYAO, "撕咬", ""},
    {?RS_NIANYA, "碾压", ""},
    {?RS_BEICI, "背刺", ""},
    {?RS_YINGXI, "影袭", "被(眩晕)"},
    {?RS_BAILIANHENGJIANG, "白练横江", ""},
    {?RS_CHANGHONGGUANRI, "长虹贯日", ""},
    {?RS_BAFANGBAOLIE, "八方爆裂", ""},
    {?RS_KUANGBAO, "狂暴", ""},
    {?RS_DUZHOU, "毒咒", "被(中毒)"},
    {?RS_DUWU, "毒雾", "被(中毒)"},
    {?RS_DINGSHENZHOU, "定身咒", "被(定身)"},
    {?RS_CHUIMIAN, "催眠", "被(催眠)"},
    {?RS_ZHENSHESHU, "震慑术", ""},
    {?RS_GUWUSHU, "鼓舞术", ""},
    {?RS_HUANSHU, "幻术", ""},
    {?RS_HUNLUAN, "混乱", ""},
    {?RS_JUEDUIFANGYU, "绝对防御", ""},
    {?RS_HUANYING, "幻影", ""},
    {?RS_YINSHEN, "隐身", ""},
    {?RS_HUICHUN, "回春", ""},
    {?RS_ZHILIAO, "治疗", ""},
    {?RS_XIXUE, "吸血", ""},
    {?RS_HUANMING, "换命", ""},
    {?RS_TUZHOU, "土咒", "被(眩晕)"},
    {?RS_XINGCHENDIDONG, "星沉地动", "被(眩晕)"},
    {?RS_LEIDONGJIUTIAN, "雷动九天", "被(眩晕)"},
    {?RS_FENGJUANCHENSHENG, "风卷尘生", "被(眩晕)"},
    {?RS_LIANYUHUOHAI, "炼狱火海", ""},
    {?RS_FENGXUEBINGTIAN, "风雪冰天", "被(眩晕)"},
    {?RS_LINGBAO, "灵暴", ""},
    {?RS_LINGLICHONGJI, "灵力冲击", ""},
    {?RS_LINGBAODANMU, "灵暴弹幕", ""},
    {?RS_LINGHEBAO, "灵核暴", ""},
    {?RS_LUOSHI, "落石", "被(眩晕)"},
    {?RS_SHANDIAN, "闪电", "被(眩晕)"},
    {?RS_XUANFENG, "旋风", "被(眩晕)"},
    {?RS_HUOZHU, "火柱", ""},
    {?RS_DONGJIE, "冻结", "被(眩晕)"},
    {?RS_BATI, "霸体", ""},
    {?RS_LEIQIE, "雷切", ""},
    {?RS_DAFENGZHOU, "大风咒", ""},
    {?RS_DAHUOYANZHOU, "大火炎咒", ""},
    {?RS_KONGHE, "恐吓", ""},
    {?RS_WUZHIBATI, "武之霸体", ""},
    {?RS_YEQIUQUAN, "野球拳", ""},
    {?RS_YINGZHIBATI, "影之霸体", ""},
    {?RS_LIZHIBATI, "力之霸体", ""},
    {?RS_QIANBEIBUZUI, "千杯不醉", ""},
    {?RS_TIANJIANGHENGCAI, "天降横财", "被(眩晕)"},
    {?RS_XINGYUN, "星陨", ""},
    {?RS_MENGYIN, "梦引", ""},
    {?RS_ZHUORECHONGJI, "灼热冲击", ""},
    {?RS_KUANGFENGJIANJUE, "狂风剑诀", "被(眩晕)"},
    {?RS_TIANJINGZHOU, "天晶咒", "被(催眠)"},
    {?RS_TIANLEIHONG, "天雷轰", "被(眩晕)"},
    {?RS_HUXIAO, "虎哮", ""},
    {?RS_XUESHENZUZHOU, "血神诅咒", ""},
    {?RS_FENGSHENJIAN, "风神箭", ""},
    {?RS_SHASHENGJIAN, "杀生剑", ""},
    {?RS_JIUYINJIUYANG, "九阴九阳", ""},
    {?RS_CHENGTIANZAIWU, "承天载物", ""},
    {?RS_HONGLIANQIANG, "红莲枪", ""},
    {?RS_FENGHUOLIAOYUAN, "风火燎原", "被(眩晕)"},
    {?RS_YEMANCHONGZHUANG, "野蛮冲撞", "被(眩晕)"},
    {?RS_ZHENYEQIUQUAN, "真野球拳", ""},
    {?RS_TIANZHIBATI, "天之霸体", ""},
    {?RS_YISHAN, "一闪", ""},
    {?RS_NISHUIHAN, "逆水寒", ""},
    {?RS_QISHIERBIAN, "七十二变", ""},
    {?RS_JIUTIANSHENLEI, "九天神雷", "被(眩晕)"},
    {?RS_JIUSIYISHENG, "九死一生", ""},
    {?RS_HUNDUNZHILI, "混沌之力", "被(眩晕)"},
    {?RS_ZIDIANSHENGUANG, "紫电神光", ""},
    {?RS_MOYINGANMIE, "魔影黯灭", ""},
    {?RS_LEITINGYIJI, "雷霆一击", ""},
    {?RS_JUEDUILINGDU, "绝对零度", ""},
    {?RS_FENGJUANCANYUN, "风卷残云", ""},
    {?RS_YEWUFEIREN, "叶舞飞刃", ""},
    {?RS_SHUIMUTIANHUA, "水幕天华", ""},
    {?RS_QIANJUNNUJI, "千钧怒击", ""},
    {?RS_YUNSHILIEDI, "陨石裂地", ""},
    {?RS_TIANHUOLIAOYUAN, "天火燎原", ""},
    {?RS_YUESHI, "月蚀", ""},
    {?RS_WUHUI, "无悔", "被(催眠)"},
    {?RS_SHENZHIBATI, "神之霸体", ""},
    {?RS_TIANHUOFENYAN, "天火焚炎", "被(眩晕)"},
    {?RS_TIANZHAN, "天斩", ""},
    {?RS_QIANNIANLUNHUI, "千年轮回", "被(眩晕)"},
    {?RS_JIANYINGWUHEN, "剑影无痕", ""},
    {?RS_QIANGUDARU, "千古大儒", ""},
    {?RS_SHENGSHOUQIHUN, "圣手棋魂", ""},
    {?RS_LINGLONGQIJING, "玲珑棋境", ""},
    {?RS_DAFENQIMIMA, "达芬奇密码", "被(眩晕)"},
    {?RS_XINGYANBAO, "星炎爆", ""},
    {?RS_BAIZIJUESHA, "白字绝杀", ""},
    {?RS_MOYING, "墨影", ""},
    {?RS_SHUIMOHUAYING, "水墨画影", "被(眩晕)"},
    {?RS_NUZHIBATI, "怒之霸体", ""},
    {?RS_DUNQIANG, "盾墙", ""},
    {?RS_HUNFEIPOSAN, "魂飞魄散", ""},
    {?RS_LIEYANCHUANXIN, "烈焰穿心", ""},
    {?RS_TAIMEIDESHANGTONG, "太美的伤痛", ""},
    {?RS_TIANMEI, "天魅", ""},
    {?RS_KUANGSHOUZHILI, "狂兽之力", ""},
    {?RS_YAOSHOUANYING, "妖兽暗影", ""},
    {?RS_ZHIMINGYIJI, "致命一击", ""},
    {?RS_ZHANLONGJUE, "斩龙决", ""},
    {?RS_ERLIANSHI, "二连矢", ""},
    {?RS_LINGYUNJIAN, "凌云剑", ""},
    {?RS_MENGUN, "闷棍", "被(眩晕)"},
    {?RS_TIANJIAN, "天剑", ""},
    {?RS_TIANSHAGUXING, "天煞孤星", ""},
    {?RS_QINGLIANHUASHU, "青莲华术", ""},
    {?RS_ZHANHONGCHEN, "斩红尘", ""},
    {?RS_JINCHANHUTI, "金蝉护体", ""},
    {?RS_LIEYANFENGHUANG, "烈焰凤凰", ""},
    {?RS_WEIWODUZUN, "唯我独尊", ""},
    {?RS_SHENGSIYIXIAN, "生死一线", ""},
    {?RS_SHUIMANJINSHAN, "水漫金山", ""},

    {?RS_QIONGZHUIMENGDA, "穷追猛打", "被(眩晕)"},
    {?RS_SHIQIANGLINGRUO, "恃强凌弱", ""},
    {?RS_WUZHONGSHENGYOU, "无中生有", "被(眩晕)"},
    {?RS_QIANGGONG, "强攻", ""},
    {?RS_HUNDUNZHENYA, "混沌镇压", ""},
    {?RS_PAISHANDAOHAI, "排山倒海", ""},
    {?RS_LUOJINGXIASHI, "落井下石", ""},
    {?RS_XUEZHEN, "血镇", ""},
    {?RS_HUIXUANJI, "回旋击", "被(眩晕)"},
    {?RS_BEISHUIYIZHAN, "背水一战", ""},
    {?RS_SHENGGUANG, "圣光", ""},
    {?RS_CHANAFANGHUA, "刹那芳华", ""},

    {?RS_QIONGZHUIMENGDA2, "穷追猛打2", "被(眩晕)"},
    {?RS_SHIQIANGLINGRUO2, "恃强凌弱2", ""},
    {?RS_WUZHONGSHENGYOU2, "无中生有2", "被(眩晕)"},
    {?RS_QIANGGONG2, "强攻2", ""},
    {?RS_HUNDUNZHENYA2, "混沌镇压2", ""},
    {?RS_PAISHANDAOHAI2, "排山倒海2", ""},
    {?RS_LUOJINGXIASHI2, "落井下石2", ""},
    {?RS_XUEZHEN2, "血镇2", ""},
    {?RS_HUIXUANJI2, "回旋击2", "被(眩晕)"},
    {?RS_BEISHUIYIZHAN2, "背水一战2", ""},
    {?RS_SHENGGUANG2, "圣光2", ""},
    {?RS_CHANAFANGHUA2, "刹那芳华2", ""},

    {?RS_YUHUOFENGHUANG, "浴火凤凰", ""},
    {?RS_BUBUSHENGLIAN, "步步生莲", ""},
    {?RS_FENGTIANYINDI, "封天印地", ""},
	{?RS_TIANDIWUJI, "天地无极，乾坤借法", ""},
    {?RS_TIANDIWUJI1, "天地无极，乾坤借法+1", ""},
	{?RS_GUOPOJINGJUE, "国破境绝", ""},
	{?RS_FENGHUAJUEDAI, "风华绝代", ""},
	{?RS_ZUIDAJINZHI, "醉打金枝", ""},
	{?RS_HUOSHAOLIANYING, "火烧联营", "被(眩晕)"},
	{?RS_WANMUZHICHUN, "万木之春", ""},
	{?RS_SHUIWUCHANGXING, "水无常形", ""},
	{?RS_BAILIEQUAN, "百烈拳", ""},
	{?RS_XIULUOMOPOQUAN, "修罗魔破拳", ""},
	{?RS_CHAOFENG, "嘲讽", ""},
	{?RS_GUIWANGBATI, "鬼王霸体", ""},
	{?RS_JIANSHIWUSHUANG, "剑士无双", ""},
	{?RS_WUJIJIANSHU, "无极剑术", ""},
	{?RS_FUSHEN, "缚神", ""},
	{?RS_DOUZHANSHENGFO, "斗战胜佛", ""},
    {?RS_XIONGSHENESHA, "凶神恶煞", ""},
    {?RS_TAOWULINGYU, "梼杌领域", ""}
]).


%% 记录战争结果
-record(
    war_result_for_rank,
    {
        fight_health    = 0,
        be_fight_health = 0,
        bout_count      = 0
    }
).

%% 评分参数
-define(MAX_VAG_ATTACK, 50).
-define(MAX_VAG_DEFENSE, 50).

start ([GameServer, ParamFile, BinaryResultFile, ResultFile]) ->

	{ok, [ParamList]} = file:consult(atom_to_list(ParamFile)),

    {runtimes, Runtimes} = lists:keyfind(runtimes, 1, ParamList),
    {role_list, {AttackRoleList, DefenseRoleList}} = lists:keyfind(role_list, 1, ParamList),
    {role_attribute_list, {AttackRoleAttributeList, DefenseRoleAttributeList}} = lists:keyfind(role_attribute_list, 1, ParamList),
    {max_bout_number, MaxBoutNumber}         = lists:keyfind(max_bout_number, 1, ParamList),
    {request_bout_number, RequestBoutNumber} = lists:keyfind(request_bout_number, 1, ParamList),
    {attack_can_not_dead_number, AttackCanNotDeadNumber} = lists:keyfind(attack_can_not_dead_number, 1, ParamList),
	{attack_dragonball_list, AttackDragonBallList} = lists:keyfind(attack_dragonball_list, 1, ParamList),
	{defense_dragonball_list, DefenseDragonBallList} = lists:keyfind(defense_dragonball_list, 1, ParamList),
    
	AttackWarPlayerData = #war_player_data{
        player_id = 1,
        player_user_name = "player",
        player_nick_name = "player_name",
		pet_stunt = AttackDragonBallList
    },
    DefenseWarPlayerData = #war_player_data{
        player_id = 2,
        player_user_name = "monster",
        player_nick_name = "monster_name",
		pet_stunt = DefenseDragonBallList
    },

    GetTibuRoleList = fun(RoleList, RoleAttributeList) ->
        [
            RoleAttribute #player_role_war_attribute.role_id
            ||
            RoleAttribute <- RoleAttributeList, RoleAttribute #player_role_war_attribute.position == 51, lists:member(RoleAttribute #player_role_war_attribute.role_id, RoleList)
        ]
    end,

    AttackTibuRoleList  = GetTibuRoleList(AttackRoleList, AttackRoleAttributeList),
    DefenseTibuRoleList = GetTibuRoleList(DefenseRoleList, DefenseRoleAttributeList),

    NewAttackRoleList = lists:subtract(AttackRoleList, AttackTibuRoleList),
    NewDefenseRoleList = lists:subtract(DefenseRoleList, DefenseTibuRoleList),
    
    ResultList = lists:map(
        fun(_N) ->
            Attack = #war_player_role_data{
                player_id      = 1,
                player_data    = AttackWarPlayerData,
                roles          = NewAttackRoleList,
                tibu_roles     = AttackTibuRoleList,
                role_attribute = AttackRoleAttributeList
            },
            Defense = #war_player_role_data{
                player_id      = 2,
                player_data    = DefenseWarPlayerData,
                roles          = NewDefenseRoleList,
                tibu_roles     = DefenseTibuRoleList,
                role_attribute = DefenseRoleAttributeList
            },

            NewDefensePlayerData = (Defense #war_player_role_data.player_data) #war_player_data{
                max_bout_number     = MaxBoutNumber,
                request_bout_number = RequestBoutNumber,
                attack_can_not_dead_number = AttackCanNotDeadNumber
            },

            WarParam = #war_param{
                attack  = Attack,
                defense = Defense #war_player_role_data{player_data = NewDefensePlayerData}
            },
            rpc:call(GameServer, mod_war, start, [simple, {WarParam}])
        end,
        lists:seq(1, Runtimes)
    ),
    
    get_result_for_demo(GameServer, 1, ResultList, atom_to_list(BinaryResultFile), atom_to_list(ResultFile)).


rank ([ParamFile, ResultFile]) ->
    put(war_result_for_rank, #war_result_for_rank{}),

    {ok, [WarList]} = file:consult(ParamFile),
    {vag_attack, VagAttack}         = lists:keyfind(vag_attack, 1, WarList),
    {vag_defense, VagDefense}       = lists:keyfind(vag_defense, 1, WarList),
    {runtimes, Runtimes}            = lists:keyfind(runtimes, 1, WarList),
    {role_list, RoleList}           = lists:keyfind(role_list, 1, WarList),
    {role_attribute, RoleAttribute} = lists:keyfind(role_attribute, 1, WarList),
    {defense_list, DefenseList}     = lists:keyfind(defense_list, 1, WarList),

    AttackWarPlayerData = #war_player_data{
        player_id = 1,
        player_user_name = "player",
        player_nick_name = "player_name"
    },
    DefenseWarPlayerData = #war_player_data{
        player_id = 2,
        player_user_name = "monster",
        player_nick_name = "monster_name"
    },
    
    WinCount = lists:foldl(
        fun(_, TeampWinCount) ->
            WinFightCount = lists:foldl(
                fun(BeFightParam, TempWinFightCount) ->
                    {role_list, BeFightRoleList} =
                        lists:keyfind(role_list, 1, BeFightParam),
                    {role_attribute, BeFightRoleAttribute} =
                        lists:keyfind(role_attribute, 1, BeFightParam),

                    Attack = #war_player_role_data{
                        player_data    = AttackWarPlayerData,
                        roles          = RoleList,
                        role_attribute = RoleAttribute
                    },
                    Defense = #war_player_role_data{
                        player_data    = DefenseWarPlayerData,
                        roles          = BeFightRoleList,
                        role_attribute = BeFightRoleAttribute
                    },

                    WarParam = #war_param{
                        attack  = Attack,
                        defense = Defense
                    },
                    Result = mod_war:start(simple, {WarParam}),
                    if
                        Result #war_result.winner_player_id =:= 1 ->
                            record_war_result(Result),
                            TempWinFightCount + 1;
                        true ->
                            TempWinFightCount
                    end
                end,
                0,
                DefenseList
            ),

            MissionLen = length(DefenseList),
            if
                WinFightCount =:= MissionLen ->
                    TeampWinCount + 1;

                true ->
                    TeampWinCount
            end
        end,
        0,
        lists:seq(1, Runtimes)
    ),

    Result = if
        WinCount > 0 ->
            get_result_for_rank(
                VagAttack,
                VagDefense,
                Runtimes,
                WinCount
            );
            
        true ->
            ""
    end,
    
    write_data(atom_to_list(ResultFile), Result).


pk ([GameServer, PkType, MasterPlayerId, SlavePlayerId, Runtimes, BinaryResultFile, ResultFile, PHPParamFile]) ->
    MasterPlayerData = rpc:call(GameServer, mod_mission, get_player_war_attribute, [list_to_integer(atom_to_list(MasterPlayerId))]),
    
    SlavePlayerData = if
        PkType =:= with_player ->
            rpc:call(GameServer, mod_mission, get_player_war_attribute, [list_to_integer(atom_to_list(SlavePlayerId))]);
        true ->
            rpc:call(GameServer, mod_mission, get_monster_war_attribute, [list_to_integer(atom_to_list(SlavePlayerId))])
    end,
    
    WarParam = #war_param{
        attack  = MasterPlayerData,
        defense = SlavePlayerData
    },
    
    ResultList = lists:map(
        fun(_N) ->
            rpc:call(GameServer, mod_war, start, [simple, {WarParam}])
        end,
        lists:seq(1, list_to_integer(atom_to_list(Runtimes)))
    ),
    
    get_result_for_demo(GameServer, list_to_integer(atom_to_list(MasterPlayerId)), ResultList, atom_to_list(BinaryResultFile), atom_to_list(ResultFile)),
    write_war_param_php(PHPParamFile, WarParam).
    

%% 获取玩家战争属性
get_war_param ([GameServer, PlayerId, PHPParamFile]) ->
    PlayerWarParam = rpc:call(GameServer, mod_mission, get_player_war_attribute, [list_to_integer(atom_to_list(PlayerId))]),
    write_war_player_param_php(atom_to_list(PHPParamFile), PlayerWarParam).

    
%% 记录战争结果
record_war_result (WarResult) ->
    {FightHealth, BeFightHealth, BoutCount} = mod_war:analysis(WarResult),

    WarResultForRank = get(war_result_for_rank),
    #war_result_for_rank{
        fight_health    = OldFightHealth,
        be_fight_health = OldBeFightHealth,
        bout_count      = OldBoutCount
    } = WarResultForRank,
    
    put(
        war_result_for_rank,
        WarResultForRank #war_result_for_rank{
            fight_health    = OldFightHealth + FightHealth,
            be_fight_health = OldBeFightHealth + BeFightHealth,
            bout_count      = OldBoutCount + BoutCount
        }
    ).


%% 获取结果
get_result_for_rank (
    VagAttack,
    VagDefense,
    Runtimes,
    WinCount
) ->
    #war_result_for_rank{
        fight_health    = FightHealth,
        be_fight_health = BeFightHealth,
        bout_count      = BoutCount
    } = get(war_result_for_rank),
    
    %% 总伤害得分
    AttackScore  = lib_misc:ceil(
        min(
            ?MAX_VAG_ATTACK,
            (FightHealth / BoutCount) / VagAttack * 100 * 0.5
        )
    ),
    %% 总损血得分
    DefenseScore = lib_misc:ceil(
        min(
            ?MAX_VAG_DEFENSE,
            VagDefense / (max(1, BeFightHealth) / BoutCount) * 100 * 0.5
        )
    ),

    AttackScoreRate  = lib_misc:ceil(AttackScore / ?MAX_VAG_ATTACK * 100),
    DefenseScoreRate = lib_misc:ceil(DefenseScore / ?MAX_VAG_DEFENSE * 100),

    Score     = AttackScore + DefenseScore,

    AvgAttack  = lib_misc:ceil(FightHealth / BoutCount),
    AvgDefense = lib_misc:ceil(max(1, BeFightHealth) / BoutCount),

    "<win_count>" ++ integer_to_list(WinCount) ++ "</win_count>" ++
    "<lose_count>" ++ integer_to_list(Runtimes - WinCount) ++ "</lose_count>" ++
    "<score>" ++ integer_to_list(Score) ++ "</score>" ++
    "<attack_score>" ++ integer_to_list(AttackScore) ++ "</attack_score>" ++
    "<defense_score>" ++ integer_to_list(DefenseScore) ++ "</defense_score>" ++
    "<avg_attack>" ++ integer_to_list(AvgAttack) ++ "</avg_attack>" ++
    "<avg_defense>" ++ integer_to_list(AvgDefense) ++ "</avg_defense>" ++
    "<bout_count>" ++ integer_to_list(lib_misc:ceil(BoutCount / Runtimes)) ++ "</bout_count>" ++
    "<attack_score_rate>" ++ integer_to_list(AttackScoreRate) ++ "</attack_score_rate>" ++
    "<defense_score_rate>" ++ integer_to_list(DefenseScoreRate) ++ "</defense_score_rate>".


get_result_for_demo (GameServer, PlayerId, ResultList, BinaryResultFile, ResultFile) ->
    %% io:format("ResultList:~p~n", [ResultList]),
    {
        AttackArmyWinCount,
        DefenseArmyWinCount,
        {AttackArmyWinReport, AttackArmyWinFlashReport},
        {DefenseArmyWinReport, DefenseArmyWinFlashReport}
    } = lists:foldl(
        fun(
            WarResult,
            {
                AttackArmyWinCountTemp,
                DefenseArmyWinCountTemp,
                AttackArmyWinReportTemp,
                DefenseArmyWinReportTemp
            }
        ) ->
            WinnerId = WarResult #war_result.winner_player_id,

            {NewAttackArmyWinCount, DefenseArmyWinCount} = if
                WinnerId =:= PlayerId ->
                    {AttackArmyWinCountTemp + 1, DefenseArmyWinCountTemp};
                true ->
                    {AttackArmyWinCountTemp, DefenseArmyWinCountTemp + 1}
            end,
            
            {NewAttackArmyWinReport, NewDefenseArmyWinReport} = if
                WinnerId =:= PlayerId andalso AttackArmyWinReportTemp =:= {"", <<>>} ->
                    {analysis_war_report(GameServer, WarResult), DefenseArmyWinReportTemp};
                WinnerId =/= PlayerId andalso DefenseArmyWinReportTemp =:= {"", <<>>} ->
                    {AttackArmyWinReportTemp, analysis_war_report(GameServer, WarResult)};
                true ->
                    {AttackArmyWinReportTemp, DefenseArmyWinReportTemp}
            end,

            {
                NewAttackArmyWinCount,
                DefenseArmyWinCount,
                NewAttackArmyWinReport,
                NewDefenseArmyWinReport
            }
        end,
        {0, 0, {"", <<>>}, {"", <<>>}},
        ResultList
    ),
    %% io:format("~p~n", [AttackArmyWinFlashReport]),
    %% io:format("~p~n", [DefenseArmyWinFlashReport]),
    write_binary_data(BinaryResultFile ++ "_attack.txt", AttackArmyWinFlashReport),
    write_binary_data(BinaryResultFile ++ "_defense.txt", DefenseArmyWinFlashReport),
    
    Result = "<attack_army_win_count>" ++ integer_to_list(AttackArmyWinCount) ++ "</attack_army_win_count>" ++
    "<defense_army_win_count>" ++ integer_to_list(DefenseArmyWinCount) ++ "</defense_army_win_count>" ++
    "<attack_army_win_report>" ++ AttackArmyWinReport ++ "</attack_army_win_report>" ++
    "<defense_army_win_report>" ++ DefenseArmyWinReport ++ "</defense_army_win_report>",
    
    write_data(ResultFile, Result).

analysis_war_report (GameServer, WarResult) -> %%io:format("~p~n", [WarResult]),
	AnalysisFun = fun() ->
		%% 生成战场脚本给flash播放
		FlashResult = rpc:call(GameServer, api_mission_out, fight_monster, [{{
			0,
			0,
			1,
			[rpc:call(GameServer, api_mission, analysis_war_result, [WarResult])],
			[],
			[],
			[],
			[],
			0,
			0,
			0,
			0
		}}]),
		
		%% 所有角色
		AtkRoleList = (WarResult #war_result.attack_player_data) #war_result_player_data.role_data ++ 
			(WarResult #war_result.attack_player_data) #war_result_player_data.tibu_role_data,
			
		DefRoleList = (WarResult #war_result.defense_player_data) #war_result_player_data.role_data ++ 
			(WarResult #war_result.defense_player_data) #war_result_player_data.tibu_role_data,
		
		put(all_role_data, AtkRoleList ++ DefRoleList),
		
		{_BoutNumber, Result} = lists:foldl(
			fun(FightList, {BoutNumberTemp, ResultTemp}) ->
				BoutNumberReport = ResultTemp ++ "第" ++ integer_to_list(BoutNumberTemp) ++ "回合:<br>",
				
				FightReport = lists:foldl(
					fun(FightResult, FightReportTemp) ->
						#war_result_fight{
							fight_role_name = TheAttackRoleName,
							stunt           = FightRoleStunt,
							be_stunt        = FightRoleBeStuntList,
							momentum        = MomentumValue,
							fight_player_id = PlayerId,
							left_health     = FightRoleLeftHealth,
							self_hurt       = SelfHurtValue,
							fight_type      = FightType,
							fight_result    = FightRoleList,
							addon_item      = AddonItem,
							weapon_effect_item = WeaponEffect,
							passivity_stunt_item = PassivityStunt,
							pet_stunt_item = _PetStuntItem,
							blood_pet_stunt_item = BloodPetStuntItem,
							virtual_role_list = VirtualRoleList
						} = FightResult,
						
						put(all_role_data, get(all_role_data) ++ VirtualRoleList),
						
						AttackRoleName = if
							TheAttackRoleName =:= "" andalso FightType =:= pet_stunt ->
								"【龙王】";
							true ->
								"【" ++ TheAttackRoleName ++ "】"
						end,
						
						Momentum = "气势" ++ integer_to_list(MomentumValue),
						SelfHurt = if
							SelfHurtValue > 0 ->
								",自己流血-" ++ integer_to_list(SelfHurtValue) ++ "剩余" ++ integer_to_list(FightRoleLeftHealth);
							true ->
								""
						end,
						
						%% 是否有妖娆救治
						YaoRaoCure = lists:keyfind(yao_rao_cure_one_role, #war_result_addon_item.type, AddonItem),
						YaoRaoAddonAttribute = lists:keyfind(tian_xuan_wu_yin_addon_attribute, #war_result_addon_item.type, AddonItem),
						
						{StrFightType, BS, BE} = case FightType of
							%% 普通攻击
							attack_type_normal ->
								{"攻击", "", ""};
							%% 反击
							attack_type_back ->
								{"反击", "", ""};
							%% 法术攻击
							attack_type_magic ->
								case YaoRaoCure of 
									#war_result_addon_item{data = {CureRoleId, CureHealth, CureRoleLeftHealth}} ->
										{
											" 治疗 " ++ integer_to_list(CureRoleId) ++ ",+" ++ integer_to_list(CureHealth) ++ "生命值,对方剩余" ++ integer_to_list(CureRoleLeftHealth) ++ "生命值",
											"",
											""
										};
									_ ->
										{"法术攻击", "", ""}
								end;
							pet_stunt ->
								{"技能攻击", "", ""};
							%% 施放战法
							_ ->
								{_FightRoleStunt, FightRoleStuntName, _Effect} = lists:keyfind(FightRoleStunt, 1, ?STUNT_TYPE),
								case YaoRaoAddonAttribute of
									#war_result_addon_item{data = AddonData} ->
										
										{
											"施放战法(" ++ FightRoleStuntName ++ "),同伴3种攻击和生命 +" ++ integer_to_list(AddonData),
											"<B>",
											"</B>"
										};
									_ ->
										{
											"施放战法(" ++ FightRoleStuntName ++ ")",
											"<B>",
											"</B>"
										}
								end
						end,
						
						Color = if
							PlayerId =:= 1 ->
								red;
							true ->
								blue
						end,
						
						BeFightReport = lists:foldl(
							fun(FightRoleResult, BeFightReportTemp) ->
								#war_result_fight_result{
									be_fight_role_name = TheBeFightRoleName,
									stunt            = _BeFightRoleStunt,
									be_stunt         = BeFightRoleBeStuntList,
									momentum         = BeMomentumValue,
									hurt             = Hurt,
									left_health      = LeftHealth,
									is_hit           = IsHit,
									is_block         = IsBlock,
									is_critical      = IsCritical
								} = FightRoleResult,
								
								BeFightRoleName = "【" ++ TheBeFightRoleName ++ "】",
								
								BeMomentum = "(气势" ++ integer_to_list(BeMomentumValue) ++ ")",
								
								%% 是否战法攻击
								IsFightByStunt = case lists:keyfind(FightType, 1, ?STUNT_TYPE) of
									false ->
										false;
									_ ->
										true
								end,

								%% 战法效果
								EffectName = if
									IsFightByStunt =:= true ->
										lists:foldr(
											fun(BeFightRoleBeStunt, TempEffectName) ->
												TempEffectName ++ "," ++ 
												element(
													3,
													lists:keyfind(BeFightRoleBeStunt, 1, ?STUNT_TYPE)
												)
											end,
											"",
											BeFightRoleBeStuntList
										);
									true ->
										""
								end,
								
								Hit = if
									IsHit =:= 1 ->
										"";
									true ->
										"(闪避)"
								end,
								Block = if
									IsBlock =:= 1 ->
										"(档格)";
									true ->
										""
								end,
								Critical = if
									IsCritical =:= 1 ->
										"(暴击)";
									true ->
										""
								end,
								
								BeStuntReport = lists:foldl(
									fun(FightRoleBeStunt, TempBeStuntReport) ->
										TempBeStuntReport ++ "," ++ case FightRoleBeStunt of
											%% 剑流云
											?RS_JIANLIUYUN ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(眩晕)</font>" ++ BE ++ "<br>";
											%% 天霜拳
											?RS_TIANSHUANGQUAN ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(眩晕)</font>" ++ BE ++ "<br>";
											%% 孟婆汤
											?RS_MENGPOTANG ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(眩晕)</font>" ++ BE ++ "<br>";
											%% 雷咒
											?RS_LEIZHOU ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(眩晕)</font>" ++ BE ++ "<br>";
											%% 风咒
											?RS_FENGZHOU ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(眩晕)</font>" ++ BE ++ "<br>";
											%% 冰晶咒
											?RS_BINGJINGZHOU ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(眩晕)</font>" ++ BE ++ "<br>";
											%% 影袭
											?RS_YINGXI ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(眩晕)</font>" ++ BE ++ "<br>";
											%% 定身咒
											?RS_DINGSHENZHOU ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(被定身)</font>" ++ BE ++ "<br>";
											%% 催眠
											?RS_CHUIMIAN ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(被催眠)</font>" ++ BE ++ "<br>";
											%% 土咒
											?RS_TUZHOU ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(被眩晕)</font>" ++ BE ++ "<br>";
											%% 星沉地动
											?RS_XINGCHENDIDONG ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(被眩晕)</font>" ++ BE ++ "<br>";
											%% 雷动九天
											?RS_LEIDONGJIUTIAN ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(被眩晕)</font>" ++ BE ++ "<br>";
											%% 风卷尘生
											?RS_FENGJUANCHENSHENG ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(被眩晕)</font>" ++ BE ++ "<br>";
											%% 风雪冰天
											?RS_FENGXUEBINGTIAN ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(被眩晕)</font>" ++ BE ++ "<br>";
											%% 落石
											?RS_LUOSHI ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(被眩晕)</font>" ++ BE ++ "<br>";
											%% 闪电
											?RS_SHANDIAN ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(被眩晕)</font>" ++ BE ++ "<br>";
											%% 旋风
											?RS_XUANFENG ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(被眩晕)</font>" ++ BE ++ "<br>";
											%% 冻结
											?RS_DONGJIE ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(被眩晕)</font>" ++ BE ++ "<br>";
											%% 惊雷闪
											?RS_JINGLEISHAN ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(被眩晕)</font>" ++ BE ++ "<br>";
											%% 天降横财
											?RS_TIANJIANGHENGCAI ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(被眩晕)</font>" ++ BE ++ "<br>";
											%% 狂风剑诀
											?RS_KUANGFENGJIANJUE ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(被眩晕)</font>" ++ BE ++ "<br>";
											%% 天晶咒
											?RS_TIANJINGZHOU ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(被催眠)</font>" ++ BE ++ "<br>";
											%% 天雷轰
											?RS_TIANLEIHONG ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(被晕)</font>" ++ BE ++ "<br>";
											%% 风火燎原
											?RS_FENGHUOLIAOYUAN ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(被晕)</font>" ++ BE ++ "<br>";
											%% 野蛮冲撞
											?RS_YEMANCHONGZHUANG ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(被晕)</font>" ++ BE ++ "<br>";
											%% 九天神雷
											?RS_JIUTIANSHENLEI ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(被晕)</font>" ++ BE ++ "<br>";
											%% 混沌之力
											?RS_HUNDUNZHILI ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(被晕)</font>" ++ BE ++ "<br>";
											%% 无悔
											?RS_WUHUI ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(被催眠)</font>" ++ BE ++ "<br>";
											%% 天火焚炎
											?RS_TIANHUOFENYAN ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(被晕)</font>" ++ BE ++ "<br>";
											%% 千年轮回
											?RS_QIANNIANLUNHUI ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(被晕)</font>" ++ BE ++ "<br>"; 
											%% 达芬奇密码
											?RS_DAFENQIMIMA ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(被晕)</font>" ++ BE ++ "<br>";
											%% 水墨画影
											?RS_SHUIMOHUAYING ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(被晕)</font>" ++ BE ++ "<br>";
											%% 烈焰穿心
											?RS_LIEYANCHUANXIN ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(被晕)</font>" ++ BE ++ "<br>";
											%% 闷棍
											?RS_MENGUN ->
												BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++ AttackRoleName ++ "(被晕)</font>" ++ BE ++ "<br>";
											_ ->
												""
										end
									end,
									"",
									FightRoleBeStuntList
								),
								
								BeStuntReport ++ BeFightReportTemp ++ BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++
									AttackRoleName ++ "(" ++ Momentum ++ SelfHurt ++ ")" ++ StrFightType ++ Critical ++ BeFightRoleName ++ BeMomentum ++ Block ++ Hit ++
									",-" ++ integer_to_list(Hurt) ++ "生命值,对方剩余" ++ integer_to_list(LeftHealth) ++ "生命值" ++ EffectName ++ "</font>" ++ BE ++ "<br>"
							end,
							"",
							FightRoleList
						),
						
						AddonItemStr = lists:foldl(
							fun(AI, TempAIStr) ->
								case AI #war_result_addon_item.type of
									other_role_hurt ->
										{AIRoleId, AIValue} = AI #war_result_addon_item.data,
										AIRolename = get_role_name_by_id(AIRoleId),
										TempAIStr ++ "【" ++ AIRolename ++ "】受到伤害" ++ integer_to_list(AIValue) ++ "<br>";
									_ ->
										TempAIStr
								end
							end,
							"",
							AddonItem
						),
						
						WeaponEffectStr = lists:foldl(
							fun(WEI, TempWEStr) ->
								case WEI #war_result_addon_item.type of
									one_increase_momentum ->
										{WERoleId, WEValue} = WEI #war_result_addon_item.data,
										WERolename = get_role_name_by_id(WERoleId),
										TempWEStr ++ "【" ++ WERolename ++ "】闪避/格挡/暴击,增加气势" ++ integer_to_list(WEValue) ++ "<br>";
									one_shield_stunt_attack ->
										{WERoleId, WEValue} = WEI #war_result_addon_item.data,
										WERolename = get_role_name_by_id(WERoleId),
										TempWEStr ++ "【" ++ WERolename ++ "】免疫绝技攻击" ++ integer_to_list(WEValue) ++ "%伤害<br>";
									one_increase_health ->
										{WERoleId, WEValue} = WEI #war_result_addon_item.data,
										WERolename = get_role_name_by_id(WERoleId),
										TempWEStr ++ "【" ++ WERolename ++ "】闪避/格挡/暴击,回复生命" ++ integer_to_list(WEValue) ++ "<br>";
									one_increase_stunt_defense ->
										{WERoleId, WEValue} = WEI #war_result_addon_item.data,
										WERolename = get_role_name_by_id(WERoleId),
										TempWEStr ++ "【" ++ WERolename ++ "】闪避/格挡/暴击,提升绝技防御" ++ integer_to_list(WEValue) ++ "<br>";
									two_role_phantom ->
										{DeadRoleId, WEValue} = WEI #war_result_addon_item.data,
										DeadRolename = get_role_name_by_id(DeadRoleId),
										TempWEStr ++ "【" ++ DeadRolename ++ "】阵亡,召唤火灵" ++ integer_to_list(WEValue) ++ "回合<br>";
									two_increase_health ->
										{DeadRoleId, WERoleId, WEValue} = WEI #war_result_addon_item.data,
										DeadRolename = get_role_name_by_id(DeadRoleId),
										WERolename = get_role_name_by_id(WERoleId),
										TempWEStr ++ "【" ++ DeadRolename ++ "】阵亡,【" ++ WERolename ++ "】回复生命" ++ integer_to_list(WEValue) ++ "<br>";
									two_dead_revive ->
										{DeadRoleId, WERoleId, WEValue} = WEI #war_result_addon_item.data,
										DeadRolename = get_role_name_by_id(DeadRoleId),
										WERolename = get_role_name_by_id(WERoleId),
										TempWEStr ++ "【" ++ DeadRolename ++ "】阵亡,【" ++ WERolename ++ "】复活,剩余" ++ integer_to_list(WEValue) ++ "%血量<br>";
									two_get_stunt_shield ->
										{DeadRoleId, WERoleId, WEValue} = WEI #war_result_addon_item.data,
										DeadRolename = get_role_name_by_id(DeadRoleId),
										WERolename = get_role_name_by_id(WERoleId),
										TempWEStr ++ "【" ++ DeadRolename ++ "】阵亡,【" ++ WERolename ++ "】获得免疫绝技攻击" ++ integer_to_list(WEValue) ++ "%伤害的护盾<br>";
									two_shield_stunt_attack ->
										{WERoleId, WEValue} = WEI #war_result_addon_item.data,
										WERolename = get_role_name_by_id(WERoleId),
										TempWEStr ++ "【" ++ WERolename ++ "】免疫绝技攻击" ++ integer_to_list(WEValue) ++ "%伤害<br>";
									two_phantom_disappear ->
										{WERoleId} = WEI #war_result_addon_item.data,
										WERolename = get_role_name_by_id(WERoleId),
										TempWEStr ++ "【" ++ WERolename ++ "】召唤火灵消失<br>";
									_ ->
										TempWEStr
								end
							end,
							"",
							WeaponEffect
						),
						
						PassivityStuntStr = lists:foldl(
							fun(PSI, TempPSStr) ->
								case PSI #war_result_addon_item.type of
									ps_shield_stunt_attack ->
										{PSRoleId, PSValue} = PSI #war_result_addon_item.data,
										PSRolename = get_role_name_by_id(PSRoleId),
										TempPSStr ++ "【" ++ PSRolename ++ "】免疫绝技攻击" ++ integer_to_list(PSValue) ++ "%伤害<br>";
									ps_decrease_momentum ->
										{PSRoleId, PSValue} = PSI #war_result_addon_item.data,
										PSRolename = get_role_name_by_id(PSRoleId),
										TempPSStr ++ "【" ++ PSRolename ++ "】受到普通攻击,降低" ++ integer_to_list(PSValue) ++ "气势<br>";
									ps_attack_poisoning ->
										{PSRoleId, PSValue} = PSI #war_result_addon_item.data,
										PSRolename = get_role_name_by_id(PSRoleId),
										TempPSStr ++ "【" ++ PSRolename ++ "】中毒,每回合受到" ++ integer_to_list(PSValue) ++ "伤害<br>";
									ps_partner_dead ->
										{PSRoleId, PSDeadRoleId, PSValue} = PSI #war_result_addon_item.data,
										PSRolename = get_role_name_by_id(PSRoleId),
										PSDeadRolename = get_role_name_by_id(PSDeadRoleId),
										TempPSStr ++ "【" ++ PSDeadRolename ++ "】阵亡,【" ++ PSRolename ++ "】提升" ++ integer_to_list(PSValue) ++ "%攻击伤害<br>";
									ps_recover_health ->
										{PSRoleId, PSValue} = PSI #war_result_addon_item.data,
										PSRolename = get_role_name_by_id(PSRoleId),
										TempPSStr ++ "【" ++ PSRolename ++ "】闪避/格挡/暴击,回复生命" ++ integer_to_list(PSValue) ++ "<br>";
									ps_cant_dead ->
										{PSRoleId, PSValue1, PSValue2} = PSI #war_result_addon_item.data,
										PSRolename = get_role_name_by_id(PSRoleId),
										TempPSStr ++ "【" ++ PSRolename ++ "】抵挡致命一击,保留生命" ++ integer_to_list(PSValue1) ++ ",无敌" ++ integer_to_list(PSValue2) ++ "回合<br>";
									ps_role_invincible ->
										{PSRoleId, PSValue} = PSI #war_result_addon_item.data,
										PSRolename = get_role_name_by_id(PSRoleId),
										TempPSStr ++ "【" ++ PSRolename ++ "】无敌,状态剩余" ++ integer_to_list(PSValue) ++ "回合<br>";
									ps_dec_speed ->
										{PSRoleId, PSValue} = PSI #war_result_addon_item.data,
										PSRolename = get_role_name_by_id(PSRoleId),
										TempPSStr ++ "【" ++ PSRolename ++ "】被减速" ++ integer_to_list(PSValue) ++ "<br>";
									ps_add_bati ->
										PSRoleId = PSI #war_result_addon_item.data,
										PSRolename = get_role_name_by_id(PSRoleId),
										TempPSStr ++ "【" ++ PSRolename ++ "】附加霸体状态<br>";
									ps_del_bati ->
										PSRoleId = PSI #war_result_addon_item.data,
										PSRolename = get_role_name_by_id(PSRoleId),
										TempPSStr ++ "【" ++ PSRolename ++ "】移除霸体状态<br>";
									ps_inc_attack ->
										{PSRoleId, PSValue} = PSI #war_result_addon_item.data,
										PSRolename = get_role_name_by_id(PSRoleId),
										TempPSStr ++ "【" ++ PSRolename ++ "】攻击将星附加法术伤害" ++ integer_to_list(PSValue) ++ "<br>";
									_ ->
										TempPSStr
								end
							end,
							"",
							PassivityStunt
						),
						
						BloodPetStuntStr = lists:foldl(
							fun(BPSI, TempBPSStr) ->
								case BPSI #war_result_addon_item.type of
									bps_dec_defense ->
										{BPSRoleId, BPSValue} = BPSI #war_result_addon_item.data,
										BPSRolename = get_role_name_by_id(BPSRoleId),
										TempBPSStr ++ "【" ++ BPSRolename ++ "】降低绝技防御" ++ integer_to_list(BPSValue) ++ "<br>";
									bps_get_momentum ->
										{BPSRoleId, BPSValue} = BPSI #war_result_addon_item.data,
										BPSRolename = get_role_name_by_id(BPSRoleId),
										TempBPSStr ++ "【" ++ BPSRolename ++ "】获取对方气势" ++ integer_to_list(BPSValue) ++ "<br>";
									bps_double_attack ->
										{BPSRoleId} = BPSI #war_result_addon_item.data,
										BPSRolename = get_role_name_by_id(BPSRoleId),
										TempBPSStr ++ "【" ++ BPSRolename ++ "】永久攻击两次<br>";
									_ ->
										TempBPSStr
								end
							end,
							"",
							BloodPetStuntItem
						),
						
						if
							YaoRaoCure =:= false, YaoRaoAddonAttribute =:= false ->
								FightReportTemp ++ BeFightReport ++ 
									"<font color=#2F4F4F>" ++ 
									PassivityStuntStr ++ "</font>" ++
									"<font color=#BC1717>" ++
									AddonItemStr ++ "</font>" ++
									"<font color=#BC1717>" ++
									BloodPetStuntStr ++ "</font>" ++
									"<font color=\"" ++ atom_to_list(green) ++ "\">" ++ 
									WeaponEffectStr ++ "</font>" ++ "<br>";
							true ->
								FightReportTemp ++
									BS ++ "<font color=\"" ++ atom_to_list(Color) ++ "\">" ++
									AttackRoleName ++ StrFightType ++ "</font>" ++ BE ++ "<br><br>"
						end
					end,
					"",
					FightList
				),
				
				{BoutNumberTemp + 1, BoutNumberReport ++ FightReport}
			end,
			{1, ""},
			WarResult #war_result.bout_list
		),
		
		{FightHealth, BeFightHealth, _BoutCount} = rpc:call(GameServer, mod_war, analysis, [WarResult]),
		
		{
			"<font color='blue'>(攻方总伤害:" ++ integer_to_list(FightHealth) ++ ", 攻方总损血:" ++ integer_to_list(BeFightHealth) ++ ")</span><br><br>" ++ Result,
			FlashResult
		}
	end,

	try AnalysisFun() of
		AnalysisResult ->
			erase(all_role_data),
			AnalysisResult
	catch
		_ : Reason ->
			Stacktrace = erlang:get_stacktrace(),
			io:format("analysis_war_report error: ~p.~n", [Reason]),
			io:format("stack trace: ~p.~n", [Stacktrace]),
			erase(all_role_data)
	end.

get_role_name_by_id (PlayerRoleId) ->
	case lists:keyfind(PlayerRoleId, #war_result_role_data.new_role_id, get(all_role_data)) of
		Result when is_record(Result, war_result_role_data) ->
			case Result #war_result_role_data.role_type of
				virtual ->
					Result #war_result_role_data.role_name ++ "分身";
				_ ->
					Result #war_result_role_data.role_name
			end;
		_ ->
			""
	end.

write_data (FileName, Data) ->
    {ok, File} = file:open(FileName, [write]),
    file:pwrite(File, 0, Data),
    file:close(File).
    
write_binary_data (FileName, Data) ->
    {ok, File} = file:open(FileName, [write, binary]),
    file:pwrite(File, 0, Data),
    file:close(File).


%% 把战争参数写成php
write_war_param_php (PHPParamFile, WarParam) ->
    AttackParam = (WarParam #war_param.attack) #war_player_role_data.role_attribute,
    DefenseParam = (WarParam #war_param.defense) #war_player_role_data.role_attribute,
    
    AttackPHPParam  = get_role_param_php_list(AttackParam),
    DefensePHPParam = get_role_param_php_list(DefenseParam),
    
    PHPParam = "array(\n" ++
        "'attack' => " ++ AttackPHPParam ++ ",\n"
        "'defense' => " ++ DefensePHPParam ++ ",\n"
    ")",
    
    write_data(PHPParamFile, PHPParam).
    

write_war_player_param_php (PHPParamFile, PlayerWarParam) ->
    PlayerPHPParam  = get_role_param_php_list(PlayerWarParam #war_player_role_data.role_attribute),
    write_data(PHPParamFile, PlayerPHPParam).
    
    
get_role_param_php_list (RoleAttributeList) ->
    "array(\n" ++
    lists:foldl(
        fun(RoleAttribute, PHPPaam) ->
            PHPPaam ++ get_role_param_php(lib_misc:index_of(RoleAttribute, RoleAttributeList), RoleAttribute) ++ ",\n"
        end,
        "",
        RoleAttributeList
    ) ++
    ")".
    
    
get_role_param_php (Index, RoleAttribute) -> %% io:format("~p~n", [RoleAttribute]),
    integer_to_list(Index) ++ " => " ++
    "array(\n" ++
        "'role_id' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.role_id) ++ ",\n"
        "'role'    => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.role) ++ ",\n"
        "'role_sign' => '" ++ RoleAttribute #player_role_war_attribute.role_sign ++ "',\n"
        "'role_name' => '" ++ RoleAttribute #player_role_war_attribute.role_name ++ "',\n"
        "'role_level' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.role_level) ++ ",\n"
        "'role_max_health' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.role_max_health) ++ ",\n"
        "'role_job_id' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.role_job_id) ++ ",\n"
        "'health' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.health) ++ ",\n"
        "'attack' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.attack) ++ ",\n"
        "'defense' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.defense) ++ ",\n"
        "'magic_attack' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.magic_attack) ++ ",\n"
        "'magic_defense' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.magic_defense) ++ ",\n"
        "'stunt_attack' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.stunt_attack) ++ ",\n"
        "'stunt_defense' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.stunt_defense) ++ ",\n"
        "'hit' => " ++ integer_to_list(lib_misc:ceil(RoleAttribute #player_role_war_attribute.hit * 100)) ++ ",\n"
        "'base_hit' => " ++ integer_to_list(lib_misc:ceil(RoleAttribute #player_role_war_attribute.base_hit * 100)) ++ ",\n"
        "'block' => " ++ integer_to_list(lib_misc:ceil(RoleAttribute #player_role_war_attribute.block * 100)) ++ ",\n"
        "'base_block' => " ++ integer_to_list(lib_misc:ceil(RoleAttribute #player_role_war_attribute.base_block * 100)) ++ ",\n"
        "'break_block' => " ++ integer_to_list(lib_misc:ceil(RoleAttribute #player_role_war_attribute.break_block * 100)) ++ ",\n"
        "'base_break_block' => " ++ integer_to_list(lib_misc:ceil(RoleAttribute #player_role_war_attribute.base_break_block * 100)) ++ ",\n"
        "'dodge' => " ++ integer_to_list(lib_misc:ceil(RoleAttribute #player_role_war_attribute.dodge * 100)) ++ ",\n"
        "'base_dodge' => " ++ integer_to_list(lib_misc:ceil(RoleAttribute #player_role_war_attribute.base_dodge * 100)) ++ ",\n"
        "'critical' => " ++ integer_to_list(lib_misc:ceil(RoleAttribute #player_role_war_attribute.critical * 100)) ++ ",\n"
        "'base_critical' => " ++ integer_to_list(lib_misc:ceil(RoleAttribute #player_role_war_attribute.base_critical * 100)) ++ ",\n"
        "'kill' => " ++ integer_to_list(lib_misc:ceil(RoleAttribute #player_role_war_attribute.kill * 100)) ++ ",\n"
        "'attack_range' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.attack_range) ++ ",\n"
        "'role_stunt_type' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.role_stunt_type) ++ ",\n"
		"'role_base_stunt' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.role_base_stunt) ++ ",\n"
        "'role_stunt' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.role_stunt) ++ ",\n"
        "'role_stunt_attack_range' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.role_stunt_attack_range) ++ ",\n"
        "'position' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.position) ++ ",\n"
        "'momentum' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.momentum) ++ ",\n"
        "'is_boss' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.is_boss) ++ ",\n"
        "'break_critical' => " ++ integer_to_list(lib_misc:ceil(RoleAttribute #player_role_war_attribute.break_critical * 100)) ++ ",\n"
        "'base_break_critical' => " ++ integer_to_list(lib_misc:ceil(RoleAttribute #player_role_war_attribute.base_break_critical * 100)) ++ ",\n"
        "'speed' => " ++ integer_to_list(lib_misc:ceil(RoleAttribute #player_role_war_attribute.speed * 100)) ++ ",\n"
        "'normal_attack' => " ++ integer_to_list(lib_misc:ceil(RoleAttribute #player_role_war_attribute.normal_attack * 100)) ++ ",\n"
        "'dec_kill' => " ++ integer_to_list(lib_misc:ceil(RoleAttribute #player_role_war_attribute.dec_kill * 100)) ++ ",\n"
        "'inc_jiangxing_injure' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.inc_jiangxing_injure) ++ ",\n"
        "'inc_jianxiu_injure' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.inc_jianxiu_injure) ++ ",\n"
        "'inc_wudao_injure' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.inc_wudao_injure) ++ ",\n"
        "'inc_lieshou_injure' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.inc_lieshou_injure) ++ ",\n"
		"'passivity_stunt' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.passivity_stunt) ++ ",\n"
		"'passivity_stunt_lv' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.passivity_stunt_lv) ++ ",\n"
		"'passivity_param1' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.passivity_param1) ++ ",\n"
		"'passivity_param2' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.passivity_param2) ++ ",\n"
		"'weapon_effect' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.weapon_effect) ++ ",\n"
		"'weapon_level' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.weapon_level) ++ ",\n"
		"'effect_prob' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.effect_prob) ++ ",\n"
		"'effect_value' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.effect_value) ++ ",\n"
		"'effect_param' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.effect_param) ++ ",\n"
		"'weapon_effect2' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.weapon_effect2) ++ ",\n"
		"'weapon_level2' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.weapon_level2) ++ ",\n"
		"'effect_prob2' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.effect_prob2) ++ ",\n"
		"'effect_value2' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.effect_value2) ++ ",\n"
		"'effect_param2' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.effect_param2) ++ ",\n"
		"'blood_pet_stunt' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.blood_pet_stunt) ++ ",\n"
        "'blood_pet_id' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.blood_pet_id) ++ ",\n"
		"'is_main_role' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.is_main_role) ++ ",\n"
		"'main_role_hurt' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.main_role_hurt) ++ ",\n"
		"'save_momentum' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.save_momentum) ++ ",\n"
		"'element' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.element) ++ ",\n"
		"'full_momentum' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.full_momentum) ++ ",\n"
        "'armor' => " ++ integer_to_list(RoleAttribute #player_role_war_attribute.armor) ++ ",\n"
    ")".
