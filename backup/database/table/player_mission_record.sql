DROP TABLE IF EXISTS `player_mission_record`;

CREATE TABLE `player_mission_record` 
(
     `player_id`                    INTEGER     NOT NULL                COMMENT '玩家ID'
    ,`mission_id`                   INTEGER     NOT NULL                COMMENT '副本ID'
    ,`rank`                         INTEGER     NOT NULL                COMMENT '评定'
    ,`times`                        INTEGER     NOT NULL    DEFAULT 1   COMMENT '挑战次数'
    ,`current_scene_lock`           INTEGER     NOT NULL    DEFAULT 0   COMMENT '挑战场景权值'
    ,`current_monster_team_lock`    INTEGER     NOT NULL    DEFAULT 0   COMMENT '挑战怪物团权值'
    ,`not_ingot_times`              INTEGER     NULL        DEFAULT 0   COMMENT '没有获取到元宝的次数'
    ,`is_finished`                  INTEGER     NOT NULL    DEFAULT 0   COMMENT '是否完成'

    ,INDEX `fk_player_mission_record__mission_id` (`mission_id` ASC)
    ,INDEX `fk_player_mission_record__player_id` (`player_id` ASC)
    
    ,UNIQUE INDEX `idx_pmr_pid_mid` (
        `player_id`     ASC, 
        `mission_id`    ASC
    )
    
    ,CONSTRAINT `fk_player_mission_record_2_mission`
        FOREIGN KEY (`mission_id`)
        REFERENCES `mission` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
        
    ,CONSTRAINT `fk_player_mission_record_2_player`
        FOREIGN KEY (`player_id`)
        REFERENCES `player` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
COMMENT         = '玩家副本'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;