%% -----------------------------------------------------------------------------
%% Descrip: 副本
%% author: qinlai.cai@gmail.com
%% -----------------------------------------------------------------------------
-module(api_04).

-include("server.hrl").

-compile([export_all]).


%% -----------------------------------------------------------------------------
%% Function: handle(0, TownId, State)
%%          TownId: 城镇ID
%% Descrip:  剧情列表，获取剧情副本信息
%%
%% -----------------------------------------------------------------------------
handle(0, <<TownId:32>>, State = #conn_info{
    sock = Sock
}) ->
    Sections = mod_mission:get_sections(TownId),
    
    BinSections = out_04:get_sections(Sections),
    
    gen_tcp:send(Sock, BinSections),
    
    State;

%% -----------------------------------------------------------------------------
%% Function: handle(1, MissionId, State)
%%          MissionId: 副本ID
%% Descrip:  进入副本，获取副本信息
%%
%% -----------------------------------------------------------------------------
handle(1, <<MissionId:32>>, State = #conn_info{
    player_id = PlayerId, 
    sock      = Sock
}) ->
    {
        Name,
        MissionId,
        SectionName,
        ScreenCount,
        Power,
        MonsterTeam,
        Result
    } = mod_mission:get_mission(MissionId, PlayerId),
    
    BinMission = out_04:get_mission(
        Name,
        MissionId,
        SectionName,
        ScreenCount,
        Power,
        MonsterTeam,
        Result
    ),
    
    gen_tcp:send(Sock, BinMission),
    
    State;

%% -----------------------------------------------------------------------------
%% Function: handle(2, MonsterTeamId, State)
%%          MonsterTeamId: 副本ID
%% Descrip:  开始战争
%%
%% -----------------------------------------------------------------------------
handle(2, <<MonsterTeamId:32>>, State = #conn_info{
    player_id = PlayerId, 
    sock      = Sock
}) ->
    {
        IsMonsterTeamChallenged,
        WarResult,
        AwardExperiences,
        AwardItems
    } = mod_mission:fight_with_monster(PlayerId, MonsterTeamId),
    
    BinData = out_04:start_fight(
        IsMonsterTeamChallenged,
        WarResult,
        AwardExperiences,
        AwardItems
    ),
    
    gen_tcp:send(Sock, BinData),
    
    State;

%% -----------------------------------------------------------------------------
%% Function: handle(3, State)
%% Descrip:  副本评价
%%
%% -----------------------------------------------------------------------------
handle(3, <<>>, State = #conn_info{sock = Sock}) ->
    {
        Score,
        Full,
        ScoreFlag,
        AttackScore,
        DefenseScore,
        BoutScore,
        AwardExperience,
        AwardSkill
    } = mod_mission:rank_mission(),

    BinData = out_04:rank_mission(
        Score,
        Full,
        ScoreFlag,
        AttackScore,
        DefenseScore,
        BoutScore,
        AwardExperience,
        AwardSkill
    ),

    gen_tcp:send(Sock, BinData),

    State;

%% -----------------------------------------------------------------------------
%% Function: handle(4, State)
%% Descrip:  副本评价
%%
%% -----------------------------------------------------------------------------
handle(4, <<>>, State = #conn_info{sock = Sock}) ->
    {
        AwardCoin,
        AwardItem
    } = mod_mission:get_award_list(),

    BinData = out_04:get_award_list(
        AwardCoin,
        AwardItem
    ),

    gen_tcp:send(Sock, BinData),

    State;

%% -----------------------------------------------------------------------------
%% Function: handle(5, State)
%% Descrip:  副本评价
%%
%% -----------------------------------------------------------------------------
handle(5, <<>>, State = #conn_info{sock = Sock}) ->
    Result = mod_mission:pickup_award(),

    BinData = out_04:pickup_award(Result),

    gen_tcp:send(Sock, BinData),

    State.