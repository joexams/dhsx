DROP TABLE IF EXISTS `player`;

CREATE TABLE `player` 
(
     `id`               INTEGER         NOT NULL    AUTO_INCREMENT  COMMENT '玩家ID'
    ,`username`         VARCHAR(20)     NOT NULL                    COMMENT '用户名'
    ,`nickname`         VARCHAR(20)     NOT NULL    DEFAULT ''      COMMENT '昵称'
    ,`main_role_id`     INTEGER             NULL    DEFAULT NULL    COMMENT '主角色ID'
    
    ,CONSTRAINT `pk_player` 
        PRIMARY KEY (`id`)
)
COMMENT         = '玩家基础数据'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
