DROP TABLE IF EXISTS `npc`;

CREATE TABLE `npc` 
(
     `id`               INTEGER         NOT NULL    AUTO_INCREMENT  COMMENT 'NPC ID'
    ,`npc_func_id`      INTEGER         NOT NULL                    COMMENT 'NPC功能ID'
    ,`sign`             VARCHAR(20)     NOT NULL                    COMMENT 'NPC标识'
    ,`name`             VARCHAR(10)     NOT NULL                    COMMENT 'NPC显示名称'
    ,`dialog`           VARCHAR(50)     NOT NULL                    COMMENT 'NPC对话'
    ,`shop_name`        VARCHAR(10)                                 COMMENT 'NPC商人类别,如：杂物商，武器商'
    ,`player_dialog`    VARCHAR(20)                                 COMMENT '玩家的响应对话：如：我要去购买'

    ,CONSTRAINT `pk_npc` 
        PRIMARY KEY (`id`)

    ,CONSTRAINT `fk_npc_2_npc_function`
        FOREIGN KEY (`npc_func_id`)
        REFERENCES `npc_function` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
COMMENT         = 'NPC模板表'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
