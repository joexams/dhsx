-module(gateway_sup).

-behaviour(supervisor).

-export([start_link/2]).
-export([init/1]).
-export([
    on_listener_start/2,
    on_listener_stop/2,
    on_connection_accept/1
]).

-define(
    TCP_OPTIONS,
    [
        binary,
        {packet, 2},
        {reuseaddr, true},
        {backlog, 128},
        {exit_on_close, false}
    ]
).

%-------------------------------------------------------------------------------

start_link (GatewayPort, ServerPort) ->
    supervisor:start_link({local, ?MODULE}, ?MODULE, [GatewayPort, ServerPort]).

%-------------------------------------------------------------------------------

init ([GatewayPort, ServerPort]) ->
    {
        ok,
        {
            {one_for_all, 0, 1},
            [
                %% The gateway logic server
                {
                    gateway_srv,
                    {
                        gateway_srv,
                        start_link,
                        [ServerPort]
                    },
                    transient,
                    16#ffffffff,
                    worker,
                    [gateway_srv]
                },
                
                %% The tcp listener supervisor
                {
                    tcp_listener_sup,
                    {
                        tcp_listener_sup,
                        start_link,
                        [
                            {0,0,0,0}, GatewayPort, ?TCP_OPTIONS, 
                            {?MODULE, on_listener_start, []},
                            {?MODULE, on_listener_stop, []},
                            {?MODULE, on_connection_accept, []},
                            5, "game gateway"
                        ]
                    },
                    transient,
                    infinity,
                    supervisor,
                    [tcp_listener_sup]
                }
            ]
        }
    }.

%-------------------------------------------------------------------------------
    
on_listener_start (_IPAddress, _Port) ->
    ok.

on_listener_stop (_IPAddress, _Port) ->
    ok.

on_connection_accept (_Sock) ->
    ok.
