%% -----------------------------------------------------------------------------
%% Descrip: ets 操作模块,存储公用缓存数据
%% author: qinlai.cai@gmail.com
%% -----------------------------------------------------------------------------
-module(ets_srv).

-behaviour(gen_server).

-export([
    start_link/1,
    create/1,
    create/2,
    create/3,
    create/4,
    insert/2,
    insert/3,
    get/2,
    get/3,
    match/2,
    match/3,
    check_record/2,
    update/3,
    delete/2,
    drop/1
]).
-export([
    init/1,
    handle_call/3,
    handle_cast/2,
    handle_info/2,
    terminate/2,
    code_change/3
]).

%% -----------------------------------------------------------------------------
%% Function: start_link()
%% Descrip:
%%
%% -----------------------------------------------------------------------------
start_link (CallBack) ->
    gen_server:start_link({local, ?MODULE}, ?MODULE, [CallBack], []).

%% -----------------------------------------------------------------------------
%% Function: create(TableName) -> TableName | error
%%              TableName: 表名[atom]
%% Descrip: 新建表
%%
%% -----------------------------------------------------------------------------
create (TableName) ->
    create(TableName, set, 1, protected).

%% -----------------------------------------------------------------------------
%% Function: create(TableName, Type) -> TableName | error
%%              TableName: 表名[atom]
%%              Type: 表类型[set|ordered_set|bag|duplicate_bag]
%% Descrip: 新建表
%%
%% -----------------------------------------------------------------------------
create (TableName, Type) ->
    create(TableName, Type, 1, protected).

%% -----------------------------------------------------------------------------
%% Function: create(TableName, Type, KeyPosition) -> TableName | error
%%              TableName: 表名[atom]
%%              Type: 表类型[set|ordered_set|bag|duplicate_bag]
%%              KeyPosition: key位置[int]
%% Descrip: 新建表
%%
%% -----------------------------------------------------------------------------
create (TableName, Type, KeyPosition) ->
    create (TableName, Type, KeyPosition, protected).

%% -----------------------------------------------------------------------------
%% Function: create(TableName, Type, KeyPosition) -> TableName | error
%%              TableName: 表名[atom]
%%              Type: 表类型[set|ordered_set|bag|duplicate_bag]
%%              KeyPosition: key位置[int]
%% Descrip: 新建表
%%
%% -----------------------------------------------------------------------------
create (TableName, Type, KeyPosition, Access) ->
    gen_server:call(?MODULE, {create, {TableName, Type, KeyPosition, Access}}).

%% -----------------------------------------------------------------------------
%% Function: insert(TableName, Value) -> true | false | error
%%              TableName: 表名[atom]
%%              Value: 值[list]
%% Descrip: 插入数据(set类型表，如果新的记录key与旧的key重复，插入会失败)
%%
%% -----------------------------------------------------------------------------
insert (TableName, Value) ->
    insert(TableName, Value, null).

%% -----------------------------------------------------------------------------
%% Function: insert_data(TableName, Value, Type) -> true | false | error
%%              TableName: 表名[atom]
%%              Value: 值[list]
%%              Type: 插入类型[replace]
%% Descrip: 插入数据(set类型表，如果新的记录key与旧的key重复，旧值会被覆盖)
%%
%% -----------------------------------------------------------------------------
insert (TableName, Value, Type) ->
     gen_server:call(?MODULE, {insert, {TableName, Value, Type}}).


%% -----------------------------------------------------------------------------
%% Function: get(TableName, Key) -> list
%%              TableName: 表名[atom]
%%              Key: key值[term]
%% Descrip: 根据Key值查询数据,返回整个记录
%%
%% -----------------------------------------------------------------------------
get (TableName, Key) ->
    get(TableName, Key, null).

%% -----------------------------------------------------------------------------
%% Function: get(TableName, Key, ColumnPosition) -> list
%%              TableName: 表名[atom]
%%              Key: key值[term]
%%              ColumnPosition: 要查询的列索引[int]
%% Descrip: 根据Key值查询数据,只返回单列数据
%%
%% -----------------------------------------------------------------------------
get (TableName, Key, ColumnPosition) ->
    gen_server:call(?MODULE, {get, {TableName, Key, ColumnPosition}}).

