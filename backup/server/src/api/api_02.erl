-module(api_02).

-include("server.hrl").
-include("db.hrl").

-include_lib("stdlib/include/ms_transform.hrl").

-compile([export_all]).


%%--------------------------------------------------------------------
%% Descrip.: 玩家物品信息
%%--------------------------------------------------------------------
handle(01, <<PlayerItemId:32>>, State = #conn_info{
    sock = Sock
}) ->
    case mod_item:get_player_item_props(PlayerItemId) of
        false ->
            noop;

        PlayerItemProps ->
            {
                ItemId,
                Name,
                IconId,
                TypeId,
                Level,
                UpgradeLevelName,
                Usage,
                Description,
                RequireLevel,
                HealthCalc,
                AttackCalc,
                DefenseCalc,
                StuntAttackCalc,
                StuntDefenseCalc,
                MagicAttackCalc,
                MagicDefenseCalc,
                SellPrice,
                GridId
            } = PlayerItemProps,

            OutBin =  out_02:get_player_item_props(
                ItemId,
                Name,
                IconId,
                TypeId,
                Level,
                UpgradeLevelName,
                Usage,
                Description,
                RequireLevel,
                HealthCalc,
                AttackCalc,
                DefenseCalc,
                StuntAttackCalc,
                StuntDefenseCalc,
                MagicAttackCalc,
                MagicDefenseCalc,
                SellPrice,
                PlayerItemId,
                GridId
            ),

            gen_tcp:send(Sock, OutBin)
    end,
    State;


%%--------------------------------------------------------------------
%% Descrip.: 物品模板信息
%%--------------------------------------------------------------------
handle(02, <<ItemId:32, ItemLevel:16>>, State = #conn_info{
    sock = Sock
}) ->
    {
        ItemId,
        Name,
        IconId,
        TypeId,
        UpgradeLevel,
        UpgradeLevelName,
        Usage,
        Description,
        RequireLevel,
        Health,
        Attack,
        Defense,
        StuntAttack,
        StuntDefense,
        MagicAttack,
        MagicDefense,
        Price
    } = mod_item:level_item_props(ItemId, ItemLevel),

    OutBin = out_02:level_item_props(
        ItemId,
        Name,
        IconId,
        TypeId,
        UpgradeLevel,
        UpgradeLevelName,
        Usage,
        Description,
        RequireLevel,
        Health,
        Attack,
        Defense,
        StuntAttack,
        StuntDefense,
        MagicAttack,
        MagicDefense,
        Price
    ),

    gen_tcp:send(Sock, OutBin),

    State;


%%--------------------------------------------------------------------
%% Descrip.: 回购物品的属性
%%--------------------------------------------------------------------
handle(04, <<RecycleItemId:32>>, State = #conn_info{
    sock = Sock
}) ->
    case mod_item:get_recycle_item_props(RecycleItemId) of
        false ->
            noop;

        RecycleItemProps ->
            {
                ItemId,
                Name,
                IconId,
                TypeId,
                Level,
                UpgradeLevelName,
                Usage,
                Description,
                RequireLevel,
                HealthCalc,
                AttackCalc,
                DefenseCalc,
                StuntAttackCalc,
                StuntDefenseCalc,
                MagicAttackCalc,
                MagicDefenseCalc,
                Price
            } = RecycleItemProps,

            OutBin =  out_02:get_recycle_item_props(
                ItemId,
                Name,
                IconId,
                TypeId,
                Level,
                UpgradeLevelName,
                Usage,
                Description,
                RequireLevel,
                HealthCalc,
                AttackCalc,
                DefenseCalc,
                StuntAttackCalc,
                StuntDefenseCalc,
                MagicAttackCalc,
                MagicDefenseCalc,
                Price,
                RecycleItemId
            ),

            gen_tcp:send(Sock, OutBin)
    end,
    State;


%%--------------------------------------------------------------------
%% Descrip.: 获取城镇物品NPC的物品属性
%%--------------------------------------------------------------------
handle(05, <<TownNpcId:32, ItemId:32>>, State = #conn_info{
    sock = Sock
}) ->
    case (catch mod_item:check_town_npc_item(TownNpcId, ItemId)) of
        true ->
            ResultList = mod_item:shop_npc_item_props(ItemId),

            {
                ItemId,
                Name,
                IconId,
                TypeId,
                UpgradeLevel,
                UpgradeLevelName,
                Usage,
                Description,
                RequireLevel,
                Health,
                Attack,
                Defense,
                StuntAttack,
                StuntDefense,
                MagicAttack,
                MagicDefense,
                Price
            } = ResultList,

            OutBin = out_02:shop_npc_item_props(
                ItemId,
                Name,
                IconId,
                TypeId,
                UpgradeLevel,
                UpgradeLevelName,
                Usage,
                Description,
                RequireLevel,
                Health,
                Attack,
                Defense,
                StuntAttack,
                StuntDefense,
                MagicAttack,
                MagicDefense,
                Price
            ),

            gen_tcp:send(Sock, OutBin);
        _  ->
            noop
    end,

    State;


