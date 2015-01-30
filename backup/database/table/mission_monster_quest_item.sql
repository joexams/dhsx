DROP TABLE IF EXISTS `mission_monster_quest_item`;

CREATE TABLE `mission_monster_quest_item` 
(
     `id`                   INTEGER     NOT NULL    AUTO_INCREMENT  COMMENT 'ID'
    ,`mission_monster_id`   INTEGER     NOT NULL    COMMENT '场景中的怪物ID'
    ,`quest_id`             INTEGER     NOT NULL    COMMENT '任务ID'
    ,`item_id`              INTEGER     NOT NULL    COMMENT '物品ID'
    ,`probability`          INTEGER     NOT NULL    COMMENT '掉落概率'

    ,CONSTRAINT `pk_mission_monster_quest_item`
        PRIMARY KEY (`id`)

    ,CONSTRAINT `fk_mission_monster_quest_item_2_mission_monster`
        FOREIGN KEY (`mission_monster_id` )
        REFERENCES `mission_monster` (`id` )
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
        
    ,CONSTRAINT `fk_mission_monster_quest_item_2_quest`
        FOREIGN KEY (`quest_id`)
        REFERENCES `quest` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
        
    ,CONSTRAINT `fk_mission_monster_quest_item_2_item`
        FOREIGN KEY (`item_id`)
        REFERENCES `item` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
COMMENT         = '副本怪物掉落的任务物品'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
