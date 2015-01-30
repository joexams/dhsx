DROP TABLE IF EXISTS `player_herbs`;

CREATE TABLE `player_herbs` 
(
     `player_id`                INTEGER          NOT NULL             COMMENT '玩家id'
    ,`herbs_id`                 INTEGER          NOT NULL             COMMENT '草药id' 
    ,`refresh_money`            INTEGER          NOT NULL             COMMENT '刷新所需元宝'
    ,CONSTRAINT `pk_player_herbs` 
        PRIMARY KEY (`player_id`)
    ,CONSTRAINT `fk_player_herbs_2_player`
        FOREIGN KEY (`player_id`)
        REFERENCES `player` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
    ,CONSTRAINT `fk_player_herbs_2_herbs`
        FOREIGN KEY (`herbs_id`)
        REFERENCES `herbs` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
COMMENT         = '草药信息'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
