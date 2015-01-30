DROP TABLE IF EXISTS `quest_type`;

CREATE TABLE `quest_type` 
(
     `id`                   INTEGER         NOT NULL    AUTO_INCREMENT  COMMENT '类型'
    ,`name`                 VARCHAR(32)    NOT NULL                     COMMENT '类型名称' 
    ,CONSTRAINT `pk_id` 
        PRIMARY KEY (`id`)
)
COMMENT         = '任务类型'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
