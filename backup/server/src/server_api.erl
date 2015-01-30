-module(server_api).

-include("server.hrl").

-export([start_link/0]).
-export([init/1, main_loop/2]).

-define(HANDSHAKE_TIMEOUT, 10).


start_link () ->
    {ok, proc_lib:spawn_link(?MODULE, init, [self()])}.
    
    
init (Parent) ->
    receive
        {go, Sock} ->
            start_connection(Parent, Sock)
    end.


start_connection(Parent, Sock) ->
    {PeerAddress, PeerPort} = socket_op(Sock, fun inet:peername/1),
    
    PeerAddressS = inet_parse:ntoa(PeerAddress),
    
    lib_log:info(
        "starting TCP connection ~p from ~s: ~p~n",
        [self(), PeerAddressS, PeerPort]
    ),
        
    try
        main_loop(Parent, #conn_info{sock = Sock})
    catch
        Ex ->
            LogFunc = (
                if
                    Ex == connection_closed_abruptly ->
                        fun lib_log:warning/2;
                    true ->
                        fun lib_log:error/2
               end
            ),
            
            LogFunc(
                "exception on TCP connection ~p from ~s: ~p~n~p~n",
                [self(), PeerAddressS, PeerPort, Ex]
            )
    after
        lib_log:info(
            "closing TCP connection ~p from ~s: ~p~n",
            [self(), PeerAddressS, PeerPort]
        ),
        ok
    end,
    
    done.


main_loop (Parent, State = #conn_info{sock = Sock}) ->
	inet:setopts(Sock, [{active, once}]),
    receive
        {tcp, Sock, Data} ->
            State1 = handle_input(Data, State),
            main_loop(Parent, State1);
            
        {send, Data} ->
            gen_tcp:send(Sock, Data),
            main_loop(Parent, State);
            
        {tcp_closed, Sock} ->
            clean(State);
            
        {error, Reason} ->
            clean(State),
            throw({inet_error, Reason});
            
        {'EXIT', Parent, Reason} ->
            clean(State),
            exit(Reason);
        
        Other ->
            %% internal error -> something worth dying for
            clean(State),
            exit({unexpected_message, Other})
    end.

clean (#conn_info{ player_id = PlayerId, town_id = TownId }) ->
    case TownId of
        undefined -> ok;
        _ -> mod_town:leave_town(PlayerId, TownId)
    end,
    
    case PlayerId of
        undefined -> ok;
        _ ->
        broadcast_srv:player_unregister({unregister,PlayerId})
    end.
    
   
handle_input (Data, State) ->
    case
        catch (
            case Data of
                <<0:8, Action:8, Args/binary>> -> api_00:handle(Action, Args, State);
                <<1:8, Action:8, Args/binary>> -> api_01:handle(Action, Args, State);
				<<2:8, Action:8, Args/binary>> -> api_02:handle(Action, Args, State);
				<<3:8, Action:8, Args/binary>> -> api_03:handle(Action, Args, State);
                <<4:8, Action:8, Args/binary>> -> api_04:handle(Action, Args, State);
                <<5:8, Action:8, Args/binary>> -> api_05:handle(Action, Args, State);
                <<6:8, Action:8, Args/binary>> -> api_06:handle(Action, Args, State);
                <<7:8, Action:8, Args/binary>> -> api_07:handle(Action, Args, State);
                <<8:8, Action:8, Args/binary>> -> api_08:handle(Action, Args, State);
                <<9:8, Action:8, Args/binary>> -> api_09:handle(Action, Args, State);
                <<10:8, Action:8, Args/binary>> -> api_10:handle(Action, Args, State);
                <<11:8, Action:8, Args/binary>> -> api_11:handle(Action, Args, State)
            end
        )
    of
        {'EXIT', Reason} -> 
            io:format("server_api: protocol error - ~p - ~p~n", [Data, Reason]),
            % lib_log:error("server_api: protocol error - ~p - ~p~n", [Data, Reason]),
            State;
            
        NewState = #conn_info{} ->
            NewState
    end.

socket_op (Sock, Fun) ->
    case Fun(Sock) of
        {ok, Res} ->
            Res;
            
        {error, Reason} ->
            lib_log:error(
                "error on TCP connection ~p: ~p~n",
                [self(), Reason]
            ),
            
            lib_log:info(
                "closing TCP connection ~p~n",
                [self()]
            ),
            
            exit(normal)
    end.