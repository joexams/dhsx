-module(game).

-export([
    start_gateway/0,
    start_server/0,
    add_server/0
]).


start_gateway () ->
    application:start(gateway).

start_server () ->
    application:start(server).

add_server () ->
    ok.