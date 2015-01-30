DROP TABLE IF EXISTS `faction_request`;

CREATE TABLE `faction_request`
(
     `id`               INTEGER     NOT NULL    AUTO_INCREMENT  COMMENT '帮派阵营ID'
    ,`faction_id`       INTEGER     NOT NULL                    COMMENT '帮派ID'
    ,`player_id`        INTEGER     NOT NULL                    COMMENT '玩家ID'
    ,`req_time`         INTEGER     NOT NULL                    COMMENT '申请时间'

    ,CONSTRAINT `pk_faction_request`
        PRIMARY KEY (`id`)
    
)
COMMENT         = '加入帮派申请'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
