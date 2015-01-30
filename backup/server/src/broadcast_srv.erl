-module(broadcast_srv).

-behaviour(gen_server).

-export([start_link/0,
         broadcast/1,
         player_register/1,
         player_unregister/1
]).
-export([
    init/1,
    handle_call/3, handle_cast/2, handle_info/2,
    terminate/2, code_change/3
]).

-include("broadcast.hrl").

%%--------------------------------------------------------------------

start_link() ->
    gen_server:start_link({local, ?MODULE},?MODULE,#bc_info{},[]).
    
broadcast(Request) ->
    gen_server:cast(?MODULE,Request).

player_register(Request) ->
    gen_server:cast(?MODULE,Request).

player_unregister(Request) ->
    gen_server:cast(?MODULE,Request).

%%--------------------------------------------------------------------

init(State) ->
    {ok, State}.

handle_call(_Request, _From, State) ->
    {noreply, State}.

handle_cast({broadcast,Message,SelfPid},State) ->
    mod_chat:chat_in_world([Message],erlang:get(),SelfPid),
    {noreply, State};

handle_cast({register,{PlayerId,Pid}},State) ->
    erlang:put(PlayerId,Pid),
    {noreply, State};
    
handle_cast({unregister,PlayerId},State) ->
    erlang:erase(PlayerId),
    {noreply, State}.

handle_info(_Request, State) ->
    {noreply, State}.

terminate(_Reason, State) ->
    State.

code_change(_OldVsn, State, _Extra) ->
    {ok, State}.
