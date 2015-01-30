DROP TABLE IF EXISTS `faction_job`;

CREATE TABLE `faction_job`
(
     `id`               INTEGER     NOT NULL    AUTO_INCREMENT  COMMENT '帮派职务ID'
    ,`sign`             VARCHAR(20) NOT NULL                    COMMENT '帮派职务标识'
    ,`name`             VARCHAR(20) NOT NULL                    COMMENT '帮派职务名称'

    ,CONSTRAINT `pk_faction_job`
        PRIMARY KEY (`id`)
    
)
COMMENT         = '帮派职务'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
