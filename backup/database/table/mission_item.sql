DROP TABLE IF EXISTS `mission_item`;

CREATE TABLE `mission_item` 
(
     `mission_id`   INTEGER     NOT NULL                    COMMENT '副本ID'
    ,`item_id`      INTEGER     NOT NULL                    COMMENT '物品ID'
    ,`number`       INTEGER     NOT NULL    DEFAULT 1       COMMENT '物品数量'
    ,`probability`  FLOAT       NOT NULL    DEFAULT 1       COMMENT '概率'

    ,UNIQUE INDEX `ix_mission_item__mission_id__item_id` (
        `mission_id`    ASC, 
        `item_id`       ASC
    )
    
    ,CONSTRAINT `fk_mission_item_2_mission`
        FOREIGN KEY (`mission_id`)
        REFERENCES `mission` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
COMMENT         = '副本奖励'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
