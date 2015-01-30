DROP TABLE IF EXISTS `role_stunt`;

CREATE TABLE `role_stunt` 
(
     `id`                   INTEGER         NOT NULL    AUTO_INCREMENT  COMMENT 'd 标识 role_stunt'
    ,`role_stunt_type_id`   INTEGER         NOT NULL                    COMMENT '战法类型'
    ,`sign`                 VARCHAR(30)     NOT NULL                    COMMENT '战法标识'
    ,`name`                 VARCHAR(10)     NOT NULL                    COMMENT '战法名称'
    ,`role_attack_range_id` INTEGER         NOT NULL                COMMENT '攻击范围'

    ,CONSTRAINT `pk_role_stunt` 
        PRIMARY KEY (`id`)

    ,CONSTRAINT `fk_role_job_2_role_stunt_type`
    FOREIGN KEY (`role_stunt_type_id`)
    REFERENCES `role_stunt_type` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION

    ,CONSTRAINT `fk_role_stunt_2_role_attack_range`
    FOREIGN KEY (`role_attack_range_id`)
    REFERENCES `role_attack_range` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
)
COMMENT         = '绝技'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;