%%--------------------------------------------------------------------
%% Descrip.: 穿戴装备：指定格子物品(非空)与角色装备对换
%%--------------------------------------------------------------------
handle(13, <<GridId:16, RoleId:32, PositionId:16>>, State = #conn_info{
    sock      = Sock
}) ->
    {Result, NewPositionId} =  mod_item:player_role_equip_item(
        GridId, RoleId, PositionId
    ),

    OutBin = out_02:equip_item(Result, GridId, NewPositionId),
    
    gen_tcp:send(Sock, OutBin),
    
    State;

%%--------------------------------------------------------------------
%% Descrip.: 装备卸下：角色装备(非空),与格子物品对换
%%--------------------------------------------------------------------
handle(14, <<RoleId:32, PositionId:16, GridId:16 >>, State = #conn_info{
    sock      = Sock
}) ->
    {Result, NewGridId} = mod_item:player_role_remove_item(
        RoleId, PositionId, GridId
    ),

    OutBin = out_02:remove_item(Result, PositionId, NewGridId),
    
    gen_tcp:send(Sock, OutBin),
    
    State;

%%--------------------------------------------------------------------
%% Descrip.: 使用格子中的物品
%%--------------------------------------------------------------------
handle(15, <<GridId:16>>, State = #conn_info{
    sock = Sock
}) ->
    Result = mod_item:use_grid_item(GridId),

    OutBin = out_02:use_grid_item(Result, GridId),
    
    gen_tcp:send(Sock, OutBin),
    
    State;

%%--------------------------------------------------------------------
%% Descrip.: 清空格子中的物品
%%--------------------------------------------------------------------
handle(16, <<GridId:16>>, State = #conn_info{
    sock = Sock
}) ->
    Result = mod_item:empty_grid(GridId),

    OutBin = out_02:empty_grid(Result, GridId),
    
    gen_tcp:send(Sock, OutBin),
    
    State;

%%--------------------------------------------------------------------
%% Descrip.: 获取玩家背包格子数据
%%--------------------------------------------------------------------
handle(20, <<GridId1:16, GridId2:16>>, State = #conn_info{
    sock = Sock
}) ->
    {GridKey, ResultList} = mod_item:get_pack_grids(
        GridId1, GridId2
    ),

    OutBin = out_02:get_pack_grids(GridKey, ResultList),
    
    gen_tcp:send(Sock, OutBin),
    
    State;

%%--------------------------------------------------------------------
%% Descrip.: 背包增加格子
%%--------------------------------------------------------------------
handle(21, _, State = #conn_info{
    sock = Sock
}) ->
    {Result, Grid} = mod_item:pack_add_grid(),

    OutBin = out_02:pack_add_grid(Result, Grid),
    
    gen_tcp:send(Sock, OutBin),
    
    State;

%%--------------------------------------------------------------------
%% Descrip.: 背包整理
%%--------------------------------------------------------------------
handle(22, _, State = #conn_info{
    sock = Sock 
}) ->
    Result = mod_item:pack_classify(),

    OutBin = out_02:pack_classify(Result),
    
    gen_tcp:send(Sock, OutBin),
    
    State;

%%--------------------------------------------------------------------
%% Descrip.: 物品移动
%%--------------------------------------------------------------------
handle(23, <<FromGridId:16, ToGridId:16>>, State = #conn_info{
    sock = Sock 
}) ->
    Result = mod_item:pack_grid_item_move(
        FromGridId, ToGridId
    ),
    
    OutBin = out_02:pack_grid_item_move(Result, FromGridId, ToGridId),

    gen_tcp:send(Sock, OutBin),
    
    State;

%%--------------------------------------------------------------------
%% Descrip.: 获取格子增加的条件
%%--------------------------------------------------------------------
handle(24, _, State = #conn_info{
    sock = Sock
}) ->
    {GridId, Ingot}= mod_item:pack_grid_add_require_props(),

    OutBin = out_02:grid_add_require_props(GridId, Ingot),
    
    gen_tcp:send(Sock, OutBin),
    
    State;

