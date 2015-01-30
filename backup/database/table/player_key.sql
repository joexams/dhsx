DROP TABLE IF EXISTS `player_key`;

CREATE TABLE `player_key` 
(
     `player_id`    INTEGER     NOT NULL                    COMMENT '玩家ID'
    ,`town`         INTEGER     NOT NULL    DEFAULT 1       COMMENT '城镇解锁权限，玩家可以访问town表中lock值小于town_key的城镇'
    ,`quest`        INTEGER     NOT NULL    DEFAULT 1       COMMENT '任务解锁权限，玩家可以领取quest表中lock值小于quest_key的任务'
    ,`section`      INTEGER     NOT NULL    DEFAULT 10      COMMENT '剧情解锁权限，玩家可以看到mission_section表中lock值小于section_key的剧情'
    ,`mission`      INTEGER     NOT NULL    DEFAULT 100     COMMENT '副本解锁权限，玩家可以挑战mission表中lock值小于mission_key的副本'
    ,`research`     INTEGER     NOT NULL    DEFAULT 1       COMMENT '奇术等级权限，根据KEY显示不同等级的科技'
    ,`pack_grid`    INTEGER     NOT NULL    DEFAULT 18      COMMENT '玩家背包格子解锁权限，玩家可以看到item_pack_grid表中lock值小于key的格子'
    ,`role_equi`    INTEGER     NOT NULL    DEFAULT 1       COMMENT '玩家角色装备解锁权限，玩家可以看到item_pack_grid表中lock值小于key的装备位置'
    ,`warehouse`    INTEGER     NOT NULL    DEFAULT 18      COMMENT '玩家仓库格子解锁权限，玩家可以看到item_pack_grid表中lock值小于key的格子'
    
    ,CONSTRAINT `pk_player_key` 
        PRIMARY KEY (`player_id`)
)
COMMENT         = '玩家的功能解锁权限'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
