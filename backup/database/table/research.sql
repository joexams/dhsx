DROP TABLE IF EXISTS `research`;

CREATE TABLE `research` 
(
     `id`               INTEGER         NOT NULL    AUTO_INCREMENT  COMMENT 'ID'
    ,`name`             VARCHAR(34)     NOT NULL                    COMMENT '奇术名称'
    ,`research_type_id` INTEGER         NOT NULL                    COMMENT '奇术类型'
    ,`research_key`     INTEGER         NOT NULL                    COMMENT '等级显示解锁权限'
    ,`content`          text            NOT NULL                    COMMENT '奇术详细说明'
    ,`addition_type_id`            INTEGER                                     COMMENT '数值类型' 
    ,`research_data_type_id`    INTEGER         NOT NULL                    COMMENT '奇术影响的数值种类ID'

    ,CONSTRAINT `pk_research` 
        PRIMARY KEY (`id`)
)
COMMENT         = '奇术表'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
