DROP TABLE IF EXISTS `faction_level`;

CREATE TABLE `faction_level`
(
     `id`                   INTEGER     NOT NULL    AUTO_INCREMENT  COMMENT '帮派等级ID'
    ,`sign`                 VARCHAR(20) NOT NULL                    COMMENT '等级标识'
    ,`faction_level_name`   VARCHAR(20) NOT NULL                    COMMENT '等级名称'
    ,`max_member`           INTEGER     NOT NULL                    COMMENT '最大成员数 '
    ,`require_coins`        INTEGER     NOT NULL                    COMMENT '需要铜钱数'

    ,CONSTRAINT `pk_faction_level`
        PRIMARY KEY (`id`)
    
)
COMMENT         = '帮派等级表 '
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
