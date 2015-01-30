-module(mod_mission).
-compile([export_all]).
-export(
    [
        get_sections/1,
        get_mission/2,
        fight_with_monster/2,
        rank_mission/0,
        get_award_list/0,
        pickup_award/0,
        get_quest_mission/1
    ]
).

-include("server.hrl").
-include("db.hrl").
-include("ets_player.hrl").
-include("ets_logic.hrl").
-include("api_04.hrl").

-include_lib("stdlib/include/ms_transform.hrl").


%% 获取剧情信息
get_sections (TownId) ->
    SectionIdList = sort_mission_section(
        get_player_can_challenge_mission_ids(TownId)
    ),
    
    LastMissionId = get_last_challenge_mission_id(),
    
    Lock =
        if
            LastMissionId > 0 ->
                #mission{lock = TLock} = get_mission_by_id(LastMissionId),
                TLock + 1;
            true ->
                1
        end,
    
    lists:map(
        fun(SectionId) ->
            #mission_section{
                name = SectioinName
            } = get_section_by_id(SectionId),

            MissionIdList = sort_mission(
                get_mission_ids_by_section_id(SectionId, Lock)
            ),

            MissionList = lists:map(
                fun(MissionId) ->
                    #player_mission_record{
                        is_finished = IsFinished,
                        rank        = Rank
                    } = get_player_mission_by_mission_id(MissionId),

                    #mission{
                        id              = MissionId,
                        name            = MissionName,
                        require_power   = RequirePower,
                        award_skill     = AwardSkill,
                        description     = MissionDescription
                    } = get_mission_by_id(MissionId),

                    Status = 
                        if 
                            is_integer(IsFinished), IsFinished >= 1 -> 1;
                            true -> 0 
                        end,
                    %% 副本信息
                    {
                        MissionId,
                        MissionName,
                        MissionDescription,
                        RequirePower,
                        AwardSkill,
                        Status,
                        Rank,
                        get_mission_item(MissionId)
                    }
                end,
                MissionIdList
            ),

            Completion = get_section_completion(SectionId),
            %% 剧情信息
            {
                SectionId,
                SectioinName,
                Completion,
                MissionList
            }
        end,
        SectionIdList
    ).

%% 获取副本信息
get_mission (MissionId, PlayerId) ->

    #mission{
        mission_section_id = SectionId,
        name = MissionName,
        lock = Lock,
        require_power = RequirePower
    } = get_mission_by_id(MissionId),
    #mission_section{
        name = SectionName
    } = get_section_by_id(SectionId),
    %% fixme (自动恢复体力机制还未确定，先不扣体力值)
    RemainPower = mod_player:decrease_power(RequirePower - RequirePower),
    if
        RemainPower =/= false ->
        %% 当前场景ID
        CurSceneId               = get_first_scene_in_mission(MissionId),
        %% 当前场景怪物团ID
        SceneMonsterTeamIdList   = get_scene_mission_monster_team(CurSceneId),
        %% 副本怪物团ID
        MissionMonsterTeamIdList = get_mission_mission_monster_team(MissionId),
        %% 当前怪物团ID
        CurMonsterTeamId         = lists:nth(1, SceneMonsterTeamIdList),

        LastChallengeMissionId   = get_last_challenge_mission_id(),

        CurLock =
            if
                LastChallengeMissionId > 0 ->
                    #mission{lock = TCurLock} =
                        get_mission_by_id(LastChallengeMissionId),
                    TCurLock;
                true ->
                    0
            end,

        if
            (CurLock + 1) >= Lock ->
                true;
            true ->
                exit({"Invalid mission id", MissionId})
        end,

        put(
            player_mission_process,
            #player_mission_process{
                cur_mission_id              = MissionId,
                cur_mission_scene_id        = CurSceneId,
                cur_mission_monster_team_id = CurMonsterTeamId,
                cur_mission_scenes          = get_scene_ids_by_mission_id(
                    MissionId
                ),
                cur_scene_monster_teams     = SceneMonsterTeamIdList,
                cur_mission_monster_teams   = MissionMonsterTeamIdList,
                mission_scene_speed         = 1,
                monster_team_speed          = 1
            }
        ),

        %% 副本怪物团列表
        {MissionMonsterTeam, SceneNumber} = lists:foldl(
            fun(TSceneMonsterTeamIdList, {L, N}) ->
                TSceneMonsterTeam = lists:foldl(
                    fun(MonsterTeamId, TL) ->
                        #mission_monster_team{
                            monster_id = MonsterId,
                            position_x = PositionX,
                            position_y = PositionY
                        } = get_mission_monster_team_by_id(MonsterTeamId),
                        #monster{
                            name = MonsterName,
                            sign = MonsterSign
                        } = get_monster_by_id(MonsterId),
                        lists:append(
                            TL,
                            [{
                                MonsterTeamId,
                                MonsterName,
                                N,
                                MonsterSign,
                                PositionX,
                                PositionY
                            }]
                        )
                    end,
                    [],
                    TSceneMonsterTeamIdList
                ),
                {lists:append(L, TSceneMonsterTeam), N + 1}
            end,
            {[], 0},
            MissionMonsterTeamIdList
        ),

        %% 添加玩家挑战记录
        #player_mission_record{player_id = TPlayerId} =
            get_player_mission_by_mission_id(MissionId),

        if
            is_integer(TPlayerId), TPlayerId =:= PlayerId ->
                Fun = fun(Row) when
                        Row #player_mission_record.player_id  =:= PlayerId,
                        Row #player_mission_record.mission_id =:= MissionId ->
                            Times = Row #player_mission_record.times + 1,
                            Row #player_mission_record{times = Times};
                        (_Row) ->
                            false
                      end,
                db:update(player_mission_record, Fun);
            true ->
                #mission_scene{
                    lock = SceneLock
                } = get_scene_by_id(CurSceneId),
                db:insert(
                    #player_mission_record{
                        player_id  = PlayerId,
                        mission_id = MissionId,
                        rank       = 0,
                        times      = 1,
                        current_scene_lock = SceneLock,
                        current_monster_team_lock = 0,
                        is_finished        = 0,
                        first_time         = lib_misc:get_local_time()
                    }
                )
        end,

        {
            MissionName,
            MissionId,
            SectionName,
            SceneNumber,
            RemainPower,
            MissionMonsterTeam,
            ?SUCCESS
        };
    true ->
        {
            "",
            0,
            "",
            0,
            0,
            [],
            ?LESS_POWER
        }
    end.

