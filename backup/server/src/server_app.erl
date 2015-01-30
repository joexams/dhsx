-module(server_app).

-behaviour(application).

-include("server.hrl").

-export([start/2, stop/1]).
-export([sync_node_info/0]).


start (_Type, _Args) ->
    {ok, Gate} = application:get_env(gate),
    
    {ok, Port} = rpc:call(Gate, gateway_srv, alloc_node_port, []),
    
    application:set_env(server, port, Port),
    
    server_sup:start_link(Port).
    
stop (_State) ->
    ok.

sync_node_info () ->
    {ok, Gate} = application:get_env(gate),
    {ok, Addr} = application:get_env(addr),
    {ok, Port} = application:get_env(port),
    {ok, MaxConn} = application:get_env(max_conn),
    
    Conn = supervisor:count_children(game_client_sup),
    
    NodeInfo = #node_info {
        node = node(),
        addr = Addr,
        port = Port,
        conn = Conn,
        max_conn = MaxConn
    },
    
    rpc:call(Gate, gateway_srv, sync_node_info, [NodeInfo]),
    
    ok.
    