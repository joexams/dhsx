%% -----------------------------------------------------------------------------
%% Descrip: 任务
%% -----------------------------------------------------------------------------
-module(api_03).

-include("server.hrl").
-include("db.hrl").

-compile([export_all]).

%% -----------------------------------------------------------------------------
%% Function: handle(1, quest_id, State) -> List
%%          quest_id: 任务ID
%% Descrip:  查看NPC任务
%%
%% -----------------------------------------------------------------------------

handle(1, <<NpcId:32>>, State = #conn_info{
    sock = Sock
}) ->
    QuestList =  mod_quest:quest_info_by_npcid(NpcId),
    QuestToBinary = out_03:npc_info_by_npcid(QuestList),
	gen_tcp:send(Sock, QuestToBinary),
    
    State;
    
handle(2, <<QuestId:32>>, State = #conn_info{
    sock = Sock
}) ->

    {
        Title,
        Content,
        BenginNpcId,
        BeginNpcName,
        EndNpcId,
        EndNpcName,
        AwardExperience,
        AwardCoins,
        ItemName,
        TownText,
        Quest_State,
        IsTalkQuest
    } = mod_quest:quest_info_by_questid(QuestId),
     % 寻路
    {MissionId, MissionName, MissionMonsterName} = mod_mission:get_quest_mission(QuestId),

    QuestToBinary = out_03:npc_info_by_questid(
        Title,
        Content,
        BenginNpcId,
        BeginNpcName,
        EndNpcId,
        EndNpcName,
        AwardExperience,
        AwardCoins,
        ItemName,
        TownText,
        Quest_State,
        MissionId,
        MissionName,
        MissionMonsterName,
        IsTalkQuest
    ),
	gen_tcp:send(Sock, QuestToBinary),
    
    State;
%% -----------------------------------------------------------------------------
%% Function: handle(2, quest_id, State) -> List
%%          quest_id: 任务ID
%% Descrip:  接受NPC任务
%%
%% -----------------------------------------------------------------------------
handle(3, <<QuestId:32>>, State = #conn_info{
    sock      = Sock,
    player_id = PlayerId
}) ->
    Queststate = mod_quest:accept_quest(PlayerId, QuestId),
    
    QuestToBinary = out_03:accept_quest(Queststate),
    
    gen_tcp:send(Sock, QuestToBinary),
    
    State;
    
%% -----------------------------------------------------------------------------
%% Function: handle(3, quest_id, State) -> List
%%          quest_id: 任务ID
%% Descrip:  完成NPC任务
%%
%% -----------------------------------------------------------------------------
handle(4, <<QuestId:32>>, State = #conn_info{
    sock = Sock,
    player_id = PlayerId
}) -> 

    {
        QuestState,
        Experience ,
        Coins,
        ItemId,
        TownKey,
        QuestKey,
        NewQuest
    } = mod_quest:complete_quest(PlayerId, QuestId),
    
    QuestToBinary = out_03:complete_quest(
        QuestState,
        Experience ,
        Coins,
        ItemId,
        TownKey,
        QuestKey,
        NewQuest
    ),

    gen_tcp:send(Sock, QuestToBinary),
    
    State;

%% -----------------------------------------------------------------------------
%% Function: handle(4, quest_id, State) -> List
%% Descrip:  玩家任务列表查看
%%
%% -----------------------------------------------------------------------------
handle(5, <<>>, State = #conn_info{
    sock = Sock
}) -> 

    ListQuest = mod_quest:list_quest(),
    
    QuestToBinary = out_03:list_player_quest(ListQuest),
   
    gen_tcp:send(Sock, QuestToBinary),
    
    State;
    


%% -----------------------------------------------------------------------------
%% Function: handle(5, quest_id, State) -> List
%%          quest_id: 任务ID
%% Descrip:  放弃NPC任务
%%
%% -----------------------------------------------------------------------------
handle(6, <<QuestId:32>>, State = #conn_info{
    sock = Sock
}) ->

    Queststate = mod_quest:giveup_quest(QuestId),
    
    QuestToBinary = out_03:giveup_quest(Queststate),
    
    gen_tcp:send(Sock, QuestToBinary),
    
    State;

%% -----------------------------------------------------------------------------
%% Function: handle(6, quest_id, State) -> List
%% Descrip:  可接任务列表
%%
%% -----------------------------------------------------------------------------
handle(7, <<TownId:32>>, State = #conn_info{
    sock = Sock
}) -> 
    QuestList = mod_quest:can_receive_quest(TownId),
    
    QuestToBinary = out_03:can_receive_quest(QuestList),
    
    gen_tcp:send(Sock, QuestToBinary),
    
    State;

%% -----------------------------------------------------------------------------
%% Function: handle(8, quest_id, State) -> List
%% Descrip:  城镇显示任务列表
%% -----------------------------------------------------------------------------
handle(8, <<>>, State = #conn_info{
    town_id = TownId,
    sock = Sock
}) -> 

    QuestList = mod_quest:town_quest_show (TownId),
    

    
    QuestToBinary = out_03:town_quest_show(QuestList),
    
    gen_tcp:send(Sock, QuestToBinary),
    
    State.
    
    