%% 开始与怪物战争
fight_with_monster (PlayerId, MonsterTeamId) ->

    %% 判断怪物团ID
    #player_mission_process{cur_mission_monster_team_id = CurMonsterTeamId} =
        get(player_mission_process),
    if
        MonsterTeamId =:= CurMonsterTeamId ->
            true;
        true ->
            exit({"Invalid monster team id", MonsterTeamId})
    end,

    IsMonsterTeamChallenged = is_monster_team_challenged(MonsterTeamId),
    %% 开始战争
    {AttackData, DefenseData, FightResult} = go_war(monster, MonsterTeamId),
    
    {WinnerPlayerId, _, _} = FightResult,
    {AwardExperience, AwardItemList} = if
        %% 玩家赢
        WinnerPlayerId =:= PlayerId ->
            update_player_mission_process(IsMonsterTeamChallenged),
            {Experience, ItemList} = fight_monster_award(
                [RoleId || {RoleId, _} <- AttackData],
                [MonsterId || {MonsterId, _} <- DefenseData]
            ),
            ExperienceList = lists:map(
                fun({RoleId, _}) ->
                    Role = db:get(role, RoleId),
                    {
                        Role #role.sign,
                        Role #role.name,
                        Experience
                     }
                end,
                AttackData
            ),
            {ExperienceList, ItemList};
        %% 玩家输
        true ->
            {[], []}
    end,
    

    IntIsMonsterTeamChallenged = if
        IsMonsterTeamChallenged =:= true ->
            1;
        true ->
            0
    end,
    {
        IntIsMonsterTeamChallenged,
        [FightResult],
        AwardExperience,
        AwardItemList
    }.

%% 副本评分
rank_mission () ->

    #player_mission_process{
        cur_mission_id = MissionId
    } = get(player_mission_process),
    #mission{
        award_skill = AwardSkill,
        award_experience = AwardExperience
    } = get_mission_by_id(MissionId),
    
    Score        = 50,
    Full         = 50,
    ScoreFlag    = 0,
    AttackScore  = 100,
    DefenseScore = 100,
    BoutScore    = 100,

    %% 经验奖励
    lists:foreach(
        fun(RoleId) ->
            try mod_role:increase_RoleExperience(RoleId, AwardExperience)
            catch
                _:_ ->
                    false
            end
        end,
        mod_role:get_current_player_roles()
    ),

    {
        Score,
        Full,
        ScoreFlag,
        AttackScore,
        DefenseScore,
        BoutScore,
        AwardExperience,
        AwardSkill
    }.

