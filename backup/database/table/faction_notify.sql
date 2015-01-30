DROP TABLE IF EXISTS `faction_notify`;

CREATE TABLE `faction_notify`
(
     `id`               INTEGER         NOT NULL    AUTO_INCREMENT  COMMENT '帮派阵营ID'
    ,`faction_id`       INTEGER         NOT NULL                    COMMENT '帮派ID'
    ,`player_id`        INTEGER         NOT NULL                    COMMENT '玩家ID'
    ,`content`          VARCHAR(280)    NOT NULL                    COMMENT '公告内容名称'
    ,`req_time`         INTEGER         NOT NULL                    COMMENT '发布时间'

    ,CONSTRAINT `pk_faction_notify`
        PRIMARY KEY (`id`)
    
)
COMMENT         = '帮派阵营'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
