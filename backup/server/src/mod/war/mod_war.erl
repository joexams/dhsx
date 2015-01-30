%% -----------------------------------------------------------------------------
%% Descrip : 战场模块
%% Author  : qinlai.cai@gmail.com
%% -----------------------------------------------------------------------------
-module(mod_war).

-include("ets_logic.hrl").

-export(
    [
        start/2,
        get_role_param/1,
        set_role_param/2
    ]
).

%% 攻方玩家数据
-define(ATTACK_PLAYER_DATA, attack_player_data).
%% 守方玩家数据
-define(DEFENSE_PLAYER_DATA, defense_player_data).
%% 角色属性
-define(ROLE_ATTRIBUTE, "role_attribute_").
%% 角色参数
-define(ROLE_PARAM, "role_process_param_").
%% 攻击方角色列表
-define(ATTACK_ROLE_LIST, attack_role_list).
%% 攻击方角色列表
-define(DEFENSE_ROLE_LIST, defense_role_list).
%% 攻击顺序
-define(FIGHT_ORDER, fight_order).
%% 攻方剩余角色列表
-define(ATTACK_REMAIN_ROLE, attack_remain_role_list).
%% 守方剩余角色列表
-define(DEFENSE_REMAIN_ROLE, defense_remain_role_list).
%% 阵形
-define(GRID, grid).
%% 攻击索引
-define(FIGHT_INDEX, fight_index).
%% 回合数
-define(BOUT_NUMBER, bout_number).

%% 战法类型
-define(STUNT_TYPE, ["ZhanFa", "AoYi", "BaTi"]).
%% 法术职业
-define(MAGIC_JOB, ["ShuShi", "FangShi"]).
%% 攻击范围
-define(RANGE_A, "A").
-define(RANGE_B, "B").
-define(RANGE_C, "C").
-define(RANGE_D, "D").

%% 攻击类型
-define(fixme, 1).
%% 最大回合数
-define(MAX_BOUT, 40).
%% 命中基数
-define(HIT_BASE, 90).
%% 每次攻击添加气势值
-define(ADD_MOMENTUM, 25).
%% 气势值满
-define(FULL_MOMENTUM, 100).

start (ParentProc, WarParam) ->

    %% 初始化战争
    init(WarParam),
    %% 命格(fixme)
    
    %% 开始战争
    FightResult = start_fight([], [], 1),
    
    ParentProc ! get_war_result(FightResult).


%% 初始化战争
init (WarParam) ->

    Attack  = WarParam #war_param.attack,
    Defense = WarParam #war_param.defense,

    %% 玩家数据
    AttackPlayerData  = Attack #war_player_role_data.player_data,
    DefensePlayerData = Defense #war_player_role_data.player_data,
    %% 角色列表
    AttackRoleList  = Attack #war_player_role_data.roles,
    DefenseRoleList = Defense #war_player_role_data.roles,

    %% 角色属性
    AttackRoleAttribute  = Attack #war_player_role_data.role_attribute,
    DefenseRoleAttribute = Defense #war_player_role_data.role_attribute,

    %% 设置玩家数据
    put(?ATTACK_PLAYER_DATA, AttackPlayerData),
    put(?DEFENSE_PLAYER_DATA, DefensePlayerData),
    
    %% 设置角色列表
    put(?ATTACK_ROLE_LIST, AttackRoleList),
    put(?DEFENSE_ROLE_LIST, DefenseRoleList),

    %% 设置剩余角色列表
    put(?ATTACK_REMAIN_ROLE, AttackRoleList),
    put(?DEFENSE_REMAIN_ROLE, DefenseRoleList),
    
    %% 设置角色属性
    RoleList          = lists:append(AttackRoleList, DefenseRoleList),
    RoleAttributeList = lists:append(AttackRoleAttribute, DefenseRoleAttribute),
    lists:foreach(
        fun(RoleId) ->
            Attribute = get_role_attribute(
                RoleId,
                RoleAttributeList
            ),
            set_role_attribute(RoleId, Attribute)
        end,
        RoleList
    ),

    %% 初始化角色参数
    init_role_param(
        "attack",
        AttackRoleList,
        AttackRoleAttribute,
        AttackPlayerData
    ),
    init_role_param(
        "defense",
        DefenseRoleList,
        DefenseRoleAttribute,
        DefensePlayerData
    ),

    %% 初始化阵形
    init_grid(RoleList, RoleAttributeList),

    %% 创建攻击顺序列表
    FightList = create_fight_list(
        AttackRoleList,
        DefenseRoleList,
        AttackRoleAttribute,
        DefenseRoleAttribute
    ),
    put(?FIGHT_ORDER, FightList),
    put(?FIGHT_INDEX, 1).

