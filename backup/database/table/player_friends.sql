DROP TABLE IF EXISTS `player_friends`;

CREATE TABLE `player_friends` 
(
     `player_id`      INTEGER     NOT NULL    COMMENT '玩家ID'
    ,`friend_id`      INTEGER     NOT NULL    COMMENT '玩家的好友ID'
    ,`group_type`     INTEGER     NOT NULL    COMMENT '好友所属群组'

    ,CONSTRAINT `pk_player_friends`
        PRIMARY KEY (`player_id`,
	             `friend_id`)
    ,CONSTRAINT `fk_player_friends_player_2_player`
        FOREIGN KEY (`player_id`)
        REFERENCES `player` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
    ,CONSTRAINT `fk_player_friends_friend_2_player`
        FOREIGN KEY (`friend_id`)
        REFERENCES `player` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
COMMENT         = '玩家好友表'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
