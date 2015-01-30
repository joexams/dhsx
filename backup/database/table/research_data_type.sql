DROP TABLE IF EXISTS `research_data_type`;

CREATE TABLE `research_data_type` 
(
     `id`   INTEGER     NOT NULL    AUTO_INCREMENT  COMMENT     'ID'
    ,`name` VARCHAR(25) NOT NULL                    COMMENT     '研究的数据种类如  血'
    ,`sign` VARCHAR(25) NOT NULL                    COMMENT     '研究的数据种类如  血 是health'
    
    ,CONSTRAINT `pk_research_data_type` 
        PRIMARY KEY (`id` )
)
COMMENT         = '奇术研究种类'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;