%% 开始战争
start_fight (BoutList, TempBoutList, BoutNumber) ->

    put(?BOUT_NUMBER, BoutNumber),
    FightRoleId     = get_fight_role(),
    BeFightRoleList = get_be_fight_role(FightRoleId),
    
    if
        %% 战争结束
        BeFightRoleList =:= [] orelse BoutNumber > ?MAX_BOUT ->
            if
                TempBoutList =:= [] ->
                    BoutList;
                true ->
                    lists:append(BoutList, [TempBoutList])
            end;
        true ->
            %% 攻击
            FirstFightResult = fight(FightRoleId, BeFightRoleList),
            %% 反击
            IsBackFight = is_back_attack(FightRoleId, FirstFightResult),
            BackFightResult = if
                IsBackFight =:= true ->
                    back_fight(lists:nth(1, BeFightRoleList), FightRoleId);
                true ->
                    []
            end,
            FightResult = lists:append(FirstFightResult, BackFightResult),
            %% 更新攻击索引
            update_fight_index(),
            FightIndex = get(?FIGHT_INDEX),
            
            {NewBoutList, NewTempBoutList, NewBoutNumber} = if
                FightIndex =:= 1 ->
                    put(?FIGHT_INDEX, 1),
                    {
                        lists:append(BoutList, [lists:append(TempBoutList, FightResult)]),
                        [],
                        BoutNumber + 1
                    };
                true ->
                    {
                        BoutList,
                        lists:append(TempBoutList, FightResult),
                        BoutNumber
                    }
            end,
            start_fight(
                NewBoutList,
                NewTempBoutList,
                NewBoutNumber
            )
    end.

%% 获取战争结果
get_war_result (BoutList) ->

    %% 战胜方玩家数据
    #war_player_data{
        player_id = WinnerPlayerId
    } = get_winner_player_data(),

    %% 角色参数
    AttackRoleParam  = get_player_role_param("attack"),
    DefenseRoleParam = get_player_role_param("defense"),

    %% 玩家数据
    AttackPlayerData  = get_init_player_data("attack"),
    DefensePlayerData = get_init_player_data("defense"),
    
    {
        AttackRoleParam,     %% 攻击方角色列表
        DefenseRoleParam,    %% 防守方角色列表
        {
            WinnerPlayerId, %% 战胜方玩家ID
            [
                AttackPlayerData,       %% 攻击方玩家数据
                DefensePlayerData       %% 防守方玩家数据
            ],
            [
                {TempBoutList} ||
                TempBoutList <- BoutList
            ] %% 战争过程
        }
    }.

%% 获取初始数据
get_init_player_data (Army) ->

    #war_player_data{
        player_id = PlayerId,
        player_nick_name = NickName
    } = get_player_data(Army),
    RoleList = get_player_role_list(Army),

    RoleData = lists:map(
        fun(RoleId) ->
            #player_role_war_attribute{
                role_id    = RoleId,
                role_sign  = RoleSign,
                role_name  = RoleName,
                health     = Health,
                role_level = Level,
                position   = Position
            } = get_role_attribute(RoleId),
            {
                RoleId,
                RoleSign,
                RoleName,
                Health,
                Level,
                Position
            }
        end,
        RoleList
    ),

    {
        PlayerId,
        NickName,
        RoleData
    }.
    
%% 获取角色战后参数
get_player_role_param (Army) ->

    RolePrefix = if
        Army =:= "attack" ->
            "attack_";
        true ->
            "defense_"
    end,
    lists:map(
        fun(RoleId) ->
            #role_process_param{
                left_health = LeftHealth
            } = get_role_param(RoleId),
            RealRoleId = list_to_integer(lists:subtract(RoleId, RolePrefix)),
            {
                RealRoleId,
                LeftHealth
            }
        end,
        get_player_role_list(Army)
    ).

