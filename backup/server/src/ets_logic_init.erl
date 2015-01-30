-module(ets_logic_init).

-include("ets_logic.hrl").

-export([
    init/0,
    gen_probability/0]
).

init () ->
    init_town_player(),
    init_player_position(),
    init_recycle_player_item(),
    init_probability(),
    ok.

    
init_town_player () ->
    ets:new(town_player, [bag, named_table, public, {keypos, #town_player.town_id}]),
    ok.
    
init_player_position() ->
    ets:new(player_position, [set, named_table, public, {keypos, #player_position.player_id}]),
    ok.
    
init_recycle_player_item() ->
    ets:new(recycle_player_item, [set, named_table, public, {keypos, #recycle_player_item.ets_key}]),
    ok.

init_probability() ->
    ets:new(probability, [ordered_set , named_table, public, {keypos, #recycle_player_item.ets_key}]),

    ets:insert(probability, gen_probability()),
    
    ok.
