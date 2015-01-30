DROP TABLE IF EXISTS `item_upgrade`;

CREATE TABLE `item_upgrade` 
(
     `level`    INTEGER         NOT NULL    AUTO_INCREMENT  COMMENT '物品强化等级'
    ,`name`     VARCHAR(10)     NOT NULL                    COMMENT '强化等级显示名称'
    
    ,CONSTRAINT `pk_item_upgrade` 
        PRIMARY KEY (`level`)
)
COMMENT         = '物品强化等级表'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;