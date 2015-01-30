DROP TABLE IF EXISTS `item_type`;

CREATE TABLE `item_type` 
(
     `id`               INTEGER     NOT NULL    AUTO_INCREMENT  COMMENT '类型ID'
    ,`sign`             VARCHAR(20) NOT NULL                    COMMENT '物品类别标识'
    ,`name`             VARCHAR(10) NOT NULL                    COMMENT '类型名称'
    ,`max_repeat_num`   INTEGER     NOT NULL    DEFAULT 1       COMMENT '最大叠加量'

    ,CONSTRAINT `pk_item_type` 
        PRIMARY KEY (`id`)
)
COMMENT         = '物品类型表'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;

# ALTER TABLE `gamedb`.`item_type` ADD COLUMN `sign` VARCHAR(20) NOT NULL COMMENT '物品类别标识'  AFTER `id` ;
