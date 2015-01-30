-module(mod_quest).

-include("db.hrl").
-include("api_03.hrl").
-include_lib("stdlib/include/ms_transform.hrl").

-export([
    quest_info_by_questid/1,
    quest_info_by_npcid/1,
    accept_quest/2,
    complete_quest/2,
    list_quest/0,
    giveup_quest/1,
    can_receive_quest/1,
    get_npcname_by_npcid/1,
    get_questid_by_npcid/1,
    town_quest_show/1,
    complete_quest_check_for_war/1,
    complete_quest_check_for_npc/1
]).

-define(ISTALK, 1).
-define(NOTALK, 0).


%% -----------------------------------------------------------------------------
%% Function: get_quest_info (QuestId) -> List
%%          NPCID: NPCID
%% Descrip:  查看NPC任务
%%
%% -----------------------------------------------------------------------------
quest_info_by_npcid (NpcId) ->

    case complete_quest_check_for_npc(NpcId) of 
        1 -> [];
        _ ->
        
        %NpcName = get_npcname_by_npcid(NpcId),
        QuestIds = get_questid_by_npcid(NpcId),
        io:format ("QuestIds ~p ~n ", [QuestIds]),
        
        QuestList = lists:foldl(
            fun (Questidt, Result) ->
                QuestId = Questidt,
                Row = db:get(quest, QuestId),

                Title           = Row #quest.title,
                Content         = Row #quest.content,
                BenginNpcId     = Row #quest.begin_npc_id,
                EndNpcId        = Row #quest.end_npc_id,
                AwardExperience = Row #quest.award_experience,
                AwardCoins      = Row #quest.award_coins,
                AwardItem       = Row #quest.award_item_id,
                IsTalk          = Row #quest.is_talk_quest,
                if AwardItem == 0 ->  ItemName = "";
                    true          -> 
                                Itemlist = db:get(item, AwardItem),
                                case  Itemlist of 
                                    [] -> 
                                        ItemName = "";
                                    Item -> 
                                        ItemName = Item #item.name
                                end
                end,
                Finds = [
                    Quest || Quest <- db:get(player_quest),
                    Quest #player_quest.quest_id == QuestId
                ],

                case Finds of 
                    [] -> 
                        State = 0 ;
                    [PlayerQuest] ->
                        S = PlayerQuest #player_quest.state,
                        State = S 
                end,

                case State of
                    0 ->    NpcTalk = Row #quest.accept_talk;
                    1 ->    NpcTalk = Row #quest.accepted_talk;
                    2 ->    NpcTalk = Row #quest.completed_talk;
                    3 ->    NpcTalk = Row #quest.completed_talk
                end,
                BeginNpcName = get_npcname_by_npcid(BenginNpcId),
                EndNpcName = get_npcname_by_npcid(EndNpcId),
                Tquest = {
                        QuestId,
                        Title,
                        Content,
                        BenginNpcId,
                        BeginNpcName,
                        EndNpcId,
                        EndNpcName,
                        AwardExperience,
                        AwardCoins,
                        ItemName,
                        State,
                        NpcTalk,
                        IsTalk
                },
                if State < 3 ->
                    [Tquest | Result];
                    true ->
                        Result
                end

            end,
            [],
            QuestIds
        ),
        QuestList
    end.

    

%% -----------------------------------------------------------------------------
%% Function: quest_info_by_questid (QuestId) -> List
%%          QuestId: 任务ID
%% Descrip:  根据NPC任务id查看任务信息
%%
%% -----------------------------------------------------------------------------
quest_info_by_questid (QuestId) ->

    Rows = db:get(quest, QuestId),
    case Rows of 
        []  ->  
            {
                "0",
                "0",
                0,
                "0",
                0,
                "0",
                0,
                0,
                "0",
                0,
                0
            };
        Row ->

            Title           = Row #quest.title,
            Content         = Row #quest.content,
            BenginNpcId     = Row #quest.begin_npc_id,
            EndNpcId        = Row #quest.end_npc_id,
            AwardExperience = Row #quest.award_experience,
            AwardCoins      = Row #quest.award_coins,
            AwardItem       = Row #quest.award_item_id,
            
            if AwardItem == 0 ->  ItemName = "";
                true          -> 
                            Itemlist = db:get(item, AwardItem),
                            case  Itemlist of 
                                [] -> 
                                    ItemName = "";
                                Item -> 
                                    ItemName = Item #item.name
                            end
            end,


            Finds = [
                Quest || Quest <- db:get(player_quest),
                Quest #player_quest.quest_id == QuestId
            ],

            case Finds of 
                [] -> 
                    State = 0 ;
                [PlayerQuest] ->
                    S = PlayerQuest #player_quest.state,
                    State = S 
            end,
            
            BeginNpcName = get_npcname_by_npcid(BenginNpcId),
            EndNpcName = get_npcname_by_npcid(EndNpcId),
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
                Row #quest.town_text, 
                State,
                Row #quest.is_talk_quest
            }
        
    end.
    


