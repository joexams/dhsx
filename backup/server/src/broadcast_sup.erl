-module(broadcast_sup).

-behaviour(supervisor).

-export([start_link/0]).
-export([init/1]).

start_link() ->
    supervisor:start_link({local, broadcast_sup}, ?MODULE, []).

init([]) ->
    {
        ok,
        {
            {one_for_one, 1, 10},
            [
                {
                    broadcast_srv,
                    {broadcast_srv, start_link,[]},
                    transient,
                    brutal_kill,
                    worker,
                    [broadcast_srv]
                }
            ]
        }
    }.