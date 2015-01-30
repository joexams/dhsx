DROP TABLE IF EXISTS `cd_time_type`;

CREATE TABLE `cd_time_type`
(
     `id`               INTEGER     NOT NULL    AUTO_INCREMENT  COMMENT '冷却时间类型ID'
    ,`cd_type_name`     VARCHAR(20) NOT NULL                    COMMENT '冷却时间类型名称'
    ,`ingot_time_ratio` INTEGER     NOT NULL                    COMMENT '1个元宝可以减少的冷却时间秒数, 如果为0则不消耗元宝'

    ,CONSTRAINT `pk_cd_time_type`
        PRIMARY KEY (`id`)
)
COMMENT         = '冷却时间类别表'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;