DROP TABLE IF EXISTS `research_type`;

CREATE TABLE `research_type` 
(
     `id`   INTEGER     NOT NULL    AUTO_INCREMENT  COMMENT     'ID'
    ,`name` VARCHAR(34) NOT NULL                    COMMENT     '类型名称'
    
    ,CONSTRAINT `pk_research_type` 
        PRIMARY KEY  (`id` )
)
COMMENT         = '奇术类型'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;