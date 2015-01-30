-module(mod_research).

-include("db.hrl").
-include("server.hrl").

-include_lib("stdlib/include/ms_transform.hrl").

-export([
        research_list/1,
        research_upgrade/1,
        clear_cd_time_show/0,
        clear_cd_time/0

        ]).
-define(SUCCEED, 0).
-define(FAILED, 1).
-define(FULL, 2).
-define(CDTIME, 3).
-define(NOENOUGHSKILL, 4).
-define(NOENOUGHINGOT, 5).

%% -----------------------------------------------------------------------------
%% Function: research_list (Type) -> List
%%  Type     奇术类型
%% Descrip:  获取奇术列表
%%
%% -----------------------------------------------------------------------------

research_list (Type) ->
    
    PlayerResearchList = db:get (player_research),
    [Player] = db:get (player),
    ReserchKey = Player #player.research_key,
    [PlayerData] = db:get (player_data),
    MySkill       = PlayerData #player_data.skill,
    % 列出权限小于 key 的奇术
    if Type == 0 ->
            MatchSpec = ets:fun2ms(
                fun(Research = #research{ research_key = RReserchKey})
                    when RReserchKey < ReserchKey + 1 ->
                        Research
                end
            ); 
        true    ->
            MatchSpec = ets:fun2ms(
                fun(Research = #research{ research_key = RReserchKey , research_type_id = ResearchKey })
                    when RReserchKey < ReserchKey + 1 , ResearchKey == Type ->
                        Research
                end
            )  
    end,
    ResearchList = db:select(research, MatchSpec),

    % 查看玩家研究等级
    ResearchLists = lists:foldl (
        fun (Researchs, Result) ->
            RId = Researchs #research.id,
            case lists:keysearch(RId, 3, PlayerResearchList) of 
                false ->  
                    Level = 0,
                    Researchleveldata = db:get (research_level_data, {RId, 1}),
                    Skill   = Researchleveldata #research_level_data.skill,
                    CdTime  = Researchleveldata #research_level_data.cd_time,
                    List = {
                        RId,
                        Researchs #research.name,
                        Level,
                        Researchs #research.research_type_id,
                        Skill,
                        Researchs #research.content,
                        CdTime
                    },
                    
                    [List | Result] ;
                {value, PlayerResearch} ->
                    Level = PlayerResearch #player_research.level,
                    case research_level_data(RId, Level + 1 ) of 
                        [] -> 
                            List = {
                                RId,
                                Researchs #research.name,
                                -1,
                                Researchs #research.research_type_id,
                                0,
                                Researchs #research.content,
                                0
                            };
                        [Researchleveldata] ->
                            Skill   = Researchleveldata #research_level_data.skill,
                            CdTime  = Researchleveldata #research_level_data.cd_time,
                            List = {
                                RId,
                                Researchs #research.name,
                                Level,
                                Researchs #research.research_type_id,
                                Skill,
                                Researchs #research.content,
                                CdTime
                            }
                    end,
                    
                    [List | Result] 
            end
                
        end,
        [],
        ResearchList
    ),
    {Time, Ingot} = mod_player:get_cd_time_props( ?CD_TYPE_RESEARCH ),

    {
        ResearchLists,
        MySkill,
        Ingot,
        Time
    }.
%% -----------------------------------------------------------------------------
%% Function: research_upgrade (ResearchId)
%%  ResearchId      升级的奇术ID
%% Descrip:  奇术升级
%%
%% -----------------------------------------------------------------------------
research_upgrade (ResearchId) ->

    % 冷却时间
    {Time, Ingot} = mod_player:get_cd_time_props( ?CD_TYPE_RESEARCH ),
    [PlayerData] = db:get (player_data),
    MySkill       = PlayerData #player_data.skill,
    [Player] = db:get (player),

    PlayerResearchList = db:get (player_research),
    State = case Time of 
        0 ->
            % 升级需求

            io:format ("PlayerResearchList  ~p ~n ", [PlayerResearchList]),
            case lists:keysearch(ResearchId, 3, PlayerResearchList) of 
                false ->  
                    Level = 0,
                    
                    db:insert ( #player_research{player_id = Player #player.id, research_id = ResearchId, level = Level}),

                    Researchleveldata = db:get (research_level_data, {ResearchId, Level + 1}),
                    Skill   = Researchleveldata #research_level_data.skill,
                    CdTime  = Researchleveldata #research_level_data.cd_time,

                    if MySkill >=  Skill ->
                            %升级
                            upgrade_research (ResearchId, Skill),
                            mod_player:set_cd_time(?CD_TYPE_RESEARCH, CdTime),
                            ?SUCCEED;
                        true ->
                            ?NOENOUGHSKILL
                    end  ; 
                    
                {value, PlayerResearch} -> 
                    Level = PlayerResearch #player_research.level + 1 , 
                    io:format ("PlayerResearch ~p ~n", [PlayerResearch] ),
                    % 下一级是否存在
                    case research_level_data(ResearchId, Level ) of 
                        [] -> ?FULL;
                        [ResearchLevelDatas] ->
                            Skill   = ResearchLevelDatas #research_level_data.skill,
                            CdTime  = ResearchLevelDatas #research_level_data.cd_time,

                            if MySkill >= Skill  ->
                                %升级
                                upgrade_research (ResearchId, Skill),
                                mod_player:set_cd_time(?CD_TYPE_RESEARCH, CdTime),
                                ?SUCCEED;
                            true ->
                                ?NOENOUGHSKILL
                            end   
                    end
            end;
        _ ->
            case lists:keysearch(ResearchId, 3, PlayerResearchList) of 
                false ->  
                    db:insert ( #player_research{player_id = Player #player.id, research_id = ResearchId, level = 0});
                {value, _} -> []
            end,
            ?CDTIME
    end,
    


    {Time2, Ingot2} = mod_player:get_cd_time_props( ?CD_TYPE_RESEARCH ),
    PlayerResearchList2 = db:get (player_research),

    {value, PlayerResearch2} = lists:keysearch(ResearchId, 3, PlayerResearchList2),

    [PlayerDataNew] = db:get (player_data),
    NewSkill       = PlayerDataNew #player_data.skill,

    case research_level_data(ResearchId, PlayerResearch2 #player_research.level + 1) of 
        [] -> 
            {
            State,
            ResearchId,
            -1 ,
            0,
            0,
            NewSkill,
            Ingot2,
            Time2
            };
        [Researchleveldata2] ->     
            {
            State,
            ResearchId,
            Researchleveldata2 #research_level_data.level,
            Researchleveldata2 #research_level_data.cd_time,
            Researchleveldata2 #research_level_data.skill,
            NewSkill,
            Ingot2,
            Time2  
            }
    end.
 
% 升级奇术
upgrade_research (Researchid, Skill) ->
    mod_player:decrease_skill (Skill),
    db:update(
        player_research,
        fun(Row = #player_research{research_id = RId}) ->
            if  RId == Researchid ->
                    Levels = Row # player_research.level + 1,
                    Row #player_research{level = Levels};
                true -> false
            end
        end
    ).
    

% 根据等级获取奇术数据
research_level_data (ResearchId, Level) ->
    MatchSpec = ets:fun2ms(
        fun (ResearchLevel = #research_level_data{research_id = RId, level = RLevel}) 
            when RId == ResearchId , RLevel == Level ->
                ResearchLevel
        end
    ),
    db:select(research_level_data, MatchSpec).

    
%% -----------------------------------------------------------------------------
%% Function: clear_cd_time_show ()
%% Descrip:  秒CD时间显示框
%%
%% -----------------------------------------------------------------------------
clear_cd_time_show() ->
    [PlayerData]    = db:get (player_data),
    MyIngot         = PlayerData #player_data.ingot,
    {Time, Ingot} = mod_player:get_cd_time_props( ?CD_TYPE_RESEARCH ),
    
    {
            Time,
            Ingot,
            MyIngot
    }.

%% -----------------------------------------------------------------------------
%% Function: clear_cd_time ()
%% Descrip:  确认秒CD时间
%%
%% -----------------------------------------------------------------------------
clear_cd_time() ->
    [PlayerData]    = db:get (player_data),
    MyIngot         = PlayerData #player_data.ingot,
    {_, Ingot} = mod_player:get_cd_time_props( ?CD_TYPE_RESEARCH ),
    if MyIngot >= Ingot ->
            Result = mod_player:clean_cd_time(?CD_TYPE_RESEARCH),
            case Result of
                false   ->   {?FAILED,  MyIngot };
                Ingot   ->
                        mod_player:decrease_ingot(Ingot),
                        {?SUCCEED, MyIngot - Ingot }
            end;
        true    ->
            {?NOENOUGHINGOT,  MyIngot }
    end.
    
    
    
    
    
    
