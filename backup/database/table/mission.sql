DROP TABLE IF EXISTS `mission`;

CREATE TABLE `mission` 
(
     `id`                   INTEGER     NOT NULL    AUTO_INCREMENT  COMMENT '副本ID'
    ,`mission_section_id`   INTEGER     NOT NULL                    COMMENT '剧情ID'
    ,`name`                 VARCHAR(50) NOT NULL                    COMMENT '副本名称'
    ,`lock`                 INTEGER     NOT NULL                    COMMENT '副本权值'
    ,`completion`           INTEGER     NOT NULL                    COMMENT '剧情进度'
    ,`require_power`        INTEGER     NOT NULL                    COMMENT '需求体力值'
    ,`require_level`        INTEGER     NOT NULL    DEFAULT 0       COMMENT '需求等级'
    ,`award_coins`          INTEGER     NOT NULL    DEFAULT 0       COMMENT '奖励铜币'
    ,`award_skill`          INTEGER     NOT NULL    DEFAULT 0       COMMENT '奖励阅历'
    ,`award_experience`     INTEGER     NOT NULL    DEFAULT 0       COMMENT '奖励经验'
    ,`award_mission_key`    INTEGER     NOT NULL                    COMMENT '奖励的副本解锁权限'
    ,`releate_quest_id`     INTEGER     NULL        DEFAULT 0       COMMENT '关联任务ID'
    ,`description`          VARCHAR(200)NOT NULL                    COMMENT '副本描述'

    ,CONSTRAINT `pk_mission`
        PRIMARY KEY (`id`)
        
    ,CONSTRAINT `fk_mission_2_mission_section`
        FOREIGN KEY (`mission_section_id`)
        REFERENCES `mission_section` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
COMMENT         = '副本'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