%% 获取宝箱奖励列表
get_award_list () ->

    #player_mission_process{
        cur_mission_id = MissionId
    } = get(player_mission_process),
    #mission{
        award_coins = AwardCoin
    } = get_mission_by_id(MissionId),
    AwardItemIdList = get_mission_item_ids_by_mission_id(MissionId),
    AwardItemList = lists:map(
        fun(ItemId) ->
            MissionItem =
                get_mission_item_by_Mission_item_id(MissionId, ItemId),
            ItemTypeId = mod_item:get_item_type(ItemId),
            ItemName   = mod_item:get_item_name(ItemId),
            {
                ItemId,
                ItemTypeId,
                ItemName,
                MissionItem #mission_item.number,
                mod_item:get_item_icon(ItemId)
            }
        end,
        AwardItemIdList
    ),
    PlayerMissionProcess = get(player_mission_process),
    put(
        player_mission_process,
        PlayerMissionProcess #player_mission_process{
            award_list = {
                AwardCoin,
                [
                    {ItemId, Number} ||
                    {
                        ItemId,
                        _ItemTypeId,
                        _ItemName,
                        Number,
                        _ItemIcon
                    } <- AwardItemList
                ]
            }
        }
    ),
    
    {
        AwardCoin,
        AwardItemList
    }.

%% 获取宝箱物品奖励
pickup_award () ->

    #player_mission_process{
        is_mission_challenge_finished = IsMissionChallengeFinished,
        award_list = {AwardCoin, AwardItem}
    } = get(player_mission_process),

    if
        IsMissionChallengeFinished =:= true ->
            try mod_player:increase_coin(AwardCoin)
            catch
                _:_ ->
                   false
            end,
            try mod_item:award_item_list(AwardItem)
            catch
                _:_ ->
                    false
            end,
            %% 清楚奖励内存
            PlayerMissionProcess = get(player_mission_process),
            put(
                player_mission_process,
                PlayerMissionProcess #player_mission_process{award_list = {0, []}}
            ),
            ?PICKUP_SUCCESS;
        true ->
            ?PICKUP_FAILED
    end.

%% -----------------------------------Interface---------------------------------

%% -----------------------------------------------------------------------------
%% Function : get_quest_mission (QuestId) -> {副本ID, 副本名称, 怪物名称}
%%                  QuestId : 任务ID
%% Descrip : 根据任务ID，获取任务对应的副本信息
%% -----------------------------------------------------------------------------
get_quest_mission (QuestId) ->

    MissionMonsterQuestItemIds =
            get_mission_monster_quest_item_ids_by_quest_id(QuestId),
    MissionList = lists:map(
        fun(MissionMonsterQuestItemId) ->
            #mission_monster_quest_item{
                mission_monster_id = MissionMonsterId
            } = get_mission_monster_quest_item_by_id(
                MissionMonsterQuestItemId
            ),
            
            #mission_monster{
                mission_monster_team_id = MonsterTeamId
            } = get_mission_monster_by_id(MissionMonsterId),

            #mission_monster_team{
                mission_scene_id = SceneId,
                monster_id = MonsterId
            } = get_mission_monster_team_by_id(MonsterTeamId),

            #mission_scene{
                mission_id = MissionId
            } = get_scene_by_id(SceneId),

            #monster{
                name = MonsterName
            } = get_monster_by_id(MonsterId),

            Mission = get_mission_by_id(MissionId),
            {
                MissionId,
                Mission #mission.name,
                MonsterName,
                Mission #mission.lock
            }
        end,
        MissionMonsterQuestItemIds
    ),
    if
        %% 没有任务物品，查副本任务关联
        MissionList =:= [] ->
            MissionIdList = get_mission_ids_by_quest_id(QuestId),
            if
                MissionIdList =:= [] ->
                    {0, "", ""};
                true ->
                    MissionId = lists:nth(1, MissionIdList),
                    Mission = get_mission_by_id(MissionId),
                    {
                        MissionId,
                        Mission #mission.name,
                        ""
                    }
            end;
        %% 有任务物品，
        true ->
            {
                MissionId,
                MissionName,
                MonsterName,
                _MissionLock
            } = lists:nth(
                1,
                lists:keysort(
                    4, MissionList
                )
            ),
            {
                MissionId,
                MissionName,
                MonsterName
            }
    end.
    

%% -----------------------------------Assist Fun--------------------------------
%% 获取副本奖励
get_mission_item (MissionId) ->

    ItemIdList = get_mission_item_ids_by_mission_id(MissionId),
    lists:map(
        fun(ItemId) ->
            ItemName = mod_item:get_item_name(ItemId),
            Quality = mod_item:get_item_quality(ItemId),
            {ItemId, ItemName, Quality}
        end,
        ItemIdList
    ).
    
%% 获取剧情挑战进度
get_section_completion (SectionId) ->
    MissionIdList = get_mission_ids_by_section_id(SectionId),
    
    lists:foldr(
        fun(MissionId, C) ->
            #player_mission_record{
                mission_id  = TMissionId,
                is_finished = IsFinished
            } = get_player_mission_by_mission_id(MissionId),
            
            if
                is_integer(TMissionId), TMissionId > 0 ->
                    #mission{
                        completion = Completion
                    } = get_mission_by_id(TMissionId),
                    
                    if
                        IsFinished >= 1, Completion > C ->
                            Completion;
                        true ->
                            C
                    end;
                true ->
                    C
            end
        end,
        0,
        MissionIdList
    ).

