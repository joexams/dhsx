-module(timer_sup).

-behaviour(supervisor).

-export([start_link/0]).
-export([init/1]).

start_link() ->
    supervisor:start_link({local, timer_sup}, ?MODULE, []).

init([]) ->
    {
        ok,
        {
            {one_for_one, 1, 10},
            [
                {
                    sync_node_info_timer,
                    {
                        timer_srv, start_link,
                        [
                            15000, server_app, sync_node_info, []
                        ]
                    },
                    temporary,
                    brutal_kill,
                    worker,
                    [timer_srv]
                }
            ]
        }
    }.