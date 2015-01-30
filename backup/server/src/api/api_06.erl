-module(api_06).

-compile([export_all]).

-include("api_06.hrl").
-include("server.hrl").


%%--------------------------------------------------------------------
%% Descrip:接收玩家发送的聊天信息并转发
%%--------------------------------------------------------------------
handle(0, <<MessageType:8, Len1:16, Message:Len1/binary, Len2:16, EipNum:Len2/binary, Len3:16, EipIndex:Len3/binary>>, State = #conn_info{
    player_id = PlayerId,
    nickname  = NickName,
    town_id   = TownId
}) ->
    
    SendMessage={
                 PlayerId,
                 NickName,
                 MessageType,
                 binary_to_list(Message),
                 binary_to_list(EipNum),
                 binary_to_list(EipIndex)
                },
        
    case MessageType of
        ?NEAR ->
            mod_chat:chat_in_town([SendMessage],TownId,self());
        ?WORLD ->
            broadcast_srv:broadcast({broadcast,SendMessage,self()})
        %?CAMP  ->
            %2;
        %?FACTION ->
            %3
    end,
    
    State.