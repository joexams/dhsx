-define(SELL_ITEM_RECYCLE_TIMEOUT,  1200).  % 秒
-define(SELL_ITEM_DISCOUNT_RATE,    30).    % 百分比

-define(CD_TYPE_UPGRADE_EQUIP,  1).    % 装备强化冷却时间类别
-define(CD_TYPE_RESEARCH,       2).    % 科技升级冷却时间类别
-define(CD_TIME_UPGRADE_EQUIP,  300).  % 装备强化冷却时间

-define(SMALL_MEDICAL_ITEM_ID,  7).    % 小气血包的item_id
-define(SMALL_MEDICAL_VALUE,    7).    % 小气血包的血量

-record(
    node_info, 
    {
        node, 
        addr, 
        port, 
        conn, 
        max_conn
    }
).

-record(
    conn_info,                  %连接信息
    {
        sock,                   %套接字
        state,                  %连接状态
        player_id = undefined,  %玩家ID
        nickname  = undefined,  %玩家昵称
        town_id   = undefined   %玩家所在城镇ID
    }
).
