DROP TABLE IF EXISTS `role_attack_range`;

CREATE TABLE `role_attack_range`
(
     `id`       INTEGER         NOT NULL    AUTO_INCREMENT  COMMENT 'id'
    ,`sign`     VARCHAR(20)     NOT NULL                    COMMENT '标识'
    ,`name`     VARCHAR(20)     NOT NULL                    COMMENT '名称'

    ,CONSTRAINT `pk_role_attack_range`
        PRIMARY KEY (`id`)
)
COMMENT         = '攻击范围'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
