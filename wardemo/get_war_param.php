<?php
# pk战争属性php文件
$ParamFile = "./{$CacheFolder}/player_php_param_{$ClientIp}";

$Act      = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
$ArmyType = isset($_REQUEST['at']) ? $_REQUEST['at'] : 'attack';
$Id       = intval($_REQUEST['id']);

if ($Id <= 0) {
    exit('error');
}
    
$SoldierAttributeList  = array(
    1 => TemplateDb::GetRoleAttribute(1),
    2 => TemplateDb::GetRoleAttribute(2),
    3 => TemplateDb::GetRoleAttribute(3),
    4 => TemplateDb::GetRoleAttribute(4),
    5 => TemplateDb::GetRoleAttribute(5),
    6 => TemplateDb::GetRoleAttribute(6)
);


# 玩家战争属性
if ('player' === $Act) {
    $Cmd = "erl -noshell -name war_demo@{$ClientIp} -setcookie {$Config['cache_server_cookie']} -s war_demo get_war_param \"{$Config['cache_server']}\" \"{$Id}\" \"{$ParamFile}\" -s init stop";
    exec_cmd($Cmd);
    
    if (file_exists($ParamFile)) {
        $SoldierAttributeListStr = file_get_contents($ParamFile);
        eval("\$OutSoldierAttributeList = {$SoldierAttributeListStr};");
        unlink($ParamFile);
    }
    else {
        exit('error');
    }
    
    $I = 1;
    foreach ($OutSoldierAttributeList as $SoldierAttribute) {
        $SoldierAttribute['role_id'] = $I;
        $SoldierAttribute['role_stunt'] = TemplateDb::GetSign('role_stunt', $SoldierAttribute['role_stunt']);
		$SoldierAttribute['role_base_stunt'] = $SoldierAttribute['role_stunt'];
        $SoldierAttributeList[$I] = $SoldierAttribute;
        $I++;
    }

    echo GetSoldierAttributeHtml(
        $Id,
        $ArmyType,
        array_keys($OutSoldierAttributeList),
        $SoldierAttributeList
    );
}
# 怪物战争属性
else if('monster' === $Act) {
    $OutSoldierAttributeList = TemplateDb::GetMonsterTeamParam($Id);
    
    if (count($OutSoldierAttributeList) < 1) {
        exit('error');
    }
    $SoldierList = array();
    
    $I = 1;
    foreach ($OutSoldierAttributeList as $SoldierAttribute) {
        $SoldierAttribute['role_id'] = $I;
        if ($SoldierAttribute['role_job_sign'] === 'FaShu') {
            $SoldierAttribute['role_sign'] = 'YingLong';
        }
        else if ($SoldierAttribute['role_job_sign'] === 'MT') {
            $SoldierAttribute['role_sign'] = 'ZhangMaZi';
        }
        else {
            $SoldierAttribute['role_sign'] = 'JianLingNan';
        }
        $SoldierAttributeList[$I] = $SoldierAttribute;
        array_push($SoldierList, $I);
        $I++;
    }

    echo GetSoldierAttributeHtml(
        $Id,
        $ArmyType,
        $SoldierList,
        $SoldierAttributeList
    );
}
else {
    echo 'error';
}
?>