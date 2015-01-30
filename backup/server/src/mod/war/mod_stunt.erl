%% -----------------------------------------------------------------------------
%% Descrip : 战场模块-战法
%% Author  : qinlai.cai@gmail.com
%% -----------------------------------------------------------------------------
-module(mod_stunt).

-include("ets_logic.hrl").

-export(
    [
        set_stunt/2,
        get_stunt_effect/3
    ]
).

%% --------------------------施加战法-------------------------------------------
%% 御剑术
set_stunt (Stunt = "YuJianShu", RoleId) ->

    StuntData = {Stunt, null},
    set_stunt_data(RoleId, StuntData);

%% 猛击
set_stunt (Stunt = "MengJi", RoleId) ->

    StuntData = {Stunt, null},
    set_stunt_data(RoleId, StuntData).


%% --------------------------战法效果-------------------------------------------
%% 战法效果(御剑术->战法攻击加1)
get_stunt_effect (
    {"YuJianShu", _StuntData},
    SelfRoleAttribute,       %% 被施加战法方的角色属性
    EnemyRoleAttribute       %% 施加战法方的角色属性
) ->

    RoleId      = SelfRoleAttribute #player_role_war_attribute.role_id,
    StuntAttack = EnemyRoleAttribute #player_role_war_attribute.stunt_attack,
    %% 战法攻击加1
    NewEnemyRoleAttribute = EnemyRoleAttribute #player_role_war_attribute{
        stunt_attack = StuntAttack + 1
    },
    %% 更新附加战法数据
    set_stunt_data(RoleId, {"", null}),
    
    {SelfRoleAttribute, NewEnemyRoleAttribute};
    
%% 猛击
get_stunt_effect (
    {"MengJi", _StuntData},
    SelfRoleAttribute,
    EnemyRoleAttribute
) ->
    get_stunt_effect(
        {"YuJianShu", _StuntData},
        SelfRoleAttribute,
        EnemyRoleAttribute
    );

get_stunt_effect (_Stunt, SelfRoleAttribute, EnemyRoleAttribute) ->

    {SelfRoleAttribute, EnemyRoleAttribute}.

%% 设置战法
set_stunt_data (RoleId, StuntData) ->
    
    RoleParam = mod_war:get_role_param(RoleId),
    mod_war:set_role_param(
        RoleId,
        RoleParam #role_process_param{
            be_stunt = StuntData
        }
    ).