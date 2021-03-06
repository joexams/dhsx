-module(tcp_acceptor).

-behaviour(gen_server).

-export([start_link/2]).
-export([
    init/1,
    handle_call/3, handle_cast/2, handle_info/2,
    terminate/2, code_change/3
]).

-record(state, {callback, sock, ref}).

%%--------------------------------------------------------------------

start_link(Callback, LSock) ->
    gen_server:start_link(?MODULE, {Callback, LSock}, []).

%%--------------------------------------------------------------------

init({Callback, LSock}) ->
    gen_server:cast(self(), accept),
    {ok, #state{callback = Callback, sock = LSock}}.


handle_call(_Request, _From, State) ->
    {noreply, State}.

handle_cast(accept, State) ->
    accept(State);

handle_cast(_Msg, State) ->
    {noreply, State}.


handle_info(
    {inet_async, LSock, Ref, {ok, Sock}},
    State = #state{callback = {M, F, A}, sock = LSock, ref = Ref}
) ->
    {ok, Mod} = inet_db:lookup_socket(LSock),
    
    inet_db:register_socket(Sock, Mod),

    try
        %% report
        {Address, Port}         = inet_op(fun () -> inet:sockname(LSock) end),
        {PeerAddress, PeerPort} = inet_op(fun () -> inet:peername(Sock) end),
        
        error_logger:info_msg(
            "accepted TCP connection on ~s:~p from ~s:~p~n",
            [
                inet_parse:ntoa(Address),
                Port,
                inet_parse:ntoa(PeerAddress),
                PeerPort
            ]
        ),
        
        %% In the event that somebody floods us with connections we can spew
        %% the above message at error_logger faster than it can keep up.
        %% So error_logger's mailbox grows unbounded until we eat all the
        %% memory available and crash. So here's a meaningless synchronous call
        %% to the underlying gen_event mechanism - when it returns the mailbox
        %% is drained.
        gen_event:which_handlers(error_logger),
        
        %% handle
        %file_handle_cache:release_on_death(apply(M, F, A ++ [Sock]))
        apply(M, F, A ++ [Sock])
        
    catch {inet_error, Reason} ->
        gen_tcp:close(Sock),
           
        error_logger:error_msg(
            "unable to accept TCP connection: ~p~n",
            [Reason]
        )
    end,

    %% accept more
    accept(State);

handle_info(
    {inet_async, LSock, Ref, {error, closed}},
    State = #state{sock = LSock, ref = Ref}
) ->
    {stop, normal, State};

handle_info(_Info, State) ->
    {noreply, State}.


terminate(_Reason, _State) ->
    ok.


code_change(_OldVsn, State, _Extra) ->
    {ok, State}.

%%--------------------------------------------------------------------

inet_op(F) ->
    lib_misc:throw_on_error(inet_error, F).


accept(State = #state{sock = LSock}) ->
    %ok = file_handle_cache:obtain(),
    
    case prim_inet:async_accept(LSock, -1) of
        {ok, Ref} ->
            {noreply, State#state{ref = Ref}};
        
        Error ->
            {stop, {cannot_accept, Error}, State}
    end.
