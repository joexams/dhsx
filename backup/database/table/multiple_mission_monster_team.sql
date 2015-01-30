DROP TABLE IF EXISTS `multiple_mission_monster_team`;

CREATE TABLE `multiple_mission_monster_team`
(
     `id`                   INTEGER     NOT NULL    AUTO_INCREMENT  COMMENT '怪物团ID'
    ,`multiple_mission_id`  INTEGER     NOT NULL                    COMMENT '多人副本ID'
    ,`lock`                 INTEGER     NOT NULL                    COMMENT '怪物团权值'
    ,`deploy_mode_id`       INTEGER     NOT NULL                    COMMENT '阵法'

    ,CONSTRAINT `pk_multiple_mission_monster_team`
        PRIMARY KEY (`id`)

    ,CONSTRAINT `fk_multiple_mission_monster_team_2_deploy_mode`
        FOREIGN KEY (`deploy_mode_id`)
        REFERENCES `deploy_mode` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION

    ,CONSTRAINT `fk_multiple_mission_monster_team_2_multiple_mission`
        FOREIGN KEY (`multiple_mission_id`)
        REFERENCES `multiple_mission` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
COMMENT         = '多人副本怪物团'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
