DROP TABLE IF EXISTS `faction_member`;

CREATE TABLE `faction_member`
(
     `id`               INTEGER     NOT NULL    AUTO_INCREMENT  COMMENT 'ID'
    ,`faction_id`       INTEGER     NOT NULL                    COMMENT '帮派ID'
    ,`player_id`        INTEGER     NOT NULL                    COMMENT '玩家ID'
    ,`add_time`         INTEGER     NOT NULL                    COMMENT '加入时间'
    ,`job_id`           INTEGER     NOT NULL                    COMMENT '担任职务ID'

    ,CONSTRAINT `pk_faction_member`
        PRIMARY KEY (`id`)
    
)
COMMENT         = '帮派成员表'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
