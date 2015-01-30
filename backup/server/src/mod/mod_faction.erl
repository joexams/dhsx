-module(mod_faction).

-include_lib("stdlib/include/ms_transform.hrl").

-include("db.hrl").
-include("api_10.hrl").

-define(FOUND_FACTION_COINS, 50000).


-compile(export_all).

-export([
    faction_list/4,             % 01
    faction_info /1,            % 02
    faction_request/1,          % 03
    found_faction/2            % 04
%    faction_members_list/4,     %05
%    facton_notify_list/4,     %
%    request_list/4,           %
%    accept_request/4,         %
%    deny_request/4,           %
%    appoint_job/4,            %
%    master_transfer/4,        %
%    kick_out_member/4,        %
%    master_disband/4          %

]).


% 01
faction_list(FactionName, FactionMaster, Requesting, PageNumber) ->
    {
        NewPageTotal, NewPageNumber, FactionList
    } = get_faction_list(FactionName, FactionMaster, Requesting, PageNumber),

    NewFactionList = [faction_props(Faction) || Faction <- FactionList],

    {
        NewPageNumber,
        NewPageTotal,
        NewFactionList
    }.


get_faction_list(_FactionName, _FactionMaster, _Requesting, _PageNumber) ->
    [].

faction_props(Faction) ->
    Faction.



% 02
faction_info(FactionId) ->

    get_faction_info(FactionId).

get_faction_info(FactionId) ->
    {
        FactionId       = 1,
        _FactionName     = "帮派名称",
        _FactionMaster   = 1,
        _MasterLevel     = 1,
        _FactionCoins    = 1,
        _MemberNumber    = 1,
        _MaxMember       = 1,
        _FactionRanking  = 1,
        _FactionDesc     = "帮派说明"
    }.


% 03
faction_request(FactionId) ->
    try faction_request_real(FactionId) of
        _  ->
            {?ACTION_SUCCESS, FactionId}
    catch
        throw : faction_not_exist ->
            {?FACTION_NOT_EXIST, 0};
        throw : faction_requesting ->
            {?FACTION_REQUESTING, 0};
        _ : _ ->
            {?ERROR_UNDEFINED, 0}
    end.


faction_request_real(FactionId) ->
    Faction = get_faction_info(FactionId),
    if
        Faction == false ->
            throw(faction_not_exist);
        true ->
            noop
    end,

    PlayerId = mod_player:get_player_id(),

    case get_faction_request(FactionId, PlayerId) of
        false ->
            noop;
        _ ->
            throw(faction_requesting)
    end,

    add_faction_request(FactionId, PlayerId).


add_faction_request(_FactionId, _PlayerId) ->
    1.


get_faction_request(_FactionId, _PlayerId) ->
    true.


% 04
found_faction(FactionName, FactionClass) ->
    try found_faction_real(FactionName, FactionClass) of
        FactionId  ->
            {?ACTION_SUCCESS, FactionId}
    catch
        throw : insufficient_coins ->
            {?INSUFFICIENT_COINS, 0};
        throw : found_one_faction ->
            {?FOUND_ONE_FACTION, 0};
        throw : faction_name_invalid ->
            {?FACTION_NAME_INVALID, 0};
        throw : faction_name_existed ->
            {?FACTION_NAME_EXISTED, 0};
        _ : _ ->
            {?ERROR_UNDEFINED, 0}
    end.


found_faction_real(FactionName, FactionClass) ->
    case mod_player:check_coin(?FOUND_FACTION_COINS) of
        false ->
            throw(insufficient_coins);
        true ->
            false
    end,

    PlayerId = mod_player:get_player_id(),

    FoundFaction = get_found_faction(PlayerId),
    if
        FoundFaction =/= false ->
            throw(found_one_faction);
        true ->
            noop
    end,

    case check_faction_name_valid(FactionName) of
        false ->
            throw(faction_name_invalid);
        _ ->
            false
    end,

    case check_faction_name_exist(FactionName) of
        true ->
            throw(faction_name_existed);
        _ ->
            false
    end,

    player_found_faction(PlayerId, FactionName, FactionClass).


get_found_faction(_PlayerId) ->
    true.

check_faction_name_exist(_FactionName) ->
    true.

check_faction_name_valid(_FactionName) ->
    true.

player_found_faction(PlayerId, FactionName, FactionClass) ->
    _FactionLevel = get_faction_level(1),

    PlayerName = mod_player:get_player_name(),

    mod_player:decrease_coin(?FOUND_FACTION_COINS),
    Faction = #faction{
        class_id    = FactionClass,
        name        = FactionName,
        level       = 1,
        member_count= 1,
        coins       = ?FOUND_FACTION_COINS,
        description = "",
        master_id   = PlayerId,
        master_name = PlayerName
    },
    Faction.

get_faction_level(_Level) ->
    ok.

