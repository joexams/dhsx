DROP TABLE IF EXISTS `mission_section_item`;

CREATE TABLE `mission_section_item` 
(
     `mission_section_id`   INTEGER     NOT NULL    AUTO_INCREMENT  COMMENT '剧情ID'
    ,`item_id`              INTEGER     NOT NULL                    COMMENT '物品ID'
    ,`number`               INTEGER     NOT NULL    DEFAULT 1       COMMENT '物品数量'

    ,UNIQUE INDEX `ix_mission_section_item__mission_section_id__item_id` (
        `mission_section_id`    ASC,
        `item_id`               ASC
    )
    
    ,CONSTRAINT `fk_mission_section_item_2_mission_section`
        FOREIGN KEY (`mission_section_id`)
        REFERENCES `mission_section` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
        
    ,CONSTRAINT `fk_mmission_section_item_2_item`
        FOREIGN KEY (`item_id`)
        REFERENCES `item` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
COMMENT         = '剧情奖励'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;