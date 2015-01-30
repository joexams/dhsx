DROP TABLE IF EXISTS `player_role`;

CREATE TABLE `player_role` 
(
     `id`           INTEGER     NOT NULL    AUTO_INCREMENT  COMMENT 'ID'
    ,`player_id`    INTEGER     NOT NULL                    COMMENT '玩家ID'
    ,`role_id`      INTEGER     NOT NULL                    COMMENT '角色ID'
    ,`level`        INTEGER     NOT NULL                    COMMENT '角色等级'
    ,`experience`   INTEGER     NOT NULL                    COMMENT '角色经验'
    ,`health`       INTEGER     NOT NULL                    COMMENT '生命值'
    ,`state`        INTEGER     NOT NULL                    COMMENT '状态  0 正常  1 下野'
    

    ,CONSTRAINT `pk_player_role` 
        PRIMARY KEY (`id`)
)
COMMENT         = '玩家角色表'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