%% 攻击
fight (FightRoleId, BeFightRoleList) ->
    
    FightType = get_fight_type(FightRoleId),
    
    FightResult = lists:map(
        fun(BeFightRoleId) ->
            {
                IsHit,
                IsBlock,
                IsCritical,
                Hurt
            } = fight_role(FightType, FightRoleId, BeFightRoleId),

            #role_process_param{
                left_health = Health,
                momentum    = Momentum
            } = get_role_param(BeFightRoleId),

            reduce_role_health(BeFightRoleId, Hurt),
            {
                BeFightRoleId,
                Momentum,
                Hurt,
                Health,
                bool_to_integer(IsHit),
                bool_to_integer(IsBlock),
                bool_to_integer(IsCritical)
            }
        end,
        BeFightRoleList
    ),
    %% 增加气势
    case lists:keyfind(1, 5, FightResult) of
        false ->
            ok;
        _ ->
            if
                FightType =/= "ZhanFa" ->
                    add_momentum(FightRoleId, ?ADD_MOMENTUM);
                true ->
                    ok
            end
    end,
    %% 战法攻击取得精确的战法
    RealFightType = case lists:member(FightType, ?STUNT_TYPE) of
        true ->
            %% 清除气势值
            RoleParam = get_role_param(FightRoleId),
            set_role_param(
                FightRoleId,
                RoleParam #role_process_param{
                    momentum = 0
                }
            ),
            Attribute = get_role_attribute(FightRoleId),
            Attribute #player_role_war_attribute.role_stunt;
        _ ->
            FightType
    end,

    %% 玩家数据
    #war_player_data{
        player_id = PlayerId
    } = get_player_data_by_role(FightRoleId),
    #war_player_data{
        player_id = BeFightPlayerId
    } = get_player_data_by_role(lists:nth(1, BeFightRoleList)),
    
    #role_process_param{
        momentum = FightMomentum
    } = get_role_param(FightRoleId),

    [{
        PlayerId,
        FightRoleId,
        FightMomentum,
        BeFightPlayerId,
        RealFightType,
        FightResult
    }].

%% 反击
back_fight (FightRoleId, BeFightRoleId) ->

    FightType = "BackAttack",
    
    {
        IsHit,
        IsBlock,
        IsCritical,
        Hurt
    } = fight_role(FightType, FightRoleId, BeFightRoleId),

    #role_process_param{
        left_health = Health,
        momentum = Momentum
    } = get_role_param(BeFightRoleId),

    %% 扣除生命值
    reduce_role_health(BeFightRoleId, Hurt),
    
    FightResult = [{
        BeFightRoleId,
        Momentum,
        Hurt,
        Health,
        bool_to_integer(IsHit),
        bool_to_integer(IsBlock),
        bool_to_integer(IsCritical)
    }],
            
    %% 玩家数据
    #war_player_data{
        player_id = PlayerId
    } = get_player_data_by_role(FightRoleId),
    #war_player_data{
        player_id = BeFightPlayerId
    } = get_player_data_by_role(BeFightRoleId),

    #role_process_param{
        momentum = FightMomentum
    } = get_role_param(FightRoleId),

    [{
        PlayerId,
        FightRoleId,
        FightMomentum,
        BeFightPlayerId,
        FightType,
        FightResult
    }].

