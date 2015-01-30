DROP TABLE IF EXISTS `player_item_upgrade_cd`;

CREATE TABLE `player_item_upgrade_cd`
(
    ,`player_id`        INTEGER     NOT NULL                    COMMENT '玩家ID'
    ,`player_item_id`   INTEGER     NOT NULL                    COMMENT '物品格子ID，不同ID段对应不同仓库'
    ,`upgrade_time`     INTEGER     NOT NULL                    COMMENT '升级操作的时间'
    
    ,CONSTRAINT `pk_player_item_upgrade_cd`
        PRIMARY KEY (`player_item_id`)

    ,CONSTRAINT `fk_player_item_upgrade_cd_2_player`
        FOREIGN KEY (`player_id`)
        REFERENCES `player` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
COMMENT         = '玩家的物品和装备'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