%% 获取玩家可以挑战的剧情ID
get_player_can_challenge_mission_ids (TownId) ->
    LastMissionId = get_last_challenge_mission_id(),
    
    CurSectionId =
        if
            LastMissionId > 0 ->
                #mission{mission_section_id = TLastSectionId} =
                    get_mission_by_id(LastMissionId),
                TLastSectionId;
            true ->
                0
        end,
        
    IsComplete =
        if
            LastMissionId > 0 ->
                is_last_mission_in_section(LastMissionId);
            true ->
                true
        end,
        
    get_can_challenge_section_ids(TownId, CurSectionId, IsComplete).

%% 根据最后挑战剧情ID获取可以挑战剧情ID
get_can_challenge_section_ids (TownId, CurSectionId, IsComplete) ->
    CurLock =
        if
            CurSectionId > 0 ->
                #mission_section{lock = TLock} =
                    get_section_by_id(CurSectionId),
                TLock;
            true ->
                0
        end,
        
    Lock =
        if
            false =:= IsComplete ->
                CurLock;
            true ->
                CurLock + 1
        end,
        
    get_can_challenge_section_ids_by_Lock(TownId, Lock).

%% 判断是否剧情中的最后一个副本
is_last_mission_in_section (MissionId) ->
    #mission{
        mission_section_id  = SectionId,
        lock                = Lock
    } = get_mission_by_id(MissionId),
    
    Lock >= get_max_section_mission_lock(SectionId).

%% 怪物团是否挑战过
is_monster_team_challenged (MonsterTeamId) ->

    MonsterTeam  = get_mission_monster_team_by_id(MonsterTeamId),
    MissionScene = get_scene_by_id(
        MonsterTeam #mission_monster_team.mission_scene_id
    ),
    MissionId = MissionScene #mission_scene.mission_id,
    PlayerMissionRecord = get_player_mission_by_mission_id(MissionId),
    if
        PlayerMissionRecord #player_mission_record.is_finished =:= true ->
            true;
        true ->
            PlayerSceneLock =
                PlayerMissionRecord #player_mission_record.current_scene_lock,
            PlayerMonsterTeamLock =
                PlayerMissionRecord #player_mission_record.current_monster_team_lock,
            MissionSceneLock = MissionScene #mission_scene.lock,
            MonsterTeamLock = MonsterTeam #mission_monster_team.lock,
            if
                PlayerSceneLock > MissionSceneLock ->
                    true;
                PlayerSceneLock =:= MissionSceneLock ->
                    PlayerMonsterTeamLock >= MonsterTeamLock;
                true ->
                    false
            end
    end.

%% 获取剧情中的最大副本权值
get_max_section_mission_lock (SectionId) ->
    MissionIdList = get_mission_ids_by_section_id(SectionId),
    lists:foldr(
        fun(MissionId, Lock) ->
            #mission{
                lock = TLock
            } = get_mission_by_id(MissionId),
            
            if
                TLock > Lock ->
                    TLock;
                true ->
                    Lock
            end
        end,
        0,
        MissionIdList
    ).

%% 获取副本中的第一个场景ID
get_first_scene_in_mission (MissionId) ->
    SceneIdList = get_scene_ids_by_mission_id(MissionId),
    
    {RSceneId, _Lock} = lists:foldr(
        fun(SceneId, {Id, Lock}) ->
            #mission_scene{
                id   = TId, 
                lock = TLock
            } = get_scene_by_id(SceneId),
            
            if
                0 =:= Lock ->
                    {TId, TLock};
                TLock < Lock ->
                    {TId, TLock};
                true ->
                    {Id, Lock}
            end
        end,
        {0, 0},
        SceneIdList
    ),
    RSceneId.

%% 获取副本怪物ID获取怪物坐标
get_mission_monster_deploy_grid_type (MissionMonsterId) ->
    #mission_monster{deploy_grid_id = DeployGridId} =
        get_mission_monster_by_id(MissionMonsterId),

    #deploy_grid{deploy_grid_type_id = DeployGridTypeId} =
        get_deploy_grid_by_id(DeployGridId),

    #deploy_grid_type{name = DeployGridTypeName} =
        get_deploy_grid_type_by_id(DeployGridTypeId),
    DeployGridTypeName.

    
%% 获取场景怪物团并权值排序
get_scene_mission_monster_team (SceneId) ->
    sort_mission_monster_team(
        get_mission_monster_team_ids_by_scene_id(SceneId)
    ).

    
%% 获取副本所有怪物团ID列表并权值排序
get_mission_mission_monster_team (MissionId) ->
    SceneIdList =
        sort_mission_scene(get_scene_ids_by_mission_id(MissionId)),
        
    lists:foldr(
        fun(SceneId, L) ->
            MonsterTeamIdList =
                sort_mission_monster_team(
                    get_mission_monster_team_ids_by_scene_id(SceneId)
                ),
            lists:append([MonsterTeamIdList], L)
        end,
        [],
        SceneIdList
    ).

    
