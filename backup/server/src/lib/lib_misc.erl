-module(lib_misc).

-compile([export_all]).


tcp_name(Prefix, IPAddress, Port)
    when is_atom(Prefix) andalso is_number(Port) ->
    list_to_atom(
        lists:flatten(
            io_lib:format(
                "~w_~s:~w",
                [Prefix, inet_parse:ntoa(IPAddress), Port]
            )
        )
    ).

throw_on_error(E, Thunk) ->
    case Thunk() of
        {error, Reason} -> throw({E, Reason});
        {ok, Res}       -> Res;
        Res             -> Res
    end.

%% -----------------------------------------------------------------------------
%% Function: get_local_time() -> string
%% Descrip: 获取当前时间(Exp:"2010-10-10 10:10:10")
%% -----------------------------------------------------------------------------
get_local_time () ->
    {{Y,M,D}, {H,I,S}} = erlang:localtime(),
    
    lists:concat([Y, "-", M, "-", D, " ", H, ":", I, ":", S]).

get_local_timestamp() ->
    calendar:datetime_to_gregorian_seconds(erlang:localtime()) - 62167219200 .

ceil(X) when X < 0 ->
    trunc(X);
ceil(X) ->
    T = trunc(X),
    case X - T == 0 of
        true -> T;
        false -> T + 1
    end.

floor(X) when X < 0 ->
    T = trunc(X),
    case X - T == 0 of
        true -> T;
        false -> T - 1
    end;
floor(X) ->
    trunc(X).

random(Range) ->
    {_H, M, S} = now(),
    random:seed(
        erlang:phash(node(), 100000),
        erlang:phash(M, S),
        S
    ),
    random:uniform(Range).

time_pos() ->
    {H, M, _S} = erlang:time(),
    H * 2  + case M of
        M when M >=30 ->
            2;
        M when M <30 ->
            1
   end.