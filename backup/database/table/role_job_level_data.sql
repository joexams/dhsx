DROP TABLE IF EXISTS `role_job_level_data`;

CREATE TABLE `role_job_level_data` 
(
     `job_id`           INTEGER     NOT NULL    COMMENT '职业ID'
    ,`level`            INTEGER     NOT NULL    COMMENT '等级'
    ,`max_health`       INTEGER     NOT NULL    COMMENT 'HP上限'
    ,`require_exp`      INTEGER     NOT NULL    COMMENT '升级所需经验值'
    ,`attack`           INTEGER     NOT NULL    COMMENT '攻击'
    ,`defense`          INTEGER     NOT NULL    COMMENT '防御'
    ,`stunt_attack`     INTEGER     NOT NULL    COMMENT '绝技攻击'
    ,`stunt_defense`    INTEGER     NOT NULL    COMMENT '绝技防御'
    ,`magic_attack`     INTEGER     NOT NULL    COMMENT '法力攻击'
    ,`magic_defense`    INTEGER     NOT NULL    COMMENT '法力防御'
    ,`critical`         INTEGER     NOT NULL    COMMENT '暴击'
    ,`dodge`            INTEGER     NOT NULL    COMMENT '闪避'
    ,`hit`              INTEGER     NOT NULL    COMMENT '命中'
    ,`block`            INTEGER     NOT NULL    COMMENT '格挡'

    ,CONSTRAINT `pk_role_job_level_data` 
        PRIMARY KEY (`job_id`, `level`)
        
    ,CONSTRAINT `fk_role_job_level_data_2_role_job`
        FOREIGN KEY (`job_id`)
        REFERENCES `role_job` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
COMMENT         = '玩家角色数据表'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
