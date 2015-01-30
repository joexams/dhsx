-module(gateway_app).

-behaviour(application).

-export([start/2, stop/1]).

%-------------------------------------------------------------------------------

start (_Type, _Args) ->
    {ok, GatewayPort} = application:get_env(gateway, gateway_port),
    {ok, ServerPort}  = application:get_env(gateway, server_port),
    
    gateway_sup:start_link(GatewayPort, ServerPort).

stop (_State) ->
    ok.