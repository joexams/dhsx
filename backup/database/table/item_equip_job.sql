DROP TABLE IF EXISTS `item_equip_job`;

CREATE TABLE `item_equip_job`
(
      `item_id`         INTEGER     NOT NULL    COMMENT '物品ID'
     ,`role_job_id`     INTEGER     NOT NULL    COMMENT '可穿戴的角色职业ID'

    ,CONSTRAINT `pk_item_equip_job`
        PRIMARY KEY (
            `item_id`
            ,`role_job_id`
        )

    ,CONSTRAINT `fk_item_equip_job_2_role_job`
        FOREIGN KEY (`role_job_id`)
        REFERENCES `role_job` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
COMMENT         = '装备穿戴要求角色的职业对应表'
ENGINE          = 'InnoDB'
CHARACTER SET   = 'utf8'
COLLATE         = 'utf8_general_ci'
;