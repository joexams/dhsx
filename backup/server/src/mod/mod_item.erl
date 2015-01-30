-module(mod_item).

-include("server.hrl").
-include("db.hrl").
-include("ets_logic.hrl").
-include("ets_player.hrl").
-include("api_00.hrl").
-include("api_02.hrl").

-include_lib("stdlib/include/ms_transform.hrl").

-compile(export_all).

-export([
    get_player_item_props/1,        % 01,02,03
    get_recycle_item_props/1,       % 04
    level_item_props/2,             % 07

    player_role_equip_item/3,       % 13
    player_role_remove_item/3,      % 14
    use_grid_item/1,                % 15
    empty_grid/1,                   % 16

    get_pack_grids/2,               % 20
    pack_add_grid/0,                % 21
    pack_classify/0,                % 22
    pack_grid_item_move/2,          % 23
    pack_grid_add_require_props/0,  % 24

    get_shop_npc_list/1,            % 30
    get_town_npc_items/1,           % 31
    npc_recycle_items/0,            % 32
    shop_npc_item_props/1,          % 33
    buy_npc_item/4,                 % 34
    sell_item/1,                    %35
    buy_back_item/2,                %36

    get_role_equit_list/2,          % 40

    get_warehouse_grids/2,          % 50
    warehouse_add_grid/0,           % 51
    warehouse_grid_add_require_props/0, % 52
    warehouse_classify/0,           % 53
    warehouse_empty_grid/1,           % 54
    warehouse_grid_moveto_pack/2,   % 55
    pack_grid_moveto_warehouse/2,   % 56

    equipment_list/4,                   % 60
    equip_upgrade_probability/0,        % 61
    equip_upgrade_stat/0,               % 62
    upgrade_equipment/2,                % 63
    clear_upgrade_cd_time/0,            % 64

    get_upgrade_probability/0,

    % 供其它模块调用的接口
    get_item_name/1,
    get_item_icon/1,
    get_item_type/1,
    get_item_quality/1,
    get_item_number/1,
    award_item_list/1,
    get_role_equip_affect_values/1
]).


%%----------------------------------------
%%  functions export for api_02
%%----------------------------------------

% 01,02,03    玩家物品属性
get_player_item_props(PlayerItemId) ->
    % 玩家物品
    PlayerItem = get_player_item(PlayerItemId),
    
    case PlayerItem of
        false ->
            false;
        _ ->
            GridId          = PlayerItem #player_item.grid_id,
            ItemId          = PlayerItem #player_item.item_id,
            Level           = PlayerItem #player_item.upgrade_level,

            % 物品模板
            Item            = db:get(item, ItemId),
            
            Name            = Item #item.name,
            TypeId          = Item #item.type_id,
            IconId          = Item #item.icon_id,
            Usage           = Item #item.usage,
            Description     = Item #item.description,
            RequireLevel    = Item #item.require_level,

            #role_equip_affect_values{
                attack        = AttackCalc,
                defense       = DefenseCalc,
                stunt_attack  = StuntAttackCalc,
                stunt_defense = StuntDefenseCalc,
                magic_attack  = MagicAttackCalc,
                magic_defense = MagicDefenseCalc,
                max_health    = HealthCalc
            } = get_player_equip_affect_values(PlayerItem),

            % 物品等级
            ItemUpgrade     = db:get(item_upgrade, Level),
            
            UpgradeLevelName= ItemUpgrade #item_upgrade.name,
            
            SellPrice       = calc_item_sell_price(ItemId, Level),
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
            }
    end.


% 04 玩家回购物品属性
get_recycle_item_props(RecycleItemId) ->
    RecycleItem = get_recycle_item(RecycleItemId),
    
    case RecycleItem of
        false ->
            false;
        _ ->
            ItemId          = RecycleItem #recycle_player_item.item_id,
            Level           = RecycleItem #recycle_player_item.upgrade_level,

            % 物品模板
            Item            = db:get(item, ItemId),
            
            Name            = Item #item.name,
            TypeId          = Item #item.type_id,
            IconId          = Item #item.icon_id,
            Usage           = Item #item.usage,
            Description     = Item #item.description,
            RequireLevel    = Item #item.require_level,

            % 物品等级
            ItemUpgrade     = db:get(item_upgrade, Level),

            UpgradeLevelName= ItemUpgrade #item_upgrade.name,

            #role_equip_affect_values{
                attack        = AttackCalc,
                defense       = DefenseCalc,
                stunt_attack  = StuntAttackCalc,
                stunt_defense = StuntDefenseCalc,
                magic_attack  = MagicAttackCalc,
                magic_defense = MagicDefenseCalc,
                max_health    = HealthCalc
            } = get_item_level_affect_values(ItemId, Level),

            SellPrice       = calc_item_sell_price(ItemId, Level),

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
                SellPrice
            }
    end.


% 07
level_item_props(ItemId, Level) ->
    Item = db:get(item, ItemId),
    case Item of
        false ->
            false;
        _ ->
            % 物品模板
            Item            = db:get(item, ItemId),

            Name            = Item #item.name,
            TypeId          = Item #item.type_id,
            IconId          = Item #item.icon_id,
            Usage           = Item #item.usage,
            Description     = Item #item.description,
            RequireLevel    = Item #item.require_level,

            % 物品等级
            ItemUpgrade     = db:get(item_upgrade, Level),
            UpgradeLevelName= ItemUpgrade #item_upgrade.name,

            #role_equip_affect_values{
                attack        = AttackCalc,
                defense       = DefenseCalc,
                stunt_attack  = StuntAttackCalc,
                stunt_defense = StuntDefenseCalc,
                magic_attack  = MagicAttackCalc,
                magic_defense = MagicDefenseCalc,
                max_health    = HealthCalc
            } = get_item_level_affect_values(ItemId, Level),

            SellPrice       = calc_item_buy_price(ItemId, Level),

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
                SellPrice
            }
    end.



% 13    玩家角色装备物品
player_role_equip_item(GridId, RoleId, PositionId) ->
    try player_role_equip_item_real(GridId, RoleId, PositionId) of
        NewPositionId ->
            mod_player:update_player_data(?PLAYER_MAX_HEALTH),
            {?ACTION_SUCCESS, NewPositionId}
    catch
        throw : player_grid_no_exist ->
            {?PLAYER_GRID_NO_EXIST, 0};
        throw : equip_position_err ->
            {?EQUIP_POSITION_ERR, 0};
        throw : player_role_not_exist ->
            {?PLAYER_ROLE_NOT_EXIST, 0};
        throw : source_grid_empty ->
            {?SOURCE_GRID_EMPTY, 0};
        throw : no_suite_role_job ->
            {?NO_SUITE_ROLE_JOB, 0};
        throw : no_suite_equip_type ->
            {?NO_SUITE_EQUIP_TYPE, 0};
        _ : _ ->
            {?ERROR_UNDEFINED, 0}
    end.