%% -----------------------------------------------------------------------------
%% Function: accept_quest(PlayerId , Quest_id) -> List
%%			PlayerId: 玩家ID
%%          quest_id: 任务ID
%% Descrip:  接受NPC任务
%%
%% -----------------------------------------------------------------------------
accept_quest(PlayerId , QuestId) ->
    PlayerQuesst = db:get(player_quest),
    Quest   = db:get (quest, QuestId),
    CurrPlyaer = [
                    X || X <- PlayerQuesst,
                    X #player_quest.quest_id == QuestId
                 ],
    case CurrPlyaer of 
        [] ->
            if Quest #quest.is_talk_quest == 1 ->
                State = 2;
                true -> State = 1
            end,
            PlayerQuest = #player_quest {
                    player_id   = PlayerId,
                    quest_id    = QuestId,
                    state       = State
            },
            db:insert(PlayerQuest),
            1;
        _  -> 0
    end.
    
    
%% -----------------------------------------------------------------------------
%% Function: complete_quest_check_for_npc(NpcId) -> List
%%			playerid: 玩家ID
%% Descrip:  判断NPC是否完成任务
%%
%% -----------------------------------------------------------------------------
complete_quest_check_for_npc(NpcId) ->
    
    PlayerQuestList = db:get (player_quest),
    FindQuestList = [
        FindQues || FindQues <- PlayerQuestList,
        FindQues #player_quest.state == 2
    ],
    IsQuest = case FindQuestList of 
        [] ->  0;
        PlayerQuests -> 
        
            lists:foldl (
                fun (PlayerQuest , Result) ->
                    Result,
                    QuestId = PlayerQuest #player_quest.quest_id,
                    Quest = db:get (quest, QuestId),
                    if Quest #quest.is_talk_quest == 1 -> 
                        io:format ("quest is_talk_quest ~n" ),
                        0;
                    true -> 
                        EndNpcId = Quest #quest.end_npc_id,
                        IsTalkQuest = Quest #quest.is_talk_quest,
                        if EndNpcId == NpcId , IsTalkQuest == 1 ->
                                %% 修改任务状态为已完成
                                db:update (
                                    player_quest, 
                                    fun(Player_quest_Row = #player_quest{quest_id = Quest_Id}) ->
                                        if 
                                            Quest_Id == QuestId  ->
                                                Player_quest_Row #player_quest{state = 3};
                                            true ->
                                                false
                                        end
                                    end
                                ),
                                1;
                            true ->  0
                        end
                    end    


                end
                ,
                0,
                PlayerQuests
            )
    end,
    IsQuest.
%% -----------------------------------------------------------------------------
%% Function: complete_quest_check_for_war(Mission_section_id) -> List
%% Descrip:  判断战场副本检查是否完成任务
%%
%% -----------------------------------------------------------------------------
complete_quest_check_for_war(QuestId) ->
    

    PlayerQuestList = db:get (player_quest),
    FindQuestList = [
        FindQues || FindQues <- PlayerQuestList,
        FindQues #player_quest.quest_id == QuestId,
        FindQues #player_quest.state == 1
    ],
    case FindQuestList of 
        [] ->   false;
        _ -> 
            %% 修改任务状态为已完成
            db:update (
                player_quest, 
                fun(Player_quest_Row = #player_quest{quest_id = Quest_Id}) ->
                    if 
                        Quest_Id == QuestId  ->
                            Player_quest_Row #player_quest{state = 2};
                        true ->
                            false
                    end
                end
            )
    end,

    QuestId.
  
%% -----------------------------------------------------------------------------
%% Function: complete_quest (PlayerId , Quest_id) -> List
%%			playerid: 玩家ID
%%          quest_id: 任务ID
%% Descrip:  完成NPC任务 领取任务奖励
%%
%% -----------------------------------------------------------------------------
complete_quest (PlayerId , QuestId) ->
    case PlayerId of 
        undefined ->
			P_Id = 1;
         _ ->
			P_Id = PlayerId
    end,
    
    Finds = [
        Quest || Quest <- db:get(player_quest),
        Quest #player_quest.quest_id == QuestId,
        Quest #player_quest.state > 0 ,
        Quest #player_quest.state < 3 
    ],
    
    [PlayerData]    = db:get(player_data),
    [Player]        = db:get(player),
    
    P_coins         = PlayerData #player_data.coins,
    
    [Player_role]   = db:get(player_role),
    
    P_town_key      = Player #player.town_key,
    P_quest_key     = Player #player.quest_key,
    P_experience    = Player_role #player_role.experience,
    
    case Finds of 
        [] -> 
            State       = 0,
            Experience  = 0,
            Coins       = 0,
            ItemId      = 0,
            TownKey     = 0,
            QuestKey    = 0;
        _ ->
            
            QuestRecord = db:get(quest, QuestId),
            Experience  = QuestRecord #quest.award_experience,
            Coins       = QuestRecord #quest.award_coins,
            ItemId      = QuestRecord #quest.award_item_id,
            TownKey     = QuestRecord #quest.award_town_key,
            QuestKey    = QuestRecord #quest.award_quest_key,


            State = 3,
            
            
            
            %% ------------------------------------
            %% 更新玩家数据
            %% ------------------------------------
            
            %%物品奖励    与物品接口对接
            AwardList = [{ItemId, 1}],
            io:format ("complete get AwardList : ~p ~n" ,[AwardList]),
            mod_item:award_item_list(AwardList),
            %% 经验
            db:update(
                player_role, 
                fun(Row = #player_role{player_id = PID}) ->
                    if PID == P_Id ->
                            Row #player_role{experience = P_experience + Experience};
                        true -> false
                    end
                end
            ),
            
            %% 铜币
            mod_player:increase_coin(Coins),
            %% key 奖励
            db:update(
                player, 
                fun(Player_Row = #player{id = PID}) ->
                    if PID == P_Id ->
                            Player_Row #player{town_key = P_town_key + TownKey , quest_key = QuestKey };
                        true -> false
                    end
                end
            ),
            
            %% 修改任务状态为已令取奖励
            db:update (
                player_quest, 
                fun(Player_quest_Row = #player_quest{quest_id = Quest_Id}) ->
                    if 
                        Quest_Id == QuestId  ->
                            Player_quest_Row #player_quest{state = State};
                        true ->
                            false
                    end
                end
            )
            
    end,
    %是否有新任务
    if State == 3 ->
            NewQuests = db:get(quest, QuestId),
            [Newplayer] = db:get (player),
            NewPlayerQuestKey = Newplayer #player.quest_key,
            NewBeginNpcId = NewQuests #quest.end_npc_id,
            MatchSpec = ets:fun2ms(
                fun(#quest{begin_npc_id = BeginNpcId, lock = Lock})
                    when  BeginNpcId == NewBeginNpcId, Lock == NewPlayerQuestKey ->
                        BeginNpcId
                end
            ),
            NQuest = db:select(quest, MatchSpec),
            case NQuest of 
                [] -> NewQuest = ?NONQUEST;
                _ -> 
                    NewQuest = ?NEWQUEST
            end;
        true -> NewQuest = ?NONQUEST
    end,
        
    {
        State,
        Experience + P_experience,
        Coins + P_coins,
        ItemId,
        TownKey + P_town_key,
        QuestKey + P_quest_key,
        NewQuest
    }.

                        
                        
%% -----------------------------------------------------------------------------
%% Function: list_quest(PlayerId)) -> List
%%			 quest_id: 任务ID
%% Descrip:  玩家任务列表查看
%% -----------------------------------------------------------------------------                 
list_quest() ->
    PlayerQuest = db:get(player_quest),
    
    ListQuest = lists:foldl(
        fun(Row, Result) ->
            QuestId = Row #player_quest.quest_id,
            S       = Row #player_quest.state,
            State   = S ,
            
            Quest   = db:get(quest, QuestId),
            
            Title   = Quest #quest.title,
            Type    = Quest #quest.type,
            Q       = {QuestId, Title, Type, State},
            if State < 3 ->
                [Q | Result];
                true -> Result
            end 
        end, 
        [], 
        PlayerQuest
    ),
    
    ListQuest.

   
%% -----------------------------------------------------------------------------
%% Function: giveup_quest(PlayerId , QuestId) -> List
%%			playerid: 玩家ID 
%%          quest_id: 任务ID
%% Descrip:  放弃NPC任务
%%
%% -----------------------------------------------------------------------------
giveup_quest(QuestId) ->

    PlayerQuest = db:get(player_quest),
    Finds = [
        Quest || Quest <- PlayerQuest,
        Quest#player_quest.quest_id == QuestId, 
        Quest#player_quest.state == 1
    ],

    case Finds of 
        [] -> 
            0 ;
        _ ->
            %% 放弃
            db:delete (
                player_quest,
                fun(#player_quest{quest_id = QuestId2})  -> 
                    if 
                        QuestId2 == QuestId  -> true ;
                        true  -> false
                    end
                end
            ),
            1
    end.


    
%% -----------------------------------------------------------------------------
%% Function: can_receive_quest(TownId)) -> List
%%			 quest_id: 任务ID
%% Descrip:  可接任务列表
%% -----------------------------------------------------------------------------       
    
can_receive_quest(TownId) ->
    [Player] = db:get (player),
    QuestKey = Player #player.quest_key,
    
    MatchSpec = ets:fun2ms(
        fun(#town_npc{npc_id = NpcId, town_id = Tid})
            when Tid == TownId ->
                NpcId
        end
    ),
    
    TownNpcIdList = db:select(town_npc, MatchSpec),
    QuestPlayer = db:get(player_quest),
    QuestIdList = lists:foldl(
        fun (NpcId, PrevQuestList) ->
            MatchSpec2 = ets:fun2ms(
                fun (#quest{id = QuestId, begin_npc_id = BeginNpcId ,lock = Lock}) 
                    when BeginNpcId == NpcId ,  Lock =< QuestKey ->
                        QuestId
                end
            ),
            
            Quests = db:select(quest, MatchSpec2),
            
            Quests ++ PrevQuestList  %% TODO:注意性能
        end,
        [],
        TownNpcIdList
    ),

    QuestList = lists:foldl(
        fun (Quest_Id, Result) ->
            Quest = db:get(quest, Quest_Id),
            
            Finds = [
                        QuestTemp || QuestTemp <- QuestPlayer,
                        QuestTemp #player_quest.quest_id == Quest_Id
                    ],

            case Finds of 
                [] -> 
                    State = 0 ;
                [PlayerQuest] ->
                    S = PlayerQuest #player_quest.state,
                    State = S
            end,
            
            QuestData = {Quest_Id, Quest #quest.title, Quest #quest.type, State },
            if State == 0 ->
                [QuestData | Result ];
                true -> Result
            end

        end,
        [],
        QuestIdList
    ),
    
    QuestList.
    
    
%% ----------------------------------------------------------------------------- 
%% Function: get_npcname_by_npcid(NpcId)) -> NpcName
%%			 quest_id: 任务ID
%% Descrip:  根据NPCID 获取NPCNAME
%% -----------------------------------------------------------------------------   
  
get_npcname_by_npcid(NpcId) -> 
    if NpcId == 0 ->    " ";
        true ->
            Npc = db:get (npc, NpcId),
            Npc #npc.name
    end.
    
%% -----------------------------------------------------------------------------
%% Function: get_questid_by_npcid(NpcId) -> QUESTID
%%			 quest_id: 任务ID
%% Descrip:  根据NPCID 获取QUESTID  过滤大于 QUEST_KEY 的任务
%% -----------------------------------------------------------------------------   
get_questid_by_npcid(NpcId) ->
    [Player] = db:get (player),
    QuestKey = Player #player.quest_key,
    PlayerQuest = db:get (player_quest),
    io:format ("PlayerQuest ~p ~n ", [PlayerQuest]),
    
    MatchSpec2 = ets:fun2ms(
        fun (#quest{id = QuestId, begin_npc_id = BeginNpcId , end_npc_id = EndNpcId , lock = Lock}) 
            when (BeginNpcId == NpcId) or (EndNpcId == NpcId), Lock == QuestKey  ->
                QuestId
        end
    ),
    QeustList = db:select(quest, MatchSpec2),
io:format ("QeustList ~p ~n ", [QeustList]),
    case QeustList of 
        []          -> [];
        [QuestIds] -> 
            Qeust = db:get (quest, QuestIds),
            if Qeust #quest.begin_npc_id ==  NpcId ->
                    %发布NPC 但完成任务不显示
                    io:format ("is begin npc questid ~p ~n", [QuestIds]),
                    case lists:keysearch(QuestIds, 3, PlayerQuest) of 
                        false -> QeustList;
                        {value, PlayerQuests} ->
                            if PlayerQuests #player_quest.state <3  , Qeust #quest.is_talk_quest == 0 ->
                                    QeustList;
                                true -> []
                            end
                    end ;   
                true ->
                    io:format ("is end npc questid ~p ~n", [QuestIds]),
                    %结束NPC接后才显示
                    case lists:keysearch(QuestIds, 3, PlayerQuest) of 
                        false -> [];
                        {value, PlayerQuests} ->
                            io:format ("PlayerQuests ~p ~n ", [PlayerQuests]),
                            if PlayerQuests #player_quest.state <3 ->
                                    QeustList;
                                true -> []
                            end
                    end 
            end
    end.
    


%% -----------------------------------------------------------------------------
%% Function: town_quest_show (TownId) -> List
%%          NPCID: NPCID
%% Descrip:  城镇任务显示
%% -----------------------------------------------------------------------------
town_quest_show (TownId) ->

    [Player] = db:get (player),
    QuestKey = Player #player.quest_key,
    
    MatchSpec = ets:fun2ms(
        fun(#town_npc{npc_id = NpcId, town_id = Tid})
            when Tid == TownId ->
                NpcId
        end
    ),
    
    TownNpcIdList = db:select(town_npc, MatchSpec),
    QuestPlayer = db:get(player_quest),
    QuestIdList = lists:foldl(
        fun (NpcId, PrevQuestList) ->
            MatchSpec2 = ets:fun2ms(
                fun (#quest{id = QuestId, begin_npc_id = BeginNpcId ,lock = Lock}) 
                    when BeginNpcId == NpcId , Lock =< QuestKey ->
                        QuestId
                end
            ),
            
            Quests = db:select(quest, MatchSpec2),
            
            Quests ++ PrevQuestList  %% TODO:注意性能
        end,
        [],
        TownNpcIdList
    ),

    QuestList = lists:foldl(
        fun (Quest_Id, Result) ->
            Quest = db:get(quest, Quest_Id),
            
            Finds = [
                        QuestTemp ||QuestTemp <- QuestPlayer,
                        QuestTemp #player_quest.quest_id == Quest_Id
                    ],

            case Finds of 
                [] -> 
                    State = 0 ;
                [PlayerQuest] ->
                    S = PlayerQuest #player_quest.state,
                    State = S 
            end,
            Npc_End_Id = Quest #quest.end_npc_id,
            IsTalk      = Quest #quest.is_talk_quest,
            Npc_Begin_Id = Quest #quest.begin_npc_id,
            {
                Mission_id, 
                Mission_name, 
                Mission_monster_name
            } = mod_mission:get_quest_mission(Quest_Id),

            QuestData = {
                        Quest_Id, 
                        Quest #quest.title, 
                        Npc_Begin_Id,
                        get_npcname_by_npcid(Npc_Begin_Id),
                        Npc_End_Id,
                        get_npcname_by_npcid(Npc_End_Id),
                        Quest #quest.type, 
                        Quest #quest.conditions, 
                        Quest #quest.town_text, 
                        State ,
                        Mission_id,
                        Mission_name,
                        Mission_monster_name,
                        IsTalk
                        },
            if State < 3 -> 
                    [QuestData | Result ];
                true ->
                    Result
            end
        end,
        [],
        QuestIdList
    ),
    
    QuestList.

    
get_player_quest (PlayerId) when 
    is_number(PlayerId) 
->
    game_db:read(#pk_player_quest{ player_id = PlayerId}),
