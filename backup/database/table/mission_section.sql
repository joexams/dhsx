DROP TABLE IF EXISTS `mission_section`;

CREATE TABLE `mission_section` 
(
     `id`                   INTEGER         NOT NULL    AUTO_INCREMENT  COMMENT '剧情ID'
    ,`lock`                 INTEGER         NOT NULL                    COMMENT '剧情权值'
    ,`sign`                 VARCHAR(20)     NOT NULL                    COMMENT '剧情标识'
    ,`name`                 VARCHAR(20)     NOT NULL                    COMMENT '剧情名称'
    ,`town_id`              INTEGER         NOT NULL                    COMMENT '所属城镇'
    ,`award_skill`          INTEGER         NOT NULL    DEFAULT 0       COMMENT '奖励阅历'
    ,`award_coins`          INTEGER         NOT NULL    DEFAULT 0       COMMENT '奖励铜币'
    ,`award_experience`     INTEGER         NOT NULL    DEFAULT 0       COMMENT '奖励经验'
    ,`award_section_key`    INTEGER         NOT NULL                    COMMENT '奖励的剧情解锁权限'
    
    ,CONSTRAINT `pk_mission_section`
        PRIMARY KEY (`id`)
)
COMMENT         = '剧情'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
