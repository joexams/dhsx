-module(mod_friend).

-export([
    add_friend/4,
    delete_friend/2,
    move_friend/3,
    send_message/2
]).

-include("db.hrl").
-include("api_09.hrl").

%%
%%添加玩家好友
%% 
%%PlayerId 玩家ID
%%FriendNickname 好友昵称
%%GroupType 好友所在群组
add_friend(PlayerId,PlayerNickName,FriendNickname,GroupType) ->
    
    %%判断玩家是否添加自己为好友
    if
        PlayerNickName=:=FriendNickname ->
            {no,same};
        true ->
            %%判断要把好友加入的群组类别
            Result=case GroupType of
                       %%如果要加入的群组为好友需要判断好友上限是否超过100
                       ?FRIEND ->
                            MyFriends=db:find (
                                player_friends,
                                fun(Row) ->
                                    if
                                        Row#player_friends.player_id=:=PlayerId 
                                            andalso Row#player_friends.group_type=:= GroupType ->
                                            true;
                                        true ->
                                            false                    
                                    end
                                end                
                            ),
                    
                            if 
                                erlang:length(MyFriends)<100 ->
                                   true;
                                true ->
                                   false
                            end;                            
                        _Other ->
                            true                
                    end,                
             
            case Result of 
                false ->
                    {no,gt_upper_limit};                
                true ->
                    %%通过昵称查找该好友的player信息
                    FriendInfo=db:find (
                        player,
                        fun(Row) ->
                            case string:equal(Row#player.nickname,FriendNickname) of
                                true ->
                                    true;
                                false ->
                                    false
                            end
                        end
                    ),
            
                    io:format(" find player ~p  result: ~p ~n",[FriendNickname,FriendInfo]),
            
                    %%判断好友是否存在
                    if 
                        erlang:length(FriendInfo)=<0 ->
                            {no,friend_no_exist};
                        true ->
                            %%查询要添加的好友是否已是自己的好友
                            [#player{id=FriendId}]=FriendInfo,
                    
                            Rows=db:find (
                                player_friends,
                                fun(Row) ->
                                    if
                                        Row#player_friends.player_id=:=PlayerId 
                                            andalso Row#player_friends.friend_id=:= FriendId ->
                                            true;
                                        true ->
                                            false                    
                                    end
                                end                
                            ),
    
                            %%通过查询结果判断是否要添加该好友    
                            if 
                                erlang:length(Rows)=<0 ->
                                    db:insert (#player_friends{
                                           player_id=PlayerId,
                                           friend_id=FriendId,
                                           group_type=GroupType
                                });
                                true ->
                                   {no,exist}
                            end
            
                   end    
            
            end        
    end.


%%
%%删除玩家好友
%% 
%%PlayerId 玩家ID
%%FriendId 好友ID
%%GroupType 好友所在群组
delete_friend(PlayerId,FriendId) -> 
    db:delete (
        player_friends,
        fun(Row) ->
            if
                Row#player_friends.player_id==PlayerId 
                andalso Row#player_friends.friend_id== FriendId ->
                    true;
                true ->
                    false
            end
        end            
    ).


%%
%%更改好友群组
%% 
%%PlayerId 玩家ID
%%FriendId 好友ID
%%GroupType 更改至目标群组
move_friend(PlayerId,FriendId,GroupType) ->
    %%判断要把好友加入的群组类别
    Result=case GroupType of
                %%如果要移入的群组为好友需要判断好友上限是否超过100
                ?FRIEND ->
                    MyFriends=db:find (
                                player_friends,
                                fun(Row) ->
                                    if
                                        Row#player_friends.player_id=:=PlayerId 
                                            andalso Row#player_friends.group_type=:= GroupType ->
                                            true;
                                        true ->
                                            false                    
                                    end
                                end                
                            ),
                    
                    if 
                        erlang:length(MyFriends)<100 ->
                            true;
                        true ->
                            false
                    end;                            
                _Other ->
                    true                
            end,                      
    
    case Result of
        false ->
            {no,move_gt_upper_limit};
        true ->
            db:update (
                player_friends,
                fun(Row) ->
                    if
                        Row#player_friends.player_id==PlayerId 
                            andalso Row#player_friends.friend_id==FriendId ->
                            #player_friends{
                                player_id=PlayerId,
                                friend_id=FriendId,
                                group_type=GroupType
                            };
                        true ->
                            false                
                    end
                end    
            )        
    end.        


%%
%%发送信息给好友
%% 
%%PlayerId 玩家ID
%%FriendId 好友ID
%%GroupType 更改至目标群组
send_message({PlayerId,_NickName,_Message,_EipNum,_EipIndex},FriendId) ->
    _Row=db:find (
            player_friends,
            fun(Row) ->
                if
                    Row#player_friends.player_id=:=PlayerId 
                        andalso Row#player_friends.friend_id=:= FriendId ->
                        true;
                    true ->
                        false                    
                end
            end                
    ),
    
    %%判断好友是否在黑名单中
    if
        #player_friends.group_type=:=?BLACKLIST ->
            {no,in_blacklist};
        true ->
            ok
    end.        