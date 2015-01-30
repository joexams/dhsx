%% -----------------------------------------------------------------------------
%% Descrip : ets 操作模块
%% Author  : qinlai.cai@gmail.com
%% -----------------------------------------------------------------------------
-module(lib_ets).

-export([
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
    select/2,
    check_record/2,
    update/3,
    delete/2,
    drop/1,
    select_delete/2
]).

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
    create(TableName, Type, KeyPosition, protected).

%% -----------------------------------------------------------------------------
%% Function: create(TableName, Type, KeyPosition, Access) -> TableName | error
%%              TableName: 表名[atom]
%%              Type: 表类型[set|ordered_set|bag|duplicate_bag]
%%              KeyPosition: key位置[int]
%% Descrip: 新建表
%%
%% -----------------------------------------------------------------------------
create (TableName, Type, KeyPosition, Access) ->
    case catch ets:new(TableName, [Type, named_table, Access, {keypos, KeyPosition}]) of
        {'EXIT', _Reason} ->
            error;
        Success ->
            Success
    end.


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
     case Type of
        null ->
            case catch ets:insert_new(TableName, Value) of
                {'EXIT', _Reason} ->
                    error;
                Success ->
                    Success
            end;
        _Other ->
            case catch ets:insert(TableName, Value) of
                {'EXIT', _Reason} ->
                    error;
                Success ->
                    Success
            end
    end.


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
    case ColumnPosition of
        null ->
            case catch ets:lookup(TableName, Key) of
                {'EXIT', _Reason} ->
                    [];
                Data ->
                    Data
            end;
        Position ->
            case catch ets:lookup_element(TableName, Key, Position) of
                {'EXIT', _Reason} ->
                    [];
                Data ->
                    Data
            end
    end.

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
    case Type of
        null ->
            case catch ets:match(TableName, MatchExp) of
                {'EXIT', _Reason} ->
                    [];
                Data ->
                    Data
            end;
        _Other ->
            case catch ets:match_object(TableName, MatchExp) of
                {'EXIT', _Reason} ->
                    [];
                Data ->
                    Data
            end
    end.

%% -----------------------------------------------------------------------------
%% Function: select(TableName, MatchSpec) -> list
%%              TableName: 表名[atom]
%%              MatchSpec: match_spec()
%% Descrip: match匹配查询数据，返回整个记录
%%
%% -----------------------------------------------------------------------------
select (TableName, MatchSpec) ->
    case catch ets:select(TableName, MatchSpec) of
        {'EXIT', _Reason} ->
            [];
        Data ->
            Data
    end.

%% -----------------------------------------------------------------------------
%% Function: check_record(TableName, Key) -> true | flase | error
%%              TableName: 表名[atom]
%%              Key: key值
%% Descrip: 根据key值检查记录是否存在
%%
%% -----------------------------------------------------------------------------
check_record (TableName, Key) ->
    case catch ets:member(TableName, Key) of
        {'EXIT', _Reason} ->
            error;
        Success ->
            Success
    end.


%% -----------------------------------------------------------------------------
%% Function: update(TableName, Key, Value) -> true | flase | error
%%              TableName: 表名[atom]
%%              Key: key值
%%              Value: 要更新的值
%% Descrip: 根据Key值更新数据
%%
%% -----------------------------------------------------------------------------
update (TableName, Key, Value) ->
     case catch ets:update_element(TableName, Key, Value) of
        {'EXIT', _Reason} ->
            error;
        Success ->
            Success
    end.


%% -----------------------------------------------------------------------------
%% Function: delete(TableName, Key) -> true | flase | error
%%              TableName: 表名[atom]
%%              Key: key值[term]
%% Descrip: 根据Key值删除数据
%%
%% -----------------------------------------------------------------------------
delete (TableName, Key) ->
    case catch ets:delete(TableName, Key) of
        {'EXIT', _Reason} ->
            error;
        Success ->
            Success
    end.


%% -----------------------------------------------------------------------------
%% Function: drop(TableName) -> true | flase | error
%%              TableName: 表名[atom]
%% Descrip: 删除表
%%
%% -----------------------------------------------------------------------------
drop (TableName) ->
    case catch ets:delete(TableName) of
        {'EXIT', _Reason} ->
            error;
        Success ->
            Success
    end.


select_delete (TableName, MatchSpec) ->
    case catch ets:select_delete(TableName, MatchSpec) of
        {'EXIT', _Reason} ->
            error;
        Success ->
            Success
    end.