%%--------------------------------------------------------------------
%% Descrip.: 获取城镇物品NPC列表
%%--------------------------------------------------------------------
handle(30, _, State = #conn_info{
    town_id = TownId, 
    sock    = Sock
}) ->
    ResultList = mod_item:get_shop_npc_list(TownId),

    OutBin = out_02:shop_npc_list(ResultList),
    
    gen_tcp:send(Sock, OutBin),
    
    State;

%%--------------------------------------------------------------------
%% Descrip.: 获取城镇物品NPC的物品列表
%%--------------------------------------------------------------------
handle(31, <<TownNpcId:32>>, State = #conn_info{
    sock    = Sock
}) ->
    ResultList = mod_item:get_town_npc_items(TownNpcId),

    OutBin = out_02:shop_npc_items(ResultList),

    gen_tcp:send(Sock, OutBin),
    
    State;

%%--------------------------------------------------------------------
%% Descrip.: 获取回购物品列表
%%--------------------------------------------------------------------
handle(32, _, State = #conn_info{
    sock    = Sock
}) ->
    ResultList = mod_item:npc_recycle_items(),

    OutBin = out_02:npc_recycle_items(ResultList),

    gen_tcp:send(Sock, OutBin),

    State;


%%--------------------------------------------------------------------
%% Descrip.: 购买NPC物品
%%--------------------------------------------------------------------
handle(34, <<TownNpcId:32, ItemId:32, GridId:16>>, State = #conn_info{
    town_id   = TownId, 
    sock      = Sock
}) ->
    {Result, NewGridId} = mod_item:buy_npc_item(
        TownId, TownNpcId, ItemId, GridId
    ),

    OutBin = out_02:buy_npc_item(Result, NewGridId),
    
    gen_tcp:send(Sock, OutBin),
    
    State;

%%--------------------------------------------------------------------
%% Descrip.: 卖出格子物品
%%--------------------------------------------------------------------
handle(35, <<GridId:16>>, State = #conn_info{
    sock      = Sock
}) ->
    Result = mod_item:sell_item(GridId),

    OutBin = out_02:sell_item(Result, GridId),

    gen_tcp:send(Sock, OutBin),

    State;

%%--------------------------------------------------------------------
%% Descrip.: 回购物品
%%--------------------------------------------------------------------
handle(36, <<RecycleId:32, GridId:16>>, State = #conn_info{
    sock      = Sock
}) ->
    {Result, NewGridId} = mod_item:buy_back_item(RecycleId, GridId),

    OutBin = out_02:buy_back_item(Result, NewGridId),

    gen_tcp:send(Sock, OutBin),

    State;


%%--------------------------------------------------------------------
%% Descrip.: 获取玩家角色装备列表
%%--------------------------------------------------------------------
handle(40, <<PlayerRoleId:32, PositionId:16>>, State = #conn_info{
    sock = Sock
}) ->
    ResultList = mod_item:get_role_equit_list(PlayerRoleId, PositionId),

    OutBin = out_02:role_equit_list(ResultList),

    gen_tcp:send(Sock, OutBin),
    
    State;


%%--------------------------------------------------------------------
%% Descrip.: 获取玩家背包格子数据
%%--------------------------------------------------------------------
handle(50, <<GridId1:16, GridId2:16>>, State = #conn_info{
    sock = Sock
}) ->
    {GridKey, ResultList} = mod_item:get_warehouse_grids(
        GridId1, GridId2
    ),

    OutBin = out_02:get_warehouse_grids(GridKey, ResultList),

    gen_tcp:send(Sock, OutBin),

    State;

%%--------------------------------------------------------------------
%% Descrip.: 背包增加格子
%%--------------------------------------------------------------------
handle(51, _, State = #conn_info{
    sock = Sock
}) ->
    {Result, Grid} = mod_item:warehouse_add_grid(),

    OutBin = out_02:warehouse_add_grid(Result, Grid),

    gen_tcp:send(Sock, OutBin),

    State;

%%--------------------------------------------------------------------
%% Descrip.: 获取格子增加的条件
%%--------------------------------------------------------------------
handle(52, _, State = #conn_info{
    sock = Sock
}) ->
    {GridId, Ingot}= mod_item:pack_grid_add_require_props(),

    OutBin = out_02:grid_add_require_props(GridId, Ingot),

    gen_tcp:send(Sock, OutBin),

    State;

%%--------------------------------------------------------------------
%% Descrip.: 丢弃仓库格子物品
%%--------------------------------------------------------------------
handle(54, <<GridId:16>>, State = #conn_info{
    sock = Sock
}) ->
    Result = mod_item:warehouse_empty_grid(GridId),

    OutBin = out_02:warehouse_empty_grid(Result, GridId),

    gen_tcp:send(Sock, OutBin),

    State;