%% 普通攻击
fight_role ("NormalAttack", FightRoleId, BeFightRoleId) ->

    {FightRoleAttribute, BeFightRoleAttribute} = get_addon_role_attribute(
        FightRoleId,
        BeFightRoleId
    ),

    #player_role_war_attribute{
        hit      = Hit,
        attack   = Attack,
        critical = Critical
    } = FightRoleAttribute,

    #player_role_war_attribute{
        defense = Defense,
        block   = Block,
        dodge   = Dodge
    } = BeFightRoleAttribute,

    %% 是否命中
    IsHit = random_number(10000) -
                (?HIT_BASE + Hit - Dodge) * 100 =< 0,
    HitValue = if
        IsHit =:= true ->
            1;
        true ->
            0
    end,

    %% 是否档格
    IsBlock = (random_number(10000) - Block * 100 =< 0) andalso (IsHit =:= true),
    BlockValue = if
        IsBlock =:= true ->
            0.5;
        true ->
            1
    end,
    %% 是否暴击
    IsCritical = (random_number(10000) - Critical * 100 =< 0) andalso
                    (IsHit =:= true) andalso (IsBlock =:= false),
    CriticalValue = if
        IsCritical =:= true ->
            1.5;
        true ->
            1
    end,

    %% 伤害比例
    HurtRate = get_hurt_rate (FightRoleId),

    %% 伤害
    Hurt = max(
        1,
        lib_misc:ceil(
            (Attack - Defense) * BlockValue * CriticalValue * HurtRate
        )
    ) * HitValue,

    %% 增加气势
    if
        IsHit =:= true ->
            add_momentum(BeFightRoleId, ?ADD_MOMENTUM);
        true ->
            ok
    end,

    {
        IsHit,          %% 是否命中
        IsBlock,        %% 是否档格
        IsCritical,     %% 是否暴击
        Hurt            %% 伤害生命值
    };

%% 反击
fight_role ("BackAttack", FightRoleId, BeFightRoleId) ->

    {FightRoleAttribute, BeFightRoleAttribute} = get_addon_role_attribute(
        FightRoleId,
        BeFightRoleId
    ),

    #player_role_war_attribute{
        attack   = Attack
    } = FightRoleAttribute,

    #player_role_war_attribute{
        defense = Defense
    } = BeFightRoleAttribute,

    %% 伤害比例
    HurtRate = get_hurt_rate (FightRoleId),

    %% 伤害
    Hurt = max(
        1,
        lib_misc:ceil(
            (Attack - Defense) * HurtRate
        )
    ),
    
    {
        true,  %% 是否命中
        false, %% 是否档格
        false, %% 是否暴击
        Hurt   %% 伤害生命值
    };

%% 普通攻击
fight_role ("ZhanFa", FightRoleId, BeFightRoleId) ->

    {FightRoleAttribute, BeFightRoleAttribute} = get_addon_role_attribute(
        FightRoleId,
        BeFightRoleId
    ),

    Hit   = FightRoleAttribute #player_role_war_attribute.hit,
    Dodge = FightRoleAttribute #player_role_war_attribute.dodge,
    
    %% 是否命中
    IsHit = random_number(10000) -
                (?HIT_BASE + Hit - Dodge) * 100 =< 0,
    HitValue = if
        IsHit =:= true ->
            %% 施加战法
            Stunt = FightRoleAttribute #player_role_war_attribute.role_stunt,
            mod_stunt:set_stunt(Stunt, BeFightRoleId),
            1;
        true ->
            0
    end,

    %% 战法生效
    BeStunt = get_role_be_stunt(BeFightRoleId),
    {RealBeFightRoleAttribute, RealFightRoleAttribute} =
        mod_stunt:get_stunt_effect(
            BeStunt,
            BeFightRoleAttribute,
            FightRoleAttribute
    ),
    
    %% 角色属性
    #player_role_war_attribute{
        attack       = Attack,
        stunt_attack = StuntAttack,
        critical     = Critical
    } = RealFightRoleAttribute,

    #player_role_war_attribute{
        stunt_defense = StuntDefense
    } = RealBeFightRoleAttribute,

    %% 是否暴击
    IsCritical = (random_number(10000) - Critical * 100 =< 0) andalso
                    (IsHit =:= true),
    CriticalValue = if
        IsCritical =:= true ->
            1.5;
        true ->
            1
    end,

    %% 伤害比例
    HurtRate = get_stunt_hurt_rate (FightRoleId),

    %% 伤害
    Hurt = max(
        1,
        lib_misc:ceil(
            (Attack + StuntAttack - StuntDefense) * CriticalValue * HurtRate
        )
    ) * HitValue,

    {
        IsHit,          %% 是否命中
        false,          %% 是否档格
        IsCritical,     %% 是否暴击
        Hurt            %% 伤害生命值
    }.

%% 获取伤害比例
get_hurt_rate (RoleId) ->

    #player_role_war_attribute{
        attack_range = FightRange
    } = get_role_attribute(RoleId),
    
    case FightRange of
        ?RANGE_B ->
            0.5;
        ?RANGE_C ->
            0.5;
        ?RANGE_D ->
            0.25;
        _ ->
            1
    end.

