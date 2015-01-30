DROP TABLE IF EXISTS `item`;

CREATE TABLE `item` 
(
     `id`               INTEGER         NOT NULL    AUTO_INCREMENT  COMMENT '物品模板ID'
    ,`name`             VARCHAR(10)     NOT NULL                    COMMENT '物品名称'
    ,`type_id`          INTEGER         NOT NULL                    COMMENT '类别ID'
    ,`icon_id`          INTEGER         NOT NULL                    COMMENT '图标ID'
    ,`price_level`      INTEGER         NOT NULL                    COMMENT '价格等级'
    ,`usage`            VARCHAR(20)     NOT NULL                    COMMENT '用途说明'
    ,`description`      VARCHAR(30)     NOT NULL                    COMMENT '详细描述'
    ,`quality`          INTEGER         NOT NULL                    COMMENT '品质等级'
    ,`attack`           INTEGER         NOT NULL    DEFAULT 0       COMMENT '普通攻击'
    ,`attack_up`        INTEGER         NOT NULL    DEFAULT 0       COMMENT '每升一级的普通攻击加值'
    ,`defense`          INTEGER         NOT NULL    DEFAULT 0       COMMENT '普通防御'
    ,`defense_up`       INTEGER         NOT NULL    DEFAULT 0       COMMENT '每升一级的普通防御加值'
    ,`stunt_attack`     INTEGER         NOT NULL    DEFAULT 0       COMMENT '绝技攻击'
    ,`stunt_attack_up`  INTEGER         NOT NULL    DEFAULT 0       COMMENT '每升一级的绝技攻击加值'
    ,`stunt_defense`    INTEGER         NOT NULL    DEFAULT 0       COMMENT '绝技防御'
    ,`stunt_defense_up` INTEGER         NOT NULL    DEFAULT 0       COMMENT '每升一级的绝技防御加值'
    ,`magic_attack`     INTEGER         NOT NULL    DEFAULT 0       COMMENT '法术攻击'
    ,`magic_attack_up`  INTEGER         NOT NULL    DEFAULT 0       COMMENT '每升一级的法术攻击加值'
    ,`magic_defense`    INTEGER         NOT NULL    DEFAULT 0       COMMENT '法术防御'
    ,`magic_defense_up` INTEGER         NOT NULL    DEFAULT 0       COMMENT '每升一级的法术防御加值'
    ,`health`           INTEGER         NOT NULL    DEFAULT 0       COMMENT '生命'
    ,`health_up`        INTEGER         NOT NULL    DEFAULT 0       COMMENT '每升一级的生命加值'
    ,`require_level`    INTEGER         NOT NULL                    COMMENT '要求角色等级'
    
    ,CONSTRAINT `pk_item` PRIMARY KEY (`id`)

    ,CONSTRAINT `fk_item_2_item_type`
        FOREIGN KEY (`type_id`)
        REFERENCES `item_type` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE

    ,CONSTRAINT `fk_item_2_item_price`
        FOREIGN KEY (`price_level`)
        REFERENCES `item_price` (`level`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION

    ,CONSTRAINT `fk_item_2_item_quality`
        FOREIGN KEY (`quality`)
        REFERENCES `item_quality` (`quality`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
COMMENT         = '物品和装备模板'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
