DROP TABLE IF EXISTS `role`;

CREATE TABLE `role` 
(
     `id`               INTEGER         NOT NULL    AUTO_INCREMENT  COMMENT '角色ID'
    ,`sign`             VARCHAR(20)     NOT NULL                    COMMENT '标识'
    ,`name`             VARCHAR(20)     NOT NULL                    COMMENT '名称'
    ,`role_job_id`      INTEGER         NOT NULL                    COMMENT '职业ID'
    ,`gender`           INTEGER         NOT NULL                    COMMENT '性别'
    ,`role_stunt_id`    INTEGER         NOT NULL                    COMMENT '绝技ID'
    ,`strength`         INTEGER         NOT NULL                    COMMENT '武（力量）'
    ,`agile`            INTEGER         NOT NULL                    COMMENT '技（敏捷）'
    ,`intellect`        INTEGER         NOT NULL                    COMMENT '术（智力）'
    ,`fees`             INTEGER         NOT NULL    default '0'     COMMENT '招募费用'

    ,CONSTRAINT `pk_role` 
        PRIMARY KEY (`id`)
)
COMMENT         = '角色表'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
