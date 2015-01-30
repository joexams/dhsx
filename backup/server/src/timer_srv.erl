-module(timer_srv).

-behaviour(gen_server).

-export([start_link/4]).
-export([
    init/1,
    handle_call/3, handle_cast/2, handle_info/2,
    terminate/2, code_change/3
]).

%%--------------------------------------------------------------------

start_link (Time, M, F, A) ->
    gen_server:start_link({local, ?MODULE}, ?MODULE, {Time, M, F, A}, []).

%%--------------------------------------------------------------------

init ({Time, M, F, A}) ->
    erlang:send_after(Time, self(), {run, M, F, A, Time, self()}),
    {ok, []}.

handle_call (_Request, _From, State) ->
    {noreply, State}.

handle_cast (_Msg, State) ->
    {noreply, State}.

handle_info ({run, M, F, A, Time, Pid}, State) ->
    apply(M, F, A),
    
    erlang:send_after(Time, Pid, {run, M, F, A, Time, Pid}),
    
    {noreply, State};
    
handle_info (_Info, State) ->
    {noreply, State}.

terminate (_Reason, State) ->
    State.

code_change (_OldVsn, State, _Extra) ->
    {ok, State}.