%% 对剧情进行权值排序
sort_mission_section (SectionIdList) ->

    SectionInfoList = [
        {SectionId, (get_section_by_id(SectionId))#mission_section.lock}
        ||
        SectionId <- SectionIdList
    ],
    
    SortedSectionInfoList = lists:keysort(2, SectionInfoList),
    
    [SectionId || {SectionId, _} <- SortedSectionInfoList].

    
%% 对副本进行权值排序
sort_mission (MissionIdList) ->

    MissionInfoList = [
        {MissionId, (get_mission_by_id(MissionId))#mission.lock} 
        || 
        MissionId <- MissionIdList
    ],
    
    SortedMissionInfoList = lists:keysort(2, MissionInfoList),

    [MissionId || {MissionId, _} <- SortedMissionInfoList].

    
%% 对场景进行权值排序
sort_mission_scene (SceneIdList) ->

    SceneInfoList = [
        {SceneId, (get_scene_by_id(SceneId))#mission_scene.lock}
        ||
        SceneId <- SceneIdList
    ],
    
    SortedSceneInfoList = lists:keysort(2, SceneInfoList),
    
    [SceneId || {SceneId, _} <- SortedSceneInfoList].

    
%% 对怪物团进行权值排序
sort_mission_monster_team (MonsterTeamIdList) ->

    MonsterTeamInfoList = [
        {
            MonsterTeamId,
            (get_mission_monster_team_by_id(MonsterTeamId))
                                                    #mission_monster_team.lock
        }
        ||
        MonsterTeamId <- MonsterTeamIdList
    ],
    
    SortedMonsterTeamInfoList = lists:keysort(2, MonsterTeamInfoList),
    
    [MonsterTeamId || {MonsterTeamId, _} <- SortedMonsterTeamInfoList].

    
%% 获取副本怪物战争属性(#war_player_role_data)
get_mission_monster_for_war (MonsterTeamId) ->
    
    MissionMonsterIdList =
        get_mission_monster_ids_by_monster_team_id(MonsterTeamId),
    MonsterTeam = get_mission_monster_team_by_id(MonsterTeamId),
    {MonsterList, MonsterAttributeList} = lists:foldr(
        fun(MissionMonsterId, {TMonsterList, TMonsterAttributeList}) ->
            #mission_monster{monster_id = MonsterId} =
                get_mission_monster_by_id(MissionMonsterId),
                
            Monster = get_monster_by_id(MonsterId),
            
            %% 战法
            #role_stunt{
                sign = RoleStuntSign,
                role_stunt_type_id = RoleStuntTypeId,
                name = RoleStuntName,
                role_attack_range_id = AttackRangeId
            } =
                mod_role:get_role_stunt_by_id(Monster #monster.role_stunt_id),
            #role_stunt_type{
                sign = RoleStuntTypeSign
            } =  mod_role:get_role_stunt_type_by_id(RoleStuntTypeId),
            #role_attack_range{
                sign = RoleAttackRangeSign
            } = mod_role:get_role_attack_range(AttackRangeId),
            %% 坐标
            Position = get_mission_monster_deploy_grid_type(MissionMonsterId),
            
            RoleId = "defense_" ++ integer_to_list(MonsterId),
            
            {
                [RoleId | TMonsterList],
                
                [#player_role_war_attribute{
                        role_id         = RoleId,
                        role_sign       = Monster #monster.sign,
                        role_name       = Monster #monster.name,
                        role_level      = Monster #monster.level,
                        role_job_sign   = "",
                        health          = Monster #monster.health,
                        attack          = Monster #monster.attack,
                        defense         = Monster #monster.defense,
                        magic_attack    = Monster #monster.magic_attack,
                        magic_defense   = Monster #monster.magic_defense,
                        stunt_attack    = Monster #monster.stunt_attack,
                        stunt_defense   = Monster #monster.stunt_defense,
                        hit             = Monster #monster.hit,
                        block           = Monster #monster.block,
                        dodge           = Monster #monster.dodge,
                        critical        = Monster #monster.critical,
                        attack_range    = "A",
                        role_stunt_type = RoleStuntTypeSign,
                        role_stunt      = RoleStuntSign,
                        role_stunt_attack_range = RoleAttackRangeSign,
                        role_stunt_name = RoleStuntName,
                        position        = Position
                    } | TMonsterAttributeList
                ]
            }
        end,
        {[], []},
        MissionMonsterIdList
    ),

    Monster = get_monster_by_id(MonsterTeam #mission_monster_team.monster_id),
    
    #war_player_role_data{
        player_data     = #war_player_data{
            player_id = 0,
            player_user_name = Monster #monster.name,
            player_nick_name = Monster #monster.name
        },
        roles           = MonsterList,
        role_attribute  = MonsterAttributeList
    }.

