-module(api_09).

-compile([export_all]).

-include("api_09.hrl").
-include("server.hrl").

%%--------------------------------------------------------------------
%% Descrip:添加玩家好友
%%--------------------------------------------------------------------
handle(0, <<Len1:16, FriendNickname:Len1/binary, GroupType:8>>, State = #conn_info{
    player_id = PlayerId,
    nickname  = NickName,
    sock = Sock
}) ->
    Result=case catch(mod_friend:add_friend(PlayerId,NickName,erlang:binary_to_list(FriendNickname),GroupType)) of
                {ok,_Row} ->
                    ?ADD_SUCCESS;
                {no,same} ->
                    ?ADD_SELF;
                {no,gt_upper_limit} ->
                    ?GT_UPPER_LIMIT;
                {no,friend_no_exist} ->
                    ?FRIEND_NOT_FOUND;
                {no, exist} ->
                    ?EXIST_IN_FRIENDLIST;
                {'EXIT',Reason} ->
                    io:format("exception in add_friend reason: ~p~n",[Reason]),
                    
                    ?ADD_ERROR            
            end,
    
    OutBin=out_09:add_friend (Result),
    
    gen_tcp:send(Sock,OutBin),
    
    State;

    
%%--------------------------------------------------------------------
%% Descrip:删除玩家好友
%%--------------------------------------------------------------------    
handle(1, <<FriendId:32>>, State = #conn_info{
    player_id = PlayerId,
    sock = Sock
}) ->
    Result=case catch(mod_friend:delete_friend(PlayerId,FriendId)) of
               ok ->
                    ?DELETE_SUCCESS;          
               {'EXIT',Reason} ->
                    io:format("exception in delete_friend reason: ~p~n",[Reason]),
                    
                    ?DELETE_ERROR                 
           end,
           
    OutBin=out_09:delete_friend(Result),
    
    gen_tcp:send(Sock,OutBin),
        
    State;


%%--------------------------------------------------------------------
%% Descrip:更改玩家好友的群组类型
%%-------------------------------------------------------------------- 
handle(2, <<FriendId:32, GroupType:8>>, State = #conn_info{
    player_id = PlayerId,
    sock = Sock
}) ->
    Result=case catch(mod_friend:move_friend(PlayerId,FriendId,GroupType)) of
               ok ->
                    ?MOVE_SUCCESS;
               {no,move_gt_upper_limit} ->
                    ?MOVE_GT_UPPER_LIMIT;                  
               {'EXIT',Reason} ->
                    io:format("exception in move_friend reason: ~p~n",[Reason]),
                    
                    ?MOVE_ERROR
           end,
           
    OutBin=out_09:move_friend(Result),
    
    gen_tcp:send(Sock,OutBin),
    
    State;


%%--------------------------------------------------------------------
%% Descrip:玩家向好友发送信息
%%--------------------------------------------------------------------     
handle(3, <<FriendId:32, Len1:16, Message:Len1/binary, Len2:16, EipNum:Len2/binary, Len3:16, EipIndex:Len3/binary>>,State = #conn_info{
    player_id = PlayerId,
    nickname  = NickName,
    sock=Sock
}) ->
    SendMessage={
                 PlayerId,
                 NickName,
                 binary_to_list(Message),
                 binary_to_list(EipNum),
                 binary_to_list(EipIndex)
                },
                
    Result=case mod_friend:send_message(SendMessage,FriendId) of
               ok ->
                    ?SEND_SUCCESS;
                {no,in_blacklist} ->
                    error;
                {'EXIT',Reason} ->
                    io:format("exception in move_friend reason: ~p~n",[Reason]),
                    ?SEND_ERROR
            end,
    
    OutBin=out_09:send_message_to_friend(Result),
    
    gen_tcp:send(Sock,OutBin),
    
    State.
    