%% player_deploy_grid  表增加 deploy_mode_id字段
%% 初始化数据增加 player_deploy_grid 加入初始值
-module(mod_player).

-include_lib("stdlib/include/ms_transform.hrl").

-include("db.hrl").
-include("api_00.hrl").
-include("ets_logic.hrl").

-define(MAX_POWER, 100).

-export([
    login/2,

    check_coin/1,
    increase_coin/1,
    decrease_coin/1,
    check_ingot/1,
    decrease_ingot/1,
    get_player_info/0,

    player_first_init/3,

    get_player_id/0,
    get_main_role_id/0,
    get_player_name/0,
    get_player_role/0,
    get_player_pack_keys/0,
    add_pack_grid_key/0,
    add_role_equi_key/0,
    add_warehouse_grid_key/0,
    decrease_skill/1,
    increase_role_num/0,
    
    get_cd_time_props/1,
    set_cd_time/2,
    clean_cd_time/1,

    get_player_medical/0,
    set_medical/1,
    decrease_medical/1,

    get_player_data/1,

    increase_power/1,
    decrease_power/1,

    update_player_data/1
]).

login (Name, HashCode) ->


    HashCode1 = string:to_lower(binary_to_list(HashCode)),

    HashCode2 = lists:concat([
        lists:flatten(io_lib:format("~2.16.0b", [A])) || A <- binary_to_list(
            erlang:md5("{FE2EA79B-CF9D-42F9-B554-001F9A3942B8}" ++ Name)
        )
    ]),

    Username = binary_to_list (Name),

    case string:equal(HashCode1, HashCode2) of
        true ->
            GPBU    = get_playerid_by_username(Name),
            case  GPBU of
                null ->
                    %% insert into player
                    Player = #player {
                        id              = 0,
                        username        = Username,
                        nickname        = Username,
                        role_num        = 1,
                        max_role_num    = 5,
                        max_power       = 100,
                        town_key        = 1,
                        quest_key       = 1,
                        section_key     = 1,
                        main_role_id    = 0,
                        pack_grid_key   = 18,
                        role_equi_key   = 6,
                        warehouse_key   = 18,
                        research_key    = 1,
                        signature       = " ",
                        lately_caontact = "",
                        deploy_mode_id  = 1
                    },
                    
                    case db:insert (Player) of  
                        {ok, Result} ->
                            Player_Id = Result #player.id,
                            %% insert into player_data
                            Player_Data = #player_data{
                                player_id    = Player_Id,
                                ingot        = 9999,
                                coins        = 9999,
                                fame         = 0,
                                power        = 100,
                                skill        = 5000,
                                medical      = 0,
                                last_town_id = 0,
                                last_pos_x   = 0,
                                last_pos_y   = 0
                            },

                            db:insert(Player_Data),
                            
                            {?FIRST_TIME, Player_Id, undefined};
                        _ ->
                             {?FAILED, 0}
                    end;
                
                {exits, PlayerIds} ->
                    {?FIRST_TIME, PlayerIds, undefined};
                PlayerId ->
                    db:init(player, PlayerId),
                    [Player] = db:get(player),
                
                    {?SUCCEED, PlayerId, Player#player.nickname}
            end;
        false ->

            {?FAILED, 0}
    end.
    
    
player_first_init (PlayerId, RoleId, NickName) ->
    %% insert into player_role
    Role = db:get (role, RoleId),
    RoleJobId = Role #role.role_job_id,
    RoleJobLevelData = db:get (role_job_level_data, {RoleJobId, 1}),
    PlayerRoles = #player_role {
        id          = 0,
        player_id   = PlayerId,
        role_id     = RoleId,
        level       = 1,
        experience  = 0,
        health      = RoleJobLevelData #role_job_level_data.max_health,
        state       = 0
    },  
    case db:insert(PlayerRoles) of 
        {ok, New_Player_Role} ->
            MianRoleId = New_Player_Role #player_role.id,
            
            Roleidb = list_to_binary(integer_to_list(MianRoleId)),
            
            Pid = list_to_binary(integer_to_list(PlayerId)),
            
            % insert player_role_data 

            Sql = <<
                        "UPDATE `player` SET ",
                        " `nickname`", " = '", NickName/binary, 
                        "',`main_role_id`", " = ", Roleidb/binary, 
                        " WHERE ",
                        "`id`", " = ", Pid/binary,
                        ";"
                    >>,
            mysql:fetch(mysql_pool, [Sql]),

            Player_Role_Data = #player_role_data{
                    player_role_id  = MianRoleId,
                    player_id       = PlayerId,
                    strength        = Role #role.strength,
                    agile           = Role #role.agile,
                    intellect       = Role #role.intellect,
                    attack          = RoleJobLevelData #role_job_level_data.attack,
                    defense         = RoleJobLevelData #role_job_level_data.defense,
                    stunt_attack    = RoleJobLevelData #role_job_level_data.stunt_attack,
                    stunt_defense   = RoleJobLevelData #role_job_level_data.stunt_defense,
                    magic_attack    = RoleJobLevelData #role_job_level_data.magic_attack,
                    magic_defense   = RoleJobLevelData #role_job_level_data.magic_defense,
                    max_health      = RoleJobLevelData #role_job_level_data.max_health,
                    critical        = RoleJobLevelData #role_job_level_data.critical,
                    dodge           = RoleJobLevelData #role_job_level_data.dodge,
                    hit             = RoleJobLevelData #role_job_level_data.hit,
                    block           = RoleJobLevelData #role_job_level_data.block
            },

            insert(Player_Role_Data),
            % insert player_deploy_grid
            PlayerDeployGrid = #player_deploy_grid{
                                    player_role_id  =   MianRoleId,
                                    deploy_grid_id  = 1,
                                    player_id       = PlayerId,
                                    deploy_mode_id  = 1
                                },
            insert (PlayerDeployGrid),
            
            
            % insert player_deploy_mode
            
            SqlDeployGrid = <<"SELECT * FROM `deploy_grid` where `require_level` = 0">>,
            {data, ResultId} = mysql:fetch(mysql_pool, [SqlDeployGrid]),
            Result = lib_mysql:get_rows(ResultId),
            lists:foldr(
                fun(List, Rusult) ->
                    {deploy_mode_id,      DeployModeId}        = lists:keyfind(deploy_mode_id,      1, List),
                    case Rusult of 
                        []   -> IsDefault = 1;
                        _    -> IsDefault = 0
                    end,
                    PlayerDeployMode = #player_deploy_mode{
                                        player_id = PlayerId,
                                        deploy_mode_id = DeployModeId,
                                        level = 0,
                                        is_default = IsDefault
                                },
                    insert (PlayerDeployMode),
                    [PlayerDeployMode | Rusult]
                end,
                [],
                Result
            ),

            db:init(player, PlayerId),
            %接第一个任务
            mod_quest:accept_quest(PlayerId , 1),
            {?SUCCEED, PlayerId};
        _ ->
            io:format ("failed" ),
            {?FAILED, PlayerId}
    end.


