DROP TABLE IF EXISTS `role_stunt_type`;

CREATE TABLE `role_stunt_type`
(
     `id`                       INTEGER         NOT NULL    AUTO_INCREMENT  COMMENT 'id'
    ,`sign`                     VARCHAR(20)     NOT NULL                    COMMENT '标识'
    ,`name`                     VARCHAR(20)     NOT NULL                    COMMENT '名称'

    ,CONSTRAINT `pk_role_stunt_type`
        PRIMARY KEY (`id`)
)
COMMENT         = '战法类型,战法、霸体、奥义'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
