{
    application, server,
    [
        {description, "The game logic server."},
        {vsn, "1.0.0"},
        {modules, []},
        {mod, {server_app, []}},
        {
            env,
            [
                {addr, "127.0.0.1"},
                {gate, gateway@localhost},
                {max_conn, 2000},

                {mysql_host,        "localhost"},
                {mysql_port,        3306},
                {mysql_user,        "root"},
                {mysql_password,    "ybybyb"},
                {mysql_database,    "gamedb"},
                {mysql_poolsize,    3},

                {dummy, dummy}
            ]
        }
    ]
}.