get_player_info () ->

    [Player]        = db:get(player),

    [PlayerData]    = db:get(player_data),

    PlayerRoleId    = Player #player.main_role_id,
    [PlayerRole]    = [
        PR || PR <- db:get(player_role), 
        PR #player_role.id == PlayerRoleId
    ],
    

    NickName    = Player #player.nickname,
    Level       = PlayerRole #player_role.level,
    Experience  = PlayerRole #player_role.experience,
    Health      = PlayerRole #player_role.health,
    Ingot       = PlayerData #player_data.ingot,
    Coins       = PlayerData #player_data.coins,
    Power       = PlayerData #player_data.power,
    

    [PlayerRoleData] = [
                PRD || PRD <- db:get (player_role_data),
                PRD #player_role_data.player_role_id == PlayerRole #player_role.id
    ],

    MaxHealth   = PlayerRoleData #player_role_data.max_health,
    % 最大经验
    Role = db:get(role, PlayerRole #player_role.role_id),
    JobId = Role #role.role_job_id,
    RoleJobLevelData = db:get(role_job_level_data, {JobId, Level + 1}),
    MaxExperience = RoleJobLevelData #role_job_level_data.require_exp,

    % 城镇位置信息
    TownId = PlayerData #player_data.last_town_id,
    Position_x = PlayerData #player_data.last_pos_x,
    Position_y = PlayerData #player_data.last_pos_y,
    
    {NickName, Level, Ingot, Coins, Health, MaxHealth, Power, Experience, MaxExperience, TownId, Position_x, Position_y}.


get_playerid_by_username(UserName) ->
    Sql = <<
        "SELECT  `id`, `main_role_id` FROM player WHERE username = '" ,UserName/binary ,"' LIMIT 1" >>,

    Mysql_Query = mysql:fetch(mysql_pool, [Sql]),

    case Mysql_Query of 
        {data, Result} ->

            case lib_mysql:get_rows(Result) of
				[[PlayerIds,MianRoleIds]] -> 
                    {id, PlayerId} = PlayerIds,
                    {main_role_id, MainRoleId} = MianRoleIds,
                    case MainRoleId of
                          0 ->  
                                {exits, PlayerId};
                          _ -> PlayerId
                    end;
                _ -> null
			end;
        _ ->    null
    end.

    
check_coin(Coins) ->
    [PlayerData] = db:get(player_data),

    if
        PlayerData #player_data.coins < Coins ->
            false;
        true ->
            true
    end.
    

increase_coin(Coins)->
    Result = db:update(
        player_data,
        fun(Row) ->
            NewCoins = Row # player_data.coins + Coins,
            Row #player_data{coins = NewCoins}
        end
    ),
    update_player_data(?PLAYER_COINS),
    Result.


decrease_coin(Coins) ->
    Result = db:update(
        player_data,
        fun(Row) ->
            NewCoins = Row # player_data.coins - Coins,
            Row #player_data{coins = NewCoins}
        end
    ),
    update_player_data(?PLAYER_COINS),
    Result.


check_ingot(Ingots) ->
    [PlayerData] = db:get(player_data),

    if
        PlayerData #player_data.ingot< Ingots ->
            false;
        true ->
            true
    end.


decrease_ingot(Ingots) ->
    Result = db:update(
        player_data,
        fun(Row) ->
            Intot = Row # player_data.ingot - Ingots,
            Row #player_data{ingot = Intot}
        end
    ),
    update_player_data(?PLAYER_INGOT),
    Result.

    
%增加玩家角色数量
increase_role_num()->
    db:update(
        player,
        fun(Row) ->
            if Row # player.role_num == Row # player.max_role_num ->
                    false;
                true ->
                    NewRoleNum = Row # player.role_num + 1,
                    Row #player{role_num = NewRoleNum}
            end
        end
    ).


% 减少阅历
decrease_skill(Skill) ->
    Result = db:update(
        player_data,
        fun(Row) ->
            Skills = Row # player_data.skill - Skill,
            if Skills < 0 ->
                    false;
                true ->
                    Row #player_data{skill = Skills}
            end
        end
    ),
    Result.


%% 增加体力
increase_power (Power) ->

    [#player_data{power = PlayerPower}] = db:get(player),
    NewPower = max(PlayerPower + Power, ?MAX_POWER),
    Result = if
        NewPower > PlayerPower ->
            db:update(
                player_data,
                fun(Row) ->
                    Row #player_data{power = NewPower}
                end
            ),
            NewPower;
        true ->
            PlayerPower
    end,
    update_player_data(?PLAYER_POWER),
    Result.

%% 扣除体力
decrease_power (Power) ->

    [#player_data{power = PlayerPower}] = db:get(player_data),
    Result = if
        Power =:= 0 ->
            PlayerPower;
        Power > 0, PlayerPower >= Power ->
            RemainPower = PlayerPower - Power,
            db:update(
                player_data,
                fun(Row) ->
                    Row #player_data{power = RemainPower}
                end
            ),
            RemainPower;
         true ->
             false
    end,
    update_player_data(?PLAYER_POWER),
    Result.

    
% 获得玩家ID    
get_player_id() ->
    [Player] = db:get (player),
    Player #player.id.

get_main_role_id() ->
    [Player] = db:get (player),
    Player #player.main_role_id.

% 获得玩家呢称
get_player_name() ->
    [Player] = db:get (player),
    Player #player.nickname.
    
% 获取玩家物品相关解锁key
get_player_pack_keys () ->
    [Player] = db:get (player),
    {
        Player #player.pack_grid_key,
        Player #player.role_equi_key,
        Player #player.warehouse_key
    }.


% 增加玩家背包格子解锁权限
add_pack_grid_key() ->
    db:update(
            player,
            fun(Row) ->
                Pack_grid_key = Row # player.pack_grid_key + 1,
                Row #player{pack_grid_key = Pack_grid_key}
            end
    ).


%玩家角色装备解锁权限
add_role_equi_key() ->
    db:update(
            player,
            fun(Row) ->
                Role_equi_key = Row # player.role_equi_key + 1,
                Row #player{role_equi_key = Role_equi_key}
            end
    ).


%玩家仓库格子解锁权限
add_warehouse_grid_key() ->
    db:update(
            player,
            fun(Row) ->
                Warehouse_key = Row # player.warehouse_key + 1,
                Row #player{warehouse_key = Warehouse_key}
            end
    ).


% 冷却时间相关

% 获取冷却时间类别属性
% get_cd_time_props (CdTypeId) -> {Time, Ingot}
get_cd_time_props (CdTypeId) ->
    CdTime = get_cd_time(CdTypeId),

    case CdTime of
        false ->
            {0, 0};
        _ ->
            CurrTimeStamp = lib_misc:get_local_timestamp(),
            if CdTime #player_cd_time.expire_time < CurrTimeStamp ->
                    {0, 0};
                true ->
                    CdType  = db:get(cd_time_type, CdTypeId),
                    Ratio   = CdType #cd_time_type.ingot_time_ratio,

                    RemainSeconds = CdTime #player_cd_time.expire_time - CurrTimeStamp,
                    Ingot   = lib_misc:ceil(RemainSeconds / Ratio),

                    {RemainSeconds, Ingot}
            end
    end.


% 设置冷却时间，成功true,失败false(旧CD时间不为零)
set_cd_time(CdTypeId, Seconds) ->
    CdTime = get_cd_time(CdTypeId),

    CurrTimeStamp = lib_misc:get_local_timestamp(),
    TimeStamp = CurrTimeStamp + Seconds,

    case CdTime of
        false ->
            PlayerCdTime = #player_cd_time{
                player_id   = get_player_id(),
                cd_type_id  = CdTypeId,
                expire_time = TimeStamp
            },
            db:insert(PlayerCdTime),
            true;
        CdTime when CdTime #player_cd_time.expire_time < CurrTimeStamp ->
            db:update(
                player_cd_time,
                fun(Row) ->
                    if
                        Row #player_cd_time.cd_type_id == CdTypeId ->
                            Row #player_cd_time{
                                expire_time = TimeStamp
                            };
                        true ->
                            false
                    end
                end
            ),
            true;
        CdTime ->
            % 旧CD时间还没过期
            false
    end.


% 冷却时间消费清零
% clean_cd_time(CdTypeId) -> false | integer()
%       false 金币不够
%       integer() 消费金币数
clean_cd_time(CdTypeId) ->
    CdTime = get_cd_time(CdTypeId),

    CurrTimeStamp = lib_misc:get_local_timestamp(),

    case CdTime of
        false ->
            0;
        CdTime when CdTime #player_cd_time.expire_time < CurrTimeStamp ->
            0;
        CdTime ->
            {_Seconds, Ingot} = get_cd_time_props(CdTypeId),

            case check_ingot(Ingot) of
                true ->
                    decrease_ingot(Ingot),
                    db:update(
                        player_cd_time,
                        fun(Row) ->
                            if
                                Row #player_cd_time.cd_type_id == CdTypeId ->
                                    Row #player_cd_time{
                                        expire_time = 0
                                    };
                                true ->
                                    false
                            end
                        end
                    ),
                    Ingot;
                false ->
                    false
            end

    end.


