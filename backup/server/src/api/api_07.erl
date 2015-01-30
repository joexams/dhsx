%% -----------------------------------------------------------------------------
%% Descrip: 奇术
%% -----------------------------------------------------------------------------
-module(api_07).

-include("server.hrl").
-include("db.hrl").

-compile([export_all]).

%% -----------------------------------------------------------------------------
%% Function: handle(0, <<Type:32>>, State) -> List
%%          quest_id: 任务ID
%% Descrip:  获取奇术列表
%%
%% -----------------------------------------------------------------------------

handle(0, <<Type:32>>, State = #conn_info{ 
    sock = Sock
}) ->
    {QuestList, MySkill, Skill, CdTime} = mod_research:research_list(Type),
   
    QuestToBinary = out_07:research_list(QuestList, MySkill, Skill, CdTime),
	gen_tcp:send(Sock, QuestToBinary),
    
    State;
    
%% -----------------------------------------------------------------------------
%% Function: handle(1, <<ResearchId:32>>, State) -> List
%%          quest_id: 任务ID
%% Descrip:  奇术升级
%%
%% -----------------------------------------------------------------------------

handle(1, <<ResearchId:32>>, State = #conn_info{
    sock = Sock
}) ->

    {
        ResearchState,
        ResearchId,
        Level,
        CdTime,
        Skill,
        MySkill,
        Ingot,
        Time 
    } = mod_research:research_upgrade(ResearchId),
    QuestToBinary = out_07:research_upgrade(
        ResearchState,
        ResearchId,
        Level,
        CdTime,
        Skill,
        MySkill,
        Ingot,
        Time 
    ),
	gen_tcp:send(Sock, QuestToBinary),
    
    State;
        
   
%% -----------------------------------------------------------------------------
%% Function: handle(1, <<>>, State) -> List
%% Descrip:  秒CD时间显示
%%
%% -----------------------------------------------------------------------------

handle(2, <<>>, State = #conn_info{
    sock = Sock
}) ->
    {CdTime, Ingot, MyIngot} = mod_research:clear_cd_time_show(),
   
    QuestToBinary = out_07:clear_cd_time_show(CdTime, Ingot, MyIngot),
	gen_tcp:send(Sock, QuestToBinary),
    
    State;

%% -----------------------------------------------------------------------------
%% Function: handle(3, <<>>, State) -> List
%% Descrip:  确认秒CD时间显示
%% -----------------------------------------------------------------------------

handle(3, <<>>, State = #conn_info{
    sock = Sock
}) ->
    {Result, MyIngot} = mod_research:clear_cd_time(),
   
    QuestToBinary = out_07:clear_cd_time(Result,  MyIngot),
	gen_tcp:send(Sock, QuestToBinary),
    
    State.