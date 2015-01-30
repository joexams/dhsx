%% -----------------------------------------------------------------------------
%% Descrip: 帮派
%% -----------------------------------------------------------------------------
-module(api_10).

-include("server.hrl").
-include("db.hrl").

-compile([export_all]).


handle(1, <<Len1:16, FactionName:Len1/binary, Len2:16, FactionMaster:Len2/binary, Requesting:8, PageNumber:16>>, State = #conn_info{
    sock = Sock
}) ->
    {
        PageCurrent,
        PageTotal,
        FactionList
    }= mod_faction:faction_list(FactionName, FactionMaster, Requesting, PageNumber),

    OutBin = out_10:faction_list(
        PageCurrent,
        PageTotal,
        FactionList
    ),
    
    gen_tcp:send(Sock, OutBin),
    
    State
;


handle(2, <<FactionId:32>>, State = #conn_info{
    sock = Sock
}) ->
    {
        FactionId,
        FactionName,
        FactionMaster,
        MasterLevel,
        FactionCoins,
        MemberNumber,
        MaxMember,
        FactionRanking,
        FactionDesc
    } = mod_faction:faction_info(FactionId),

    OutBin = out_10:faction_info(
        FactionId,
        FactionName,
        FactionMaster,
        MasterLevel,
        FactionCoins,
        MemberNumber,
        MaxMember,
        FactionRanking,
        FactionDesc
    ),
    
    gen_tcp:send(Sock, OutBin),
    
    State
;


handle(3, <<FactionId:32>>, State = #conn_info{
    sock = Sock
}) ->
    {FactionId, Result } = mod_faction:faction_request(FactionId),

    OutBin = out_10:faction_request(FactionId, Result),
    
    gen_tcp:send(Sock, OutBin),
    
    State
;


handle(4, <<FactionClass:8, Len1:16, FactionName:Len1/binary>>, State = #conn_info{
    sock = Sock
}) ->
    {FactionId, Result} = mod_faction:found_faction(FactionName, FactionClass),

    OutBin = out_10:found_faction(FactionId, Result),
    
    gen_tcp:send(Sock, OutBin),
    
    State
;


handle(5, <<PageNumber:16, Order:8>>, State = #conn_info{
    sock = Sock
}) ->
    {
        PageCurrent,
        PageTotal,
        Order,
        FactionList
    }= mod_faction:faction_members_list(PageNumber, Order),

    OutBin = out_10:faction_members_list(
        PageCurrent,
        PageTotal,
        Order,
        FactionList
    ),
    
    gen_tcp:send(Sock, OutBin),
    
    State
;


handle(6, <<PageNumber:16>>, State = #conn_info{
    sock = Sock
}) ->
    {
        PageCurrent,
        PageTotal,
        NotifyList
    }= mod_faction:facton_notify_list(PageNumber),

    OutBin = out_10:facton_notify_list(
        PageCurrent,
        PageTotal,
        NotifyList
    ),
    
    gen_tcp:send(Sock, OutBin),
    
    State
;


handle(7, <<PageNumber:16>>, State = #conn_info{
    sock = Sock
}) ->
    {
        PageCurrent,
        PageTotal,
        RequestList
    }= mod_faction:request_list(PageNumber),

    OutBin = out_10:request_list(
        PageCurrent,
        PageTotal,
        RequestList
    ),
    
    gen_tcp:send(Sock, OutBin),
    
    State
;


handle(8, <<RequestId:32>>, State = #conn_info{
    sock = Sock
}) ->
    Result = mod_faction:accept_request(RequestId),

    OutBin = out_10:accept_request(Result),
    
    gen_tcp:send(Sock, OutBin),
    
    State
;


handle(9, <<RequestId:32>>, State = #conn_info{
    sock = Sock
}) ->
    Result = mod_faction:deny_request(RequestId),

    OutBin = out_10:deny_request(Result),
    
    gen_tcp:send(Sock, OutBin),
    
    State
;


handle(10, <<FactionId:32, PlayerId:32>>, State = #conn_info{
    sock = Sock
}) ->
    Result = mod_faction:appoint_job(FactionId, PlayerId),

    OutBin = out_10:appoint_job(Result),

    gen_tcp:send(Sock, OutBin),

    State
;


handle(11, <<FactionId:32, PlayerId:32>>, State = #conn_info{
    sock = Sock
}) ->
    Result = mod_faction:dismiss_job(FactionId, PlayerId),

    OutBin = out_10:dismiss_job(Result),

    gen_tcp:send(Sock, OutBin),

    State
;


handle(12, <<FactionId:32, PlayerId:32>>, State = #conn_info{
    sock = Sock
}) ->
    Result = mod_faction:master_transfer(FactionId, PlayerId),

    OutBin = out_10:master_transfer(Result),
    
    gen_tcp:send(Sock, OutBin),
    
    State
;



handle(13, <<FactionId:32, PlayerId:32>>, State = #conn_info{
    sock = Sock
}) ->
    Result = mod_faction:kick_out_member(FactionId, PlayerId),

    OutBin = out_10:kick_out_member(Result),
    
    gen_tcp:send(Sock, OutBin),
    
    State;



handle(14, <<FactionId:32>>, State = #conn_info{
    sock = Sock
}) ->
    Result = mod_faction:disband_faction(FactionId),

    OutBin = out_10:disband_faction(Result),

    gen_tcp:send(Sock, OutBin),

    State
.
