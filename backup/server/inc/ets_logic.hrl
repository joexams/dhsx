-record(
    town_player,        %城镇中的玩家
    {
        town_id,        %城镇ID
        process_id,     %玩家进程ID
        player_id,      %玩家ID
        nickname,       %玩家昵称
        player_role_id,  %玩家角色ID
        role_id,        %角色ID
        job_id          %玩家职业ID
    }
).

-record(
    player_position,
    {
        player_id,      %玩家ID
        pos_x,          %玩家X轴坐标
        pos_y           %玩家Y轴坐标
    }
).

%% -----------------------------------------------------------------------------
%% 副本
%%
-record(
    player_mission_process,
    {
        cur_mission_id,                 % 当前副本ID
        cur_mission_scene_id,           % 当前场景ID
        cur_mission_monster_team_id,    % 当前挑战怪物团
        cur_mission_scenes,             % 当前副本场景列表
        cur_scene_monster_teams,        % 当前场景怪物团列表
        cur_mission_monster_teams,      % 副本所有怪物团列表
        mission_scene_speed,            % 场景挑战进度
        monster_team_speed,             % 怪物团挑战进度
        is_mission_challenge_finished = false,  % 副本挑战是否完成
        award_list = {0, []}            % 宝箱物品列表
    }
).

%% -----------------------------------------------------------------------------
%% 战争
%%
-record(
    player_role_war_attribute,
    {
        role_id,             % 角色ID
        role_sign,           % 角色标识
        role_name,           % 角色名称
        role_level,          % 角色等级
        role_job_sign,       % 所属职业标识
        health,              % 生命值
        attack,              % 攻击
        defense,             % 防御
        magic_attack,        % 法术攻击
        magic_defense,       % 法术防御
        stunt_attack,        % 绝技攻击
        stunt_defense,       % 绝技防御
        hit,                 % 命中
        block,               % 格挡
        dodge,               % 闪避
        critical,            % 暴击
        attack_range,        % 攻击范围
        role_stunt_type,     % 战法类型
        role_stunt,          % 战法
        role_stunt_attack_range, % 战法攻击范围
        role_stunt_name,     % 战法名称
        fate,                % 命格
        fate_probability = 0,% 命格概率
        position             % 坐标
    }
).

-record(
    war_player_data,
    {
        player_id = 0,   % 玩家ID
        player_user_name = "",% 玩家名称
        player_nick_name = "" % 玩家昵称
    }
).

-record(
    war_player_role_data,
    {
        player_data = #war_player_data{},
        roles = [],  % 角色列表
        role_attribute = [#player_role_war_attribute{}]
    }
).

-record(
    war_param,
    {
        attack  = #war_player_role_data{},
        defense = #war_player_role_data{},
        role_attribute = [#player_role_war_attribute{}]
    }
).

-record(
    role_process_param,
    {
        player_id   = 0, % 玩家ID
        army        = "attack", % 所属军队
        left_health = 0, % 剩余生命值
        momentum    = 0, % 气势
        be_stunt    = {"", null} % 被施加战法
    }
).

%% -----------------------------------------------------------------------------
%% 物品
%%
-record(
    recycle_player_item,   % 回购物品
    {
        ets_key,        % {player_id, id}
        player_id,      % ID
        id,             % ID
        item_id,        % 物品ID
        upgrade_level,  % 物品等级
        number,         % 物品等级
        expire_time     % 过期时间,gregorian_seconds - 62167219200 = UNIX_TIMESTAMP
     }
 ).

-record(
    role_equip_affect_values,   % 玩家装备影响属性
    {
        attack          = 0,    % 普通攻击加值
        defense         = 0,    % 普通防御加值
        stunt_attack    = 0,    % 绝技攻击加值
        stunt_defense   = 0,    % 绝技防御加值
        magic_attack    = 0,    % 法术攻击加值
        magic_defense   = 0,    % 法术防御加值
        max_health      = 0     % health上限加值
     }
 ).


-record(
    probability,                % 成功率
    {
        ets_key     = 0,        % ets_key
        time_pos    = 0,        % 时间段
        direction   = up,        % 方向，up|down
        value       = 0         % 概率
    }
).
