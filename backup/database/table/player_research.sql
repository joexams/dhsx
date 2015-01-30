DROP TABLE IF EXISTS `player_research`;

CREATE TABLE `player_research` 
(
     `player_id`    INTEGER     NOT NULL    COMMENT    '玩家ID'
    ,`research_id`  INTEGER     NOT NULL    COMMENT    '研究奇术ID'
    ,`level`        INTEGER     NOT NULL    COMMENT    '研究等级'
    
    ,CONSTRAINT `pk_player_research` 
        PRIMARY KEY `player_id` (`player_id`, `research_id`)
)
COMMENT         = '玩家研究奇术数据'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;