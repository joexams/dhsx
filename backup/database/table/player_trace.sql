DROP TABLE IF EXISTS `player_trace`;

CREATE TABLE `player_trace` 
(
     `player_id`        INTEGER     NOT NULL    COMMENT '玩家ID'
    ,`last_login_ip`    CHAR(15)    NOT NULL    COMMENT '最后登录IP'
    ,`last_login_time`  DATETIME    NOT NULL    COMMENT '最后登录时间'
    ,`first_login_ip`   CHAR(15)    NOT NULL    COMMENT '首次登录IP'
    ,`first_login_time` DATETIME    NOT NULL    COMMENT '首次登录时间'
    
    ,CONSTRAINT `pk_player_trace` 
        PRIMARY KEY (`player_id`)
)
COMMENT         = '玩家追踪数据'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;