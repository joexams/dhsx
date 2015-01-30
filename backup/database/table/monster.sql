DROP TABLE IF EXISTS `monster`;

CREATE TABLE `monster` 
(
     `id`                   INTEGER         NOT NULL    AUTO_INCREMENT  COMMENT '怪物ID'
    ,`sign`                 VARCHAR(20)     NOT NULL                    COMMENT '标识'
    ,`name`                 VARCHAR(30)     NOT NULL                    COMMENT '怪物名称'
    ,`level`                INTEGER         NOT NULL    DEFAULT 1       COMMENT '等级'
    ,`attack`               INTEGER         NOT NULL                    COMMENT '普通攻击'
    ,`defense`              INTEGER         NOT NULL                    COMMENT '普通防御'
    ,`stunt_attack`         INTEGER         NOT NULL                    COMMENT '绝迹攻击'
    ,`stunt_defense`        INTEGER         NOT NULL                    COMMENT '绝迹防御'
    ,`magic_attack`         INTEGER         NOT NULL    DEFAULT 0       COMMENT '法术攻击'
    ,`magic_defense`        INTEGER         NOT NULL    DEFAULT 0       COMMENT '法术防御'
    ,`health`               INTEGER         NOT NULL                    COMMENT '生命'
    ,`critical`             INTEGER         NOT NULL    DEFAULT 0       COMMENT '暴击'
    ,`dodge`                INTEGER         NOT NULL    DEFAULT 0       COMMENT '闪避'
    ,`hit`                  INTEGER         NOT NULL    DEFAULT 0       COMMENT '命中'
    ,`block`                INTEGER         NOT NULL    DEFAULT 0       COMMENT '格挡'
    ,`role_stunt_id`        INTEGER         NOT NULL                    COMMENT '战法ID'
    ,`award_item_id`        INTEGER         NOT NULL                    COMMENT '奖励物品ID'
    ,`award_experience`     INTEGER         NOT NULL    DEFAULT 0       COMMENT '奖励经验'


    ,CONSTRAINT `pk_monster`
        PRIMARY KEY (`id`)

    ,CONSTRAINT `fk_monster_2_role_stunt`
        FOREIGN KEY (`role_stunt_id`)
        REFERENCES `role_stunt` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
COMMENT         = '怪物数据'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
