DROP TABLE IF EXISTS `player_day_quest`;

CREATE TABLE `player_day_quest` 
(
     `player_id`    INTEGER     NOT NULL    COMMENT    '玩家ID'
    ,`quest_id`     INTEGER     NOT NULL    COMMENT    '任务ID'
    ,`state`        INTEGER     NOT NULL    COMMENT    '任务状态'
    ,`stat_count`   INTEGER     NOT NULL    COMMENT    '任务星级'
    ,`complete_count`        INTEGER     NOT NULL   DEFAULT '0' COMMENT   '完成次数'
    
    ,CONSTRAINT `pk_quest` 
        PRIMARY KEY `player_id` (`player_id`, `quest_id`)
)
COMMENT         = '玩家每日任务数据'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;