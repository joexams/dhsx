#!/bin/sh
erl -sname server_00@localhost -setcookie game_server_cookie -pa bin -s game start_server -detached