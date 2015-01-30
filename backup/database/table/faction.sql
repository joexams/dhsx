DROP TABLE IF EXISTS `faction`;

CREATE TABLE `faction`
(
     `id`               INTEGER     NOT NULL    AUTO_INCREMENT  COMMENT '帮派ID'
    ,`class_id`         INTEGER     NOT NULL                    COMMENT '阵营阵营ID'
    ,`name`             VARCHAR(20) NOT NULL                    COMMENT '帮派名称'
    ,`level`            INTEGER     NOT NULL                    COMMENT '帮派等级'
    ,`member_count`     INTEGER     NOT NULL                    COMMENT '帮派人数'
    ,`coins`            INTEGER     NOT NULL                    COMMENT '帮派铜钱'
    ,`description`      VARCHAR(255)NOT NULL                    COMMENT '帮派描述'
    ,`master_id`        INTEGER     NOT NULL                    COMMENT '帮主ID'
    ,`master_name`      VARCHAR(20) NOT NULL                    COMMENT '帮主名称'

    ,CONSTRAINT `pk_faction_class`
        PRIMARY KEY (`id`)

    ,CONSTRAINT `fk_faction_2_faction_class`
        FOREIGN KEY (`class_id`)
        REFERENCES `faction_class` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION

    ,CONSTRAINT `fk_faction_2_faction_level`
        FOREIGN KEY (`level`)
        REFERENCES `faction_level` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
    
)
COMMENT         = '帮派表'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