get_cd_time(CdTypeId) ->
    TimeList = db:get(player_cd_time),

    TypeTimeList = [Time || Time <- TimeList, Time #player_cd_time.cd_type_id == CdTypeId],

    case TypeTimeList of
        [] ->
            false;
        [Time] ->
            Time
    end.



insert (Row = #player_role_data{}) ->
    PlayerRoleId       = int_to_bin(Row #player_role_data.player_role_id),
    PlayerId       = int_to_bin(Row #player_role_data.player_id),
    Strength       = int_to_bin(Row #player_role_data.strength),
    Agile          = int_to_bin(Row #player_role_data.agile),
    Intellect      = int_to_bin(Row #player_role_data.intellect),
    Attack         = int_to_bin(Row #player_role_data.attack),
    Defense        = int_to_bin(Row #player_role_data.defense),
    StuntAttack    = int_to_bin(Row #player_role_data.stunt_attack),
    StuntDefense   = int_to_bin(Row #player_role_data.stunt_defense),
    MagicAttack    = int_to_bin(Row #player_role_data.magic_attack),
    MagicDefense   = int_to_bin(Row #player_role_data.magic_defense),
    MaxHealth      = int_to_bin(Row #player_role_data.max_health),
    Critical       = int_to_bin(Row #player_role_data.critical),
    Dodge          = int_to_bin(Row #player_role_data.dodge),
    Hit            = int_to_bin(Row #player_role_data.hit),
    Block          = int_to_bin(Row #player_role_data.block),

    Sql = <<
        "INSERT INTO `player_role_data` SET "
            " `player_role_id`",      " = ", PlayerRoleId/binary
            ," , `player_id`",      " = ", PlayerId/binary
            ,",`strength`",       " = ", Strength/binary
            ,",`agile`",          " = ", Agile/binary
            ,",`intellect`",      " = ", Intellect/binary
            ,",`attack`",         " = ", Attack/binary
            ,",`defense`",        " = ", Defense/binary
            ,",`stunt_attack`",   " = ", StuntAttack/binary
            ,",`stunt_defense`",  " = ", StuntDefense/binary
            ,",`magic_attack`",   " = ", MagicAttack/binary
            ,",`magic_defense`",  " = ", MagicDefense/binary
            ,",`max_health`",     " = ", MaxHealth/binary
            ,",`critical`",       " = ", Critical/binary
            ,",`dodge`",          " = ", Dodge/binary
            ,",`hit`",            " = ", Hit/binary
            ,",`block`",          " = ", Block/binary
    >>,

    Last_Insert_Id = lib_mysql:insert(mysql_pool, [Sql]),
    
    NewRow = Row #player_role_data{ player_role_id = Last_Insert_Id },

    {ok, NewRow};

    
insert (Row = #player_deploy_grid{}) ->
    PlayerRoleId   = int_to_bin(Row #player_deploy_grid.player_role_id),
    DeployGridId   = int_to_bin(Row #player_deploy_grid.deploy_grid_id),
    PlayerId       = int_to_bin(Row #player_deploy_grid.player_id),
    DeployModeId   = int_to_bin(Row #player_deploy_grid.deploy_mode_id),

    Sql = <<
        "INSERT INTO `player_deploy_grid` SET "
            ," `player_role_id`", " = ", PlayerRoleId/binary
            ,",`deploy_grid_id`", " = ", DeployGridId/binary
            ,",`player_id`",      " = ", PlayerId/binary
            ,",`deploy_mode_id`",      " = ", DeployModeId/binary
    >>,

    mysql:fetch(mysql_pool, [Sql]),
    ok;


insert (Row = #player_deploy_mode{}) ->
    PlayerId       = int_to_bin(Row #player_deploy_mode.player_id),
    DeployModeId   = int_to_bin(Row #player_deploy_mode.deploy_mode_id),
    Level          = int_to_bin(Row #player_deploy_mode.level),
    IsDefault      = int_to_bin(Row #player_deploy_mode.is_default),

    Sql = <<
        "INSERT INTO `player_deploy_mode` SET "
            ," `player_id`",      " = ", PlayerId/binary
            ,",`deploy_mode_id`", " = ", DeployModeId/binary
            ,",`level`",          " = ", Level/binary
            ,",`is_default`",     " = ", IsDefault/binary
    >>,

    mysql:fetch(mysql_pool, [Sql]),

    ok.

	
int_to_bin (null) ->
    <<"NULL">>;
int_to_bin (Value) ->
    list_to_binary(integer_to_list(Value)).


get_player_medical() ->
    get_player_data(?PLAYER_MEDICAL).


set_medical(SetNumber)->
    Result = db:update(
        player_data,
        fun(Row) ->
            NewMedical = Row # player_data.medical + SetNumber,
            Row #player_data{medical = NewMedical}
        end
    ),
    update_player_data(?PLAYER_MEDICAL),
    Result.


decrease_medical(Value) ->
    Result = db:update(
        player_data,
        fun(Row) ->
            Medical = Row # player_data.medical - Value,
            Row #player_data{medical = Medical}
        end
    ),
    update_player_data(?PLAYER_MEDICAL),
    Result.


get_player_data(?PLAYER_LEVEL) ->
    PlayerRole = get_player_role(),
    PlayerRole #player_role.level;

get_player_data(?PLAYER_INGOT) ->
    [PlayerData]    = db:get(player_data),
    PlayerData #player_data.ingot;

get_player_data(?PLAYER_COINS) ->
    [PlayerData]    = db:get(player_data),
    PlayerData #player_data.coins;

get_player_data(?PLAYER_HEALTH) ->
    PlayerRole = get_player_role(),
    PlayerRole #player_role.health;

get_player_data(?PLAYER_MAX_HEALTH) ->
    % TODO 迁到mod_role
    PlayerRole = get_player_role(),
    PlayerRoleId = PlayerRole #player_role.id,
    [PlayerRoleData] = [
                PRD || PRD <- db:get (player_role_data),
                PRD #player_role_data.player_role_id == PlayerRole #player_role.id
    ],

    RoleEquipAffectValues = mod_item:get_role_equip_affect_values(PlayerRoleId),
    AddMaxHealth = RoleEquipAffectValues #role_equip_affect_values.max_health,

    MaxHealth = PlayerRoleData #player_role_data.max_health + AddMaxHealth,

    MaxHealth;

get_player_data(?PLAYER_POWER) ->
    [PlayerData]    = db:get(player_data),
    PlayerData #player_data.power;

get_player_data(?PLAYER_MAX_POWER) ->
    [Player]        = db:get(player),
    Player#player.max_power;

get_player_data(?PLAYER_EXPERIENCE) ->
    PlayerRole = get_player_role(),
    PlayerRole #player_role.experience;

get_player_data(?PLAYER_MAX_EXPERIENCE) ->
    PlayerRole = get_player_role(),
    Level       = PlayerRole #player_role.level,
    get_player_max_experience(PlayerRole, Level);

get_player_data(?PLAYER_MEDICAL) ->
    [PlayerData]    = db:get(player_data),
    PlayerData #player_data.medical.


update_player_data(DataDef) ->
    Value = get_player_data(DataDef),
    OutBin = out_00:update_player_data(DataDef, Value),
    self() ! {send, OutBin}.


get_player_role() ->
    [Player]        = db:get(player),

    PlayerRoleId    = Player #player.main_role_id,
    [PlayerRole]    = [
        PR || PR <- db:get(player_role),
        PR #player_role.id == PlayerRoleId
    ],

    PlayerRole.


get_player_max_experience(PlayerRole, Level) ->
    Role                = db:get(role, PlayerRole #player_role.role_id),
    JobId               = Role #role.role_job_id,
    RoleJobLevelData    = db:get(role_job_level_data, {JobId, Level + 1}),

    RoleJobLevelData #role_job_level_data.require_exp.
