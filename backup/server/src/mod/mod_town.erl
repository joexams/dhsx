-module(mod_town).

-include("db.hrl").
-include("ets_logic.hrl").

-include_lib("stdlib/include/ms_transform.hrl").

-export([
    enter_town/2,
    leave_town/2,
    move_to/6,
    get_players/1
]).

%%
%% 进入城镇
%%
%% PlayerId 玩家ID
%% TownId   城镇ID
%%
enter_town (PlayerId, TownId) ->
    [Player]        = db:get (player),
    
    [PlayerRole]    = [
        X || X <- db:get (player_role),
        X #player_role.id == Player #player.main_role_id
    ],
    
    Role = db:get (role, PlayerRole #player_role.role_id),
    
    TownPlayer = #town_player {
        town_id         = TownId, 
        process_id      = self(),
        player_id       = PlayerId,
        nickname        = Player #player.nickname,
        player_role_id  = Player #player.main_role_id,
        role_id         = PlayerRole #player_role.role_id,
        job_id          = Role #role.role_job_id
    },

    [PlayerData] = db:get(player_data),
    
    PlayerPos = if 
        PlayerData #player_data.last_town_id == TownId ->
            #player_position {
                player_id = PlayerId,
                pos_x     = PlayerData #player_data.last_pos_x,
                pos_y     = PlayerData #player_data.last_pos_y
            };
        true ->
            #player_position {
                player_id = PlayerId,
                pos_x     = 200,
                pos_y     = 450
            }
    end,
    
    OutBin = out_01:enter_town(
        PlayerId, 
        PlayerRole #player_role.role_id,
        Player #player.nickname, 
        PlayerPos #player_position.pos_x, 
        PlayerPos #player_position.pos_y
    ),
    
    lib_ets:insert(town_player, TownPlayer, replace),
    
    lib_ets:insert(player_position, PlayerPos, replace),

    Rows = lib_ets:get(town_player, TownId),
    
    [Pid ! {send, OutBin} || #town_player{ process_id = Pid } <- Rows],
    
    ok.
  
%%
%% 离开城镇
%%
%% PlayerId 玩家ID
%% TownId   城镇ID
%% 
leave_town (PlayerId, TownId) ->
    OutBin = out_01:leave_town(PlayerId),
    
    Rows = lib_ets:get(town_player, TownId),
    
    [Pid ! {send, OutBin} || #town_player{ process_id = Pid } <- Rows],
    
    MatchSpec = ets:fun2ms(
        fun(A = #town_player{ town_id = Tid, player_id = Pid }) 
            when Tid == TownId, Pid == PlayerId -> true 
        end
    ),

    lib_ets:select_delete(town_player, MatchSpec),
    
    [PlayerPos] = lib_ets:get(player_position, PlayerId),
    
    lib_ets:delete(player_position, PlayerId),
    
    db:update(player_data, fun(PlayerData) ->
        PlayerData #player_data {
            last_town_id = TownId,
            last_pos_x   = PlayerPos #player_position.pos_x,
            last_pos_y   = PlayerPos #player_position.pos_y
        }
    end),
    
    ok.
 
%%
%% 玩家移动
%%
%% PlayerId 玩家ID
%% TownId   城镇ID
%% FromX    当前X轴坐标
%% FromY    当前Y轴坐标
%% ToX      目标X轴坐标
%% ToY      目标Y轴坐标
%% 
move_to (PlayerId, TownId, FromX, FromY, ToX, ToY) ->

    PlayerPos = #player_position {
        player_id = PlayerId,
        pos_x     = FromX,
        pos_y     = FromY
    },
    
    lib_ets:insert(player_position, PlayerPos, replace),

    OutBin = out_01:move_to(PlayerId, FromX, FromY, ToX, ToY),
    
    Rows = lib_ets:get(town_player, TownId),
    
    [Pid ! {send, OutBin} || #town_player{ process_id = Pid } <- Rows],
    
    ok.
   
%%
%% 玩家移动
%%
%% TownId   城镇ID
%% FromX    当前X轴坐标
%% FromY    当前Y轴坐标
%% ToX      目标X轴坐标
%% ToY      目标Y轴坐标
%% 
get_players (TownId) ->

    Rows = lib_ets:get(town_player, TownId),
    
    lists:foldl(
        fun(Row, Result) ->
            PlayerId  = Row #town_player.player_id,
            Nickname  = Row #town_player.nickname,
            RoleId    = Row #town_player.role_id,
            [Pos] = lib_ets:get(player_position, PlayerId),
            
            PositionX = Pos #player_position.pos_x,
            PositionY = Pos #player_position.pos_y,
            
            
            Item = {PlayerId, RoleId, Nickname, PositionX, PositionY},

            [Item | Result]
        end, 
        [], 
        Rows
    ).
    