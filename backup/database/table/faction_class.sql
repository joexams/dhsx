DROP TABLE IF EXISTS `faction_class`;

CREATE TABLE `faction_class`
(
     `id`               INTEGER     NOT NULL    AUTO_INCREMENT  COMMENT '帮派阵营ID'
    ,`sign`             VARCHAR(20) NOT NULL                    COMMENT '阵营标识'
    ,`name`             VARCHAR(20) NOT NULL                    COMMENT '阵营名称'

    ,CONSTRAINT `pk_faction_class`
        PRIMARY KEY (`id`)
    
)
COMMENT         = '帮派阵营表'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
