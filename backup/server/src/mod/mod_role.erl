-module(mod_role).
-include_lib("stdlib/include/ms_transform.hrl").
-include("api_00.hrl").
-include("db.hrl").
-include("ets_logic.hrl").

-export([
    get_role_stunt_type_by_id/1,
    get_role_attack_range/1,
    get_role_stunt_by_id/1,
    get_role_info/1,
    get_town_info/0,
    get_current_player_roles/0,
    get_current_player_role_war_attrs/0,
    get_role_list/0,
    increase_RoleExperience/2,
    get_rolenameby_roleid/1
]).


-export([
    get_player_role_name/2,
    get_player_role_job/2
]).

%% 根据战法类型ID获取战法类型
get_role_stunt_type_by_id (RoleStuntTypeId) ->

    db:get(role_stunt_type, RoleStuntTypeId).
    
%% 根据战法ID获取战法信息
get_role_stunt_by_id (RoleStuntId) ->
    db:get(role_stunt, RoleStuntId).

%% 根据攻击范围ID获取攻击范围
get_role_attack_range (RoleAttackRangeId) ->

    db:get(role_attack_range, RoleAttackRangeId).

%% -----------------------------------------------------------------------------
%% Function: get_role_info(PlayerRoleId) -> List
%%			PlayerRoleId: 玩家角色ID
%% Descrip:  获取角色信息
%% -----------------------------------------------------------------------------
get_role_info(PlayerRoleId) ->

    %%  Player_Role_War_Attrs = get_current_player_role_war_attrs(),  
    %%  io:format ("Player_Role_War_Attrs ~p ~n ",[Player_Role_War_Attrs]),
    
    Finds = [
        PlayerRole || PlayerRole <- db:get(player_role), 
        PlayerRole#player_role.id == PlayerRoleId
    ],
    case Finds of 
        [] -> 
            0;
        [Player_Role] ->
            RoleExperience  = Player_Role #player_role.experience,
            Level           = Player_Role #player_role.level,
            Health          = Player_Role #player_role.health,
            
            [Player]          = db:get (player),
            Role = db:get(role, Player_Role #player_role.role_id),
            JobId = Role #role.role_job_id,
            RoleStuntId = Role #role.role_stunt_id,
            Job = db:get(role_job, JobId),
            
            JobName = Job #role_job.name,
            

            RoleJobLevelData = db:get(role_job_level_data, {JobId, Level + 1}),
            MaxExperience = RoleJobLevelData #role_job_level_data.require_exp,
            

            
            PlayerRoleDataList = [
                PlayerRoleData || PlayerRoleData <- db:get(player_role_data),
                PlayerRoleData #player_role_data.player_role_id == PlayerRoleId
            ],
            case PlayerRoleDataList of 
                [] ->
                    <<1:32>>;
                [PlayerRoleData] ->
                    Strength    = PlayerRoleData #player_role_data.strength,

                    Agile       = PlayerRoleData #player_role_data.agile,
                    Intellect   = PlayerRoleData #player_role_data.intellect,
                    RoleStunt = db:get(role_stunt,RoleStuntId),
                    
                    MaxHealth     = PlayerRoleData #player_role_data.max_health,
                    
                    Role_stunt_type_id = RoleStunt #role_stunt.role_stunt_type_id,
                    Role_stunt_type = db:get (role_stunt_type,Role_stunt_type_id),
                    StuntType   = Role_stunt_type #role_stunt_type.name,

                    Live_type = 0,
                    Factions  =  " ",
                    Intimacy  = 0,

                    {
                        Player #player.main_role_id,
                        PlayerRoleId,
                        RoleExperience,
                        MaxExperience,
                        Level,
                        JobName,
                        StuntType,
                        Health,
                        MaxHealth,
                        Strength,
                        Agile,
                        Intellect,
                        Live_type,
                        Factions,
                        Intimacy
                    }
            end
    end.
    
%% -----------------------------------------------------------------------------
%% Function: get_town_info() -> List
%% Descrip:  获取城镇玩家信息

%% -----------------------------------------------------------------------------
get_town_info() ->
    [Player] = db:get (player),
    PlayerName = Player #player.nickname,
    PlayerRoleId = Player #player.main_role_id,
    PlayerRole = db:get(player_role),   
    [MianRole] = [
                   PR || PR <- PlayerRole,
                   PR #player_role.id == PlayerRoleId
                 ],
    Level = MianRole #player_role.level,
    [PlayerData] = db:get(player_data),
    Ingot = PlayerData #player_data.ingot,
    Coins = PlayerData #player_data.coins,
    Medical = PlayerData #player_data.medical,
    Power = PlayerData #player_data.power,
    {
        PlayerName,
        Level,
        Ingot,
        Coins,
        Medical,
        Power
    }.


%% -----------------------------------------------------------------------------
%% Function: get_current_player_roles() -> List
%% Descrip:  获取玩家角色ID列表 [1,2,3,4]
%% -----------------------------------------------------------------------------

get_current_player_roles() ->
    PlayerRole = db:get(player_role),   
    case PlayerRole of
        undefined -> [];
        _ ->
            lists:foldl(
                    fun(PRole, Result) -> 
                        RoleId = PRole #player_role.role_id,
                        [RoleId | Result]
                    end
                    , 
                    [], 
                    PlayerRole
                    )
    end.

 
%% -----------------------------------------------------------------------------
%% Function: get_current_player_role_war_attrs() -> List
%% RETURN       #player_role_war_attribute
%% Descrip:   获取角色属性(角色属性值包含玩家装备等附加属性值)
%% -----------------------------------------------------------------------------
get_current_player_role_war_attrs() ->


    PlayerRoleList  = db:get(player_role),   
    Discount    = [1, 0.7, 1, 0.7, 1, 0.7],     % 折算(攻击力,防御力,战法攻击力,战法防御力,技攻击力,绝技防御力)
    Decrease    = [50, 35, 50, 35, 50, 35],     % 减值(攻击力,防御力,战法攻击力,战法防御力,技攻击力,绝技防御力)
    [Player]    = db:get (player),
    DefautDeployModeId  = Player #player.deploy_mode_id,

    PlayerDeployGrid    = db:get (player_deploy_grid),
    PDGList  = [
                X || X <- PlayerDeployGrid,
                X #player_deploy_grid.deploy_mode_id == DefautDeployModeId
                ],
    io:format ("PDGList ~p ~n ", [PDGList]),
    lists:foldl(
        fun(PDGL, Result) ->
            PlayerRoleId    =   PDGL #player_deploy_grid.player_role_id,
            io:format ("PlayerRoleId ~p ~n ", [PlayerRoleId]),
            Playerroles      =   [
                                    X || X <- PlayerRoleList,
                                    X #player_role.id == PlayerRoleId
                                    ],
            case Playerroles of 
                [] -> Result;
                [Playerrole] ->

                RoidId = Playerrole #player_role.role_id,

                %PlayerRoleId        = Playerrole #player_role.id,
                Role                = db:get (role, RoidId),
                RoleName            = Role #role.name,
                RoleSign            = Role #role.sign,
                RoleJobId           = Role #role.role_job_id,
                RoleStuntId         = Role #role.role_stunt_id,
                RoleStunt           = db:get(role_stunt,RoleStuntId),
                RoleJob             = db:get (role_job, RoleJobId),
                Rolestunttypeid     = RoleStunt #role_stunt.role_stunt_type_id,
                Strikingrangeid     = RoleJob #role_job.role_attack_range_id,
                RoleJobSign         = RoleJob #role_job.sign,
                
                Rolestrikingrange   = db:get (role_attack_range, Strikingrangeid),
                Strikingrange       = Rolestrikingrange #role_attack_range.sign,
                
                RolestunttypeTable  = db:get (role_stunt_type, Rolestunttypeid),
                Rolestunttype       =  RolestunttypeTable #role_stunt_type.sign,

                #role_attack_range{
                    sign = RoleAttackRange
                } =
                db:get(
                    role_attack_range,
                    RoleStunt #role_stunt.role_attack_range_id
                ),

                Health              = Playerrole #player_role.health,
                [PlayerRoleData] = [
                    PRD || PRD <- db:get (player_role_data),
                    PRD #player_role_data.player_role_id == PlayerRoleId
                ],

                Rolestuntsign       = RoleStunt #role_stunt.sign,
                Rolestuntname       = RoleStunt #role_stunt.name,
                
                
                Strength            = PlayerRoleData #player_role_data.strength,    % 武力
                Agile               = PlayerRoleData #player_role_data.agile,       % 绝技
                Intellect           = PlayerRoleData #player_role_data.intellect,   % 法术
                
                AttackAdded              = lib_misc:ceil (Strength * lists:nth (1, Discount) - lists:nth (1, Decrease)),
                DefenseAdded             = lib_misc:ceil (Strength * lists:nth (2, Discount) - lists:nth (2, Decrease)),
                
                MagicattackAdded         = lib_misc:ceil (Agile * lists:nth (3, Discount) - lists:nth (3, Decrease)),
                MagicdefenseAdded        = lib_misc:ceil (Agile * lists:nth (4, Discount) - lists:nth (4, Decrease)),

                
                StuntattackAdded         = lib_misc:ceil (Intellect * lists:nth (5, Discount) - lists:nth (5, Decrease)),
                StuntdefenseAdded        = lib_misc:ceil (Intellect * lists:nth (6, Discount) - lists:nth (6, Decrease)),
                
                Attack              = PlayerRoleData #player_role_data.attack + AttackAdded,
                Defense             = PlayerRoleData #player_role_data.defense + DefenseAdded,
                Magicattack         = PlayerRoleData #player_role_data.magic_attack + MagicattackAdded,
                Magicdefense        = PlayerRoleData #player_role_data.magic_defense + MagicdefenseAdded,
                Stuntattack         = PlayerRoleData #player_role_data.stunt_attack + StuntattackAdded,
                Stuntdefense        = PlayerRoleData #player_role_data.stunt_defense + StuntdefenseAdded,
                
                
                Hit                 = PlayerRoleData #player_role_data.hit,
                Block               = PlayerRoleData #player_role_data.block,
                Dodge               = PlayerRoleData #player_role_data.dodge,
                Critical            = PlayerRoleData #player_role_data.critical,
                % 座标
                PlayerdeploygridList    = db:get(player_deploy_grid),
                [Player_deploy_grid]    = [ 
                                                    PDG || PDG <- PlayerdeploygridList,
                                                    PDG #player_deploy_grid.player_role_id == PlayerRoleId
                                          ],
                Deploygridid            = Player_deploy_grid #player_deploy_grid.deploy_grid_id,
                Deploygrid              = db:get (deploy_grid, Deploygridid),
                Deploygridtypeid        = Deploygrid #deploy_grid.deploy_grid_type_id,
                Deploygridtype          = db:get (deploy_grid_type, Deploygridtypeid),
                Position                = Deploygridtype #deploy_grid_type.name,
                %  #role_equip_affect_values{}
                RoleEquipAffectValues = mod_item:get_role_equip_affect_values(PlayerRoleId),

                Player_role_war_attribute = #player_role_war_attribute
                {
                    role_id                 = RoidId ,                  % 角色ID
                    role_sign               = RoleSign,                 % 角色标识
                    role_name               = RoleName,                 % 角色名称
                    role_level              = Playerrole #player_role.level, % 角色等级
                    role_job_sign           = RoleJobSign,              % 所属职业标识
                    health                  = Health,                   % 生命值
                    attack                  = Attack + RoleEquipAffectValues #role_equip_affect_values.attack,                      % 攻击
                    defense                 = Defense + RoleEquipAffectValues #role_equip_affect_values.defense,                    % 防御
                    magic_attack            = Magicattack + RoleEquipAffectValues #role_equip_affect_values.magic_attack,           % 法术攻击
                    magic_defense           = Magicdefense + RoleEquipAffectValues #role_equip_affect_values.magic_defense,         % 法术防御
                    stunt_attack            = Stuntattack + RoleEquipAffectValues #role_equip_affect_values.stunt_attack,           % 绝技攻击
                    stunt_defense           = Stuntdefense + RoleEquipAffectValues #role_equip_affect_values.stunt_defense,         % 绝技防御
                    hit                     = Hit,                  % 命中
                    block                   = Block,                % 格挡
                    dodge                   = Dodge,                % 闪避
                    critical                = Critical,             % 暴击
                    attack_range            = Strikingrange,        % 攻击范围
                    role_stunt_type         = Rolestunttype,        % 战法类型
                    role_stunt              = Rolestuntsign,        % 战法
                    role_stunt_attack_range = RoleAttackRange,      % 战法攻击范围
                    role_stunt_name         = Rolestuntname,        % 战法名称
                    position                = Position              % 坐标
                },
                [Player_role_war_attribute | Result]
            end
        end, 
        [], 
        PDGList
    ).



