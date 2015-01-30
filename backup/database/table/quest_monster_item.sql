DROP TABLE IF EXISTS `quest_monster_item`;

CREATE TABLE `quest_monster_item` 
(
    `quest_id`             INTEGER     NOT NULL    COMMENT '任务ID'
    ,`item_id`              INTEGER     NOT NULL    COMMENT '物品ID'
    ,`item_count`           INTEGER     NOT NULL    COMMENT '物品数量'

    ,CONSTRAINT `pk_quest_monster_item`
        PRIMARY KEY `quest_id` (`quest_id`, `item_id`)

    ,CONSTRAINT `fk_quest_monster_item_2_quest`
        FOREIGN KEY (`quest_id` )
        REFERENCES `quest` (`id` )
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
        
    ,CONSTRAINT `fk_quest_monster_item_2_item`
        FOREIGN KEY (`item_id`)
        REFERENCES `item` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
COMMENT         = '任务物品关联'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
