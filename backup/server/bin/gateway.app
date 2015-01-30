{
    application, gateway,
    [
        {description, "The game gateway server."},
        {vsn, "1.0.0"},
        {modules, []},
        {mod, {gateway_app, []}},
        {
            env,
            [
                {gateway_port, 10010},
                {server_port, 10086}
            ]
        }
    ]
}.