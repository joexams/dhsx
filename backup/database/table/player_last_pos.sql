DROP TABLE IF EXISTS `player_last_pos`;

CREATE TABLE `player_last_pos` 
(
     `player_id`    INTEGER     NOT NULL                COMMENT '玩家ID'
    ,`town_id`      INTEGER     NOT NULL    DEFAULT 1   COMMENT '玩家最后所在城镇'
    ,`x`            INTEGER     NOT NULL    DEFAULT 200 COMMENT '玩家最后所在位置'
    ,`y`            INTEGER     NOT NULL    DEFAULT 450 COMMENT '玩家最后所在位置'
    
    ,CONSTRAINT `pk_player_last_pos` 
        PRIMARY KEY (`player_id`)
)
COMMENT         = '玩家最后所在位置'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
