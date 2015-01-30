DROP TABLE IF EXISTS `mission_monster`;

CREATE TABLE `mission_monster` 
(
     `id`                       INTEGER     NOT NULL    AUTO_INCREMENT  COMMENT 'ID'
    ,`mission_monster_team_id`  INTEGER     NOT NULL                    COMMENT '怪物团ID'
    ,`monster_id`               INTEGER     NOT NULL                    COMMENT '怪物ID'
    ,`deploy_grid_id`           INTEGER     NOT NULL                    COMMENT '阵法站位ID'

    ,CONSTRAINT `pk_mission_monster`
        PRIMARY KEY (`id`)
    
    ,CONSTRAINT `fk_mission_monster_2_mission_monster_team`
        FOREIGN KEY (`mission_monster_team_id`)
        REFERENCES `mission_monster_team` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
        
    ,CONSTRAINT `fk_mission_monster_2_monster`
        FOREIGN KEY (`monster_id`)
        REFERENCES `monster` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
        
    ,CONSTRAINT `fk_mission_monster_2_deploy_grid`
        FOREIGN KEY (`deploy_grid_id`)
        REFERENCES `deploy_grid` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
COMMENT         = '场景中的怪物团成员'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
