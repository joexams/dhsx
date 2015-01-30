DROP TABLE IF EXISTS `player_cd_time`;

CREATE TABLE `player_cd_time`
(
     `player_id`    INTEGER     NOT NULL                    COMMENT '玩家ID'
    ,`cd_type_id`   INTEGER     NOT NULL                    COMMENT '角色ID'
    ,`expire_time`  INTEGER     NOT NULL                    COMMENT '角色等级'

    ,CONSTRAINT `pk_player_cd_time`
        PRIMARY KEY (
            `player_id`,
            `cd_type_id`
        )
)
COMMENT         = '玩家冷却时间表'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
