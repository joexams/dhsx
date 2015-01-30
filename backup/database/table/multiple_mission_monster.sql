DROP TABLE IF EXISTS `multiple_mission_monster`;

CREATE TABLE `multiple_mission_monster`
(
     `id`                                INTEGER     NOT NULL    AUTO_INCREMENT  COMMENT 'ID'
    ,`multiple_mission_monster_team_id`  INTEGER     NOT NULL                    COMMENT '多人副本怪物团ID'
    ,`monster_id`                        INTEGER     NOT NULL                    COMMENT '怪物ID'
    ,`deploy_grid_id`                    INTEGER     NOT NULL                    COMMENT '阵法站位ID'

    ,CONSTRAINT `pk_multiple_mission_monster`
        PRIMARY KEY (`id`)

    ,CONSTRAINT `fk_multiple_mission_monster_2_multiple_mission_monster_team`
        FOREIGN KEY (`multiple_mission_monster_team_id`)
        REFERENCES `multiple_mission_monster_team` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION

    ,CONSTRAINT `fk_multiple_mission_monster_2_monster`
        FOREIGN KEY (`monster_id`)
        REFERENCES `monster` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION

    ,CONSTRAINT `fk_multiple_mission_monster_2_deploy_grid`
        FOREIGN KEY (`deploy_grid_id`)
        REFERENCES `deploy_grid` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
COMMENT         = '多人副本怪物团成员'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