%%--------------------------------------------------------------------
%% Descrip.: 背包整理
%%--------------------------------------------------------------------
handle(53, _, State = #conn_info{
    sock = Sock
}) ->
    Result = mod_item:warehouse_classify(),

    OutBin = out_02:warehouse_classify(Result),

    gen_tcp:send(Sock, OutBin),

    State;

%%--------------------------------------------------------------------
%% Descrip.: 仓库物品移动到背包
%%--------------------------------------------------------------------
handle(55, <<FromGridId:16, ToGridId:16>>, State = #conn_info{
    sock = Sock
}) ->
    {Result, NewGridId} = mod_item:warehouse_grid_moveto_pack(
        FromGridId, ToGridId
    ),

    OutBin = out_02:warehouse_grid_moveto_pack(Result, FromGridId, NewGridId),

    gen_tcp:send(Sock, OutBin),

    State;


%%--------------------------------------------------------------------
%% Descrip.: 背包物品移动到仓库
%%--------------------------------------------------------------------
handle(56, <<FromGridId:16, ToGridId:16>>, State = #conn_info{
    sock = Sock
}) ->
    {Result, NewGridId} = mod_item:pack_grid_moveto_warehouse(
        FromGridId, ToGridId
    ),

    OutBin = out_02:pack_grid_moveto_warehouse(Result, FromGridId, NewGridId),

    gen_tcp:send(Sock, OutBin),

    State;


%%--------------------------------------------------------------------
%% Descrip.: 仓库物品移动
%%--------------------------------------------------------------------
handle(57, <<FromGridId:16, ToGridId:16>>, State = #conn_info{
    sock = Sock
}) ->
    Result = mod_item:warehouse_grid_item_move(
        FromGridId, ToGridId
    ),

    OutBin = out_02:warehouse_grid_item_move(Result, FromGridId, ToGridId),

    gen_tcp:send(Sock, OutBin),

    State;


%%--------------------------------------------------------------------
%% Descrip.: 获取装备列表
%%--------------------------------------------------------------------
handle(60, <<EquipType:16, Equipped:8, EquipRoleId:32, Page:8>>, State = #conn_info{
    sock = Sock
}) ->
    {PageCurrent, PageTotal, List} = mod_item:equipment_list(
        EquipType, Equipped, EquipRoleId, Page
    ),

    OutBin = out_02:equipment_list(PageCurrent, PageTotal, List),

    gen_tcp:send(Sock, OutBin),

    State;


%%--------------------------------------------------------------------
%% Descrip.: 获取装备强化成功率
%%--------------------------------------------------------------------
handle(61, _, State = #conn_info{
    sock = Sock
}) ->
    {Probability, Direction} = mod_item:equip_upgrade_probability(),

    OutBin = out_02:equip_upgrade_probability(Probability, Direction),

    gen_tcp:send(Sock, OutBin),

    State;


%%--------------------------------------------------------------------
%% Descrip.: 获取装备强化冷却时间
%%--------------------------------------------------------------------
handle(62, _, State = #conn_info{
    sock = Sock
}) ->
    {RemainSeconds, Ingot} = mod_item:equip_upgrade_stat(),

    OutBin = out_02:equip_upgrade_stat(RemainSeconds, Ingot),

    gen_tcp:send(Sock, OutBin),

    State;


%%--------------------------------------------------------------------
%% Descrip.: 装备强化
%%--------------------------------------------------------------------
handle(63, <<PlayerItemId:32, Probability:8>>, State = #conn_info{
    sock = Sock
}) ->
    {Result, LevelName, UpgradePrice} = mod_item:upgrade_equipment(
        PlayerItemId, Probability
    ),

    OutBin = out_02:upgrade_equipment(PlayerItemId, LevelName, UpgradePrice, Result),

    gen_tcp:send(Sock, OutBin),

    State;


%%--------------------------------------------------------------------
%% Descrip.: 装备强化冷却时间清零
%%--------------------------------------------------------------------
handle(64, _, State = #conn_info{
    sock = Sock
}) ->
    Result = mod_item:clear_upgrade_cd_time(),

    OutBin = out_02:clear_upgrade_cd_time(Result),

    gen_tcp:send(Sock, OutBin),

    State;


%%--------------------------------------------------------------------
%% Descrip.: 背包物品移动到仓库
%%--------------------------------------------------------------------
handle(Func, Data, State = #conn_info{
}) ->
    io:format("mod_item: ~p~n~p~n", [Func, Data]),
    State.