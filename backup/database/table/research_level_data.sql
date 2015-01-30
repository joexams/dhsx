DROP TABLE IF EXISTS `research_level_data`;

CREATE TABLE `research_level_data` 
(
     `research_id`      INTEGER         NOT NULL    COMMENT 'ID'
    ,`level`            INTEGER         NOT NULL    COMMENT '研究等级'
    ,`skill`            INTEGER         NOT NULL    COMMENT '所需阅历'
    ,`research_value`   INTEGER         NOT NULL    COMMENT '该等级研究的值(叠加)' 
    ,`cd_time`          INTEGER         NOT NULL    COMMENT 'CD时间'
    ,`player_level`     INTEGER         COMMENT             '需求玩家等级' 

    ,CONSTRAINT `pk_research_level_data` 
        PRIMARY KEY `research_level_data_id` (`research_id`, `level`)
)
COMMENT         = '奇术等级表'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;   