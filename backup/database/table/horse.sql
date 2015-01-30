DROP TABLE IF EXISTS `horse`;

CREATE TABLE `horse` 
(
     `id`                INTEGER          NOT NULL  AUTO_INCREMENT    COMMENT 'id'
    ,`sign`              varchar(50)      NOT NULL                    COMMENT '神兽标识'
    ,`name`              varchar(20)      NOT NULL                    COMMENT '神兽名称'
    ,CONSTRAINT `horse` 
        PRIMARY KEY (`id`)
)
COMMENT         = '神兽信息'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;