-module(gateway_srv).

-behaviour(gen_server).

-include("server.hrl").

-export([
    start_link/1
]).
-export([
    init/1,
    handle_call/3, handle_cast/2, handle_info/2,
    terminate/2, code_change/3
]).
-export([
    get_nodes/0,
    sync_node_info/1,
    alloc_node_port/0
]).

-define(SERVER, ?MODULE).

%%--------------------------------------------------------------------

start_link (ServerPort) ->
    gen_server:start_link({local, ?SERVER}, ?MODULE, [ServerPort], []).

%%--------------------------------------------------------------------

get_nodes () ->
    gen_server:call(?SERVER, get_nodes).
    
alloc_node_port () ->
    gen_server:call(?SERVER, alloc_node_port).
    
sync_node_info (NodeInfo) ->
    gen_server:call(?SERVER, {sync_node_info, NodeInfo}).

%%--------------------------------------------------------------------

init ([ServerPort]) ->
    ets:new(nodes, [set, named_table, {keypos, #node_info.node}]),
    {ok, ServerPort}.

handle_call (get_nodes, _From, State) ->
    Nodes = ets:match(nodes, '$1'),
    {reply, Nodes, State};

handle_call (alloc_node_port, _From, State) ->
    {reply, {ok, State}, State + 1};

handle_call ({sync_node_info, NodeInfo = #node_info{}}, _From, State) ->
    %io:format("update node~n"),
    ets:insert(nodes, NodeInfo),
    {reply, ok, State};

handle_call (_Request, _From, State) ->
    {noreply, State}.

handle_cast (_Msg, State) ->
    {noreply, State}.

handle_info (_Info, State) ->
    {noreply, State}.

terminate (_Reason, State) ->
    State.

code_change (_OldVsn, State, _Extra) ->
    {ok, State}.
