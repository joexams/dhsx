DROP TABLE IF EXISTS `npc_function`;

CREATE TABLE `npc_function`
(
     `id`               INTEGER         NOT NULL    AUTO_INCREMENT  COMMENT 'ID'
    ,`sign`             VARCHAR(20)     NOT NULL                    COMMENT 'NPC功能标识'
    ,`name`             VARCHAR(10)     NOT NULL                    COMMENT 'NPC功能名称'

    ,CONSTRAINT `pk_npc_function`
        PRIMARY KEY (`id`)
)
COMMENT         = 'NPC功能表'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
