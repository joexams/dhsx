DROP TABLE IF EXISTS `player_item`;

CREATE TABLE `player_item` 
(
     `id`               INTEGER     NOT NULL   AUTO_INCREMENT   COMMENT 'ID(玩家格子实例)'
    ,`player_id`        INTEGER     NOT NULL                    COMMENT '玩家ID'
    ,`grid_id`          INTEGER     NOT NULL                    COMMENT '物品格子ID，不同ID段对应不同仓库'
    ,`item_id`          INTEGER     NULL                        COMMENT '物品ID'
    ,`number`           INTEGER     NULL                        COMMENT '存放数量(当前格子)'
    ,`upgrade_level`    INTEGER     NULL                        COMMENT '强化等级'
    ,`player_role_id`   INTEGER     NULL                        COMMENT '玩家角色ID,当格子为201-300时有意义'
    ,`sell_lock`        INTEGER     NOT NULL DEFAULT 0          COMMENT '卖出锁定，0否，1是'
    
    ,CONSTRAINT `pk_player_item` 
        PRIMARY KEY (`id`)

    ,CONSTRAINT `fk_player_item_2_player`
        FOREIGN KEY (`player_id`)
        REFERENCES `player` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION

    ,CONSTRAINT `fk_player_item_2_item_pack_grid`
        FOREIGN KEY (`grid_id`)
        REFERENCES `item_pack_grid` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION

    ,CONSTRAINT `fk_player_item_2_player_role`
        FOREIGN KEY (`player_role_id`)
        REFERENCES `player_role` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION

    ,CONSTRAINT `fk_player_item_2_item`
        FOREIGN KEY (`item_id`)
        REFERENCES `item` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION

    ,CONSTRAINT `fk_player_item_2_item_upgrade`
        FOREIGN KEY (`upgrade_level`)
        REFERENCES `item_upgrade` (`level`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
COMMENT         = '玩家的物品和装备'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
