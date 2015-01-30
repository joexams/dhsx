<?php
# php战争参数
$PhpParamFile    = "./{$CacheFolder}/php_param_{$ClientIp}";
# erlang战争参数
$ErlangParamFile = "./{$CacheFolder}/erlang_param_{$ClientIp}";
# 结果文件
$ResultFile       = "./{$CacheFolder}/result_{$ClientIp}";
# flash结果文件
$FlashResultFile = "./{$CacheFolder}/result_win_flash_{$ClientIp}";
# pk战争属性php文件
$PKParamFile = "./{$CacheFolder}/pk_php_param_{$ClientIp}";

$AttackFlashResultFile = "{$FlashResultFile}_attack.txt";
$DefenseFlashResultFile = "{$FlashResultFile}_defense.txt";

$Runtimes    = 1;
$MaxBoutNumber     = 0;
$RequestBoutNumber = 0;
$AttackCanNotDeadNumber = 0;

$DragonBall1 = '[{0, 0, 0}, {0, 0, 0}, {0, 0, 0}, {0, 0, 0}, {0, 0, 0}]';
$DragonBall2 = '[{0, 0, 0}, {0, 0, 0}, {0, 0, 0}, {0, 0, 0}, {0, 0, 0}]';

$AttackWinCount   = 0;
$DefenseWinCount  = 0;
$AttackWinReport  = '';
$DefenseWinReport = '';
$MasterPlayer     = 0;
$SlavePlayer      = 0;
$WarTimes         = 1;
$PKType           = 'with_monster';

$AttackSoldierAttribute  = array(
    1 => TemplateDb::GetRoleAttribute(1),
    2 => TemplateDb::GetRoleAttribute(2),
    3 => TemplateDb::GetRoleAttribute(3),
    4 => TemplateDb::GetRoleAttribute(4),
    5 => TemplateDb::GetRoleAttribute(5),
    6 => TemplateDb::GetRoleAttribute(6)
);

$DefenseSoldierAttribute = array(
    1 => TemplateDb::GetRoleAttribute(1),
    2 => TemplateDb::GetRoleAttribute(2),
    3 => TemplateDb::GetRoleAttribute(3),
    4 => TemplateDb::GetRoleAttribute(4),
    5 => TemplateDb::GetRoleAttribute(5),
    6 => TemplateDb::GetRoleAttribute(6)
);

$SoldierAttributeList = array(
    'attack'  => $AttackSoldierAttribute,
    'defense' => $DefenseSoldierAttribute
);

$AttackSoldierList  = array();
$DefenseSoldierList = array();