%% 获取战法攻击伤害比例
get_stunt_hurt_rate (RoleId) ->

    #player_role_war_attribute{
        role_stunt_attack_range = StuntFightRange
    } = get_role_attribute(RoleId),

    case StuntFightRange of
        ?RANGE_B ->
            0.5;
        ?RANGE_C ->
            0.5;
        ?RANGE_D ->
            0.25;
        _ ->
            1
    end.

%% 获取攻击类型
get_fight_type (RoleId) ->

    #player_role_war_attribute{
        role_stunt_type = StuntType,
        role_job_sign   = Job
    } = get_role_attribute(RoleId),
    #role_process_param{
        momentum = Momentum
    } = get_role_param(RoleId),
    
    IsMagicJob = lists:member(Job, ?MAGIC_JOB),
    if
        %% 战法攻击
        Momentum >= ?FULL_MOMENTUM ->
            StuntType;
        true ->
            if
                IsMagicJob =:= true ->
                    "MagicAttack";
                true ->
                    "NormalAttack"
            end
    end.

%% 扣除生命值
reduce_role_health (RoleId, Hurt) ->

    %% 更新剩余生命值
    RoleParam = get_role_param(RoleId),
    NewLeftHurt = max(
        0,
        RoleParam #role_process_param.left_health - Hurt
    ),
    set_role_param(
        RoleId,
        RoleParam #role_process_param{
            left_health = NewLeftHurt
        }
    ),
    
    %% 删除阵亡的角色
    if
        NewLeftHurt =< 0 ->
            Army = RoleParam #role_process_param.army,
            Key = if
                Army =:= "attack" ->
                    ?ATTACK_REMAIN_ROLE;
                true ->
                    ?DEFENSE_REMAIN_ROLE
            end,
            OrderList = lists:delete(RoleId, get(?FIGHT_ORDER)),
            RemainRoleList = lists:delete(RoleId, get(Key)),
            put(?FIGHT_ORDER, OrderList),
            put(Key, RemainRoleList);
       true ->
           ok
    end.

%% 获取战胜方玩家ID
get_winner_player_data () ->

    AttackRemainRoleNumber  = length(get(?ATTACK_REMAIN_ROLE)),
    DefenseRemainRoleNumber = length(get(?DEFENSE_REMAIN_ROLE)),
    if
        DefenseRemainRoleNumber > 0 ->
            get(?DEFENSE_PLAYER_DATA);
        AttackRemainRoleNumber > 0 ->
            get(?ATTACK_PLAYER_DATA);
        true ->
            get(?DEFENSE_PLAYER_DATA)
    end.

%% 初始化角色属性
init_role_param (Army, RoleList, RoleAttributeList, PlayerData) ->

    PlayerId = PlayerData #war_player_data.player_id,
    lists:foreach(
        fun(RoleId) ->
            #player_role_war_attribute{
                health = Health
            } = get_role_attribute(
                RoleId,
                RoleAttributeList
            ),
            Param = #role_process_param{
                player_id   = PlayerId,
                army        = Army,
                left_health = Health,
                momentum    = 0,
                be_stunt    = {"", null}
            },
            set_role_param(
                RoleId,
                Param
            )
        end,
        RoleList
    ).

%% 初始化阵形
init_grid (RoleList, RoleAttributeList) ->

    GridList = lists:map(
        fun(RoleId) ->
            Attribute = get_role_attribute(
                RoleId,
                RoleAttributeList
            ),
            #role_process_param{
                army = Army
            } = get_role_param(RoleId),
            Position = list_to_integer(
                Attribute #player_role_war_attribute.position
            ),
            {RoleId, Army, Position}
        end,
        RoleList
    ),
    put(?GRID, GridList).

%% 创建攻击顺序列表
create_fight_list (
    AttackRoleList,
    DefenseRoleList,
    AttackRoleAttribute,
    DefenseRoleAttribute
) ->
    SortAttackRoleList  = sort_role_by_option(
        AttackRoleList,
        AttackRoleAttribute
    ),
    SortDefenseRoleList = sort_role_by_option(
        DefenseRoleList,
        DefenseRoleAttribute
    ),
    cross_merge_list(SortAttackRoleList, SortDefenseRoleList).

