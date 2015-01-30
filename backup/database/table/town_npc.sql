DROP TABLE IF EXISTS `town_npc`;

CREATE TABLE `town_npc` 
(
     `id`           INTEGER     NOT NULL    AUTO_INCREMENT  COMMENT 'NPC实例ID'
    ,`town_id`      INTEGER     NOT NULL                    COMMENT '城镇ID'
    ,`npc_id`       INTEGER     NOT NULL                    COMMENT 'NPC 模板ID'
    ,`position_x`   INTEGER     NOT NULL                    COMMENT 'X轴坐标'
    ,`position_y`   INTEGER     NOT NULL                    COMMENT 'Y轴坐标'
    ,`resource_id`  INTEGER     NOT NULL                    COMMENT '图片或动画的资源ID'

    ,INDEX `ix_town_npc_town_id` (`town_id` ASC)

    ,CONSTRAINT `pk_town_npc` 
        PRIMARY KEY (`id`)

    ,CONSTRAINT `fk_town_npc_2_town`
        FOREIGN KEY (`town_id`)
        REFERENCES `town` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION

    ,CONSTRAINT `fk_town_npc_2_npc`
        FOREIGN KEY (`npc_id`)
        REFERENCES `npc` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
COMMENT         = '城镇NPC,具体城镇里面的NPC实例'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
