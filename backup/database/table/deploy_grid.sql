DROP TABLE IF EXISTS `deploy_grid`;

CREATE TABLE `deploy_grid` 
(
     `id`                   INTEGER     NOT NULL    AUTO_INCREMENT  COMMENT '站位ID'
    ,`deploy_mode_id`       INTEGER     NOT NULL                    COMMENT '阵法ID'
    ,`deploy_grid_type_id`  INTEGER     NOT NULL                    COMMENT '站位类型ID'
    ,`require_level`        INTEGER     NOT NULL    DEFAULT 0       COMMENT '要求阵法等级'

    ,CONSTRAINT `pk_deploy_grid`
        PRIMARY KEY (`id`)
    
    ,CONSTRAINT `fk_deploy_grid_2_deploy_mode`
        FOREIGN KEY (`deploy_mode_id`)
        REFERENCES `deploy_mode` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
        
    ,CONSTRAINT `fk_deploy_grid_2_deploy_grid_type`
        FOREIGN KEY (`deploy_grid_type_id`)
        REFERENCES `deploy_grid_type` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
COMMENT         = '阵法站位'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
