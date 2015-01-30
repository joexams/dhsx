DROP TABLE IF EXISTS `quest`;

CREATE TABLE `quest` 
(
     `id`                   INTEGER         NOT NULL    AUTO_INCREMENT  COMMENT '任务ID'
    ,`type`                 INTEGER         NOT NULL                    COMMENT '任务类型'
    ,`lock`                 INTEGER         NOT NULL                    COMMENT '任务解锁权值'
    ,`level`                INTEGER         NOT NULL                    COMMENT '任务等级'
    ,`title`                VARCHAR(32)     NOT NULL                    COMMENT '任务标题'
    ,`content`              VARCHAR(128)    NOT NULL                    COMMENT '任务描述'
    ,`conditions`           VARCHAR(128)    NOT NULL                    COMMENT '完成条件'
    ,`town_text`            text                                        COMMENT '城镇显示任务目标'      
    ,`begin_npc_id`         INTEGER         NOT NULL                    COMMENT '起始NPC'
    ,`end_npc_id`           INTEGER         NOT NULL                    COMMENT '结束NPC'
    ,`award_experience`     INTEGER         NOT NULL                    COMMENT '奖励经验'
    ,`award_coins`          INTEGER         NOT NULL                    COMMENT '奖励铜钱'
    ,`award_item_id`        INTEGER         NOT NULL                    COMMENT '奖励物品'
    ,`is_talk_quest`        INTEGER         NOT NULL                    COMMENT '是否对话任务 0 普通任务 1 对话框任务'
    ,`award_town_key`       INTEGER         NOT NULL                    COMMENT '奖励城镇解锁权限'
    ,`award_quest_key`      INTEGER         NOT NULL                    COMMENT '奖励任务解锁权限'
    ,`unlock_level`         INTEGER         NOT NULL                    COMMENT '直线任务的解锁需求等级'
    ,`accept_talk`          text                                        COMMENT '接受任务对话框'                                              
    ,`accepted_talk`        text                                        COMMENT '未完成任务对话框'                                             
    ,`completed_talk`       text                                        COMMENT '完成任务对话框'      
    ,CONSTRAINT `pk_quest` 
        PRIMARY KEY (`id`)
)
COMMENT         = '玩家任务数据'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;