%% 获取当前出击角色
get_fight_role () ->

    FightIndex = get(?FIGHT_INDEX),
    FightList  = get(?FIGHT_ORDER),
    lists:nth(FightIndex, FightList).

%% 获取被攻击角色
get_be_fight_role (RoleId) ->

    %% 获取被攻击军队角色列表
    #role_process_param{
        army = Army
    } = get_role_param(RoleId),
    BeFightArmy = case Army of
        "attack" ->
            "defense";
        _ ->
            "attack"
    end,
    BeFightRoleList = get_remain_role(BeFightArmy),
    BeFightNumber = lists:map(
        fun(BeFightRoleId) ->
            {_RoleId, _Army, Grid} = lists:keyfind(BeFightRoleId, 1, get(?GRID)),
            Grid
        end,
        BeFightRoleList
    ),
    
    %% 攻击范围
    #player_role_war_attribute{
        attack_range = FightRange,
        position     = Position
    } = get_role_attribute(RoleId),
    
    RealBeFightNumber = get_attack_grid(
        FightRange,
        list_to_integer(Position),
        BeFightNumber
    ),
    lists:foldl(
        fun(BeFightRoleId, RealBeFightRoleList) ->
            #player_role_war_attribute{
                position     = BeFightPosition
            } = get_role_attribute(BeFightRoleId),
            IsBeFightPosition = lists:member(
                list_to_integer(BeFightPosition),
                RealBeFightNumber
            ),
            if
                IsBeFightPosition =:= true ->
                    [BeFightRoleId | RealBeFightRoleList];
                true ->
                    RealBeFightRoleList
            end
        end,
        [],
        BeFightRoleList
    ).

%% 获取攻击对象布阵值(C攻击范围)
get_attack_grid ("C", _AttackGridNumber, DefenseGridNumberList) ->

    DefenseGridColumnList =
        [
            {DefenseGridNumber div 3, DefenseGridNumber} ||
            DefenseGridNumber <- DefenseGridNumberList
        ],
    {FirstDefenseGridColumn, _FirstDefenseGridNumber} = lists:nth(
        1,
        lists:keysort(1, DefenseGridColumnList)
    ),
    [
        DefenseGridNumber ||
        {DefenseGridColumn, DefenseGridNumber} <- DefenseGridColumnList,
                                DefenseGridColumn =:= FirstDefenseGridColumn
    ];

%% 获取攻击对象布阵值(D攻击范围)
get_attack_grid ("D", _AttackGridNumber, DefenseGridNumberList) ->

    DefenseGridNumberList;

%% 获取攻击对象布阵值(A/B攻击范围)
get_attack_grid (AttackRange, AttackGridNumber, DefenseGridNumberList) ->

    AttackRow = AttackGridNumber rem 3,
    RowList = lists:foldl(
        fun(DefenseGridNumber, TempRowList) ->
            DefenseRow = DefenseGridNumber rem 3,
            [{AttackRow - DefenseRow, DefenseGridNumber} | TempRowList]
        end,
        [],
        DefenseGridNumberList
    ),
    lists:foldl(
        fun(TempDistance, TempGridNumber) ->
            if
                length(TempGridNumber) > 0 ->
                    TempGridNumber;
                true ->
                    FindList = [
                        {Distance, GridNumber} ||
                        {Distance, GridNumber} <- RowList,
                                                    Distance =:= TempDistance
                    ],
                    if
                        length(FindList) > 0 ->
                            AttackGridList = if
                                AttackRange =:= "A" ->
                                    [lists:nth(1, lists:keysort(2, FindList))];
                                true ->
                                    FindList
                            end,
                            [
                                GridNumber ||
                                {_Distance, GridNumber} <- AttackGridList
                            ];
                        true ->
                            []
                    end
            end
        end,
        [],
        [0, -1, -2, 1, 2]
    ).

%% 更新攻击索引
update_fight_index () ->

    FightIndex  = get(?FIGHT_INDEX),
    FightOrderList = get(?FIGHT_ORDER),
    Index = FightIndex rem length(FightOrderList) + 1,
    put(?FIGHT_INDEX, Index).
    