player_role_equip_item_real(GridId, PlayerRoleId, PositionId) ->
    % 检查格子编号
    case check_player_grid_id(GridId) of
        false ->
            throw(player_grid_no_exist);
        true ->
            noop
    end,

    % 检查装备位置编号
    if
        PositionId =/= 0 ->
            case check_role_position_id(PositionId) of
                false ->
                    throw(equip_position_err);
                true ->
                    noop
            end;
        true ->
            noop
    end,

    % 检查指定的源格子
    GridItem = get_player_grid(GridId),

    case GridItem of
        false ->
            throw(source_grid_empty);
        _ ->
            noop
    end,

    % 检查角色
    NewPlayerRoleId = case PlayerRoleId of
        0 ->
            (mod_player:get_player_role()) #player_role.id;
        _ ->
            PlayerRoleId
    end,

    PlayerId = mod_player:get_player_id(),

    RoleJobId = mod_role:get_player_role_job(PlayerId, NewPlayerRoleId),

    case RoleJobId of
        false ->
            throw(player_role_not_exist);
        _ ->
            noop
    end,

    try db:get(item_equip_job, {GridItem#player_item.item_id, RoleJobId}) of
        _ ->
            noop
    catch
        _ : _ ->
            throw(no_suite_role_job)
    end,

    NewPositionId  = player_item_equip_position(GridItem, PositionId),
    
    if
        NewPositionId == false ->
            throw(no_suite_equip_type);
        true ->
            noop
    end,


    case get_player_role_equi(NewPlayerRoleId, NewPositionId) of
        false ->
            PlayeritemId = GridItem #player_item.id,
            update_role_equip_item(PlayeritemId, NewPlayerRoleId, NewPositionId);
        EquipItem ->
            player_grid_swap(GridItem, EquipItem)
    end,

    NewPositionId.


% 14
player_role_remove_item(RoleId, PositionId, GridId) ->
    try player_role_remove_item_real(RoleId, PositionId, GridId) of
        NewGridId->
            mod_player:update_player_data(?PLAYER_MAX_HEALTH),
            mod_player:update_player_data(?PLAYER_HEALTH),
            {?ACTION_SUCCESS, NewGridId}
    catch
        throw : player_grid_no_exist ->
            {?PLAYER_GRID_NO_EXIST, 0};
        throw : player_role_not_exist ->
            {?PLAYER_ROLE_NOT_EXIST, 0};
        throw : equip_position_err ->
            {?EQUIP_POSITION_ERR, 0};
        throw : role_pos_equi_empty ->
            {?ROLE_POS_EQUI_EMPTY, 0};
        throw : no_suite_role_job ->
            {?NO_SUITE_ROLE_JOB, 0};
        throw : un_avaliable_grid ->
            {?UN_AVALIABLE_GRID, 0};
        throw : no_suite_equip_type ->
            {?NO_SUITE_EQUIP_TYPE, 0};
        _ : _ -> 
            {?ERROR_UNDEFINED, 0}
    end.


player_role_remove_item_real(RoleId, PositionId, GridId) ->
    % 检查格子编号
    if
        GridId =/= 0 ->
            case check_player_grid_id(GridId) of
                false ->
                    throw(player_grid_no_exist);
                true ->
                    noop
            end;
        true ->
            noop
    end,

    % 检查装备位置编号
    case check_role_position_id(PositionId) of
        false ->
            throw(equip_position_err);
        true ->
            noop
    end,

    % 检查角色
    PlayerId = mod_player:get_player_id(),

    RoleJobId = mod_role:get_player_role_job(PlayerId, RoleId),

    case RoleJobId of
        false ->
            throw(player_role_not_exist);
        _ ->
            noop
    end,
%    Role = get_player_role(RoleId),
%    if
%        Role == false ->
%            throw(player_role_not_exist);
%        true ->
%            noop
%    end,

    % 检查角色装备
    EquipItem = get_player_role_equi(RoleId, PositionId),

    if
        EquipItem == false ->
            throw(role_pos_equi_empty);
        true  ->
            noop
    end,
    
    % 脱下位置
    NewGrid = player_item_avaliable_grid(pack, EquipItem, GridId),
    if
        NewGrid == false ->
            throw(un_avaliable_grid);
        true ->
            noop
    end,

    NewGridId = case NewGrid of
        {new, ToGridId}->
            % 卸下到空格子
            PlayerItemId = EquipItem #player_item.id,
            update_player_item_pack(PlayerItemId, ToGridId),
            ToGridId;
        NewGrid ->
            % 交换时有要换上的装备

            % 换下位置的物品能否穿上
            try db:get(item_equip_job, {NewGrid#player_item.item_id, RoleJobId}) of
                _ ->
                    noop
            catch
                _ : _ ->
                    throw(no_suite_role_job)
            end,


            case player_item_equip_position(NewGrid, PositionId) of
                false ->
                    throw(no_suite_equip_type);
                _ ->
                    player_grid_swap(NewGrid, EquipItem),
                    NewGrid #player_item.id
            end
    end,

    NewGridId.


% 15
use_grid_item(GridId) ->
    try use_grid_item_real(GridId) of
        _ ->
            {?ACTION_SUCCESS, GridId}
    catch
        throw : player_grid_no_exist ->
            {?PLAYER_GRID_NO_EXIST, 0};
        throw : source_grid_empty ->
            {?SOURCE_GRID_EMPTY, 0};
        throw : unusable_item ->
            {?UNUSABLE_ITEM, 0};
        _ : _ ->
            {?ERROR_UNDEFINED, 0}
    end.


use_grid_item_real(GridId) ->
    case check_player_grid_id(GridId) of
        false ->
            throw(player_grid_no_exist);
        true ->
            noop
    end,

    GridItem = get_player_grid(GridId),
    case GridItem of
        false ->
            throw(source_grid_empty);
        true ->
            noop
    end,

    MedicalNumber = case GridItem #player_item.item_id of
        ?SMALL_MEDICAL_ITEM_ID ->
            ?SMALL_MEDICAL_VALUE;
        _ ->
            throw(unusable_item)
    end,

    % 修改玩家属性
    % 玩家增加预备血量，自动补充
    mod_player:set_medical(MedicalNumber),

    Number = get_player_item_number(GridItem),

    case Number of
        1 ->
            delete_player_item(GridItem);
        _BigNumber ->
            player_item_decr_number(GridItem)
    end.


% 16
empty_grid(GridId) ->
    case get_player_grid(GridId) of
         false ->
             ?PLAYER_GRID_NO_EXIST;
         Grid when Grid#player_item.item_id == null ->
             ?SOURCE_GRID_EMPTY;
         _Grid ->
             delete_grid_item(GridId),
             ?ACTION_SUCCESS
     end.


% 54
warehouse_empty_grid(GridId) ->
    case get_player_grid(GridId) of
         false ->
             ?PLAYER_GRID_NO_EXIST;
         Grid when Grid#player_item.item_id == null ->
             ?SOURCE_GRID_EMPTY;
         _Grid ->
             delete_grid_item(GridId),
             ?ACTION_SUCCESS
     end.

% 20
% 获取玩家背包列表，如果未指定GridId1和GridId2,则返回全部Grid列表
get_pack_grids(GridId1, GridId2) ->
    {PackKey, _, _} = mod_player:get_player_pack_keys(),
    GridIds = if
         GridId1 == 0 andalso GridId2 == 0 ->
             get_player_pack_grid_ids();
         true ->
             [GridId1, GridId2]
    end,

    {
        PackKey,
        [get_player_grid_props(GridId) || GridId <- GridIds]
    }.


% 50
% 获取玩家背包列表，如果未指定GridId1和GridId2,则返回全部Grid列表
get_warehouse_grids(GridId1, GridId2) ->
    {_, _, WareHouseKey} = mod_player:get_player_pack_keys(),
    GridIds = if
         GridId1 == 0 andalso GridId2 == 0 ->
             get_player_warehouse_grid_ids();
         true ->
             [GridId1, GridId2]
    end,

    {
        WareHouseKey,
        [get_player_grid_props(GridId) || GridId <- GridIds]
    }.


%%--------------------------------------------------------------------
%% Function: get_player_grid(GridId)
%%           Grid   = integer()    玩家格子编号
%% Descrip.: 获取玩家格子的祥细信息
%% Returns : tuple() 参见协议文本对应02_item.txt:20
%%--------------------------------------------------------------------
get_player_grid_props(GridId) ->
    Grid = get_player_grid(GridId),

    case Grid of
        false ->
            {
                GridId,
                0,  % PlayerItemId,
                0,  % ItemTypeId,
                0,  % ItemIconId,
                0,  % PlayerItemNumber,
                0,  % PlayerItemLevel
                0   % ItemId
            };

        _ ->
            PlayerItemId    = Grid #player_item.id,

            ItemId      = Grid #player_item.item_id,
            ItemNumber  = get_player_item_number(Grid),
            ItemLevel   = Grid #player_item.upgrade_level,

            Item        = db:get(item, ItemId),
            ItemTypeId  = Item #item.type_id,
            ItemIconId  = Item #item.icon_id,

            {
                GridId,
                PlayerItemId,
                ItemTypeId,
                ItemIconId,
                ItemNumber,
                ItemLevel,
                ItemId
            }
    end.


% 21
pack_add_grid() ->
    try pack_add_grid_real() of
        GridId ->
            {0, GridId}
    catch
        throw : pack_full ->
            {?PACK_FULL, 0};
        throw : insufficient_ingot ->
            {?INSUFFICIENT_INGOT, 0};
        _ : _ ->
            {?ERROR_UNDEFINED, 0}
    end.


pack_add_grid_real() ->
    {GridId, NeedIngots} = pack_grid_add_require_props(),

    if 
        GridId == false ->
            throw(pack_full);
        true ->
            nop
    end,

    case mod_player:check_ingot(NeedIngots) of
        false ->
            throw(insufficient_ingot);
        _ ->
            noop
    end,

    % TRANS START
    mod_player:decrease_ingot(NeedIngots),

    mod_player:add_pack_get_player_infogrid_key(),
    % TRANS END

    GridId.


% 51
warehouse_add_grid() ->
    try warehouse_add_grid_real() of
        GridId ->
            {0, GridId}
    catch
        throw : warehouse_full ->
            {?WAREHOUSE_FULL, 0};
        throw : insufficient_ingot ->
            {?INSUFFICIENT_INGOT, 0};
        _ : _ ->
            {?ERROR_UNDEFINED, 0}
    end.


warehouse_add_grid_real() ->
    {GridId, NeedIngots} = warehouse_grid_add_require_props(),

    if
        GridId == false ->
            throw(warehouse_full);
        true ->
            nop
    end,

    case mod_player:check_ingot(NeedIngots) of
        false ->
            throw(insufficient_ingot);
        _ ->
            noop
    end,

    % TRANS START
    mod_player:decrease_ingot(NeedIngots),

    mod_player:add_warehouse_grid_key(),
    % TRANS END

    GridId.


% 22
pack_classify() ->
    GridIds = get_player_pack_grid_ids(),
    [
        grid_classify(GridIds, ToPosition)
        ||
        ToPosition <- lists:seq(1, length(GridIds))
    ] ,
    ?ACTION_SUCCESS.


% 52
warehouse_classify() ->
    GridIds = get_player_warehouse_grid_ids(),
    [
        grid_classify(GridIds, ToPosition)
        ||
        ToPosition <- lists:seq(1, length(GridIds))
    ] ,
    ?ACTION_SUCCESS.


grid_classify(GridIds, ToPosition) ->
    [
        stack_grid_remain(GridIds, ToPosition, FromPosition)
        ||
        FromPosition <- lists:seq(ToPosition +1 , length(GridIds))
    ].


stack_grid_remain(GridIds, ToPosition, FromPosition) ->
    PreGrid = case ToPosition of
        1 ->
            head;
        _ ->
            PreGridId = lists:nth(ToPosition - 1, GridIds),
            get_player_grid(PreGridId)
    end,

    ToGridId    = lists:nth(ToPosition,GridIds),
    ToGrid      = get_player_grid(ToGridId),

    FromGridId  = lists:nth(FromPosition,GridIds),
    FromGrid    = get_player_grid(FromGridId),
    
     case {PreGrid, FromGrid, ToGrid} of
        {_, false, _} ->
            % 源格子为空
            noop;
        {_, _, false} ->
            % 目标格子为空,直接交换
            update_player_item_pack(FromGrid #player_item.id, ToGridId);
        {PreGrid, FromGrid, ToGrid} ->
            classify_grid(PreGrid, FromGrid, ToGrid)
    end.


classify_grid(PreGrid, FromGrid, ToGrid) ->
    FromItemId  = FromGrid #player_item.item_id,
    FromLevel   = FromGrid #player_item.upgrade_level,

    ToItemId    = ToGrid #player_item.item_id,
    ToLevel     = ToGrid #player_item.upgrade_level,

    SameItem    = FromItemId == ToItemId,
    SameLevel   = FromLevel == ToLevel,

    case {SameItem, SameLevel}of
        {true, true} ->
            player_grid_stack_same(FromGrid, ToGrid);
        _ ->
            case stack_grid_check_swap(PreGrid, FromGrid, ToGrid) of
                true ->
                    player_grid_swap(FromGrid, ToGrid);
                _ ->
                    noop
            end
    end.


stack_grid_check_swap(head, FromGrid, ToGrid) ->
    FromItemId  = FromGrid #player_item.item_id,
    ToItemId    = ToGrid #player_item.item_id,
    if
        FromItemId == ToItemId ->
            % 2个格子物品相同，quality高排前, level高排前
            FromQuality = get_item_quality(FromGrid #player_item.item_id),
            ToQuality   = get_item_quality(ToGrid #player_item.item_id),

            FromLevel   = FromGrid #player_item.upgrade_level,
            ToLevel     = ToGrid #player_item.upgrade_level,

            if
                FromQuality > ToQuality ->
                    true;
                FromQuality == ToQuality , FromLevel > ToLevel ->
                    true;
                true ->
                    false
            end;
        true ->
            % 2个格子物品不同，type_id小排前，item_id小排前
            FromType    = get_item_type(FromGrid #player_item.item_id),
            ToType      = get_item_type(ToGrid #player_item.item_id),

            if
                FromType < ToType ->
                    true;
                FromType == ToType , FromItemId < ToItemId ->
                    true;
                true ->
                    false
            end
    end;


stack_grid_check_swap(PreGrid, FromGrid, ToGrid) ->
    PreItemId   = PreGrid #player_item.item_id,
    FromItemId  = FromGrid #player_item.item_id,
    ToItemId    = ToGrid #player_item.item_id,

    case {PreItemId, FromItemId, ToItemId} of
        {PreItemId, PreItemId, PreItemId} ->
            % 3格物品相同，进行后2格判断
            stack_grid_check_swap(head, FromGrid, ToGrid);
        {PreItemId, PreItemId, _} ->
            % 中间物品不同，直接进行交换
            true;
        {PreItemId, FromItemId, ToItemId} ->
            % 3格物品不同，进行后2格判断
            stack_grid_check_swap(head, FromGrid, ToGrid)
    end.


% 23
pack_grid_item_move(FromGridId, ToGridId) ->
    OpenGridIds     = get_player_pack_grid_ids(),
    try pack_grid_item_move_real(OpenGridIds, FromGridId, ToGridId) of
        _ ->
            ?ACTION_SUCCESS
    catch
        throw : grid_not_exist ->
            ?PLAYER_GRID_NO_EXIST;
        throw : source_grid_empty ->
            ?SOURCE_GRID_EMPTY;
        _ : _ ->
            ?ERROR_UNDEFINED
    end.


warehouse_grid_item_move(FromGridId, ToGridId) ->
    OpenGridIds     = get_player_warehouse_grid_ids(),
    try pack_grid_item_move_real(OpenGridIds, FromGridId, ToGridId) of
        _ ->
            ?ACTION_SUCCESS
    catch
        throw : grid_not_exist ->
            ?PLAYER_GRID_NO_EXIST;
        throw : source_grid_empty ->
            ?SOURCE_GRID_EMPTY;
        _ : _ ->
            ?ERROR_UNDEFINED
    end.


pack_grid_item_move_real(OpenGridIds, FromGridId, ToGridId) ->

    case lists:member(FromGridId, OpenGridIds) of
        false ->
            throw(grid_not_exist);
        true ->
            noop
    end,
    case lists:member(ToGridId, OpenGridIds) of
        false ->
            throw(grid_not_exist);
        true ->
            noop
    end,

    FromGrid = get_player_grid(FromGridId),
    if
        FromGrid == false ->
            throw(source_grid_empty );
        true ->
            noop
    end,

    ToGrid = get_player_grid(ToGridId),

    case {FromGrid, ToGrid} of
        {_, false} ->
            % 目标格子为空,直接交换
            update_player_item_pack(FromGrid #player_item.id, ToGridId);
        _ ->
            FromItemId  = FromGrid #player_item.item_id,
            ToItemId    = ToGrid #player_item.item_id,
            if
                FromItemId =/= ToItemId ->
                    % 不同物品直接交换
                    player_grid_swap(FromGrid, ToGrid);
                true ->
                    % 2个格子物品相同，等级相同则堆叠
                    FromLevel   = FromGrid #player_item.upgrade_level,
                    ToLevel     = ToGrid #player_item.upgrade_level,
                    if
                        FromLevel =/= ToLevel ->
                            player_grid_swap(FromGrid, ToGrid);
                        true ->
                            player_grid_stack_same(FromGrid, ToGrid)
                    end
            end
    end.


% 24
pack_grid_add_require_props() ->
    {PackKey, _, _} = mod_player:get_player_pack_keys(),

    AvaliablePackGrid = get_pack_grid_list(),

    GridList = [
        PackGrid || PackGrid <- AvaliablePackGrid,
        PackGrid #item_pack_grid.unlock_level > PackKey
    ],
    case GridList of
        [Grid | _] ->
            {Grid #item_pack_grid.id, Grid #item_pack_grid.ingot};
        [] ->
            {0, 0}
    end.


% 54
warehouse_grid_add_require_props() ->
    {_, _, WareHouseKey} = mod_player:get_player_pack_keys(),

    AvaliablePackGrid = get_warehouse_grid_list(),

    GridList = [
        PackGrid || PackGrid <- AvaliablePackGrid,
        PackGrid #item_pack_grid.unlock_level > WareHouseKey
    ],
    case GridList of
        [Grid | _] ->
            {Grid #item_pack_grid.id, Grid #item_pack_grid.ingot};
        [] ->
            {0, 0}
    end.


% 30
% return    shop_npc_list
get_shop_npc_list(TownId) ->
    MatchSpec = ets:fun2ms(
        fun(Rec = #town_npc{town_id = Tid})
            when Tid == TownId ->
                Rec
        end
    ),
    
    TownNpcList = db:select (town_npc, MatchSpec),
    
    lists:foldl(
        fun(TownNpc, Result) ->
            TownNpcId   = TownNpc #town_npc.id,
            NpcId       = TownNpc #town_npc.npc_id,
            ResourceId  = TownNpc #town_npc.resource_id,
            PositionX   = TownNpc #town_npc.position_x,
            PositionY   = TownNpc #town_npc.position_y,

            Npc= db:get(npc, NpcId),
            
            NpcName     = Npc #npc.name,
            NpcChat     = Npc #npc.dialog,
            NpcShopName = Npc #npc.shop_name,

            Item = {
                TownNpcId,
                ResourceId,
                PositionX,
                PositionY,
                NpcName,
                NpcChat,
                NpcShopName
            },

            [Item | Result]
        end,
            [],
            TownNpcList
    ).


% 31
% return    shop_npc_list
get_town_npc_items(TownNpcId) ->
    case (catch db:get(town_npc, TownNpcId) ) of
        {'EXIT', _} ->
            [];
        _ ->
            MatchSpec = ets:fun2ms(
                fun(#town_npc_item{item_id = ItemId, town_npc_id = TNid})
                    when TNid == TownNpcId ->
                        ItemId
                end
            ),

            Items = db:select (town_npc_item, MatchSpec),

            lists:foldl(
                fun(ItemId, Result) ->
                    ItemProp    = db:get(item, ItemId),

                    ItemName    = ItemProp #item.name,
                    IconId      = ItemProp #item.icon_id,
                    TypeId      = ItemProp #item.type_id,

                    TypeProp    = db:get(item_type, TypeId),

                    MaxRepeatNum= TypeProp #item_type.max_repeat_num,

                    ItemPrice   = calc_item_buy_price(ItemId, 1),

                    Item = {
                        ItemId,
                        1,          % 默认等级1
                        ItemName,
                        ItemPrice,
                        IconId,
                        TypeId,
                        MaxRepeatNum
                    },

                    [Item | Result]
                end,
                    [],
                    Items
            )
    end.


% 32
npc_recycle_items() ->
    List = get_recycle_list(),
    [ recycle_list_props(Item) || Item <-List].

recycle_list_props( RecyclePlayerItem = #recycle_player_item{}) ->
    #recycle_player_item{
        ets_key       = _,
        player_id     = _,
        id            = Id,
        item_id       = ItemId,
        upgrade_level = UpgradeLevel,
        number        = Number,
        expire_time   = ExpireTime
    } = RecyclePlayerItem,

    Item        = db:get(item, ItemId),

    ItemName    = Item #item.name,
    IconId      = Item #item.icon_id,
    TypeId      = Item #item.type_id,

    SellPrice   = calc_item_sell_price(ItemId, UpgradeLevel),

    TotalPrice  = SellPrice * Number,

    {
        Id,
        ItemId,
        UpgradeLevel,
        Number,
        IconId,
        ItemName,
        TotalPrice,
        ExpireTime,
        TypeId
    }.


% 05
shop_npc_item_props(ItemId) ->
    ItemProp        = db:get(item, ItemId),
    
    Name            = ItemProp #item.name,
    IconId          = ItemProp #item.icon_id,
    TypeId          = ItemProp #item.type_id,
    Usage           = ItemProp #item.usage,
    Description     = ItemProp #item.description,

    Attack          = ItemProp #item.attack,
    Defense         = ItemProp #item.defense,
    StuntAttack     = ItemProp #item.stunt_attack,
    StuntDefense    = ItemProp #item.stunt_defense,
    MagicAttack     = ItemProp #item.magic_attack,
    MagicDefense    = ItemProp #item.magic_defense,
    Health          = ItemProp #item.health,

    RequireLevel    = ItemProp #item.require_level,

    UpgradeLevel    = 1,
    ItemUpgrade     = db:get(item_upgrade, UpgradeLevel),
    
    UpgradeLevelName= ItemUpgrade #item_upgrade.name,

    Price           = get_item_init_price(ItemId),

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
    }.


% 34
buy_npc_item(TownId, TownNpcId, ItemId, GridId) ->
    try buy_npc_item_real(TownId, TownNpcId, ItemId, GridId) of
        Value ->
            {?ACTION_SUCCESS, Value}
    catch
        throw : npc_no_exist ->
            {?NPC_NO_EXIST, 0};
        throw : npc_item_no_exist ->
            {?NPC_ITEM_NO_EXIST, 0};
        throw : insufficient_coin ->
            {?INSUFFICIENT_COIN, 0};
        throw : un_avaliable_grid ->
            {?UN_AVALIABLE_GRID, 0};
        _ : _   -> 
            {?ERROR_UNDEFINED, 0}
    end.

buy_npc_item_real(TownId, TownNpcId, ItemId, GridId) ->
    % 检查npc
    case check_town_npc(TownId, TownNpcId) of
        check_town_npc ->
            throw(npc_no_exist);
        _ ->
            nop
    end,

    % 检查item
    case check_town_npc_item(TownNpcId, ItemId) of
        npc_item_no_exist ->
            throw(npc_item_no_exist);
        _ ->
            nop
    end,

    % 检查价格
    ItemPrice   = get_item_init_price(ItemId),

    case mod_player:check_coin(ItemPrice) of
        false ->
            throw(insufficient_coin);
        _ ->
            noop
    end,

    % 确定格子ID
    NewGrid = get_pack_avaliable_grid(ItemId, 1, 1, GridId),
    case NewGrid of
        false ->
            throw(un_avaliable_grid);
        _ ->
            noop
    end,

    % 购买操作
    do_buy_item(ItemId, NewGrid).


% 35
sell_item(GridId) ->
    try sell_item_real(GridId) of
        _ ->
            ?ACTION_SUCCESS
    catch
        throw : player_grid_no_exist ->
            ?PLAYER_GRID_NO_EXIST;
        throw : source_grid_empty ->
            ?SOURCE_GRID_EMPTY;
        _ : _ ->
            ?ERROR_UNDEFINED
    end.

sell_item_real(GridId) ->
    % 检查格子编号
    case check_player_grid_id(GridId) of
        false ->
            throw(player_grid_no_exist);
        true ->
            noop
    end,

    case get_player_grid(GridId) of
        false ->
            throw(source_grid_empty);
        Grid ->
            ItemId = Grid #player_item.item_id,
            Number = get_player_item_number(Grid),
            Level  = Grid #player_item.upgrade_level,

            TotalPrice = Number * calc_item_sell_price(ItemId, Level),

            add_recycle_item(ItemId, Level, Number),
            mod_player:increase_coin(TotalPrice),
            delete_grid_item(GridId)
    end.

% 36
buy_back_item(RecycleId, GridId) ->
    try buy_back_item_real(RecycleId, GridId) of
        NewGridId ->
            {?ACTION_SUCCESS, NewGridId}
    catch
        throw : recycle_item_no_exist ->
            ?RECYCLE_ITEM_NO_EXIST;
        throw : recycle_item_expired ->
            ?RECYCLE_ITEM_EXPIRED;
        throw : insufficient_coin ->
            ?INSUFFICIENT_COIN;
        throw : un_avaliable_grid ->
            ?UN_AVALIABLE_GRID;
        _ : _   ->
            {?ERROR_UNDEFINED, 0}
    end.

buy_back_item_real(RecycleId, GridId) ->
    RecycleItem = get_recycle_item(RecycleId),
    if
        RecycleItem == false ->
            throw(recycle_item_no_exist);
        true ->
            noop
    end,

    ItemId      = RecycleItem #recycle_player_item.item_id,
    Level       = RecycleItem #recycle_player_item.upgrade_level,
    Number      = RecycleItem #recycle_player_item.number,
    ExpireTime  = RecycleItem #recycle_player_item.expire_time,

    TimeStamp = lib_misc:get_local_timestamp(),

    if
        TimeStamp > ExpireTime ->
            throw(recycle_item_expired);
        true ->
            noop
    end,

    TotalPrice   = Number * calc_item_sell_price(ItemId, Level),

    case mod_player:check_coin(TotalPrice) of
        false ->
            throw(insufficient_coin);
        _ ->
            noop
    end,

    NewGrid  = get_pack_avaliable_grid(ItemId, Level, Number, GridId),
    if 
        NewGrid ==false ->
            throw(un_avaliable_grid);
        true ->
            noop
    end,

    NewGridId = do_buy_back_item(ItemId, Level, Number, TotalPrice, NewGrid),
    del_recycle_item(RecycleId),

    NewGridId.

do_buy_back_item(ItemId, Level, Number, TotalPrice, NewGrid) ->
    mod_player:decrease_coin(TotalPrice),

    NewGridId = put_item_into_grid(ItemId, Level, Number, NewGrid),

    NewGridId.

% 40
get_role_equit_list(PlayerRoleId, PositionId) ->
    OpenPosIds     = get_role_position_ids(),

    PosIds = if
        PositionId =/= 0 ->
            case lists:member(PositionId, OpenPosIds) of
                true ->
                    [PositionId];
                false ->
                    []
            end;
        true ->
            OpenPosIds
    end,

    lists:sort([ get_role_equi(PlayerRoleId, PosId) || PosId <- PosIds]).

get_role_equi(PlayerRoleId, PositionId) ->
    RoleEqui        = get_player_role_equi(PlayerRoleId, PositionId),
    
    EquipItemType   = db:get(item_pack_grid, PositionId),
    TypeId          = EquipItemType #item_pack_grid.equip_item_type,

    case RoleEqui of
        false ->
            {
                PositionId,
                0,
                0,  % ItemId,
                0,  % ItemLevel,
                0,  % ItemIconId
                TypeId
            };
        _ ->
            ItemId          = RoleEqui #player_item.item_id,

            PlayerItemId    = RoleEqui #player_item.id,

            ItemLevel       = RoleEqui #player_item.upgrade_level,

            ItemIconId     = get_item_icon_id(ItemId),
            
            {
                PositionId,
                PlayerItemId,
                ItemId,
                ItemLevel,
                ItemIconId,
                TypeId
            }
    end.


% 55
warehouse_grid_moveto_pack(FromGridId, ToGridId) ->
    try warehouse_grid_moveto_pack_real(FromGridId, ToGridId) of
        NewGridId ->
            {?ACTION_SUCCESS, NewGridId}
    catch
        throw : grid_not_exist ->
            {?PLAYER_GRID_NO_EXIST, 0};
        throw : source_grid_empty ->
            {?SOURCE_GRID_EMPTY, 0};
        _ : _ ->
            {?ERROR_UNDEFINED, 0}
    end.


warehouse_grid_moveto_pack_real(FromGridId, ToGridId) ->
    WareHouseGridIds     = get_player_warehouse_grid_ids(),
    case lists:member(FromGridId, WareHouseGridIds) of
        false ->
            throw(grid_not_exist);
        true ->
            noop
    end,

    if
        ToGridId =/= 0 ->
            PackGridIds     = get_player_pack_grid_ids(),
            case lists:member(ToGridId, PackGridIds) of
                false ->
                    throw(grid_not_exist);
                true ->
                    noop
            end;
        true ->
            nop
    end,

    FromGrid = get_player_grid(FromGridId),
    if
        FromGrid == false ->
            throw(source_grid_empty );
        true ->
            noop
    end,

    ToGrid = if
            ToGridId == 0 ->
                player_item_avaliable_grid(pack, FromGrid, ToGridId);
            true ->
                case get_player_grid(ToGridId) of
                    false ->
                        {new, ToGridId};
                    TmpToGrid  ->
                        TmpToGrid
                end
     end,

    WareHouseGridId = case ToGrid of
        {new, NewGridId}->
            % 目标格子为空
            PlayerItemId = FromGrid #player_item.id,
            update_player_item_pack(PlayerItemId, NewGridId),
            NewGridId;
        ToGrid ->
            % 目标格子非空
            FromItemId  = FromGrid #player_item.item_id,
            ToItemId    = ToGrid #player_item.item_id,
            FromLevel   = FromGrid #player_item.upgrade_level,
            ToLevel     = ToGrid #player_item.upgrade_level,
            if
                FromItemId == ToItemId  andalso FromLevel == ToLevel ->
                    player_grid_stack_same(FromGrid, ToGrid);
                true ->
                    % 不同物品直接交换
                    player_grid_swap(FromGrid, ToGrid)
            end,
            ToGrid #player_item.grid_id
    end,

    WareHouseGridId.


% 56
pack_grid_moveto_warehouse(FromGridId, ToGridId) ->
    try pack_grid_moveto_warehouse_real(FromGridId, ToGridId) of
        NewGridId ->
            {?ACTION_SUCCESS, NewGridId}
    catch
        throw : grid_not_exist ->
            {?PLAYER_GRID_NO_EXIST, 0};
        throw : source_grid_empty ->
            {?SOURCE_GRID_EMPTY, 0};
        _ : _ ->
            {?ERROR_UNDEFINED, 0}
    end.


pack_grid_moveto_warehouse_real(FromGridId, ToGridId) ->
    PackGridIds     = get_player_pack_grid_ids(),
    case lists:member(FromGridId, PackGridIds) of
        false ->
            throw(grid_not_exist);
        true ->
            noop
    end,

    if
        ToGridId =/= 0 ->
            WareHouseGridIds     = get_player_warehouse_grid_ids(),
            case lists:member(ToGridId, WareHouseGridIds) of
                false ->
                    throw(grid_not_exist);
                true ->
                    noop
            end;
        true ->
            nop
    end,

    FromGrid = get_player_grid(FromGridId),
    if
        FromGrid == false ->
            throw(source_grid_empty );
        true ->
            noop
    end,

    ToGrid = if
        ToGridId == 0 ->
            player_item_avaliable_grid(warehouse, FromGrid, ToGridId);
        true ->
            case get_player_grid(ToGridId) of
                false ->
                    {new, ToGridId};
                TmpToGrid  ->
                    TmpToGrid
            end
    end,

    PackGridId = case ToGrid of
        {new, NewGridId}->
            % 目标格子为空
            PlayerItemId = FromGrid #player_item.id,
            update_player_item_pack(PlayerItemId, NewGridId),
            NewGridId;
        ToGrid ->
            % 目标格子非空
            FromItemId  = FromGrid #player_item.item_id,
            ToItemId    = ToGrid #player_item.item_id,
            FromLevel   = FromGrid #player_item.upgrade_level,
            ToLevel     = ToGrid #player_item.upgrade_level,
            if
                FromItemId == ToItemId  andalso FromLevel == ToLevel ->
                    player_grid_stack_same(FromGrid, ToGrid);
                true ->
                    % 不同物品直接交换
                    player_grid_swap(FromGrid, ToGrid)
            end,
            ToGrid #player_item.grid_id
    end,

    PackGridId.


% 60
equipment_list(EquipType, Equipped, EquipRoleId, _Page) ->
    EquipList = get_player_equipment_list(),

    EquipList1 = if
        EquipType >= 1 andalso EquipType =< 6 ->
            lists:filter(
                fun(Element) ->
                    Item = db:get(item, Element #player_item.item_id),
                    if
                        Item #item.type_id == EquipType ->
                            true;
                        true ->
                            false
                    end
                end,
                EquipList
            );
        true ->
            EquipList
    end,

    EquipList2 = case {Equipped, EquipRoleId} of
        {0, 0} ->
            EquipList1;
        {_, 0} ->
            [
                Temp1 || Temp1 <- EquipList1,
                Temp1 #player_item.player_role_id =/= null
            ];
        {_, PlayerRoleId} ->
            case get_player_role(PlayerRoleId) of
                false ->
                    [];
                _ ->
                    [
                        Temp2 || Temp2 <- EquipList1,
                        Temp2 #player_item.player_role_id == PlayerRoleId
                    ]
            end
    end,
    
    List = [player_equip_item_props(EquipItem) || EquipItem <- EquipList2],
    {1, 1, List}.

get_player_equipment_list() ->
    List = lists:filter(
        fun(Row) ->
            ItemId  = Row #player_item.item_id,
            Item    = db:get(item, ItemId),
            ItemType    = Item #item.type_id,
            if
                ItemType =<6 andalso ItemType >= 1 ->
                    true;
                true ->
                    false
            end
        end,
        db:get(player_item)
    ),
    lists:sort(List).


% 61
equip_upgrade_probability() ->
    Data = get_curr_probability(),
    Direct = case Data #probability.direction of
        up ->
            ?RAISING;
        down ->
            ?FALLING
    end,
    {Data #probability.value, Direct}.


% 62
 equip_upgrade_stat() ->
    get_cd_time_props().


% 63
upgrade_equipment(PlayerItemId, Probability) ->
    try upgrade_equipment_real(PlayerItemId, Probability) of
        _Result ->
            PlayerItem = get_player_item(PlayerItemId),
            {
                _, %               PlayerItemId,
                _, %               ItemName,
                _, %               PlayerRoleName,
                _, %               ItemTypeId,
                _, %               ItemIconId,
                LevelName,
                UpgradePrice
            } = player_equip_item_props(PlayerItem),
            {?UPGRADE_SUCCESS, LevelName , UpgradePrice}
    catch
        throw : player_item_no_exist ->
            {?PLAYER_ITEM_NO_EXIST, "", 0};
        throw : insufficient_coin ->
            {?INSUFFICIENT_COIN, "", 0};
        throw : level_limit ->
            {?LEVEL_LIMIT, "", 0};
        throw : cooldown_time ->
            {?COOLDOWN_TIME, "", 0};
        throw : probability_change ->
            {?PROBABILITY_CHANGE, "", 0};
        throw : low_success_rate->
            {?LOW_SUCCESS_RATE, "", 0};
        _ : _ ->
            {?ERROR_UNDEFINED, "", 0}
    end.

upgrade_equipment_real(PlayerItemId, Probability) ->
    PlayerItem = get_player_item(PlayerItemId),

    if
        PlayerItem == false ->
            throw(player_item_no_exist);
        true ->
            noop
    end,

    UpgradePrice= get_player_item_upgrade_price(PlayerItem),

    case mod_player:check_coin(UpgradePrice) of
        false ->
            throw(insufficient_coin);
        true ->
            noop
    end,

    case get_upgrade_probability() of
        {Probability, _} ->
            noop;
        _CurValue ->
            throw(probability_change)
    end,

    case get_cd_time_props() of
        {0, 0} ->
            noop;
        _ ->
            throw(cooldown_time)
    end,

    SuccessRate = lib_misc:random(80) + 20,

    mod_player:decrease_coin(UpgradePrice),

    mod_player:set_cd_time(?CD_TYPE_UPGRADE_EQUIP, ?CD_TIME_UPGRADE_EQUIP),

    if
        SuccessRate < Probability ->
            throw(low_success_rate);
        true ->
            do_equip_upgrade(PlayerItem)
    end,

    success.

clear_upgrade_cd_time() ->
    try clear_upgrade_cd_time_real() of
        _Result ->
            ?ACTION_SUCCESS
    catch
        throw : insufficient_ingot ->
            ?INSUFFICIENT_INGOT;
        throw : zero_cd_time ->
            ?ZERO_CD_TIME;
        _ : _ ->
            ?ERROR_UNDEFINED
    end.

clear_upgrade_cd_time_real() ->
    {RemainSeconds, Ingot} = get_cd_time_props(),

    case RemainSeconds of
        0 ->
            throw(zero_cd_time);
        _ ->
            noop
    end,

    case mod_player:check_ingot(Ingot) of
        false ->
            throw(insufficient_ingot);
        _ ->
            noop
    end,
    mod_player:clean_cd_time(?CD_TYPE_UPGRADE_EQUIP).


get_cd_time_props() ->
    mod_player:get_cd_time_props(?CD_TYPE_UPGRADE_EQUIP).


do_equip_upgrade(PlayerItem) ->
    db:update(
        player_item,
        fun(Row) ->
            if
                Row #player_item.id == PlayerItem #player_item.id ->
                    Row #player_item{
                        upgrade_level = Row #player_item.upgrade_level + 1
                    };
                true ->
                    false
            end
        end
    ).

get_upgrade_probability() ->
    CurrProb    = mod_item:get_curr_probability(),

    Direction = case CurrProb #probability.direction of
        up ->
            ?RAISING;
        down ->
            ?FALLING
    end,
    {CurrProb #probability.value, Direction}.

get_curr_probability() ->
    TimePos = lib_misc:time_pos(),
    [Rec] = ets:lookup(probability, TimePos),
    Rec.


player_equip_item_props(EquipItem) ->
    PlayerItemId    = EquipItem #player_item.id,
    ItemId          = EquipItem #player_item.item_id,
    Level           = EquipItem #player_item.upgrade_level,

    PlayerRoleName  = case EquipItem #player_item.player_role_id of
        null ->
            "";
        Value ->
            get_player_role_name(Value)
    end,

    Item        = db:get(item, ItemId),
    ItemTypeId  = Item #item.type_id,
    ItemName    = Item #item.name,
    ItemIconId  = Item #item.icon_id,

    LevelProp   = db:get(item_upgrade, Level),
    LevelName   = LevelProp #item_upgrade.name,

    UpgradePrice= calc_item_buy_price(Item, Level),

    {
        PlayerItemId,
        ItemName,
        PlayerRoleName,
        ItemTypeId,
        ItemIconId,
        LevelName,
        UpgradePrice
    }.

get_player_role_name(PlayerRoleId) ->
    PlayerRole = get_player_role(PlayerRoleId),
    Role       = db:get(role, PlayerRole #player_role.role_id),
    Role #role.name.

%%------------------------------------------------------------------------------
%%  物品模板相关
%%------------------------------------------------------------------------------
get_pack_grid_list() ->
    List = [
        PackGrid || PackGrid <- ets:tab2list(item_pack_grid),
        PackGrid #item_pack_grid.id < 100
        andalso PackGrid #item_pack_grid.id >= 1
    ],
    lists:sort(List).

get_warehouse_grid_list() ->
    List = [
        PackGrid || PackGrid <- ets:tab2list(item_pack_grid),
        PackGrid #item_pack_grid.id < 200
        andalso PackGrid #item_pack_grid.id > 100
    ],
    lists:sort(List).


get_item_type(ItemId) when is_integer(ItemId)  ->
    Item = db:get(item, ItemId),
    Item #item.type_id.

get_item_icon_id(ItemId) when is_integer(ItemId) ->
    Item = db:get(item, ItemId),
    Item #item.icon_id.

get_item_quality(ItemId) when is_integer(ItemId) ->
    Item = db:get(item, ItemId),
    Item #item.quality.

%% 获取物品的允许堆叠数
get_item_max_repeat(ItemId) ->
    Item    = db:get(item, ItemId),
    
    TypeId  = Item #item.type_id,
    
    Type    = db:get(item_type, TypeId),
    
    Type #item_type.max_repeat_num.
    

get_item_init_price(Item = #item{}) ->
    ItemTypeId  = Item #item.type_id,

    PriceLevel  = Item #item.price_level,

    PriceProp   = db:get(item_price, PriceLevel),

    if 
        ItemTypeId =< 6 andalso ItemTypeId >= 1 ->
            PriceProp #item_price.equip_price;
        true ->
            PriceProp #item_price.item_price
    end;


get_item_init_price(ItemId) ->
    Item = db:get(item, ItemId),

    get_item_init_price(Item).


calc_player_item_buy_price(PlayerItem = #player_item{}) ->
    calc_item_buy_price(
        PlayerItem #player_item.item_id,
        PlayerItem #player_item.upgrade_level
    ).


calc_item_buy_price(ItemId, Level) ->
    Price       = get_item_init_price(ItemId),

    case Level of
        1 ->
            Price;
        _ ->
            UpgradePrice = calc_item_upgrade_total_price(ItemId, Level),
            Price + UpgradePrice
    end.


calc_item_upgrade_total_price(Item = #item{}, Level) ->
    ItemType    = Item #item.type_id,
    ItemQuality = Item #item.quality,

    LevelList = ets:tab2list(item_upgrade),

    PriceList = [
        (
            db:get(item_upgrade_price, { UpgradeLevel  #item_upgrade.level , ItemType, ItemQuality})
        ) #item_upgrade_price.upgrade_price
        || UpgradeLevel <- LevelList, UpgradeLevel #item_upgrade.level =< Level
    ],

    lists:sum(PriceList);


calc_item_upgrade_total_price(ItemId, Level) ->
    Item = db:get(item, ItemId),

    calc_item_upgrade_total_price(Item, Level).



calc_item_sell_price(PlayerItem = #player_item{}) ->
    calc_item_sell_price(
        PlayerItem #player_item.item_id,
        PlayerItem #player_item.upgrade_level
    ).


calc_item_sell_price(ItemId, Level) ->
    Price = calc_item_buy_price(ItemId, Level),

    lib_misc:ceil(?SELL_ITEM_DISCOUNT_RATE /100 * Price).


get_player_item_upgrade_price(PlayerItem = #player_item{}) ->
    Item = db:get(item, PlayerItem #player_item.item_id),

    NextLevel = PlayerItem #player_item.upgrade_level + 1,

    get_item_upgrade_price(Item, NextLevel).


get_item_upgrade_price(Item = #item{}, Level) ->
    get_item_upgrade_price(
        Item #item.type_id,
        Item #item.quality,
        Level
    ).


get_item_upgrade_price(ItemType, ItemQuality, Level) ->
    ItemUpgrade = db:get(item_upgrade_price, {Level, ItemType, ItemQuality}),
    ItemUpgrade #item_upgrade_price.upgrade_price.


get_player_pack_grid_ids() ->
    {PackKey, _, _} = mod_player:get_player_pack_keys(),

    MatchSpec = ets:fun2ms(
        fun(Grid)
            when Grid #item_pack_grid.id < 100
            andalso Grid #item_pack_grid.id >= 1
            andalso Grid #item_pack_grid.unlock_level =< PackKey ->
               Grid #item_pack_grid.id
        end
    ),

    lists:sort(db:select(item_pack_grid, MatchSpec)).

get_player_warehouse_grid_ids() ->
    {_, _, WareHouseKey} = mod_player:get_player_pack_keys(),

    MatchSpec = ets:fun2ms(
        fun(Grid)
            when Grid #item_pack_grid.id > 100
            andalso Grid #item_pack_grid.id < 200
            andalso Grid #item_pack_grid.unlock_level =< WareHouseKey ->
               Grid #item_pack_grid.id
        end
    ),

    lists:sort(db:select(item_pack_grid, MatchSpec)).


get_role_position_ids() ->
    {_, PosKey, _} = mod_player:get_player_pack_keys(),

    MatchSpec = ets:fun2ms(
        fun(Grid)
            when Grid #item_pack_grid.id =< 300
            andalso Grid #item_pack_grid.id > 200
            andalso Grid #item_pack_grid.unlock_level =< PosKey->
               Grid #item_pack_grid.id
        end
    ),

    lists:sort(db:select(item_pack_grid, MatchSpec)).


check_player_grid_id(GridId) ->
    OpenGridIds     = get_player_pack_grid_ids(),
    lists:member(GridId, OpenGridIds).

check_role_position_id(PositionId) ->
    OpenPosIds     = get_role_position_ids(),
    lists:member(PositionId, OpenPosIds).

%% 背包列表
get_player_pack_list() ->
    PlayerItemList = db:get(player_item),
    PackGridList = [
        Item || Item <- PlayerItemList,
        Item #player_item.grid_id < 100
    ],
    lists:keysort(#player_item.grid_id, PackGridList).

%% 仓库列表
get_player_warehouse_list() ->
    PlayerItemList = db:get(player_item),
    PackGridList = [
        Item || Item <- PlayerItemList,
        Item #player_item.grid_id < 200
        andalso Item #player_item.grid_id > 100
    ],
    lists:keysort(#player_item.grid_id, PackGridList).
%%--------------------------------------------------------------------
%% Function: calc_level_value(Level, BaseValue, UpValue)
%% @param    Level      = integer()
%% @param    BaseValue  = integer()
%% @param    UpValue    = integer()
%% Descrip.: 根据等级、基值、升级加值计算对应等级的加值
%% Returns : integer()
%%--------------------------------------------------------------------
calc_level_value(Level, BaseValue, UpValue) ->
    case BaseValue of
        0 ->
            0;
        _ ->
            BaseValue + (Level - 1) * UpValue
    end.


get_player_equip_affect_values(PlayerItem = #player_item{}) ->
    ItemId          = PlayerItem #player_item.item_id,
    Level           = PlayerItem #player_item.upgrade_level,
    get_item_level_affect_values(ItemId, Level).


 get_item_level_affect_values(ItemId, Level) ->
    Item            = db:get(item, ItemId),

    Attack          = Item #item.attack,
    AttackUp        = Item #item.attack_up,
    Defense         = Item #item.defense,
    DefenseUp       = Item #item.defense_up,
    StuntAttack     = Item #item.stunt_attack,
    StuntAttackUp   = Item #item.stunt_attack_up,
    StuntDefense    = Item #item.stunt_defense,
    StuntDefenseUp  = Item #item.stunt_defense_up,
    MagicAttack     = Item #item.magic_attack,
    MagicAttackUp   = Item #item.magic_attack_up,
    MagicDefense    = Item #item.magic_defense,
    MagicDefenseUp  = Item #item.magic_defense_up,
    Health          = Item #item.health,
    HealthUp        = Item #item.health_up,

    #role_equip_affect_values{
        attack        = calc_level_value(Level, Attack, AttackUp),
        defense       = calc_level_value(Level, Defense, DefenseUp),
        stunt_attack  = calc_level_value(Level, StuntAttack, StuntAttackUp),
        stunt_defense = calc_level_value(Level, StuntDefense, StuntDefenseUp),
        magic_attack  = calc_level_value(Level, MagicAttack, MagicAttackUp),
        magic_defense = calc_level_value(Level, MagicDefense, MagicDefenseUp),
        max_health    = calc_level_value(Level, Health, HealthUp)
    }.


get_role_equip_change_values(false, PlayerItem) ->
    #role_equip_affect_values{
        attack        = AttackCalc,
        defense       = DefenseCalc,
        stunt_attack  = StuntAttackCalc,
        stunt_defense = StuntDefenseCalc,
        magic_attack  = MagicAttackCalc,
        magic_defense = MagicDefenseCalc,
        max_health    = MaxHealthCalc

    } = get_player_equip_affect_values(PlayerItem),
    {
        AttackCalc,
        DefenseCalc,
        StuntAttackCalc,
        StuntDefenseCalc,
        MagicAttackCalc,
        MagicDefenseCalc,
        MaxHealthCalc
    };

get_role_equip_change_values(PlayerItem, false) ->
    #role_equip_affect_values{
        attack        = AttackCalc,
        defense       = DefenseCalc,
        stunt_attack  = StuntAttackCalc,
        stunt_defense = StuntDefenseCalc,
        magic_attack  = MagicAttackCalc,
        magic_defense = MagicDefenseCalc,
        max_health    = MaxHealthCalc
    } = get_player_equip_affect_values(PlayerItem),

    #role_equip_affect_values{
        attack        = AttackCalc * -1,
        defense       = DefenseCalc * -1,
        stunt_attack  = StuntAttackCalc * -1,
        stunt_defense = StuntDefenseCalc * -1,
        magic_attack  = MagicAttackCalc * -1,
        magic_defense = MagicDefenseCalc * -1,
        max_health    = MaxHealthCalc * -1
    };


get_role_equip_change_values(PlayerItem1, PlayerItem2) ->
    #role_equip_affect_values{
        attack        = AttackCalc1,
        defense       = DefenseCalc1,
        stunt_attack  = StuntAttackCalc1,
        stunt_defense = StuntDefenseCalc1,
        magic_attack  = MagicAttackCalc1,
        magic_defense = MagicDefenseCalc1,
        max_health    = MaxHealthCalc1
    } = get_player_equip_affect_values(PlayerItem1),

    #role_equip_affect_values{
        attack        = AttackCalc2,
        defense       = DefenseCalc2,
        stunt_attack  = StuntAttackCalc2,
        stunt_defense = StuntDefenseCalc2,
        magic_attack  = MagicAttackCalc2,
        magic_defense = MagicDefenseCalc2,
        max_health    = MaxHealthCalc2
    } = get_player_equip_affect_values(PlayerItem2),

    #role_equip_affect_values{
        attack        = AttackCalc1         - AttackCalc2,
        defense       = DefenseCalc1        - DefenseCalc2,
        stunt_attack  = StuntAttackCalc1    - StuntAttackCalc2,
        stunt_defense = StuntDefenseCalc1   - StuntDefenseCalc2,
        magic_attack  = MagicAttackCalc1    - MagicAttackCalc2,
        magic_defense = MagicDefenseCalc1   - MagicDefenseCalc2,
        max_health    = MaxHealthCalc1      - MaxHealthCalc2
    }.

%% 获取玩家物品的堆叠数量
get_player_item_number(PlayerItem = #player_item{}) ->
    case PlayerItem #player_item.item_id of
        null ->
            0;
        _ ->
            PlayerItem #player_item.number
    end.


%% 获取玩家物品类别的可装备位置
get_item_equip_pos(ItemId) ->
    Item = db:get(item, ItemId),
    
    TypeId = Item #item.type_id,

    get_item_type_to_position(TypeId).
   

get_item_type_to_position(TypeId) ->
    MatchSpec = ets:fun2ms(
        fun(Grid)
            when Grid #item_pack_grid.id > 200
            andalso Grid #item_pack_grid.id =< 300
            andalso Grid #item_pack_grid.equip_item_type == TypeId ->
                Grid
        end
    ),

    case db:select(item_pack_grid, MatchSpec) of
        [] ->
            false;
        [Grid] ->
            Grid #item_pack_grid.id
    end.


% 获取背包格子,包括背包格子和仓库格子
get_player_grid(GridId) ->
    if
        GridId > 200 orelse GridId < 1 ->
            false;
        true ->
            GridList = db:get(player_item),

            FindGridList = [
                Grid || Grid <- GridList,
                Grid #player_item.grid_id == GridId
            ],

            case FindGridList of
                [] ->
                    false;
                [Grid|_] ->
                    Grid
            end
    end.
    

get_player_item(PlayerItemId) ->
    PlayerItemList = db:get(player_item),

    FindList = [
        PlayerItem || PlayerItem <- PlayerItemList,
        PlayerItem #player_item.id == PlayerItemId
    ],

    case FindList of
        [] ->
            false;
        [PlayerItem] -> PlayerItem
    end.


% 获取玩家角色
% TODO 调用mod_role
get_player_role(RoleId) ->
    PlayerId = mod_player:get_player_id(),
    Find = db:find(
        player_role,
        fun(Row) ->
            if
                Row #player_role.id == RoleId 
                andalso Row #player_role.player_id == PlayerId ->
                    true;
                true ->
                    false
            end
        end
    ),

    case Find of
        [] ->
            false;
        [Role] ->
            Role
    end.


get_player_role_equi_list(RoleId) ->
    db:find(
        player_item,
        fun(Row) ->
            if
                Row #player_item.player_role_id == RoleId ->
                    true;
                true ->
                    false
            end
        end
    ).


player_item_decr_number(GridItem) ->
    PlayrItemId = GridItem #player_item.id,

    if
        GridItem #player_item.number == 1 ->
            delete_player_item(PlayrItemId);
        true ->
            db:update(
                player_item,
                fun(Row) ->
                    if
                        Row #player_item.id == PlayrItemId ->
                            Number = Row # player_item.number - 1,
                            Row #player_item{number = Number};
                        true ->
                            false
                    end
                end
            )
    end.


player_item_set_number(Grid, Number) when Number > 0 ->
    PlayerItemId = Grid #player_item.id,
    db:update(
        player_item,
        fun(Row) ->
            if
                Row #player_item.id == PlayerItemId ->
                    if
                        Number == 0 ->
                            Row #player_item{
                                item_id         = null,
                                number          = null,
                                upgrade_level   = null,
                                player_role_id  = 0
                                };
                        true ->
                            Row #player_item{number = Number}
                    end;
                true ->
                    false
            end
        end
     ).

%-OK
update_player_item_pack(PlayerItemId, GridId) ->
    db:update(
        player_item,
        fun(Row) ->
            if
                Row #player_item.id == PlayerItemId->
                    Row #player_item{
                        grid_id        = GridId,
                        player_role_id = null
                    };
                true ->
                    false
            end
        end
    ).

%-OK
update_role_equip_item(PlayerItemId, RoleId, PositionId) ->
    db:update(
        player_item,
        fun(Row) ->
            if
              Row #player_item.id  == PlayerItemId ->
                    Row #player_item{
                        grid_id        = PositionId,
                        player_role_id = RoleId
                };
                true ->
                    false
            end
        end
    ).


do_buy_item(ItemId, NewGrid) ->
    ItemPrice   = get_item_init_price(ItemId),

    mod_player:decrease_coin(ItemPrice),

    put_item_into_grid(ItemId, 1, 1, NewGrid).


% 获取格子物品可装备位置ID，
% 没有合适位置返回false
player_item_equip_position(PlayerItem, PositionId) ->
    ItemId = PlayerItem #player_item.item_id,

    CheckPositionId = get_item_equip_pos(ItemId),

    case PositionId of
        0 ->
            CheckPositionId;
        _ ->
            if
                CheckPositionId == PositionId ->
                    CheckPositionId;
                true ->
                    false
            end
    end.

% 获取可以容纳该物品的格子
%-delete
player_item_avaliable_grid(pack, PlayerItem, GridId) ->
    ItemId      = PlayerItem #player_item.item_id,
    Level       = PlayerItem #player_item.upgrade_level,
    Number      = get_player_item_number(PlayerItem),

    get_pack_avaliable_grid(ItemId, Level, Number, GridId);


player_item_avaliable_grid(warehouse, PlayerItem, GridId) ->
    ItemId      = PlayerItem #player_item.item_id,
    Level       = PlayerItem #player_item.upgrade_level,
    Number      = get_player_item_number(PlayerItem),

    get_warehouse_avaliable_grid(ItemId, Level, Number, GridId).


%   get_pack_avaliable_grid(ItemId, Level, Number, 0) ->
%    false | {new, GridId}| PlayerGrid
get_pack_avaliable_grid(ItemId, Level, Number, 0) ->
    PlayerGridList  = get_player_pack_list(),
    OpenGridIds     = get_player_pack_grid_ids(),
    get_avaliable_grid(ItemId, Level, Number, PlayerGridList, OpenGridIds);

get_pack_avaliable_grid(ItemId, Level, Number, GridId) ->
    if
        GridId > 100 ->
            false;
        true ->
            case get_player_grid(GridId) of
                false ->
                    OpenGridIds     = get_player_pack_grid_ids(),
                    case lists:member(GridId, OpenGridIds) of
                        true ->
                            {new, GridId};
                        false ->
                            false
                    end;
                Grid ->
                    case check_item_stack(ItemId, Level, Number, Grid) of
                        true ->
                            Grid;
                        _ ->
                            false
                    end
            end
    end.


get_warehouse_avaliable_grid(ItemId, Level, Number, 0) ->
    WareHouseGridList  = get_player_warehouse_list(),
    WareHouseGridIds     = get_player_warehouse_grid_ids(),
    get_avaliable_grid(ItemId, Level, Number, WareHouseGridList, WareHouseGridIds);

get_warehouse_avaliable_grid(ItemId, Level, Number, GridId) ->
    if
        GridId > 200 orelse GridId < 100 ->
            false;
        true ->
            case get_player_grid(GridId) of
                false ->
                    WareHouseGridIds    = get_player_warehouse_grid_ids(),
                    case lists:member(GridId, WareHouseGridIds) of
                        true ->
                            {new, GridId};
                        false ->
                            false
                    end;
                Grid ->
                    case check_item_stack(ItemId, Level, Number, Grid) of
                        true ->
                            Grid;
                        _ ->
                            false
                    end
            end
    end.

% 
get_avaliable_grid( _ItemId, _Level, _Number, [], [])->
    false;

get_avaliable_grid( _ItemId, _Level, _Number, [], OpenGridList) ->
    OpenGridId = lists:nth(1, OpenGridList),
    {new, OpenGridId};

get_avaliable_grid( ItemId, Level, Number, PlayerGridList, OpenGridList) ->
    [PlayerGrid | NewPlayerGridList ] = PlayerGridList,
    [OpenGridId | NewOpenGridList ] = OpenGridList,

    if 
        OpenGridId < PlayerGrid #player_item.grid_id ->
            % 当前格子空
            {new, OpenGridId};
        true ->
            % 当前格子有物品
            case check_item_stack(ItemId, Level, Number, PlayerGrid) of
                true ->
                    PlayerGrid;
                _ ->
                    get_avaliable_grid( ItemId, Level, Number, NewPlayerGridList, NewOpenGridList)
            end
    end.

% 检查物品能否放入格子
check_item_stack(ItemId, Level, Number, ToGrid  = #player_item{}) ->
    ToItemId    = ToGrid  #player_item.item_id,
    ToLevel     = ToGrid  #player_item.upgrade_level,

    if
        ItemId == ToItemId andalso Level == ToLevel ->
            To_Number       = get_player_item_number(ToGrid),
            MaxRepeatNum    = get_item_max_repeat(ItemId),
            Number + To_Number =< MaxRepeatNum;
        true ->
            false
    end.



%% 堆叠物品
%-OK
player_grid_stack_same(FromGridId, ToGridId)
when is_integer(FromGridId)
andalso is_integer(ToGridId) ->
    FromGrid = get_player_grid(FromGridId),
    ToGrid   = get_player_grid(ToGridId),
    player_grid_stack_same(FromGrid, ToGrid);

player_grid_stack_same(FromGrid = #player_item{}, ToGrid = #player_item{})->
    FromNumber         = get_player_item_number(FromGrid),

    ToNumber           = get_player_item_number(ToGrid),

    ItemId = FromGrid #player_item.item_id,

    MaxRepeatNum        = get_item_max_repeat(ItemId),

    if
        FromNumber + ToNumber > MaxRepeatNum ->
            player_item_set_number(ToGrid, MaxRepeatNum),
            player_item_set_number(FromGrid, FromNumber + ToNumber - MaxRepeatNum);
        true ->
            player_item_set_number(ToGrid, FromNumber + ToNumber),
            delete_grid_item(FromGrid #player_item.grid_id)
    end,

    1.


player_grid_swap(FromGrid = #player_item{}, ToGrid = #player_item{}) ->
    FromId      = FromGrid #player_item.id,
    FromGridId  = FromGrid #player_item.grid_id,
    FromRoleId  = FromGrid #player_item.player_role_id,

    ToId        = ToGrid #player_item.id,
    ToGridId    = ToGrid #player_item.grid_id,
    ToRoleId    = ToGrid #player_item.player_role_id,

    db:update(
        player_item,
        fun(Row) ->
            case Row #player_item.id of
                FromId ->
                    Row #player_item{
                        grid_id        = ToGridId,
                        player_role_id = ToRoleId
                    };
                ToId ->
                    Row #player_item{
                        grid_id        = FromGridId,
                        player_role_id = FromRoleId
                    };
                _ ->
                    false
            end
        end
    ).


% 清空格子,删除对应记录
delete_grid_item(GridId) ->
    db:delete(
        player_item,
        fun(Row) ->
            if
                Row #player_item.grid_id == GridId->
                    true;
                true ->
                    false
            end
        end
     ).


% 删除玩家物品
delete_player_item(GridItem = #player_item{}) ->
    PlayerItemId = GridItem #player_item.id,
    delete_player_item(PlayerItemId);

delete_player_item(PlayerItemId) ->
    db:delete(
        player_item,
        fun(Row) ->
            if
                Row #player_item.id == PlayerItemId ->
                    true;
                true ->
                    false
            end
        end
     ).


%%----------------------------------------
%%  玩家回购物品
%%----------------------------------------
get_recycle_list() ->
    PlayerId = mod_player:get_player_id(),

    MatchSpec = ets:fun2ms(
        fun(Row) when Row #recycle_player_item.player_id == PlayerId ->
            Row
        end
    ),

    List = lib_ets:select(recycle_player_item, MatchSpec),

    NewList = filter_recycle_list(List, []),

    lists:reverse(lists:keysort(#recycle_player_item.id, NewList)).

filter_recycle_list([], Target) ->
    Target;

filter_recycle_list(Source, Target) ->
    RecycleItem = lists:nth(1, Source),
    NewSource   = lists:nthtail(1, Source),

    if
        length(Source)>45 ->
            Id = RecycleItem #recycle_player_item.id,
            del_recycle_item(Id),
            filter_recycle_list(NewSource, Target);
        true ->
            TimeStamp = lib_misc:get_local_timestamp(),

            ItemTime    = RecycleItem #recycle_player_item.expire_time,
            if
                ItemTime =< TimeStamp ->
                    Id = RecycleItem #recycle_player_item.id,
                    del_recycle_item(Id),
                    filter_recycle_list(NewSource, Target);
                true ->
                    filter_recycle_list(NewSource, [RecycleItem | Target])
            end
    end.


get_recycle_item(Id) ->
    PlayerId    = mod_player:get_player_id(),
    EtsKey      = {PlayerId, Id},
    case lib_ets:get(recycle_player_item, EtsKey) of
        [] ->
            false;
        [Row] ->
            Row
    end.


del_recycle_item(Id) ->
    PlayerId    = mod_player:get_player_id(),
    EtsKey      = {PlayerId, Id},
    lib_ets:delete(recycle_player_item, EtsKey).

add_recycle_item(ItemId, Level, Number) ->
    List = get_recycle_list(),
    if
        length(List) >= 45 ->
            DelItem = lists:nth(1, List),
            DelId   = DelItem #recycle_player_item.id,
            del_recycle_item(DelId);
        true ->
            noop
    end,
    PlayerId    = mod_player:get_player_id(),
    TimeStamp   = lib_misc:get_local_timestamp() + ?SELL_ITEM_RECYCLE_TIMEOUT,

    Id =  case List of
        [] ->
            1;
        List ->
            lists:max([Item #recycle_player_item.id || Item <- List]) + 1
    end,


    EtsKey      = {PlayerId, Id},

    RecyclePlayerItem = #recycle_player_item{
        ets_key         = EtsKey,
        player_id       = PlayerId,
        id              = Id,
        item_id         = ItemId,
        upgrade_level   = Level,
        number          = Number,
        expire_time     = TimeStamp
    },

    lib_ets:insert(recycle_player_item, RecyclePlayerItem).


%%----------------------------------------
%%  角色装备
%%----------------------------------------


%%--------------------------------------------------------------------
%% Function: get_player_role_equi(PlayerRoleId, PositionId)
%%           PlayerRoleId   = integer() 角色ID
%%           PositionId     = integer() 位置ID 如果为0，返回全部
%% Descrip.: 获取玩家角色RoleId的装备的物品列表
%% Returns : list()
%%--------------------------------------------------------------------

%% 获取角色所有装备
get_player_role_equi(PlayerRoleId) ->
    PlayerItemList = db:get(player_item),
    RoleEquiList = [
        Equi || Equi <- PlayerItemList,
        Equi #player_item.player_role_id == PlayerRoleId
    ],
    lists:sort(RoleEquiList).

% 获取角色的某个装备
get_player_role_equi(PlayerRoleId, 0) ->
    get_player_role_equi(PlayerRoleId);

get_player_role_equi(PlayerRoleId, PositionId) ->
    PlayerItemList = db:get(player_item),
    Find = [
        Equi || Equi <- PlayerItemList,
        Equi #player_item.player_role_id == PlayerRoleId andalso
        Equi #player_item.grid_id == PositionId
    ],
    case Find of
        [] ->
            false;
        [RoleEqui] ->
            RoleEqui
    end.

%%--------------------------------------------------------------------
%% NPC Recycle Item List
%%--------------------------------------------------------------------

check_town_npc(TownId, TownNpcId) ->
    MatchSpec = ets:fun2ms(
        fun(#town_npc{town_id = TownTest, id = TownNpcTest})
            when TownTest == TownId , TownNpcTest == TownNpcId ->
                true
        end
    ),
    
    case db:select(town_npc, MatchSpec) of
        [] ->
            npc_no_exist;
        _ ->
            true
    end.

check_town_npc_item(TownNpcId, ItemId) ->
    MatchSpec = ets:fun2ms(
        fun(#town_npc_item{town_npc_id = TownNpcTest, item_id = ItemTest})
            when TownNpcTest == TownNpcId , ItemTest == ItemId ->
                true
        end
    ),
    
    case db:select(town_npc_item, MatchSpec) of
        [] ->
            npc_item_no_exist;
        _ ->
            true
    end.


%%--------------------------------------------------------------------
%% Export for other module
%%--------------------------------------------------------------------

get_item_icon(Id) when is_integer(Id) ->
    Item = db:get(item, Id),
    Item #item.icon_id.


get_item_name(Id) when is_integer(Id) ->
    Item = db:get(item, Id),
    Item #item.name;


get_item_name(IdList) when is_list(IdList) ->
    [{Id, get_item_name(Id)} || Id <- IdList ].


get_item_number(Id) when is_integer(Id) ->
    PlayerItemList = db:get(player_item),
    NumberList = [
        get_player_item_number(PlayerItem) || PlayerItem <- PlayerItemList,
        PlayerItem #player_item.item_id == Id
    ],
    lists:sum(NumberList);


get_item_number(IdList) when is_list(IdList) ->
    [{Id, get_item_number(Id)} || Id <- IdList ].


%%--------------------------------------------------------------------
%%  award_item([AwardItem])
%%              AwardItem= {ItemId, Number}
%%  desc:   奖励获得物品
%%  return: [Result]
%%           Result = {ok, ItemId, Number} | {err, ItemId, Number}
%%--------------------------------------------------------------------
award_item_list(AwardList) ->
    Result =[
        award_item(ItemId, Number) || {ItemId, Number} <- AwardList,
        ItemId =/= 0
    ],
    lists:flatten(Result).

award_item(ItemId, Number) ->
    % 可容纳1个物品的格子列表
    % AvaliableGridList = get_pack_avaliable_grid_list(ItemId, 1, 1),
    PackGridList = get_player_pack_list(),
    OpenGridIds  = get_player_pack_grid_ids(),

    put_items_into_grids(ItemId, Number, PackGridList, OpenGridIds, 0).

get_role_equip_affect_values(RoleId) ->
    EquipItemList = get_player_role_equi_list(RoleId),

    calc_role_equip_affcect_values(#role_equip_affect_values{}, EquipItemList).


calc_role_equip_affcect_values(Values = #role_equip_affect_values{}, []) ->
    Values;

calc_role_equip_affcect_values(
    Values = #role_equip_affect_values{},
    [PlayerItem | NewList]
) when is_record(PlayerItem , player_item) ->
    #role_equip_affect_values{
        attack        = AttackCalc1,
        defense       = DefenseCalc1,
        stunt_attack  = StuntAttackCalc1,
        stunt_defense = StuntDefenseCalc1,
        magic_attack  = MagicAttackCalc1,
        magic_defense = MagicDefenseCalc1,
        max_health    = MaxHealth1
    } = Values,

    #role_equip_affect_values{
        attack        = AttackCalc2,
        defense       = DefenseCalc2,
        stunt_attack  = StuntAttackCalc2,
        stunt_defense = StuntDefenseCalc2,
        magic_attack  = MagicAttackCalc2,
        magic_defense = MagicDefenseCalc2,
        max_health    = MaxHealth2
    } = get_player_equip_affect_values(PlayerItem),

    NewValues = #role_equip_affect_values{
        attack        = AttackCalc1 + AttackCalc2,
        defense       = DefenseCalc1 + DefenseCalc2,
        stunt_attack  = StuntAttackCalc1 + StuntAttackCalc2,
        stunt_defense = StuntDefenseCalc1 + StuntDefenseCalc2,
        magic_attack  = MagicAttackCalc1 + MagicAttackCalc2,
        magic_defense = MagicDefenseCalc1 + MagicDefenseCalc2,
        max_health    = MaxHealth1 + MaxHealth2
    },

    calc_role_equip_affcect_values(NewValues, NewList).


% 检查格子是否可以容纳物品的格子列表
% 自动寻找适合的格子
%-OK
get_pack_avaliable_grid_list(ItemId, Level, Number) ->
    List = db:find(
        player_item,
        fun(Row) ->
            GridId = Row #player_item.grid_id,

            if
                GridId > 100 ->
                    % 非背包格子
                    false;
                true ->
                    % 背包格子
                    TestItemId  = Row #player_item.item_id,
                    TestItemLvl = Row #player_item.upgrade_level,

                    if
                        TestItemId == ItemId andalso TestItemLvl == Level ->
                            TestItemNum = get_player_item_number(Row),
                            MaxNum      = get_item_max_repeat(TestItemId),

                            if
                                TestItemNum + Number =< MaxNum ->
                                    true;
                                true ->
                                    false
                            end;
                        true ->
                            false
                    end
            end
        end
    ),
    lists:keysort(#player_item.grid_id, List).


% 物品已经全部放入
put_items_into_grids(ItemId, 0, _GridList, _GridIds, NumSuccess) ->
    [
        {ok, ItemId, NumSuccess}
    ];

% 没有剩余的格子可用
put_items_into_grids(ItemId, Number, [], [], NumSuccess) ->
    [
        {ok, ItemId, NumSuccess},
        {err, ItemId, Number}
    ];

% 还有物品放入
put_items_into_grids(ItemId, Remain, [], GridIds, NumSuccess) ->
    GridId = lists:nth(1, GridIds),

    MaxRepeat = get_item_max_repeat(ItemId),

    PutNum = if
        Remain > MaxRepeat ->
            MaxRepeat;
        true ->
            Remain
    end,

    put_item_into_grid(ItemId, 1, PutNum, {new, GridId}),

    NewRemain   = Remain - PutNum,
    NewSuccess  = NumSuccess + PutNum,

    NewGridIds = lists:nthtail(1, GridIds),

    put_items_into_grids(ItemId, NewRemain, [], NewGridIds, NewSuccess);

put_items_into_grids(ItemId, Remain, GridList, GridIds, NumSuccess) ->
    PlayerItem  = lists:nth(1, GridList),

    GridId      = lists:nth(1, GridIds),

    ItemNumber  = get_player_item_number(PlayerItem),
    MaxRepeat   = get_item_max_repeat(ItemId),

    {PutGrid, PutNum, NewGridList, NewGridIds} = if
        GridId < PlayerItem #player_item.grid_id ->
            % 当前为空格子
            {
                GridId,
                if
                    Remain > MaxRepeat ->
                        MaxRepeat;
                    true ->
                        Remain
                end,
                GridList,
                lists:nthtail(1, GridIds)
            };
        true ->
            TestItemId = PlayerItem #player_item.item_id,
            TestLevel  = PlayerItem #player_item.upgrade_level,
            if
                TestItemId == ItemId andalso TestLevel == 1 ->
                    % 可以堆叠
                    AvaliableNumber = MaxRepeat - ItemNumber,

                    {
                        PlayerItem,
                        if
                            Remain > AvaliableNumber ->
                                AvaliableNumber;
                            true ->
                                Remain
                        end,
                        lists:nthtail(1, GridList),
                        lists:nthtail(1, GridIds)
                    };
                true ->
                    % 不可堆叠
                    {
                        PlayerItem,
                        0,
                        lists:nthtail(1, GridList),
                        lists:nthtail(1, GridIds)
                    }
            end
    end,

    if
        PutNum > 0 ->
            put_item_into_grid(ItemId, 1, PutNum, PutGrid);
        true ->
            noop
    end,

    NewRemain   = Remain - PutNum,
    NewSuccess  = NumSuccess + PutNum,

    put_items_into_grids(ItemId, NewRemain, NewGridList, NewGridIds, NewSuccess).



put_item_into_grid(ItemId, Level, Number, Grid) ->
    NewGridId = case Grid of
        {new, GridId} ->
            % 空格子直接放入
            PlayerItem = player_item_new_item(GridId, ItemId, Level, Number),
            PlayerItem #player_item.grid_id;
        _ ->
            % 增加数量
            NewNumber = get_player_item_number(Grid) + Number,
            player_item_set_number(Grid, NewNumber),
            Grid #player_item.grid_id
    end,
    NewGridId.


player_item_new_item(GridId, ItemId, Level, Number) ->
    PlayerId = mod_player:get_player_id(),

    PlayerItemGrid  = #player_item{
        player_id       = PlayerId,
        grid_id         = GridId,
        item_id         = ItemId,
        number          = Number,
        upgrade_level   = Level,
        player_role_id  = null
    },

    {ok, NewRow} = db:insert(PlayerItemGrid),

    NewRow.
