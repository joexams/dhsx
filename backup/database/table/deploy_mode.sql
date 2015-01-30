DROP TABLE IF EXISTS `deploy_mode`;

CREATE TABLE `deploy_mode` 
(
     `id`           INTEGER         NOT NULL    AUTO_INCREMENT  COMMENT '阵法ID'
    ,`name`         VARCHAR(10)     NOT NULL                    COMMENT '阵法名称'
    ,`research_id`  INTEGER         NOT NULL    DEFAULT 0       COMMENT '对应奇术ID'

    ,CONSTRAINT `pk_deploy_mode`
    
        PRIMARY KEY (`id`)
)
COMMENT         = '阵法'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