%% -----------------------------------------------------------------------------
%% Function: match(TableName, MatchExp) -> list
%%              TableName: 表名[atom]
%%              MatchExp: match匹配表达式
%% Descrip: match匹配查询数据,只返回单列数据
%%
%% -----------------------------------------------------------------------------
match (TableName, MatchExp) ->
    match(TableName, MatchExp, null).

%% -----------------------------------------------------------------------------
%% Function: match(TableName, MatchExp, Type) -> list
%%              TableName: 表名[atom]
%%              MatchExp: match匹配表达式
%%              Type: match查询类型[object]
%% Descrip: match匹配查询数据，返回整个记录
%%
%% -----------------------------------------------------------------------------
match (TableName, MatchExp, Type) ->
    gen_server:call(?MODULE, {match, {TableName, MatchExp, Type}}).


%% -----------------------------------------------------------------------------
%% Function: check_record(TableName, Key) -> true | flase | error
%%              TableName: 表名[atom]
%%              Key: key值
%% Descrip: 根据key值检查记录是否存在
%%
%% -----------------------------------------------------------------------------
check_record (TableName, Key) ->
    gen_server:call(?MODULE, {check_record, {TableName, Key}}).


%% -----------------------------------------------------------------------------
%% Function: update(TableName, Key, Value) -> true | flase | error
%%              TableName: 表名[atom]
%%              Key: key值
%%              Value: 要更新的值
%% Descrip: 根据Key值更新数据
%%
%% -----------------------------------------------------------------------------
update (TableName, Key, Value) ->
     gen_server:call(?MODULE, {update, {TableName, Key, Value}}).


%% -----------------------------------------------------------------------------
%% Function: delete(TableName, Key) -> true | flase | error
%%              TableName: 表名[atom]
%%              Key: key值[term]
%% Descrip: 根据Key值删除数据
%%
%% -----------------------------------------------------------------------------
delete (TableName, Key) ->
    gen_server:call(?MODULE, {delete, {TableName, Key}}).


%% -----------------------------------------------------------------------------
%% Function: drop(TableName) -> true | flase | error
%%              TableName: 表名[atom]
%% Descrip: 删除表
%%
%% -----------------------------------------------------------------------------
drop (TableName) ->
    gen_server:call(?MODULE, {drop, {TableName}}).



%% -----------------------------------------------------------------------------
init ([CallBack]) ->
    [apply(M, F, A) || {M, F, A} <- CallBack],
    {ok, 0}.
    
handle_cast (_Arg, _State) ->
    {noreply, ok}.

%% 创建表
handle_call ({create, {TableName, Type, KeyPosition, Access}}, _From, State) ->
    Result = lib_ets:create(TableName, Type, KeyPosition, Access),
    {reply, Result, State};

%% 插入数据
handle_call ({insert, {TableName, Value, Type}}, _From, State) ->
    Result = lib_ets:insert(TableName, Value, Type),
    {reply, Result, State};

%% 根据key查询数据
handle_call ({get, {TableName, Key, ColumnPosition}}, _From, State) ->
    Result = lib_ets:get(TableName, Key, ColumnPosition),
    {reply, Result, State};

 %% match查询数据
 handle_call ({match, {TableName, MatchExp, Type}}, _From, State) ->
    Result = lib_ets:match(TableName, MatchExp, Type),
    {reply, Result, State};

%% 记录是否存在
handle_call ({check_record, {TableName, Key}}, _From, State) ->
    Result = lib_ets:check_record(TableName, Key),
    {reply, Result, State};

%% 更新数据
handle_call ({update, {TableName, Key, Value}}, _From, State) ->
    Result = lib_ets:update_element(TableName, Key, Value),
    {reply, Result, State};

%% 删除数据
handle_call ({delete, {TableName, Key}}, _From, State) ->
    Result = lib_ets:delete(TableName, Key),
    {reply, Result, State};

%% 删除表
handle_call ({drop, {TableName}}, _From, State) ->
    Result = lib_ets:drop(TableName),
    {reply, Result, State}.

handle_info (_Msg, _State) ->
    {noreply, ok}.

terminate (_Reason, _State) ->
    {noreply, ok}.

code_change (_OldSvn, _State, _Ext) ->
    {noreply, ok}.