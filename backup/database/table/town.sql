DROP TABLE IF EXISTS `town`;

CREATE TABLE `town` 
(
     `id`           INTEGER         NOT NULL    AUTO_INCREMENT  COMMENT '城镇ID'
    ,`sign`         VARCHAR(20)     NOT NULL                    COMMENT '城镇标识'
    ,`name`         VARCHAR(10)     NOT NULL                    COMMENT '城镇名称'
    ,`lock`         INTEGER         NOT NULL                    COMMENT '城镇解锁权值'
    ,`training_coins`         INTEGER         NOT NULL          COMMENT '培养所需铜钱'
    ,`description`  VARCHAR(140)    NOT NULL                    COMMENT '描述'

    ,CONSTRAINT `pk_town` 
        PRIMARY KEY (`id`)
)
COMMENT         = '城镇数据表'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
