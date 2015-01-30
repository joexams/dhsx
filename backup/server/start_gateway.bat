@echo off

cls

erl -sname gateway@localhost -setcookie game_server_cookie -pa bin -s game start_gateway