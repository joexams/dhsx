DROP TABLE IF EXISTS `town_npc_item`;

CREATE TABLE `town_npc_item`
(
     `id`           INTEGER     NOT NULL    AUTO_INCREMENT  COMMENT 'ID'
    ,`town_npc_id`  INTEGER     NOT NULL                    COMMENT '模板NPC ID'
    ,`item_id`      INTEGER     NOT NULL                    COMMENT '物品ID'

    ,CONSTRAINT `pk_town_npc_item`
        PRIMARY KEY (`id`)

    ,UNIQUE INDEX `ix_town_npc_item_npc_id_item_id` (
        `town_npc_id`   ASC,
        `item_id`       ASC
    )

    ,CONSTRAINT `fk_town_npc_item_2_town_npc`
        FOREIGN KEY (`town_npc_id`)
        REFERENCES `town_npc` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION

    ,CONSTRAINT `fk_town_npc_item_2_item`
        FOREIGN KEY (`item_id`)
        REFERENCES `item` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
COMMENT         = 'NPC携带物品表'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;