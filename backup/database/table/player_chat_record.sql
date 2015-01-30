DROP TABLE IF EXISTS `player_chat_record`;

CREATE TABLE `player_chat_record` 
(
     `id`                   INTEGER         NOT NULL    AUTO_INCREMENT  COMMENT '聊天记录ID'
    ,`player_id`            INTEGER         NOT NULL                    COMMENT '发消息的玩家ID'
    ,`to_id`                INTEGER         NOT NULL                    COMMENT '接收消息的玩家ID'
    ,`message`              VARCHAR(100)    NOT NULL                    COMMENT '信息'
    ,`eip_num`              VARCHAR(50)     NOT NULL                    COMMENT '信息中表情所需的信息'
    ,`eip_index`            VARCHAR(50)     NOT NULL                    COMMENT '表情索引'
    ,`send_time`            INTEGER         NOT NULL                    COMMENT '信息发送时间'                                              
    ,`is_send`              INTEGER         NOT NULL                    COMMENT '信息是否已经发送 0未发送 1已经发送'                                                  
    ,CONSTRAINT `pk_player_chat_record` 
        PRIMARY KEY (`id`)
    ,CONSTRAINT `fk_player_chat_record_2_player`
        FOREIGN KEY (`player_id`)
        REFERENCES `player` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
COMMENT         = '玩家与好友聊天记录'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
