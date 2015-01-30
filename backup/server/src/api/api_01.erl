-module(api_01).

-include("server.hrl").

-compile([export_all]).

%==================================================================================

handle(0, <<TownId:32>>, State = #conn_info{
    player_id = PlayerId,
    town_id   = undefined
}) ->
    Result = mod_town:enter_town(PlayerId, TownId),
    
    case Result of
        ok ->
            State#conn_info{ town_id = TownId };
        _ ->
            State
    end;

%==================================================================================

handle(1, <<>>, State = #conn_info{
    player_id = PlayerId,
    town_id   = TownId
}) ->
    case mod_town:leave_town(PlayerId, TownId) of
        ok ->
            State#conn_info{ town_id = undefined };
        _ ->
            State
    end;

%==================================================================================
    
handle(2, <<FromX:16, FromY:16, ToX:16, ToY:16>>, State = #conn_info{
    player_id = PlayerId,
    town_id   = TownId
}) ->
    mod_town:move_to(PlayerId, TownId, FromX, FromY, ToX, ToY),
    
    State;

%==================================================================================

handle(3, <<>>, State = #conn_info{
    sock    = Sock, 
    town_id = TownId
}) ->
    Players = mod_town:get_players(TownId),
    
    OutBin = out_01:get_players(Players),
    
    gen_tcp:send(Sock, OutBin),
    
    State.