if (isset($_REQUEST['submit'])) {
    @unlink("{$ResultFile}_attack.txt");
    @unlink("{$ResultFile}_defense.txt");
    
    $Ac = $_REQUEST['rac'];
    $PKType       = isset($_REQUEST['pk_type']) ? $_REQUEST['pk_type'] : 'with_player';
    
    $MasterPlayer = intval($_REQUEST['master_player']);
    $SlavePlayer  = intval($_REQUEST['slave_player']);
    
    if ($Ac === 'war_demo') {
        $MaxBoutNumber     = isset($_REQUEST['max_bout_number'])?intval($_REQUEST['max_bout_number']):1;
        $RequestBoutNumber = isset($_REQUEST['request_bout_number'])?intval($_REQUEST['request_bout_number']):1;
        $AttackCanNotDeadNumber = isset($_REQUEST['attack_can_not_dead_number'])?intval($_REQUEST['attack_can_not_dead_number']):0;
        $Runtimes = isset($_REQUEST['runtimes'])?intval($_REQUEST['runtimes']):1;
		$DragonBall1 = isset($_REQUEST['dragonball1'])?$_REQUEST['dragonball1']:$DragonBall1;
		$DragonBall2 = isset($_REQUEST['dragonball2'])?$_REQUEST['dragonball2']:$DragonBall2;

        $AttackSoldierList  = isset($_REQUEST['attack_role_list'])?$_REQUEST['attack_role_list']:array();
        $DefenseSoldierList = isset($_REQUEST['defense_role_list'])?$_REQUEST['defense_role_list']:array();
        
        $AttackRoleList = array(1, 2, 3, 4, 5, 6);
        $DefenseRoleList = array(1, 2, 3, 4, 5, 6); 
        
        $AttackSoldierAttribute  = GetPostRoleAttribute(
            'attack',
            $AttackRoleList,
            $_REQUEST
        );
        $DefenseSoldierAttribute = GetPostRoleAttribute(
            'defense',
            $DefenseRoleList,
            $_REQUEST
        );

        $SoldierAttributeList = array(
            'attack'  => $AttackSoldierAttribute,
            'defense' => $DefenseSoldierAttribute
        );
        
        # 保存参数
        file_put_contents($PhpParamFile, serialize($SoldierAttributeList));
        
        # 生成erlang参数文件
        $StrAttackSoldierList  = ArrayToErlangList($AttackSoldierList);
        $StrDefenseSoldierList = ArrayToErlangList($DefenseSoldierList);

        $StrAttackSoldierAttribute = SoldierAttributeListToErlang($AttackSoldierAttribute);
        $StrDefenseSoldierAttribute = SoldierAttributeListToErlang($DefenseSoldierAttribute);
        
        $ErlangParam = "[
            {runtimes, {$Runtimes}},
            {role_list, {{$StrAttackSoldierList}, {$StrDefenseSoldierList}}},
            {role_attribute_list, {{$StrAttackSoldierAttribute}, {$StrDefenseSoldierAttribute}}},
            {max_bout_number, {$MaxBoutNumber}},
            {request_bout_number, {$RequestBoutNumber}},
            {attack_can_not_dead_number, {$AttackCanNotDeadNumber}},
			{attack_dragonball_list, {$DragonBall1}},
			{defense_dragonball_list, {$DragonBall2}}
        ].";
        file_put_contents($ErlangParamFile, $ErlangParam);
    }
    
    # 战争
    if ((!empty ($AttackSoldierList) && !empty ($DefenseSoldierList) && $Runtimes > 0 && $Runtimes <= 1000) || $Ac === 'pk') {
        @unlink($ResultFile);
        $Cmd = "erl -noshell -name war_demo@{$ClientIp} -setcookie {$Config['game_server_cookie']} -s war_demo start \"{$Config['game_server']}\" \"{$ErlangParamFile}\" \"{$FlashResultFile}\" \"{$ResultFile}\" -s init stop";
        if ('pk' === $Ac) {
            if ($MasterPlayer <= 0 || $SlavePlayer <= 0) {
                exit('invalid player');
            }
            if (TemplateDb::CheckPlayerId($MasterPlayer) !== true) {
                exit('invalid player');
            }
            $ExitSlave = false;
            if ('with_monster' === $PKType) {
                $ExitSlave = TemplateDb::CheckMonsterTeamId($SlavePlayer);
            }
            else {
                $ExitSlave = TemplateDb::CheckPlayerId($SlavePlayer);
            }
            if ($ExitSlave !== true) {
                exit('invalid player');
            }
            
            $Cmd = "erl -noshell -name war_demo@{$ClientIp} -setcookie {$Config['game_server_cookie']} -s war_demo pk \"{$Config['game_server']}\" \"{$PKType}\" \"{$MasterPlayer}\" \"{$SlavePlayer}\" \"{$WarTimes}\" \"{$FlashResultFile}\" \"{$ResultFile}\" \"{$PKParamFile}\" -s init stop";
        }
        exec_cmd($Cmd);
        
        if ($Ac === 'war_demo') {
            //unlink($ErlangParamFile);
        }
        else {
            $SoldierAttributeListStr = file_get_contents($PKParamFile);
            eval("\$OutSoldierAttributeList = {$SoldierAttributeListStr};");
            foreach ($OutSoldierAttributeList as $ArmyType => $SoldierAttributes) {
                
                $I = 1;
                foreach ($SoldierAttributes as $SoldierAttribute) {
                    if ($ArmyType === 'attack') {
                        array_push($AttackSoldierList, $I);
                    }
                    else {
                        array_push($DefenseSoldierList, $I);
                    }
                    $SoldierAttribute['role_id'] = $I;
                    
                    $SoldierAttribute['attack_range']            = TemplateDb::GetId('role_attack_range', $SoldierAttribute['attack_range']);
                    $SoldierAttribute['role_stunt_type']         = TemplateDb::GetId('role_stunt_type', $SoldierAttribute['role_stunt_type']);
                    $SoldierAttribute['role_stunt']              = TemplateDb::GetId('role_stunt', $SoldierAttribute['role_stunt']);
                    $SoldierAttribute['role_stunt_attack_range'] = TemplateDb::GetId('role_attack_range', $SoldierAttribute['role_stunt_attack_range']);
                    
                    $SoldierAttributeList[$ArmyType][$I] = $SoldierAttribute;
                    $I++;
                }
            }
        }
        
        # 获取战争结果
        $WarResult = trim(file_get_contents($ResultFile));
        if (strlen($WarResult)) {
            preg_match('/<attack_army_win_count>(.*)<\/attack_army_win_count>/', $WarResult, $AttackArmyWinCount);
            preg_match('/<defense_army_win_count>(.*)<\/defense_army_win_count>/', $WarResult, $DefenseArmyWinCount);
            preg_match('/<attack_army_win_report>(.*)<\/attack_army_win_report>/', $WarResult, $AttackArmyWinReport);
            preg_match('/<defense_army_win_report>(.*)<\/defense_army_win_report>/', $WarResult, $DefenseArmyWinReport);
        }
        
        $AttackWinCount   = $AttackArmyWinCount[1];
        $DefenseWinCount  = $DefenseArmyWinCount[1];
        $AttackWinReport  = $AttackArmyWinReport[1];
        $DefenseWinReport = $DefenseArmyWinReport[1];
        
        echo "
            <div class='report_item'>
                                <a href=\"{$AttackFlashResultFile}\" target=\"_blank\" style=\"color:green;font-weight: bold\">导出flash播放脚本</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href =\"http://cdn.sxd.com/qq/tool/wardemo/standaloneWar.swf?url={$AttackFlashResultFile}\" target=\"_blank\" style=\"color:green;font-weight: bold\">flash播放</a><br><br>
                                <font color=\"red\">攻方战胜战报 (胜 {$AttackWinCount} 场)</font><br><br>{$AttackWinReport}
                            </div>
                            <div class='report_item'>
                                <a href=\"{$DefenseFlashResultFile}\" target=\"_blank\" style=\"color:green;font-weight: bold\">导出flash播放脚本</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href =\"http://cdn.sxd.com/qq/tool/wardemo/standaloneWar.swf?url={$DefenseFlashResultFile}\" target=\"_blank\" style=\"color:green;font-weight: bold\">flash播放</a><br><br>
                                <font color=\"blue\">守方战胜战报 (胜 {$DefenseWinCount} 场)</font><br><br>{$DefenseWinReport}
                            </div>
        ";
    }
}
else {
    if (file_exists($PhpParamFile)) {
        $SoldierAttributeList = unserialize(file_get_contents($PhpParamFile));
    }
    include TEMPLATE . '/war_demo.html';
}
?>