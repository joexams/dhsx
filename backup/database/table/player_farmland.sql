DROP TABLE IF EXISTS `player_farmland`;

CREATE TABLE `player_farmland` 
(
     `id`                      INTEGER          NOT NULL    AUTO_INCREMENT   COMMENT '农田id'
    ,`player_id`               INTEGER          NOT NULL                     COMMENT '玩家id'
    ,`herbs_id`                INTEGER                                       COMMENT '种植在农田上的草药id'                 
    ,`player_role_id`          INTEGER                                       COMMENT '种植草药的角色id'
    ,`harvest_time`            INTEGER                                       COMMENT '草药收获时间'
    ,CONSTRAINT `pk_player_farmland` 
        PRIMARY KEY (`id`)

    ,CONSTRAINT `fk_player_farmland_2_player`
        FOREIGN KEY (`player_id`)
        REFERENCES `player` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
    
     ,CONSTRAINT `fk_player_farmland_2_herbs`
        FOREIGN KEY (`herbs_id`)
        REFERENCES `herbs` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
COMMENT         = '玩家农田'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;