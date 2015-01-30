%% -----------------------------------------------------------------------------
%% Descrip: 角色
%% -----------------------------------------------------------------------------
-module(api_05).

-include("server.hrl").
-include("db.hrl").

-compile([export_all]).

%% -----------------------------------------------------------------------------
%% Function: handle(1, , State) -> List
%% Descrip:  获取角色信息
%%
%% -----------------------------------------------------------------------------
handle(1, <<PlayerRoleId:32>>, State = #conn_info{
       sock = Sock
      }) ->



    {
        PlayerMainRoleId,
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
    } = mod_role:get_role_info(PlayerRoleId),

    QuestToBinary = out_05:get_role_info(
        PlayerMainRoleId,
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
    ),

	%% io:format("binary quest  ~p ~n" , [QuestToBinary]),
	gen_tcp:send(Sock,QuestToBinary),
    State;
    
%% -----------------------------------------------------------------------------
%% Function: handle(1, , State) -> List
%% Descrip:  获取城镇玩家信息
%%
%% -----------------------------------------------------------------------------
handle(2, 
                <<
                >>,
                State = #conn_info
                {
                    sock = Sock
                }
            ) ->

    {
        PlayerName,
        Level,
        Ingot,
        Coins,
        Medical,
        Power
    } = mod_role:get_town_info(),
    QuestToBinary = out_05:get_town_player_info 
    (
        PlayerName,
        Level,
        Ingot,
        Coins,
        Medical,
        Power
    ),
	%% io:format("binary quest  ~p ~n" , [QuestToBinary]),
	gen_tcp:send(Sock,QuestToBinary),
    State;
    
%% -----------------------------------------------------------------------------
%% Function: handle(3, , State) -> List
%% Descrip:  获取玩家角色列表
%%
%% -----------------------------------------------------------------------------
handle(3, 
                <<
                >>,
                State = #conn_info
                {
                    sock = Sock
                }
            ) ->

    Result = mod_role:get_role_list(),
    QuestToBinary = out_05:get_role_list (Result),
	%% io:format("binary quest  ~p ~n" , [QuestToBinary]),
	gen_tcp:send(Sock,QuestToBinary),
    State.