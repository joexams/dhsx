DROP TABLE IF EXISTS `mission_scene`;

CREATE TABLE `mission_scene` 
(
     `id`           INTEGER         NOT NULL    AUTO_INCREMENT  COMMENT '场景ID'
    ,`mission_id`   INTEGER         NOT NULL                    COMMENT '副本ID'
    ,`lock`         INTEGER         NOT NULL                    COMMENT '场景权值'
    ,`name`         VARCHAR(20)     NOT NULL                    COMMENT '场景名称'

    ,CONSTRAINT `pk_mission_scene`
        PRIMARY KEY (`id`)
    
    ,CONSTRAINT `fk_mission_scene_2_mission`
        FOREIGN KEY (`mission_id`)
        REFERENCES `mission` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
COMMENT         = '副本中的场景'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
