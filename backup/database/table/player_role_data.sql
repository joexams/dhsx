DROP TABLE IF EXISTS `player_role_data`;

CREATE TABLE `player_role_data` 
(
     `player_role_id`       INTEGER     NOT NULL    AUTO_INCREMENT  COMMENT '玩家角色ID'
    , `player_id`           INTEGER     NOT NULL                    COMMENT '玩家ID'
    ,`strength`             INTEGER     NOT NULL                    COMMENT '武(力量)'
    ,`agile`                INTEGER     NOT NULL                    COMMENT '技(敏捷)'
    ,`intellect`            INTEGER     NOT NULL                    COMMENT '术(智力)'
    ,`strength_additional`  INTEGER     NOT NULL    DEFAULT 0       COMMENT '武加值(力量)'
    ,`agile_additional`     INTEGER     NOT NULL    DEFAULT 0       COMMENT '技加值(敏捷)'
    ,`intellect_additional` INTEGER     NOT NULL    DEFAULT 0       COMMENT '术加值(智力)'
    ,`attack`               INTEGER     NOT NULL                    COMMENT '防御'
    ,`defense`              INTEGER     NOT NULL                    COMMENT '攻击'
    ,`stunt_attack`         INTEGER     NOT NULL                    COMMENT '绝技攻击'
    ,`stunt_defense`        INTEGER     NOT NULL                    COMMENT '绝技防御'
    ,`magic_attack`         INTEGER     NOT NULL                    COMMENT '法力攻击'
    ,`magic_defense`        INTEGER     NOT NULL                    COMMENT '法力防御'
    ,`max_health`           INTEGER     NOT NULL                    COMMENT 'HP上限'
    ,`critical`             INTEGER     NOT NULL                    COMMENT '暴击'
    ,`dodge`                INTEGER     NOT NULL                    COMMENT '闪避'
    ,`hit`                  INTEGER     NOT NULL                    COMMENT '命中'
    ,`block`                INTEGER     NOT NULL                    COMMENT '格挡'

    ,CONSTRAINT `pk_player_role_data` 
        PRIMARY KEY (`player_role_id`)
)
COMMENT         = '玩家角色数据表'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
