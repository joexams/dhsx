DROP TABLE IF EXISTS `player_data`;

CREATE TABLE `player_data` 
(
     `player_id`            INTEGER         NOT NULL                COMMENT '玩家ID'
    ,`ingot`                INTEGER         NOT NULL    DEFAULT 0   COMMENT '元宝'
    ,`coins`                INTEGER         NOT NULL    DEFAULT 0   COMMENT '铜钱'
    ,`fame`                 INTEGER         NOT NULL    DEFAULT 0   COMMENT '威望'
    ,`skill`                INTEGER         NOT NULL    DEFAULT 0   COMMENT '技能'
    ,`medical`              INTEGER         NOT NULL    DEFAULT 0   COMMENT '气血包剩余血量'
    ,`init_medical`         INTEGER         NOT NULL    DEFAULT 0   COMMENT '气血包初始血量'
    ,`power`                INTEGER         NOT NULL    DEFAULT 0   COMMENT '体力'
    ,`max_power`            INTEGER         NOT NULL    DEFAULT 1   COMMENT '最大体力'
    ,`role_num`             INTEGER         NOT NULL    DEFAULT 1   COMMENT '当前伙伴数量'
    ,`max_role_num`         INTEGER         NOT NULL    DEFAULT 1   COMMENT '伙伴数量上限'
    ,`deploy_mode_id`       INTEGER         NOT NULL    DEFAULT 0   COMMENT '默认阵行ID'
    ,`signature`            VARCHAR(20)     NOT NULL    DEFAULT ''  COMMENT '玩家好友界面签名'
    ,`last_contact_list`    VARCHAR(50)     NOT NULL    DEFAULT ''  COMMENT '最近联系人id信息'
    ,`mounted_player_item`  INTEGER         NOT NULL    DEFAULT 0   COMMENT '坐骑对应的player_item_id'
    ,`avatar_item`          INTEGER         NOT NULL    DEFAULT 0   COMMENT '变身卡对应的item_id'
    
    ,CONSTRAINT `pk_player_data` 
        PRIMARY KEY (`player_id`)
)
COMMENT         = '玩家频繁变更的数据'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;

ALTER TABLE player_data ADD `day_quest_count`  INT (11) NOT NULL DEFAULT '0' COMMENT '每日任务完成次数';
# ALTER TABLE `gamedb`.`player_data` ADD COLUMN `init_medical` INTEGER NOT NULL DEFAULT 0 COMMENT '气血包初始血量' AFTER `medical` ;
# ALTER TABLE `gamedb`.`player_data` ADD COLUMN `mounted_player_item`     INT             NOT NULL    DEFAULT 0   COMMENT '坐骑对应的player_item_id'
# ALTER TABLE `gamedb`.`player_data` ADD COLUMN `avatar_item` INT(11) NOT NULL DEFAULT 0 COMMENT '变身卡对应的player_item_id'  AFTER `mounted_player_item` ;
