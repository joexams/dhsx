DROP TABLE IF EXISTS `mission_monster_team`;

CREATE TABLE `mission_monster_team` 
(
     `id`               INTEGER     NOT NULL    AUTO_INCREMENT  COMMENT '怪物团ID'
    ,`mission_scene_id` INTEGER     NOT NULL                    COMMENT '场景ID'
    ,`lock`             INTEGER     NOT NULL                    COMMENT '怪物团权值'
    ,`deploy_mode_id`   INTEGER     NOT NULL                    COMMENT '阵法'
    ,`monster_id`       INTEGER     NOT NULL                    COMMENT '代表怪ID'
    ,`position_x`       INTEGER     NOT NULL    DEFAULT 0       COMMENT '场景中x坐标'
    ,`position_y`       INTEGER     NOT NULL    DEFAULT 0       COMMENT '场景中y坐标'

    ,CONSTRAINT `pk_mission_monster_team`
        PRIMARY KEY (`id`)
    
    ,CONSTRAINT `fk_mission_monster_team_2_deploy_mode`
        FOREIGN KEY (`deploy_mode_id`)
        REFERENCES `deploy_mode` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
        
    ,CONSTRAINT `fk_mission_monster_team_2_mission_scene`
        FOREIGN KEY (`mission_scene_id`)
        REFERENCES `mission_scene` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
COMMENT         = '副本场景中的怪物团'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
