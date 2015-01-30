@echo off

cls

hstart "erl -sname gateway@localhost -setcookie game_server_cookie -pa bin -s game start_gateway"
hstart "erl -sname server_00@localhost -setcookie game_server_cookie -pa bin -s game start_server"