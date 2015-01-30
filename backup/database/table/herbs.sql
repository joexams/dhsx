DROP TABLE IF EXISTS `herbs`;

CREATE TABLE `herbs` 
(
     `id`                INTEGER          NOT NULL    AUTO_INCREMENT  COMMENT '草药id'
    ,`sign`              VARCHAR(50)      NOT NULL                    COMMENT '标识'
    ,`name`              VARCHAR(20)      NOT NULL                    COMMENT '草药名称'
    ,`ripe_time`         INTEGER          NOT NULL                    COMMENT '成长时间'
    ,`experience`        INTEGER          NOT NULL                    COMMENT '种植获得经验'
    ,`star_level`        INTEGER          NOT NULL                    COMMENT '星级'
    ,`lock`              INTEGER          NOT NULL                    COMMENT '同player_key表town字段 ,当town等于lock可种植改草药'
    ,CONSTRAINT `pk_herbs` 
        PRIMARY KEY (`id`)
)
COMMENT         = '草药信息'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;