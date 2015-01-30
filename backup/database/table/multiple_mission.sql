DROP TABLE IF EXISTS `multiple_mission`;

CREATE TABLE `multiple_mission`
(
     `id`                   INTEGER     NOT NULL    AUTO_INCREMENT  COMMENT '多人副本ID'
    ,`mission_id`           INTEGER     NOT NULL                    COMMENT '普通副本ID'
    ,`name`                 VARCHAR(50) NOT NULL                    COMMENT '多人副本名称'
    ,`award_skill`          INTEGER     NOT NULL    DEFAULT 0       COMMENT '奖励阅历'
    ,`award_experience`     INTEGER     NOT NULL    DEFAULT 0       COMMENT '奖励经验'
    ,`award_item`           INTEGER     NOT NULL                    COMMENT '奖励物品'

    ,CONSTRAINT `pk_multiple_mission`
        PRIMARY KEY (`id`)

    ,CONSTRAINT `fk_multiple_mission_2_mission`
        FOREIGN KEY (`mission_id`)
        REFERENCES `mission` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
COMMENT         = '多人副本'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;