%% 根据位置排序角色列表
sort_role_by_option (RoleList, RoleAttributeList) ->

    RoleGridList = lists:map(
        fun(RoleId) ->
            Attribute = get_role_attribute(
                RoleId,
                RoleAttributeList
            ),
            {RoleId, Attribute #player_role_war_attribute.position}
        end,
        RoleList
    ),
    SortRoleGridList = lists:keysort(2, RoleGridList),
    [RoleId || {RoleId, _Grid} <- SortRoleGridList].

%% 增加气势
add_momentum (RoleId, Value) ->

    RoleParam = get_role_param(RoleId),
    set_role_param(
        RoleId,
        RoleParam #role_process_param{
            momentum = RoleParam #role_process_param.momentum + Value
        }
    ).
    
%% 获取玩家角色列表
get_player_role_list (Army) ->

    if
        Army =:= "attack" ->
            get(?ATTACK_ROLE_LIST);
        true ->
            get(?DEFENSE_ROLE_LIST)
    end.
    
%% 获取玩家数据
get_player_data (Army) ->

    if
        Army =:= "attack" ->
            get(?ATTACK_PLAYER_DATA);
        true ->
            get(?DEFENSE_PLAYER_DATA)
    end.

%% 等级差影响
add_role_level_effect (FightRoleAttribute, BeFightRoleAttribute) ->

    #player_role_war_attribute{
        role_level = FightRoleLevel
    } = FightRoleAttribute ,
    #player_role_war_attribute{
        role_level = BeFightRoleLevel
    } = BeFightRoleAttribute ,

    Step = FightRoleLevel - BeFightRoleLevel - 2,
    Attribute = if
        %% 攻击方等级高
        Step > 0 ->
            BeFightRoleAttribute;
        %% 防守方等级低
        Step < 0 ->
            FightRoleAttribute;
        true ->
            true
    end,

    if
        Step =/= 0 ->
            EffectValue = abs(Step) * 1,
            #player_role_war_attribute{
                attack        = Attack,
                defense       = Defense,
                magic_attack  = MagicAttack,
                magic_defense = MagicDefense,
                stunt_attack  = StuntAttack,
                stunt_defense = StuntDefense,
                block         = Block,
                dodge         = Dodge,
                critical      = Critical
            } = Attribute,
            NewAttribute = Attribute #player_role_war_attribute{
                attack        = max(0, lib_misc:ceil(Attack * (1 - EffectValue / 100))),
                defense       = max(0, lib_misc:ceil(Defense * (1 - EffectValue / 100))),
                magic_attack  = max(0, lib_misc:ceil(MagicAttack * (1 - EffectValue / 100))),
                magic_defense = max(0, lib_misc:ceil(MagicDefense * (1 - EffectValue / 100))),
                stunt_attack  = max(0, lib_misc:ceil(StuntAttack * (1 - EffectValue / 100))),
                stunt_defense = max(0, lib_misc:ceil(StuntDefense * (1 - EffectValue / 100))),
                block         = max(0, Block - EffectValue),
                dodge         = max(0, Dodge - EffectValue),
                critical      = max(0, Critical - EffectValue)
            },
            if
                %% 属性影响防守方
                Step > 0 ->
                    {FightRoleAttribute, NewAttribute};
                %% 属性影响攻击方
                true ->
                    {NewAttribute, BeFightRoleAttribute}
            end;
        true ->
            {FightRoleAttribute, BeFightRoleAttribute}
    end.
    
%% 获取玩家数据
get_player_data_by_role (RoleId) ->

    #role_process_param{
        army = Army
    } = get_role_param(RoleId),
    get_player_data(Army).

%% 交叉合并列表
cross_merge_list (List1, List2) ->

    MaxLength = max(length(List1), length(List2)),
    {NewList, _, _} = lists:foldl(
        fun(_Index, {TempNewList, RemainList1, RemainList2}) ->
            Length1 = length(RemainList1),
            Length2 = length(RemainList2),
            {NewList1, NewRemainList1} = if
                Length1 > 0 ->
                    Element1 = lists:nth(1, RemainList1),
                    {
                        [
                            lists:nth(1, RemainList1) |
                            TempNewList
                        ],
                        lists:delete(Element1, RemainList1)
                    };
                true ->
                    {TempNewList, []}
            end,
            {NewList2, NewRemainList2} = if
                Length2 > 0 ->
                    Element2 = lists:nth(1, RemainList2),
                    {
                        [
                            lists:nth(1, RemainList2) |
                            NewList1
                        ],
                        lists:delete(Element2, RemainList2)
                    };
                true ->
                    {NewList1, []}
            end,
            {NewList2, NewRemainList1, NewRemainList2}
        end,
        {[], List1, List2},
        lists:seq(1, MaxLength)
    ),
    lists:reverse(NewList).