%% 获取玩家战争属性(#war_player_role_data)
get_player_for_war () ->

    [Player] = db:get(player),
    RolseList = [
        "attack_" ++ integer_to_list(RoleId) ||
        RoleId <- mod_role:get_current_player_roles()
    ],
    AttributeList = lists:map(
        fun(Attribute) ->
            RoleId = Attribute #player_role_war_attribute.role_id,
            Attribute #player_role_war_attribute{
                role_id = "attack_" ++ integer_to_list(RoleId)
            }
        end,
        mod_role:get_current_player_role_war_attrs()
    ),
    
    #war_player_role_data{
        player_data = #war_player_data{
            player_id        = Player #player.id,
            player_user_name = Player #player.username,
            player_nick_name = Player #player.nickname
        },
        roles           = RolseList,
        role_attribute  = AttributeList
    }.

%% 开始战争
go_war(monster, MonsterTeamId) ->

    Monster = get_mission_monster_for_war(MonsterTeamId),
    Player  = get_player_for_war(),
    
    WarParam = #war_param{
        attack  = Player,
        defense = Monster,
        role_attribute = []
    },
    spawn(mod_war, start, [self(), WarParam]),
    receive
        Result ->
            Result
    after 1000 ->
        error
    end.

%% 更新玩家当前副本挑战进程
update_player_mission_process (IsMonsterTeamChallenged) ->

    #player_mission_process{
        cur_mission_id               = MissionId,
        cur_mission_scene_id         = CurMissionSceneId,
        cur_mission_monster_team_id  = CurMonsterTeamId,
        cur_mission_scenes           = CurSceneIdList,
        cur_scene_monster_teams      = CurSceneMonsterTeamIdList,
        cur_mission_monster_teams    = CurMissionMonsterTeamIdList,
        mission_scene_speed          = MissionSceneSpeed,
        monster_team_speed           = MonsterTeamSpeed
    } = get(player_mission_process),

    CurrentMonsterTeam = get_mission_monster_team_by_id(CurMonsterTeamId),
    CurrentScene = get_scene_by_id(CurMissionSceneId),
    LastMonsterTeamId = lists:last(lists:last(CurMissionMonsterTeamIdList)),

    TMonsterTeamSpeed = MonsterTeamSpeed + 1,
    
    {
        IsMissionChallengeFinished,
        NewMissionSceneId,
        NewMissionMonsterTeamId,
        NewSceneMonsterTeam,
        NewMissionSceneSpeed,
        NewMonsterTeamSpeed
    } = if
            %% 副本挑战过关
            CurMonsterTeamId =:= LastMonsterTeamId ->
                if
                    IsMonsterTeamChallenged =:= false ->
                        Fun = fun(Row) when Row #player_mission_record.mission_id =:= MissionId ->
                                Row #player_mission_record{
                                    current_scene_lock =
                                        CurrentScene #mission_scene.lock,
                                    current_monster_team_lock =
                                        CurrentMonsterTeam #mission_monster_team.lock,
                                    is_finished = 1
                                };
                              (_Row) -> false
                        end,
                        db:update(player_mission_record, Fun);
                     true ->
                         true
                 end,
                %% 任务关联
                #mission{
                    releate_quest_id = QuestId
                } = get_mission_by_id(MissionId),
                try mod_quest:complete_quest_check_for_war(QuestId)
                catch
                    _:_ -> false
                end,
                {true, 0, 0, [], 0, 0};
            true ->
                LastSceneMonsterTeamId = lists:last(CurSceneMonsterTeamIdList),
                
                if
                    %% 场景完成
                    CurMonsterTeamId =:= LastSceneMonsterTeamId ->
                        TMissionSceneSpeed = MissionSceneSpeed + 1,
                        TCurMissionSceneId = lists:nth(
                            TMissionSceneSpeed,
                            CurSceneIdList
                        ),
                        TCurSceneMonsterTeamIdList =
                            get_scene_mission_monster_team(TCurMissionSceneId),
                        if
                            IsMonsterTeamChallenged =:= false ->
                                 Fun = fun(Row) when Row #player_mission_record.mission_id =:= MissionId ->
                                        Row #player_mission_record{
                                            current_scene_lock =
                                                CurrentScene #mission_scene.lock,
                                            current_monster_team_lock =
                                                CurrentMonsterTeam #mission_monster_team.lock
                                        };
                                    (_Row) -> false
                                end,
                                db:update(player_mission_record, Fun);
                            true ->
                                true
                        end,
                        TCurMissionMonsterTeamId =
                            lists:nth(1, TCurSceneMonsterTeamIdList),
                        {
                            false,
                            TCurMissionSceneId,
                            TCurMissionMonsterTeamId,
                            TCurSceneMonsterTeamIdList,
                            TMissionSceneSpeed,
                            1
                        };
                    %% 场景未完成
                    true ->
                        TCurMissionMonsterTeamId =
                            lists:nth(
                                TMonsterTeamSpeed,
                                CurSceneMonsterTeamIdList
                            ),

                        if
                            IsMonsterTeamChallenged =:= false ->
                                Fun = fun(Row) when Row #player_mission_record.mission_id =:= MissionId ->
                                    Row #player_mission_record{
                                        current_monster_team_lock =
                                            CurrentMonsterTeam #mission_monster_team.lock
                                    };
                                   (_Row) -> false
                                end,
                                db:update(player_mission_record, Fun);
                            true ->
                                true
                        end,
                        {
                            false,
                            CurMissionSceneId,
                            TCurMissionMonsterTeamId,
                            CurSceneMonsterTeamIdList,
                            MissionSceneSpeed,
                            TMonsterTeamSpeed
                        }
                end
        end,

    update_player_mission_process(
        NewMissionSceneId,
        NewMissionMonsterTeamId,
        NewSceneMonsterTeam,
        NewMissionSceneSpeed,
        NewMonsterTeamSpeed,
        IsMissionChallengeFinished
    ).
    
