DROP TABLE IF EXISTS `item_price`;

CREATE TABLE `item_price` 
(
      `level`           INTEGER     NOT NULL    AUTO_INCREMENT  COMMENT '物品价格等级，1个等级的对应有普通物品价格和装备价格以及装备升级价格'
     ,`item_price`      INTEGER     NOT NULL    DEFAULT '0'     COMMENT '普通物品初始价格'
     ,`equip_price`     INTEGER     NOT NULL    DEFAULT '0'     COMMENT '装备初始价格'

    ,CONSTRAINT `pk_player` 
        PRIMARY KEY (`level`)
)
COMMENT         = '物品价格等级表'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;