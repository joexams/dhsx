%% -----------------------------------------------------------------------------
%% Descrip: ets 监控树
%% author: qinlai.cai@gmail.com
%% -----------------------------------------------------------------------------
-module(ets_sup).

-behaviour(supervisor).

-export([start_link/1]).

-export([init/1]).

start_link(CallBack) ->
    supervisor:start_link({local, ?MODULE}, ?MODULE, [CallBack]).

init([CallBack]) ->
    {
        ok,
        {
            {one_for_one, 10, 10},
            [
                {
                    ets_srv,
                    {ets_srv, start_link, [CallBack]},
                    transient,
                    brutal_kill,
                    worker,
                    [ets_srv]
                }
            ]
        }
    }.