% 05
faction_members_list(PageNumber, Order) ->
    FactionId = get_faction_id(),
    {
        PageCurrent,
        PageTotal,
        Order,
        FactionMemberList
    } = get_faction_member_list(FactionId, PageNumber, Order),

    NewFactionList = [member_props(Member) || Member <- FactionMemberList],

    {
        PageCurrent,
        PageTotal,
        Order,
        NewFactionList
    }.


get_faction_member_list(_FactionId, _PageNumber, _Order) ->
    [].

member_props(Member) ->
%    {
%        player_id           : int       // 玩家ID
%        player_name         : int       // 玩家名称
%        player_level        : string    // 玩家等级
%        faction_job         : short     // 帮派职务
%        contribution        : short     // 贡献
%        ranking             : short     // 排名
%    }
    Member.


% 06
facton_notify_list(PageNumber) ->
    FactionId = get_faction_id(),

    {
        PageCurrent,
        PageTotal,
        NotifyList
    }  = get_notify_list(FactionId, PageNumber),

    NewList = [notify_props(Notify) || Notify <- NotifyList],

    {
        PageCurrent,
        PageTotal,
        NewList
    }.


notify_props(Notify) ->
%    {
%        player_id           : int       // 玩家ID
%        player_name         : int       // 玩家名称
%        player_level        : string    // 玩家等级
%        faction_job         : short     // 帮派职务
%        contribution        : short     // 贡献
%        ranking             : short     // 排名
%    }
    Notify.


get_faction_id() ->
    1.


get_notify_list(FactionId, PageNumber) ->
    FactionId,
    PageNumber,
    [].


% 07
request_list(PageNumber) ->
    FactionId = get_faction_id(),

    {
        PageCurrent,
        PageTotal,
        RequestList
    }  = get_request_list(FactionId, PageNumber),

    NewList = [request_props(Request) || Request <- RequestList],

    {
        PageCurrent,
        PageTotal,
        NewList
    }.


get_request_list(FactionId, PageNumber) ->
    FactionId,
    PageNumber,
    [].


request_props(Request) ->
%    {
%                request_id          : int       // 公告ID
%                player_id           : int       // 发布者ID
%                player_name         : string    // 发布者
%                player_level        : string    // 发布者
%                timestamp           : int       // 发布时间
%    }
    Request.


% 08
accept_request(RequestId) ->
    try accept_request_real(RequestId) of
        _  ->
            {?ACTION_SUCCESS}
    catch
        throw : request_not_exist ->
            {?REQUEST_NOT_EXIST};
        throw : faction_requesting ->
            {?FACTION_REQUESTING};
        throw : faction_manage_no_permit ->
            {?FACTION_MANAGE_NO_PERMIT};
        throw : faction_max_member ->
            {?FACTION_MAX_MEMBER};
        _ : _ ->
            {?ERROR_UNDEFINED}
    end.


accept_request_real(RequestId) ->
    Request = get_faction_request(RequestId),
    if
        Request == false ->
            throw(request_not_exist);
        true ->
            noop
    end,

    case check_faction_accept_request_permit() of
        false ->
            throw(faction_manage_no_permit);
        _ ->
            noop
    end,

    Faction = get_faction_info(get_faction_id()),
    case get_faction_max_member(Faction) of
        _MaxMember when _MaxMember < 100 -> % when Faction #faction.members >= MaxMember ->
            throw(?FACTION_MAX_MEMBER);
        _ ->
            noop
    end,

    faction_add_member(Faction, Request),
    delete_request(Request).


check_faction_accept_request_permit() ->
    _PlayerId = mod_player:get_player_id(),
    true.


get_faction_max_member(_Faction) ->
    % FactionLevel = Faction #faction.level,
    % FactionLevel #faction_level.max_member.
    10.

get_faction_request(RequestId) ->
    RequestId.


faction_add_member(FactionId, Request) ->
    {FactionId, Request}.


delete_request(_Request) ->
    ok.


deny_request(RequestId) ->
    try deny_request_real(RequestId) of
        _  ->
            {?ACTION_SUCCESS}
    catch
        throw : request_not_exist ->
            {?REQUEST_NOT_EXIST};
        throw : faction_requesting ->
            {?FACTION_REQUESTING};
        throw : faction_manage_no_permit ->
            {?FACTION_MANAGE_NO_PERMIT};
        throw : faction_max_member ->
            {?FACTION_MAX_MEMBER};
        _ : _ ->
            {?ERROR_UNDEFINED}
    end.


deny_request_real(RequestId) ->
    Request = get_faction_request(RequestId),
    if
        Request == false ->
            throw(request_not_exist);
        true ->
            noop
    end,

    case check_faction_accept_request_permit() of
        false ->
            throw(faction_manage_no_permit);
        _ ->
            noop
    end,

    delete_request(Request).


appoint_job(_FactionId, _PlayerId) ->
    ok.


dismiss_job(_Faction_Id, _PlayerId) ->
    ok.


master_transfer(FactionId, PlayerId) ->
    FactionId,
    PlayerId,
    ok.


kick_out_member(FactionId, PlayerId) ->
    FactionId,
    PlayerId,
    ok.


disband_faction(_FactionId) ->
    ok.