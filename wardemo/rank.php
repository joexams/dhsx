<?php
# php战争参数
$PhpParamFile    = "./{$CacheFolder}/php_rank_param_{$ClientIp}";
# erlang战争参数
$ErlangParamFile = "./{$CacheFolder}/erlang_rank_param_{$ClientIp}";
# 结果文件
$ResultFile       = "./{$CacheFolder}/result_rank_{$ClientIp}";

$MissionId   = 1;
$VagAttack   = 9;
$VagDefense  = 8;
$Runtimes    = 1;
$Result      = '';
$FightResult = '';

$MissionList = TemplateDb::GetMissionList();

$AttackSoldierAttribute  = array(
    1 => TemplateDb::GetRoleAttribute(1),
    2 => TemplateDb::GetRoleAttribute(2),
    3 => TemplateDb::GetRoleAttribute(3),
    4 => TemplateDb::GetRoleAttribute(4),
    5 => TemplateDb::GetRoleAttribute(5),
    6 => TemplateDb::GetRoleAttribute(6),
    7 => TemplateDb::GetRoleAttribute(7),
    8 => TemplateDb::GetRoleAttribute(8),
    9 => TemplateDb::GetRoleAttribute(9)
);

$SoldierAttributeList = array(
    'attack'  => $AttackSoldierAttribute
);

$AttackSoldierList  = array();
$DefenseSoldierList = array();

if (isset($_POST['submit'])) {
    $Runtimes = isset($_POST['runtimes'])?intval($_POST['runtimes']):1;
    $MissionId  = intval($_POST['mission']);
    $VagAttack  = intval($_POST['vag_attack']);
    $VagDefense = intval($_POST['vag_defense']);
    $AttackSoldierList  = isset($_POST['attack_role_list'])?$_POST['attack_role_list']:array();

    $AttackSoldierAttribute  = GetPostRoleAttribute(
        'attack',
        array(1, 2, 3, 4, 5, 6, 7, 8, 9),
        $_POST
    );

    $SoldierAttributeList = array(
        'attack'  => $AttackSoldierAttribute
    );
    
    $SceneList = TemplateDb::GetSceneMonsterTeam($MissionId);
    
    # 保存参数
    file_put_contents($PhpParamFile, serialize($SoldierAttributeList));
    
    # 生成erlang参数文件
    $StrAttackSoldierList  = ArrayToErlangList($AttackSoldierList);

    $StrAttackSoldierAttribute = SoldierAttributeListToErlang($AttackSoldierAttribute);

    $DefenseList = array();
    foreach ($SceneList as $MissionMonsterTeam) {
        $DefenseSoldierList = ArrayToErlangList(array_keys($MissionMonsterTeam));
        $StrDefenseSoldierAttribute = SoldierAttributeListToErlang($MissionMonsterTeam);
        array_push(
            $DefenseList,
            "[
                {role_list, {$DefenseSoldierList}},
                {role_attribute, {$StrDefenseSoldierAttribute}}
             ]"
        );
    }
    
    $WarParamList = "[
        {vag_attack, {$VagAttack}},
        {vag_defense, {$VagDefense}},
        {runtimes, {$Runtimes}},
        {role_list, {$StrAttackSoldierList}},
        {role_attribute, {$StrAttackSoldierAttribute}},
        {
            defense_list,
            [
                " . implode(', ', $DefenseList)  ."
            ]
        }
    ].";
    file_put_contents($ErlangParamFile, $WarParamList);

    # 战争
    if (!empty ($AttackSoldierList) && $Runtimes > 0 && $Runtimes <= 1000) {
        $Cmd = "erl -noshell -pa ./ebin -s war_demo rank \"{$ErlangParamFile}\" \"{$ResultFile}\" -s init stop";
        exec_cmd($Cmd);
        unlink($ErlangParamFile);

        # 读取结果
        $WarResult = trim(file_get_contents($ResultFile));

        if (strlen($WarResult) > 0) {
            preg_match('/<win_count>(.*)<\/win_count>/', $WarResult, $WinCount);
            preg_match('/<lose_count>(.*)<\/lose_count>/', $WarResult, $LoseCount);
            preg_match('/<score>(.*)<\/score>/', $WarResult, $Score);
            preg_match('/<attack_score>(.*)<\/attack_score>/', $WarResult, $AttackScore);
            preg_match('/<defense_score>(.*)<\/defense_score>/', $WarResult, $DefenseScore);
            preg_match('/<avg_attack>(.*)<\/avg_attack>/', $WarResult, $AvgAttack);
            preg_match('/<avg_defense>(.*)<\/avg_defense>/', $WarResult, $AvgDefense);
            preg_match('/<bout_count>(.*)<\/bout_count>/', $WarResult, $BoutCount);
            preg_match('/<attack_score_rate>(.*)<\/attack_score_rate>/', $WarResult, $AttackScoreRate);
            preg_match('/<defense_score_rate>(.*)<\/defense_score_rate>/', $WarResult, $DefenseScoreRate);

            if (empty ($WinCount) || empty ($LoseCount) || empty ($Score) || empty ($AttackScore) || empty ($DefenseScore) || empty ($AttackScoreRate) || empty ($DefenseScoreRate)) {
                $FightResult = $WarResult;
            }
            else {
                $Result = "<span style='color:red'>胜{$WinCount[1]}</span>&nbsp&nbsp;<span style='color:blue'>败{$LoseCount[1]}</span><br>";
                $FightResult = "<table border='0' cellspacing='1' bgcolor='#333222'>
                                    <tr bgcolor='#FFFFFF' height='25px'><td width='60px'>分数:</td><td colspan=3>{$Score[1]}</td></tr>
                                    <tr bgcolor='#FFFFFF' height='25px'><td>伤害:</td><td width='80px'>{$AvgAttack[1]}(平均)</td><td width='50px'>{$AttackScore[1]}(分数)</td><td>{$AttackScoreRate[1]}%(百分比)</td></tr>
                                    <tr bgcolor='#FFFFFF' height='25px'><td>损血:</td><td width='80px'>{$AvgDefense[1]}(平均)</td><td>{$DefenseScore[1]}(分数)</td><td>{$DefenseScoreRate[1]}%(百分比)</td></tr>
                                </table>";
            }
        }
        else {
            $Result = "<span style='color:red'>胜0</span>&nbsp&nbsp;<span style='color:blue'>败{$Runtimes}</span><br>";
        }
    }
}
else {
    if (file_exists($PhpParamFile)) {
        $SoldierAttributeList = unserialize(file_get_contents($PhpParamFile));
    }
}
include TEMPLATE . '/rank.html';
?>