%% 是否有反击
is_back_attack (FightRoleId, FightResult) ->

    [{
        _PlayerId,
        _FightRoleId,
        _FightMomentum,
        _BeFightPlayerId,
        FightType,
        FightRoleResult
    }] = FightResult,
    
    %% 攻击角色数量
    FightRoleNumber = length(FightRoleResult),

    %% 是否飞羽
    #player_role_war_attribute{
        role_sign = FightRoleSign
    } = get_role_attribute(FightRoleId),
    IsFeiYu = lists:member(FightRoleSign, ["FeiYuNan", "FeiYuNv"]),

    %% 是否被档格
    BlockFight = lists:keyfind(1, 6, FightRoleResult),
    IsBlock = BlockFight =/= false,

    if
        %% 多攻击不会被反击
        FightRoleNumber > 1 ->
            false;
        %% 飞羽不被反击
        IsFeiYu =:= true ->
            false;
        FightType =:= "NormalAttack", IsBlock =:= true ->
            %% 被攻击角色ID
            BeFightRoleId = element(1, BlockFight),
            is_role_alive(BeFightRoleId);
        true ->
            false
    end.
    
%% 获取兵种属性
get_role_attribute (RoleId, AttributeList) ->

    lists:keyfind(
        RoleId,
        #player_role_war_attribute.role_id,
        AttributeList
    ).

%% 获取角色属性
get_role_attribute (RoleId) ->

    get(?ROLE_ATTRIBUTE ++ RoleId).

%% 角色属性附加属性(等级差/战法/法术效果)
get_addon_role_attribute (FightRoleId, BeFightRoleId) ->

    %% 等级差
    {LevelFightRoleAttribute, LevelBeFightRoleAttribute} =
        add_role_level_effect(
            get_role_attribute(FightRoleId),
            get_role_attribute(BeFightRoleId)
    ),
    %% 攻击方战法效果
    Stunt = get_role_be_stunt(FightRoleId),
    mod_stunt:get_stunt_effect(
        Stunt,
        LevelFightRoleAttribute,
        LevelBeFightRoleAttribute
    ).

%% 获取角色参数
get_role_param (RoleId) ->
    
    get(?ROLE_PARAM ++ RoleId).

%% 获取剩余角色列表
get_remain_role (Army) ->

    case Army of
        "attack" ->
            get(?ATTACK_REMAIN_ROLE);
        _ ->
            get(?DEFENSE_REMAIN_ROLE)
    end.

%% 是否还未阵亡
is_role_alive (RoleId) ->

    RemainRoleList = lists:append(
        get_remain_role("attack"),
        get_remain_role("defense")
    ),
    lists:member(RoleId, RemainRoleList).
    
%% 获取角色被施加的战法
get_role_be_stunt (RoleId) ->

    (get_role_param(RoleId)) #role_process_param.be_stunt.
    
%% 设置兵种属性
set_role_attribute (RoleId, Attribute) ->

    put(?ROLE_ATTRIBUTE ++ RoleId, Attribute).

%% 设置兵种参数
set_role_param (RoleId, Param) ->

    put(?ROLE_PARAM ++ RoleId, Param).

%% 把true/false转化成1/0
bool_to_integer (BoolValue) ->

    if
        BoolValue =:= false ->
            0;
        true ->
            1
    end.
    
%% 随机数
random_number (Range) ->

    Seed = case get(seed) of
        undefined ->
            NewSeed = 10000,
            put(seed, NewSeed),
            NewSeed;
        OldSeed ->
            NewSeed = OldSeed + random:uniform(1000),
            put(seed, NewSeed),
            NewSeed
    end,
    {_H, M, S} = now(),
    random:seed(
		erlang:phash(time(), Seed),
		erlang:phash(M, max(1, S)),
		S
	),
    random:uniform(Range).