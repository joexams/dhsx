-module(api_00).

-include("api_00.hrl").
-include("server.hrl").

-compile([export_all]).

%==================================================================================
handle(0, <<Len1:16, PlayerName:Len1/binary, Len2:16, HashCode:Len2/binary>>, State = #conn_info{
    sock      = Sock, 
    player_id = undefined
}) ->
    {Result, PlayerId, Nickname} = mod_player:login( PlayerName, HashCode),
    
    OutBin = out_00:login(Result, PlayerId),
    
    gen_tcp:send(Sock, OutBin),
    
    case Result of
        ?SUCCEED ->
            broadcast_srv:player_register({register,{PlayerId,self()}}),
            
            State#conn_info{ player_id = PlayerId, nickname = Nickname };
        ?FIRST_TIME ->
            broadcast_srv:player_register({register,{PlayerId,self()}}),
            
            State#conn_info{ player_id = PlayerId, nickname = Nickname };
        ?FAILED ->
            State
    end;


%==================================================================================
handle(1, << RoleId:32, Len1:16, NickName:Len1/binary>>, State = #conn_info{
    sock      = Sock,
    player_id = PlayerId
}) ->
    {Result, PlayerId} = mod_player:player_first_init(PlayerId, RoleId, NickName),
    
    OutBin = out_00:player_first_init(Result),
    
    gen_tcp:send(Sock, OutBin),
    
    case Result of
        ?SUCCEED ->
            State#conn_info{ nickname = NickName };
        ?FAILED ->
            State
    end;
    
%==================================================================================
handle(2, <<>>, State = #conn_info{
    sock      = Sock
}) ->

    {
    NickName, 
    Level, 
    Ingot, 
    Coins, 
    Health, 
    MaxHealth, 
    Power, 
    Experience, 
    MaxExperience, 
    TownId,
    Position_x, 
    Position_y
    } = mod_player:get_player_info(),
    
    OutBin = out_00:get_player_info( NickName, Level, Ingot, Coins, Health, MaxHealth, Power, Experience, MaxExperience, TownId, Position_x, Position_y),
    
    gen_tcp:send(Sock, OutBin),
    
    State;


%==================================================================================
handle(3, <<DataType:8>>, State = #conn_info{
    sock      = Sock
}) ->

    DataValue = mod_player:get_player_data(DataType),

    OutBin = out_00:update_player_data(DataType, DataValue),

    gen_tcp:send(Sock, OutBin),

    State.