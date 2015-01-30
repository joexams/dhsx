DROP TABLE IF EXISTS `item_quality`;

CREATE TABLE `item_quality` 
(
     `quality`   INTEGER        NOT NULL   AUTO_INCREMENT  COMMENT '品质等级'
    ,`name`      VARCHAR(10)    NOT NULL                   COMMENT '显示名称'

    ,CONSTRAINT `pk_item_quality` 
        PRIMARY KEY (`quality`)
)
COMMENT         = '物品品质等级表'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;