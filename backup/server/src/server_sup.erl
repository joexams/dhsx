-module(server_sup).

-behaviour(supervisor).

-export([start_link/1]).
-export([init/1]).
-export([
    on_game_server_start/2,
    on_game_server_stop/2,
    on_game_server_accept/1
]).
-export([
    on_policy_server_start/2,
    on_policy_server_stop/2,
    on_policy_server_accept/1
]).

-define(
    GAME_SRV_OPTS,
    [
        binary,
        {packet, 2},
        {backlog, 128},
        {reuseaddr, true},
        {exit_on_close, false}
    ]
).
-define(
    POLICY_SRV_OPTS,
    [
        binary,
        {packet, 0},
        {backlog, 128},
        {active, false},
        {reuseaddr, true}
    ]
).

%-------------------------------------------------------------------------------

start_link (Port) ->
    supervisor:start_link({local, ?MODULE}, ?MODULE, [Port]).

%-------------------------------------------------------------------------------

init ([Port]) ->
    % 获取应用Mysql参数，！必须由app启动才能正常获得
    {ok, Db_Host}      = application:get_env(mysql_host),
    {ok, Db_Port}      = application:get_env(mysql_port),
    {ok, Db_User}      = application:get_env(mysql_user),
    {ok, Db_Password}  = application:get_env(mysql_password),
    {ok, Db_Database}  = application:get_env(mysql_database),
    {ok, Db_PoolSize}  = application:get_env(mysql_poolsize),
    
    Db_LogFun = fun(_, _, _) -> ok end,
    
    {
        ok,
        {
            {one_for_all, 0, 1},
            [
                %% MySql connection pool
                {
                    mysql_pool,
                    {
                        mysql,
                        start_link,
                        [
                            mysql_pool,     % pool id
                            Db_Host,        % mysql host
                            Db_Port,        % mysql port
                            Db_User,        % mysql user
                            Db_Password,    % mysql password
                            Db_Database,    % database
                            Db_LogFun,      % LogFun
                            Db_PoolSize,    % pool size
                            true            % reconnection
                        ]
                    },
                    transient,
                    16#FFFFFFFF,
                    worker,
                    [mysql]
                },

                %% The ets supervisor
                {
                    ets_sup,
                    {
                        ets_sup,
                        start_link,
                        [
                            [
                                {db, init, [system]},
                                {ets_logic_init, init, []}
                            ]
                        ]
                    },
                    transient,
                    infinity,
                    supervisor,
                    [ets_sup]
                },

                %% The tcp client supervisor
                {
                    game_client_sup,
                    {
                        tcp_client_sup,
                        start_link,
                        [
                            {local, game_client_sup},
                            {server_api, start_link, []}
                        ]
                    },
                    transient,
                    infinity,
                    supervisor,
                    [tcp_client_sup]
                },
                
                %% The flash policy file server
                {
                    policy_server_sup,
                    {
                        tcp_listener_sup,
                        start_link,
                        [
                            {0,0,0,0}, 843, ?POLICY_SRV_OPTS, 
                            {?MODULE, on_policy_server_start, []},
                            {?MODULE, on_policy_server_stop, []},
                            {?MODULE, on_policy_server_accept, []},
                            5, "policy server"
                        ]
                    },
                    transient,
                    infinity,
                    supervisor,
                    [tcp_listener_sup]
                },
                
                %% The tcp listener supervisor
                {
                    tcp_listener_sup,
                    {
                        tcp_listener_sup,
                        start_link,
                        [
                            {0,0,0,0}, Port, ?GAME_SRV_OPTS, 
                            {?MODULE, on_game_server_start, []},
                            {?MODULE, on_game_server_stop, []},
                            {?MODULE, on_game_server_accept, []},
                            5, "game server"
                        ]
                    },
                    transient,
                    infinity,
                    supervisor,
                    [tcp_listener_sup]
                },
                
                %% The timer supervisor
                {
                    timer_sup,
                    {
                        timer_sup,
                        start_link,
                        [
                        ]
                    },
                    transient,
                    infinity,
                    supervisor,
                    [timer_sup]
                },
                
                %% The broadcast supervisor
                {
                    broadcast_sup,
                    {
                        broadcast_sup,
                        start_link,
                        [
                        ]
                    },
                    transient,
                    infinity,
                    supervisor,
                    [broadcast_sup]
                }
            ]
        }
    }.

%-------------------------------------------------------------------------------
    
on_game_server_start (_IPAddress, _Port) ->
    server_app:sync_node_info(),
    ok.

on_game_server_stop (_IPAddress, _Port) ->
    ok.

on_game_server_accept (Sock) ->
    {ok, MaxConn} = application:get_env(max_conn),
    
    case supervisor:count_children(game_client_sup) of
        MaxConn ->
            gen_tcp:close(Sock),
            server_app:sync_node_info();
            
        _ ->
            inet:setopts(Sock, [{active, false}]),
            
            {ok, Pid} = supervisor:start_child(game_client_sup, []),
            
            ok = gen_tcp:controlling_process(Sock, Pid),
            
            Pid ! {go, Sock},
            
            Pid
    end.

%-------------------------------------------------------------------------------
    
on_policy_server_start (_IPAddress, _Port) ->
    ok.

on_policy_server_stop (_IPAddress, _Port) ->
    ok.

on_policy_server_accept (Sock) ->
	case gen_tcp:recv(Sock, 0) of
		{ok, _Request} ->
			gen_tcp:send(
                Sock,
                <<
				"<cross-domain-policy>",
				"<allow-access-from domain=\"*\" to-ports=\"*\" />",
				"<allow-access-from domain=\"localhost\" to-ports=\"*\" />",
				"</cross-domain-policy>",0
				>>
            );
		_ ->
			ok
	end,
	gen_tcp:close(Sock).
