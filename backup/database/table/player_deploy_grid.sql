DROP TABLE IF EXISTS `player_deploy_grid`;

CREATE TABLE `player_deploy_grid` 
(
     `player_role_id`   INTEGER     NOT NULL    COMMENT '玩家角色ID'
    ,`deploy_grid_id`   INTEGER     NOT NULL    COMMENT '阵法站位ID'
    ,`player_id`        INTEGER     NOT NULL    COMMENT '玩家ID'
    ,`deploy_mode_id`   INTEGER     NOT NULL    COMMENT '阵形ID'

    ,INDEX `ix_player_deploy_grid__player_role_id` (`player_role_id` ASC)
    ,INDEX `ix_player_deploy_grid__player_id` (`player_id` ASC)
    ,PRIMARY KEY (player_role_id, `deploy_grid_id`)
    ,CONSTRAINT `fk_player_deploy_grid_2_player_role`
        FOREIGN KEY (`player_role_id`)
        REFERENCES `player_role` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
        
    ,CONSTRAINT `fk_player_deploy_grid_2_deploy_grid`
        FOREIGN KEY (`deploy_grid_id`)
        REFERENCES `deploy_grid` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
        
    ,CONSTRAINT `fk_player_deploy_grid_2_player`
        FOREIGN KEY (`player_id`)
        REFERENCES `player` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
COMMENT         = '玩家阵法部署'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;