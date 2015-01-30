DROP TABLE IF EXISTS `item_pack_grid`;

CREATE TABLE `item_pack_grid`
(
     `id`               INTEGER         NOT NULL    AUTO_INCREMENT  COMMENT '位置ID：1-100背包位置ID；101-200仓库格子ID；201-300角色装备位置'
    ,`name`             VARCHAR(10)     NOT NULL                    COMMENT '显示名称'
    ,`ingot`            INTEGER         NOT NULL    DEFAULT 0       COMMENT '增加格子所需要元宝'
    ,`unlock_level`     INTEGER         NOT NULL    DEFAULT 1       COMMENT '解锁权值'
    ,`equip_item_type`  INTEGER         NULL                        COMMENT '角色装备位置，可装备物品类别，id为201-300时有效'

    ,CONSTRAINT `pk_item_pack_grid`
        PRIMARY KEY (`id`)
)
COMMENT         = '物品位置模板表'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