%% -----------------------------------------------------------------------------
%% Function: get_town_info() -> List
%% Descrip:  获取玩家列表
%% -----------------------------------------------------------------------------
get_role_list() ->

    PlayerRole_list = db:get(player_role),      
    [Player] = db:get (player),
    RoleList = lists:foldl(
        fun (PlayerRole, Result) ->
            PlayerRoidId = PlayerRole #player_role.id,
            Level   = PlayerRole #player_role.level,
            Roleid = PlayerRole #player_role.role_id,
            Role            = db:get (role, Roleid),
            RoleName       = Role #role.name,
            MainRoleId      = Player #player.main_role_id,
            [{RoleName, MainRoleId, PlayerRoidId, Roleid, Level} | Result ]
            
        end
        ,
        []
        ,
        PlayerRole_list
    ),
    lists:reverse(RoleList). 

%% -----------------------------------------------------------------------------
%% Function: get_rolenameby_roleid(RoldId) -> RoleName
%% Descrip:  根据角色ID获取角色名称
%% -----------------------------------------------------------------------------
get_rolenameby_roleid(RoleId) ->
    Role = db:get (role,RoleId),
    Role #role.name.

%% -----------------------------------------------------------------------------
%% Function: increase_RoleExperience(PlayerRoleId, ExperienceValue) -> Experience
%%  RoleId  : RoleId   角色ID
%% Descrip:  添加玩家角色经验
%% -----------------------------------------------------------------------------
increase_RoleExperience(RoleId, ExperienceValue) ->

    PlayerRoles = player_role_by_roleid (RoleId),

    case PlayerRoles of 
        []  -> 0;
        _ ->
            PlayerRoleId    = PlayerRoles #player_role.id,

            Level   = PlayerRoles #player_role.level,
            Role    = db:get (role, RoleId),
            JobId   = Role #role.role_job_id,
            Experience = PlayerRoles #player_role.experience + ExperienceValue,

            RoleJobLevelData = db:get (role_job_level_data, {JobId, Level}),
            MaxExperience = RoleJobLevelData #role_job_level_data.require_exp,

            if MaxExperience =<  Experience ->
                    % 超过当前经验等级 升级
                    
                    CurLevel    = Level + 1,
                    CurExp      = Experience - MaxExperience ,
                    role_upgrade (PlayerRoleId, JobId, CurLevel),

                    mod_player:update_player_data(?PLAYER_LEVEL),
                    mod_player:update_player_data(?PLAYER_MAX_EXPERIENCE);
                true ->
                    CurLevel    = Level ,
                    CurExp      = Experience
            end,

            db:update(
                player_role, 
                fun(Row = #player_role{id = RecordRoleId}) ->
                    if RecordRoleId == PlayerRoleId ->
                            Row #player_role{experience = CurExp, level = CurLevel};
                        true -> false
                    end
                end
            ),

            mod_player:update_player_data(?PLAYER_EXPERIENCE),
            
            NewPlayerRole = player_role_by_roleid (RoleId),

            NewPlayerRole #player_role.experience
    end.
        
player_role_by_roleid(RoleId) ->
    PlayerRoles = [
        PlayerRole || PlayerRole <- db:get(player_role), 
        PlayerRole#player_role.role_id == RoleId
    ],
    case PlayerRoles of 
        [] -> [];
        [Player_Role] ->  Player_Role
    end.

%%角色升级
role_upgrade (PlayerRoleId, JobId, Level) ->
    RoleJobLevelData = db:get (role_job_level_data, {JobId, Level}),
    db:update(
                player_role_data, 
                fun(Row = #player_role_data{player_role_id = RecordRoleId}) ->
                    if RecordRoleId == PlayerRoleId 
                        ->
                            Row #player_role_data{
                                            attack          = Row #player_role_data.attack + RoleJobLevelData #role_job_level_data.attack,
                                            defense         = Row #player_role_data.defense + RoleJobLevelData #role_job_level_data.defense,
                                            stunt_attack    = Row #player_role_data.stunt_attack + RoleJobLevelData #role_job_level_data.stunt_attack,
                                            stunt_defense   = Row #player_role_data.stunt_defense + RoleJobLevelData #role_job_level_data.stunt_defense,
                                            magic_attack    = Row #player_role_data.magic_attack + RoleJobLevelData #role_job_level_data.magic_attack,
                                            magic_defense   = Row #player_role_data.magic_defense + RoleJobLevelData #role_job_level_data.magic_defense,
                                            max_health      = Row #player_role_data.max_health + RoleJobLevelData #role_job_level_data.max_health,
                                            critical        = Row #player_role_data.critical + RoleJobLevelData #role_job_level_data.critical,
                                            dodge           = Row #player_role_data.dodge + RoleJobLevelData #role_job_level_data.dodge,
                                            hit             = Row #player_role_data.hit + RoleJobLevelData #role_job_level_data.hit,
                                            block           = Row #player_role_data.block + RoleJobLevelData #role_job_level_data.block
                                            };
                        true -> false
                    end
                end
            ).
    

get_player_role_name(PlayerId, PlayerRoleId) ->
    case mod_player:get_main_role_id() of
        PlayerRoleId ->
            mod_player:get_player_name();
        _ ->
            List = [
                Row || Row <- db:get(player_role),
                Row #player_role.id == PlayerRoleId
                andalso Row #player_role.player_id == PlayerId
            ],

            case List of
                [] ->
                    false;
                [PlayerRole] ->
                    Role = db:get(role, PlayerRole #player_role.role_id),
                    Role #role.name
            end
    end.


get_player_role_job(PlayerId, PlayerRoleId) ->
    List = [
        Row || Row <- db:get(player_role),
        Row #player_role.id == PlayerRoleId
        andalso Row #player_role.player_id == PlayerId
    ],

    case List of
        [] ->
            false;
        [PlayerRole] ->
            Role = db:get(role, PlayerRole #player_role.role_id),
            Role #role.role_job_id
    end.