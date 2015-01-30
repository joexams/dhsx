-module(tcp_client_sup).

-behaviour(supervisor).

-export([start_link/1, start_link/2]).

-export([init/1]).

start_link(Callback) ->
    supervisor:start_link(?MODULE, Callback).

start_link(SupName, Callback) ->
    supervisor:start_link(SupName, ?MODULE, Callback).

init({M,F,A}) ->
    {
        ok,
        {
            {simple_one_for_one, 10, 10},
            [
                {
                    tcp_client,
                    {M, F, A},
                    temporary,
                    brutal_kill,
                    worker,
                    [M]
                }
            ]
        }
    }.