%% 更新玩家当前副本挑战进程
update_player_mission_process (
    MissionSceneId,
    MissionMonsterTeamId,
    SceneMonsterTeam,
    MissionSceneSpeed,
    MonsterTeamSpeed,
    IsMissionChallengeFinished
) ->
    PlayerMissionProcess = get(player_mission_process),
    
    put(
        player_mission_process,
        PlayerMissionProcess #player_mission_process{
            cur_mission_scene_id        = MissionSceneId,
            cur_mission_monster_team_id = MissionMonsterTeamId,
            cur_scene_monster_teams     = SceneMonsterTeam,
            mission_scene_speed         = MissionSceneSpeed,
            monster_team_speed          = MonsterTeamSpeed,
            is_mission_challenge_finished  = IsMissionChallengeFinished
        }
    ).

%% 打副本怪物奖励
fight_monster_award (RoleList, MonsterIdList) ->

    {AwardExperience, AwardItem} = lists:foldl(
        fun(MonsterId, {TempAwardExperience, TempAwardItem}) ->
            Monster = get_monster_by_id(MonsterId),
            ItemId = Monster #monster.award_item_id,
            {
                TempAwardExperience + Monster #monster.award_experience,
                [{ItemId, 1} | TempAwardItem]
            }
        end,
        {0, []},
        MonsterIdList
    ),
    %% 奖励经验
    lists:foreach(
        fun(RoleId) ->
            try mod_role:increase_RoleExperience(RoleId, AwardExperience)
            catch
                _:_ ->
                    false
            end
        end,
        RoleList
    ),
    %% 奖励物品
    AwardItemResult =
    try mod_item:award_item_list(AwardItem)
    catch
        _:_ ->
            []
    end,
    RealAwardItem = lists:foldl(
        fun({Result, ItemId, Number}, TempRealAwardItem) ->
            if
                Result =:= ok ->
                    ItemName = mod_item:get_item_name(ItemId),
                    [{ItemId, ItemName, Number} | TempRealAwardItem];
                true ->
                    TempRealAwardItem
            end
        end,
        [],
        AwardItemResult
    ),
    {AwardExperience, RealAwardItem}.

    
%% -----------------------------------System DB---------------------------------

