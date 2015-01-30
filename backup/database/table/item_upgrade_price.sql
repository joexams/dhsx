DROP TABLE IF EXISTS `item_upgrade_price`;

CREATE TABLE `item_upgrade_price`
(
      `upgrade_level`   INTEGER     NOT NULL    COMMENT '装备当前强化等级'
     ,`item_type_id`    INTEGER     NOT NULL    COMMENT '装备类别ID'
     ,`item_quality_id` INTEGER     NOT NULL    COMMENT '装备品质ID'
     ,`upgrade_price`   INTEGER     NOT NULL    COMMENT '装备强化需要价格'

    ,CONSTRAINT `pk_item_upgrade_price`
        PRIMARY KEY (
            `upgrade_level`
            ,`item_type_id`
            ,`item_quality_id`
    )
)
COMMENT         = '物品强化价格表'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;