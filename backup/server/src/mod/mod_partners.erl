-module(mod_partners).

-include("db.hrl").
-include("server.hrl").
-include("api_08.hrl").

-include_lib("stdlib/include/ms_transform.hrl").

-export([
        partners_list/1,
        partners_invite/1
        ]).

%% -----------------------------------------------------------------------------
%% Function: research_list (Type) -> List
%%  Type     伙伴类型
%% Descrip:  获取伙伴列表
%%
%% -----------------------------------------------------------------------------

partners_list (Type) ->

    if Type == 0 ->
            MatchSpec = ets:fun2ms(
                fun(Roles = #role{}) ->
                    Roles
                end
            ); 
        true    ->
            MatchSpec = ets:fun2ms(
                fun(Roles = #role{ role_job_id = RoleJobId })
                    when RoleJobId == Type ->
                        Roles
                end
            )  
    end,
    PlayerRoleList = mod_role:dirty_get_player_role_by_playerid(PlayerId ),
?DEBUG ("PlayerRoleList ~p ~n", [PlayerRoleList]),
    RoleList = game_db:dirty_select(role, MatchSpec),

    RoleListBaseData = lists:foldl(
        fun (Role , Result) ->
            RoldId  = Role #role.id,
            Name    = Role #role.name,
            Fees    = Role #role.fees,
            RoleJobId = Role #role.role_job_id,
            RoleStuntId = Role #role.role_stunt_id,
            RoleStunt = db:get (role_stunt, RoleStuntId),
            RoleStuntName = RoleStunt #role_stunt.name,
            RoleJob = db:get (role_job, RoleJobId),
            RoleJobName = RoleJob #role_job.name,
            Strength    = Role #role.strength,
            Agile       = Role #role.agile,
            Intellect   = Role #role.intellect,
            
            case lists:keysearch(RoldId, 4, PlayerRoleList) of 
                false   ->  
                    Level = 1,
                    RoleJobLevelData = db:get (role_job_level_data, {RoleJobId, 1} ),
                    MaxHealth = RoleJobLevelData #role_job_level_data.max_health,
                    Health = MaxHealth,

                    [{RoldId, Name, Level, Fees, RoleJobName, RoleStuntName, Health, MaxHealth, Strength, Agile, Intellect, 1} | Result];
                    
                {value, PlayerRole} ->

                    PlayerState = PlayerRole #player_role.state,

                    case PlayerState of 
                        1 ->
                            Health = PlayerRole #player_role.health,
                            Level  = PlayerRole #player_role.level,
                            RoleJobLevelData2 = db:get (role_job_level_data, {RoleJobId, Level} ),
                            MaxHealth = RoleJobLevelData2 #role_job_level_data.max_health,
                            [{RoldId, Name, Level, Fees, RoleJobName, RoleStuntName, Health, MaxHealth, Strength, Agile, Intellect, 1} | Result];
                        _ -> 
                            Result
                    end
            end
        end
        ,
        []
        ,RoleList
    ),
    [Player] = db:get (player),
    
    {RoleListBaseData , Player #player.max_role_num, Player #player.role_num}.
    
%% -----------------------------------------------------------------------------
%% Function: partners_invite (RoleId)
%%          PlayerId :  玩家ID
%%          RoleId: 角色ID
%% Descrip:  邀请伙伴
%% -----------------------------------------------------------------------------
   
partners_invite ( RoleId) ->
    PlayerRoleList = db:get (player_role),
    Role = db:get (role, RoleId),
    Fees = Role #role.fees,
    [Player]    = db:get (player),
    PlayerId    = Player #player.id,
    RoleNum     = Player #player.role_num,
    MaxRoleNum  = Player #player.max_role_num,
    io:format ("RoleId: ~p ~n", [RoleId]),
    
    if MaxRoleNum == RoleNum ->
            ?COUNTLIMIT;   %人数上限
        true ->
            [PlayerData] = db:get (player_data),
            Mycoins = PlayerData #player_data.coins ,
            if Mycoins < Fees -> 
                    ?NOENOUGHFEES;   % 铜钱不足
                true ->
                    case lists:keysearch(RoleId, 4, PlayerRoleList) of 
                        false   ->  
                            %% insert into player_role
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
                                    % insert player_role_data
                                    Player_Role_Data = #player_role_data{
                                        player_role_id  = New_Player_Role #player_role.id,
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

                                    db:insert(Player_Role_Data),
                                    % 修改 角色人数
                                    mod_player:increase_role_num(),
                                    %修改铜钱
                                    mod_player:decrease_coin(Fees),
                                    ?SUCCEED;
                                _ ->
                                    ?FAILED
                            end;   
                        {value, _} -> 
                            %修改角色状态
                            change_role_state (RoleId, 0),
                            %修改人数 与铜钱
                            mod_player:increase_role_num(),
                            mod_player:decrease_coin(Fees),
                            ?SUCCEED
                    end
            end
    end.
    
%% -----------------------------------------------------------------------------
%% Function: change_role_state (RoleId, RoleState)
%%          RoleState :  状态   0 正常,  1 下野
%%          RoleId: 角色ID
%% Descrip:  修改角色状态
%% -----------------------------------------------------------------------------
change_role_state (RoleId, RoleState) ->
    db:update(
        player_role, 
        fun(Row = #player_role{role_id = RecordRoleId}) ->
            if RecordRoleId == RoleId ->
                    Row #player_role{state = RoleState};
                true -> false
            end
        end
    ).