%% 根据城镇ID获取剧情ID
get_section_ids_by_town_id (TownId) ->
    MatchSpec = ets:fun2ms(
        fun(#mission_section{id = Id, town_id = TId}) when TId =:= TownId->
            Id
        end
    ),

    db:select(mission_section, MatchSpec).

%% 根据剧情ID获取剧情信息
get_section_by_id (SectionId) ->
    db:get(mission_section, SectionId).

%% 根据剧情ID获取副本ID
get_mission_ids_by_section_id (SectionId) ->
    get_mission_ids_by_section_id(SectionId, 10000000).

%% 根据剧情ID和权值获取副本ID
get_mission_ids_by_section_id (SectionId, Lock) ->
    MatchSpec = ets:fun2ms(
        fun(#mission{id = Id, mission_section_id = SId, lock = TLock})
            when SId =:= SectionId, TLock =< Lock ->
            Id
        end
    ),
    db:select(mission, MatchSpec).

%% 根据副本ID获取副本信息
get_mission_by_id (MissionId) ->
    db:get(mission, MissionId).

%% 根据副本ID获取副本Id
get_mission_ids_by_quest_id (QuestId) ->

    MatchSpec = ets:fun2ms(
        fun(#mission{id = MissionId, releate_quest_id = ReleaseQuestId})
            when ReleaseQuestId =:= QuestId->
                MissionId
        end
    ),
    db:select(mission, MatchSpec).

%% 根据副本ID获取副本奖励物品ID
get_mission_item_ids_by_mission_id (MissionId) ->
    MatchSpec = ets:fun2ms(
        fun(#mission_item{item_id = ItemId, mission_id = TMissionId})
            when TMissionId =:= MissionId ->
                ItemId
        end
    ),
    db:select(mission_item, MatchSpec).

get_mission_item_by_Mission_item_id (MissionId, ItemId) ->

    db:get(mission_item, {MissionId, ItemId}).
    
%% 根据Lock获取可以挑战剧情ID
get_can_challenge_section_ids_by_Lock (TownId, Lock) ->
    MatchSpec = ets:fun2ms(
        fun(#mission_section{id = Id, town_id = TTownId, lock = TLock})
            when TTownId =:= TownId, TLock =< Lock ->
                Id
        end
    ),
    db:select(mission_section, MatchSpec).

%% 根据副本ID获取副本场景ID
get_scene_ids_by_mission_id (MissionId) ->
    get_scene_ids_by_mission_id_and_Lock(MissionId, 0).

%% 根据副本ID和权值获取副本场景ID
get_scene_ids_by_mission_id_and_Lock (MissionId, Lock) ->
    MatchSpec = ets:fun2ms(
        fun(#mission_scene{id = Id, mission_id = TMissionId, lock = TLock})
            when TMissionId =:= MissionId, TLock > Lock ->
                Id
        end
    ),
    sort_mission_scene(db:select(mission_scene, MatchSpec)).

%% 根据场景ID获取场景信息
get_scene_by_id (SceneId) ->
    db:get(mission_scene, SceneId).

%% 根据场景ID获取场景怪物团ID
get_mission_monster_team_ids_by_scene_id (SceneId) ->
    MatchSpec = ets:fun2ms(
        fun(#mission_monster_team{id = Id, mission_scene_id = TSceneId})
            when TSceneId =:= SceneId ->
                Id
        end
    ),
    db:select(mission_monster_team, MatchSpec).

%% 根据怪物团ID获取怪物团信息
get_mission_monster_team_by_id (MonsterTeamId) ->
    db:get(mission_monster_team, MonsterTeamId).

%% 根据怪物团ID获取副本怪物ID列表
get_mission_monster_ids_by_monster_team_id (MonsterTeamId) ->
    MatchSpec = ets:fun2ms(
        fun(#mission_monster{id = Id, mission_monster_team_id = TMonsterTeamId})
            when TMonsterTeamId =:= MonsterTeamId ->
                Id
        end
    ),
    db:select(mission_monster, MatchSpec).

%% 根据副本怪物ID获取怪物信息
get_mission_monster_by_id (MissionMonsterId) ->
    db:get(mission_monster, MissionMonsterId).

%% 根据怪物ID获取怪物信息
get_monster_by_id (MonsterId) ->
    db:get(monster, MonsterId).

%% 根据阵法ID获取阵法信息
get_deploy_grid_by_id (DeployGridId) ->
    db:get(deploy_grid, DeployGridId).

%% 根据阵法站位类型ID获取站位
get_deploy_grid_type_by_id (DeployGridTypeId) ->
    db:get(deploy_grid_type, DeployGridTypeId).
    
%% 根据ID获取副本怪掉落的任务物品
get_mission_monster_quest_item_by_id (MissionMonsterQuestItemId) ->

    db:get(mission_monster_quest_item, MissionMonsterQuestItemId).

%% 根据任务ID获取副本怪掉落的任务物品
get_mission_monster_quest_item_ids_by_quest_id (QuestId) ->

    MatchSpec = ets:fun2ms(
        fun(#mission_monster_quest_item{id = Id, quest_id = TempQuestId})
            when QuestId =:= TempQuestId ->
                Id
        end
    ),
    db:select(mission_monster_quest_item, MatchSpec).
    
%% -----------------------------------Player DB---------------------------------

%% 根据玩家ID/副本ID获取玩家副本信息
get_player_mission_by_mission_id (MissionId) ->
    Rows = db:get(player_mission_record),
    
    Result = 
        case lists:keyfind(MissionId, #player_mission_record.mission_id, Rows) of
            false ->
                #player_mission_record{rank = 0};
            Data ->
                Data
        end,
    Result.

%% 获取玩家最后挑战完成的副本ID
get_last_challenge_mission_id () ->
    Rows = db:get(player_mission_record),
    
    {MissionId, _} =
        lists:foldr(
            fun(
                #player_mission_record{
                    mission_id  = TMissionId,
                    is_finished = IsFinished
                },
                {Id, Lock}
            ) ->
                #mission{lock = TLock} = get_mission_by_id(TMissionId),
                
                if
                    IsFinished >= 1, TLock > Lock ->
                        {TMissionId, TLock};
                    true ->
                        {Id, Lock}
                end
            end,
            {0, 0},
            Rows
        ),
    MissionId.