-module(mod_chat).

-export([
    chat_in_town/3,
    chat_in_world/3
]).

-include("ets_logic.hrl").
-include_lib("stdlib/include/ms_transform.hrl").

%%
%%发送聊天信息到城镇中的玩家
%% 
%%Message 要发送的信息
%%TownId  城镇ID
chat_in_town(Message,TownId,SelfPid) ->
    %%生成matchspec 用于查询当前玩家所在城镇中的所有玩家的进程并排除当前玩家自身
    MatchSpec=ets:fun2ms(
                 fun(#town_player{ town_id = Town_id, process_id = Process_id }) 
                     when Town_id =:= TownId andalso Process_id=/=SelfPid -> Process_id
                 end
                ),
    
    %%根据matchspec查询城镇中的玩家进程
    TownPorcessList=lib_ets:select (town_player, MatchSpec),
    
    %%判断城镇中是否有玩家    
    if 
        erlang:length(TownPorcessList)=/=0 ->
            OutMessage=out_06:bro_to_players(Message),
             
            lists:foreach(
                fun(Process_id) ->
                    erlang:send(Process_id,{send,OutMessage})          
                end,
                TownPorcessList
            ),
            
            ok;
        true ->
            ok
    end.        
 
%%
%%发送聊天信息到全世界的玩家
%% 
%%MessageList 要发送的信息列表
chat_in_world(Message,ToPlayerList,SelfPid) ->
    OutMessage=out_06:bro_to_players(Message),
    
    lists:foreach(
        fun({_PlayerId,Process_id}) when is_pid(Process_id)->
                case is_process_alive(Process_id) of
                    true-> 
                        if
                            SelfPid=/=Process_id ->
                                io:format(" send message to world player\n"),
                                
                                erlang:send(Process_id, {send,OutMessage});
                            true ->
                                io:format(" world nosend to self\n"),
                                
                                ok
                        end;
                    false ->
                        ok
                end;    
           (_Ohter) ->
               ok
        end,
        ToPlayerList
    ),
    
    ok.

