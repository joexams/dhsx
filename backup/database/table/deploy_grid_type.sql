DROP TABLE IF EXISTS `deploy_grid_type`;

CREATE TABLE `deploy_grid_type` 
(
     `id`       INTEGER     NOT NULL    AUTO_INCREMENT  COMMENT '阵法站位类型ID'
    ,`name`     CHAR(2)     NOT NULL                    COMMENT '阵法站位类型名称'

    ,CONSTRAINT `pk_deploy_grid_type`
        PRIMARY KEY (`id`)
)
COMMENT         = '阵法站位类型[A1, A2, A3]\n[B1, B2, B3]\n[C1, C2, C3]'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
