%% -----------------------------------------------------------------------------
%% Descrip: 伙伴招募
%% -----------------------------------------------------------------------------
-module(api_08).

-include("server.hrl").
-include("db.hrl").

-compile([export_all]).

%% -----------------------------------------------------------------------------
%% Function: handle(0, <<Type:32>>, State) -> List
%%          Type: 角色类别
%% Descrip:  获取伙伴列表
%% -----------------------------------------------------------------------------

handle(0, <<Type:32>>, State = #conn_info{ 
    sock = Sock
}) ->
    {PartnersList, MaxCount, CurrCount} = mod_partners:partners_list(Type),
   
    QuestToBinary = out_08:partners_list(PartnersList, MaxCount, CurrCount),
	gen_tcp:send(Sock, QuestToBinary),
    
    State;
    
%% -----------------------------------------------------------------------------
%% Function: handle(1, <<RoleId:32>>, State) -> List
%%          RoleId: 角色ID
%% Descrip:  邀请伙伴
%% -----------------------------------------------------------------------------

handle(1, <<RoleId:32>>, State = #conn_info{
    sock = Sock
}) ->
    Result = mod_partners:partners_invite( RoleId),
   
    QuestToBinary = out_08:partners_invite(Result),
	gen_tcp:send(Sock, QuestToBinary),
    
    State.
 