%% -----------------------------------------------------------------------------
%% Descrip : 战场模块-命格
%% Author  : qinlai.cai@gmail.com
%% -----------------------------------------------------------------------------
-module(mod_fate).
-compile([export_all]).
-include("ets_logic.hrl").

%% --------------------------命格效果-------------------------------------------

%% 破军星(攻击力、绝技攻击力提升100%，防御力、绝技防御力下降80%)
fate (
    "PoJunXing",
    RoleId,
    SelfRoleAttributeList,
    EnemyRoleAttributeList
) ->
    Attribute = mod_war:get_role_attribute(RoleId, SelfRoleAttributeList),
    #player_role_war_attribute{
        attack        = Attack,
        stunt_attack  = StuntAttack,
        defense       = Defense,
        stunt_defense = StuntDefense
    } = Attribute,
    NewSelfRoleAttribute = Attribute #player_role_war_attribute{
        attack        = Attack * (1 + 1),
        stunt_attack  = StuntAttack * (1 + 1),
        defense       = Defense * (1 - 0.8),
        stunt_defense = StuntDefense * (1 - 0.8)
    },
    NewSelfRoleAttributeList =
        [NewSelfRoleAttribute | lists:delete(Attribute, SelfRoleAttributeList)],
    {NewSelfRoleAttributeList, EnemyRoleAttributeList}.