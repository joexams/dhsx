-define(DFLAST_SEND_TIME_POINT,undefine).

-define(INTERVAL,100).

-define(MESSAGE_LIST,[]).

-define(TIMEOUT,infinity).

-record(
    bc_info,
    {
        last_send_time_point=?DFLAST_SEND_TIME_POINT,      %最后一次发送消息到所有玩家的时间点
        interval=?INTERVAL,                                %服务器广播信息时间间隔(毫秒)
        message_list=?MESSAGE_LIST,                        %消息列表
        timeout=?TIMEOUT                                   %超时时间
    }
).