DROP TABLE IF EXISTS `role_job`;

CREATE TABLE `role_job` 
(
     `id`                       INTEGER         NOT NULL    AUTO_INCREMENT  COMMENT '角色ID'
    ,`role_attack_range_id`     INTEGER         NOT NULL                    COMMENT '攻击范围'
    ,`sign`                     VARCHAR(20)     NOT NULL                    COMMENT '标识'
    ,`name`                     VARCHAR(10)     NOT NULL                    COMMENT '名称'

    ,CONSTRAINT `pk_role_job` 
        PRIMARY KEY (`id`)
        
    ,CONSTRAINT `fk_role_job_2_role_attack_range`
        FOREIGN KEY (`role_attack_range_id`)
        REFERENCES `role_attack_range` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
COMMENT         = '职业'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
