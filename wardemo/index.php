<?php
require_once 'db.php';

$ConfigString = file_get_contents('config.ini');
preg_match('/{mysql_server,(.*)}/', $ConfigString, $MysqlServer);
preg_match('/{mysql_db,(.*)}/', $ConfigString, $MysqlDb);
preg_match('/{mysql_user,(.*)}/', $ConfigString, $MysqlUser);
preg_match('/{mysql_pwd,(.*)}/', $ConfigString, $MysqlPwd);
preg_match('/{game_server,(.*)}/', $ConfigString, $GameServer);
preg_match('/{game_server_cookie,(.*)}/', $ConfigString, $GameServerCookie);
preg_match('/{cache_server,(.*)}/', $ConfigString, $CacheServer);
preg_match('/{cache_server_cookie,(.*)}/', $ConfigString, $CacheServerCookie);

$Config['mysql_server']       = trim($MysqlServer[1]);
$Config['mysql_db']           = trim($MysqlDb[1]);
$Config['mysql_user']         = trim($MysqlUser[1]);
$Config['mysql_pwd']          = trim($MysqlPwd[1]);
$Config['game_server']        = trim($GameServer[1]);
$Config['game_server_cookie'] = trim($GameServerCookie[1]);
$Config['cache_server']        = trim($CacheServer[1]);
$Config['cache_server_cookie'] = trim($CacheServerCookie[1]);

$db = new Db($Config);

define('TEMPLATE', 'template/');

# 模板数据
$Role            = TemplateDb::GetRole();
$StuntType       = TemplateDb::GetStunt();
$AttackRangeType = TemplateDb::GetAttackRange();
$PositionList    = TemplateDb::GetGridType();
$WeaponEffect1	 = TemplateDb::GetWeaponEffect1();
$WeaponEffect2	 = TemplateDb::GetWeaponEffect2();
$IsBoss			 = TemplateDb::GetIsBoss();
$RoleType		 = TemplateDb::GetRoleType();
$ElementType	 = TemplateDb::GetElementType();
$PassivityStunt  = TemplateDb::GetPassivityStunt();
$BloodPetStunt   = TemplateDb::GetBloodPetStunt();

# 缓存数据目录
$ClientIp = $_SERVER['REMOTE_ADDR'];

# 有小数数据
$FloatList = array(
        'hit',
        'base_hit',
        'block',
        'base_block',
        'break_block',
        'base_break_block',
        'dodge',
        'base_dodge',
        'critical',
        'base_critical',
        'break_critical',
        'base_break_critical',
        'kill',
        'speed',
        'dec_kill'
    );

$LevelValue = array(
  'hit',
  'block',
  'dodge',
  'critical',
  'break_block',
  'break_critical'
);
    
$CacheFolder = 'cache';
if(!file_exists($CacheFolder)) {
    mkdir($CacheFolder);
    chmod($CacheFolder, 0777);
}

$Action = isset($_REQUEST['ac'])?$_REQUEST['ac']:'';
if ($Action == '') {
    include 'war_demo.php';
}
else if ($Action == 'rank') {
    include 'rank.php';
}
else if ($Action == 'gwp') {
    include 'get_war_param.php';
}

function exec_cmd ($Cmd) {
    $SuDo = '';
    $Os = strtolower(php_uname());
    if (strpos($Os, 'kernel') !== false || strpos($Os, 'linux') !==false || strpos($Os, 'unix') !== false) {
        $SuDo = 'sudo ';
    }
    exec("{$SuDo}{$Cmd}>cache/result");
}

function GetPostRoleAttribute ($ArmyType, $RoleIndexList, $Post) {
    global $JobType, $StuntType;
    global $FloatList, $LevelValue;
    
    $AttributeList = array();
    
    foreach ($RoleIndexList as $Index) {
        $Attribute = TemplateDb::GetRoleAttribute($Index);
        $AttributeKeyList = array_keys($Attribute);
        foreach ($AttributeKeyList as $AttributeKey) {
            $TextName = "{$ArmyType}_{$Index}_{$AttributeKey}";
            
            if (isset($Post[$TextName])) {
                $Attribute[$AttributeKey] = in_array($AttributeKey, $FloatList) ? $Post[$TextName] * 100 : $Post[$TextName];
                $Attribute[$AttributeKey] = in_array($AttributeKey, $LevelValue) ? $Attribute[$AttributeKey] / 10 : $Attribute[$AttributeKey];
            }
        }
        $RoleJob = TemplateDb::GetJob($Attribute['role_sign']);
        $Attribute['role_max_health'] = $Attribute['health'];
        $Attribute['role_job_id']     = $RoleJob['id'];
        $Attribute['role_job_sign']   = $RoleJob['sign'];
		$Attribute['role_base_stunt'] = $Attribute['role_stunt'];
        $Attribute['role_stunt_type'] = TemplateDb::GetStuntType($Attribute['role_stunt']);
        $AttributeList[$Attribute['role_id']] = $Attribute;
    }
    
    return $AttributeList;
}


function ArrayToErlangList ($List) {
    return '[' . implode(',', $List) . ']';
}

/**
 * 组装属性列表
 */
function SoldierAttributeListToErlang ($SoldierAttributeList) {
    global $FloatList;
    $List = array();
    $StringAttribute = array(
        'role_sign',
        'role_name'
    );
    $MacroArray = array(
        'attack_range'      => 'role_attack_range',
        'role_stunt_type'   => 'role_stunt_type',
        'role_stunt'        => 'role_stunt',
		'role_base_stunt'	=> 'role_stunt',
        'role_stunt_attack_range' => 'role_attack_range',
    );
    
    foreach ($SoldierAttributeList as $SoldierAttribute) {
        
        $attribute = array();
        unset($SoldierAttribute['role_job_sign']);
        
        foreach ($SoldierAttribute as $Key => $Value) {
            if (in_array($Key, $StringAttribute)) {
                $attribute[$Key] = "\"{$Value}\"";
            }
            else if (in_array($Key, array_keys($MacroArray))) {
                $attribute[$Key] = TemplateDb::GetId($MacroArray[$Key], $Value);
            }
            else {
                $attribute[$Key] = in_array($Key, $FloatList) ? intval($Value) / 100 : intval($Value);
            }
        }
        
        array_push(
            $List,
            '{player_role_war_attribute, ' . implode(', ', $attribute) . '}'
        );
    }

    return '[' . implode(',', $List) . ']';
}


/**
 * 获取角色属性HTML数据
 */
function GetSoldierAttributeHtml ($PlayerId, $ArmyType, $SoldierList, $SoldierAttributeList) {
    $Top = "                  <table border=\"0\" cellspacing=\"1\" bgcolor=\"#333222\" width=\"100%\">
                      <tr bgcolor=\"#FFFFFF\">
                        <td colspan=\"50\" align=\"center\">
                            <input type=\"button\" value=\"加载玩家数据\" onclick=\"call_gwp_ajax('{$ArmyType}', 'player');\" />
                            ID<input id=\"{$ArmyType}_gwp_id\" type=\"textbox\" size=\"5\" value=\"{$PlayerId}\" />
                            <input type=\"button\" value=\"加载怪数据\" onclick=\"call_gwp_ajax('{$ArmyType}', 'monster');\" />
                        </td>
                      </tr>
                      <tr align=\"center\" bgcolor=\"#CCCCFF\">
                          <td>参战</td>
                          <td>名称</td>
                          <td>角色</td>
                          <td>攻击<br>范围</td>
                          <td width=\"200px\">战法</td>
                          <td>战法<br>攻击<br>范围</td>
                          <td>等级</td>
                          <td>生命</td>
                          <td>攻击</td>
                          <td>防御</td>
                          <td>法术<br>攻击</td>
                          <td>法术<br>防御</td>
                          <td>绝技<br>攻击</td>
                          <td>绝技<br>防御</td>
                          <td>命中(LV)</td>
                          <td>基础<br>命中</td>
                          <td>格挡(LV)</td>
                          <td>基础<br>格挡</td>
                          <td>降低<br>格挡(LV)</td>
                          <td>基础<br>降低<br>格挡</td>
                          <td>闪避(LV)</td>
                          <td>基础<br>闪避</td>
                          <td>暴击(LV)</td>
                          <td>基础<br>暴击</td>
                          <td>降低<br>暴击(LV)</td>
                          <td>基础<br>降低<br>暴击</td>
                          <td>必杀</td>
                          <td>阵形<br>坐标</td>
                          <td>初始<br>气势</td>
						  <td>施放<br>绝技<br>气势</td>
                          <td>速度</td>
                          <td>神甲</td>
						  <td>是否<br>boss</td>
						  <td>角色<br>类型</td>
						  <td>主角<br>伤害<br>加成</td>
						  <td>保留<br>气势</td>
						  <td>元素<br>类型</td>
						  <td>血契<br>灵兽</td>
                          <td>普通伤害</td>
                          <td>降低<br>必杀</td>
						  <td>被动<br>技能</td>
						  <td>技能<br>等级</td>
						  <td>技能<br>参数1</td>
						  <td>技能<br>参数2</td>
						  <td>天罡<br>神兵</td>
						  <td>提升<br>数值</td>
						  <td>参数<br>数值</td>
						  <td>地煞<br>神兵</td>
						  <td>提升<br>数值</td>
						  <td>参数<br>数值</td>
                      </tr>";
    
    
    $Middle = '';
    
    $I = 1;
    foreach($SoldierAttributeList as $Attribute) {
        $MiddleTemplate = GetAttributeHtml($ArmyType, $SoldierList, $I, $Attribute);
        $Middle .= $MiddleTemplate;
        $I++;
    }
    
    $Bottom = '</table>';
    
    return "{$Top}{$Middle}{$Bottom}";
}


function GetAttributeHtml ($ArmyType, $SoldierList, $Index, $SoldierAttribute) {
    global $PositionList,
           $AttackRangeType,
           $StuntType,
           $Role,
           $FloatList,
           $LevelValue,
		   $WeaponEffect1,
		   $WeaponEffect2,
		   $IsBoss,
		   $RoleType,
		   $ElementType,
		   $PassivityStunt,
		   $BloodPetStunt;
    
    foreach($SoldierAttribute as $AttributeKey => $Value) {
        $$AttributeKey = in_array($AttributeKey, $FloatList) ? $Value / 100 : $Value;
        $$AttributeKey = in_array($AttributeKey, $LevelValue) ? $$AttributeKey * 10 : $$AttributeKey;

        $Key = "{$AttributeKey}_text_name";
        $$Key = "{$ArmyType}_{$Index}_{$AttributeKey}";
    }
    
    $IsChk = in_array($role_id, $SoldierList) ? 'checked' : '';
    
    $BgColor = 'checked' == $IsChk ? "gray" : "#FFFFFF";
    
    # 角色列表
    $RoleHtml = '';
    foreach ($Role as $RoleSign => $RoleName) {
        $Selected = '';
        if ($RoleSign==$role_sign) {
            $Selected = 'selected';
        }
        $RoleHtml .= "<option value=\"{$RoleSign}\" {$Selected}>{$RoleName}</option>";
    }
    
    
    # 攻击范围列表
    $AttackRangeHtml = '';
    foreach ($AttackRangeType as $AttackTypeKey => $AttackTypeValue) {
        $Selected = '';
        if ($AttackTypeKey == $attack_range) {
            $Selected = 'selected';
        }
        $AttackRangeHtml .= "<option value=\"{$AttackTypeKey}\" {$Selected}>{$AttackTypeValue}</option>";
    }
    
    # 战法列表
    $RoleStuntHtml = '';
    foreach ($StuntType as $StuntKey => $StuntValue) {
        $Selected = '';
        if ($StuntKey == $role_stunt) {
            $Selected = 'selected';
        }
        $RoleStuntHtml .= "<option value=\"{$StuntKey}\" {$Selected}>{$StuntValue}</option>";
    }
    
    # 战法攻击范围列表
    $RoleStuntAttackRangeHtml = '';
    foreach ($AttackRangeType as $AttackTypeKey => $AttackTypeValue) {
        $Selected = '';
        if ($AttackTypeKey == $role_stunt_attack_range) {
            $Selected = 'selected';
        }
        $RoleStuntAttackRangeHtml .= "<option value=\"{$AttackTypeKey}\" {$Selected}>{$AttackTypeValue}</option>";
    }
    
    # 坐标列表
    $PositionHtml = '';
    foreach ($PositionList as $PositionValue => $PositionDesc) {
        $Selected = '';
        if ($PositionValue == $position) {
            $Selected = 'selected';
        }
        
        $PositionHtml .= "<option value=\"{$PositionValue}\" {$Selected}>{$PositionDesc}</option>";
    }
	
	# 是否boss
	$IsBossHtml = '';
	foreach ($IsBoss as $IsBossValue => $IsBossDesc) {
        $Selected = '';
        if ($IsBossValue == $is_boss) {
            $Selected = 'selected';
        }
        
        $IsBossHtml .= "<option value=\"{$IsBossValue}\" {$Selected}>{$IsBossDesc}</option>";
    }
	
	# 角色类型列表
	$RoleTypeHtml = '';
	foreach ($RoleType as $RoleTypeValue => $RoleTypeDesc) {
        $Selected = '';
        if ($RoleTypeValue == $is_main_role) {
            $Selected = 'selected';
        }
        
        $RoleTypeHtml .= "<option value=\"{$RoleTypeValue}\" {$Selected}>{$RoleTypeDesc}</option>";
    }
	
	# 元素类型列表
	$ElementTypeHtml = '';
	foreach ($ElementType as $ElementTypeValue => $ElementTypeDesc) {
        $Selected = '';
        if ($ElementTypeValue == $element) {
            $Selected = 'selected';
        }
        
        $ElementTypeHtml .= "<option value=\"{$ElementTypeValue}\" {$Selected}>{$ElementTypeDesc}</option>";
    }
	
	# 被动技能类型列表
	$PassivityStuntHtml = '';
	foreach ($PassivityStunt as $PassivityStuntValue => $PassivityStuntDesc) {
        $Selected = '';
        if ($PassivityStuntValue == $passivity_stunt) {
            $Selected = 'selected';
        }
        
        $PassivityStuntHtml .= "<option value=\"{$PassivityStuntValue}\" {$Selected}>{$PassivityStuntDesc}</option>";
    }
	
	# 血契灵兽技能类型列表
	$BloodPetStuntHtml = '';
	foreach ($BloodPetStunt as $BloodPetStuntValue => $BloodPetStuntDesc) {
        $Selected = '';
        if ($BloodPetStuntValue == $blood_pet_stunt) {
            $Selected = 'selected';
        }
        
        $BloodPetStuntHtml .= "<option value=\"{$BloodPetStuntValue}\" {$Selected}>{$BloodPetStuntDesc}</option>";
    }
	
	# 神兵效果列表
	$WeaponEffectHtml1 = '';
    foreach ($WeaponEffect1 as $WeaponEffectValue => $WeaponEffectDesc) {
        $Selected = '';
        if ($WeaponEffectValue == $weapon_effect) {
            $Selected = 'selected';
        }
        
        $WeaponEffectHtml1 .= "<option value=\"{$WeaponEffectValue}\" {$Selected}>{$WeaponEffectDesc}</option>";
    }
	
	# 神兵效果列表
	$WeaponEffectHtml2 = '';
    foreach ($WeaponEffect2 as $WeaponEffectValue => $WeaponEffectDesc) {
        $Selected = '';
        if ($WeaponEffectValue == $weapon_effect2) {
            $Selected = 'selected';
        }
        
        $WeaponEffectHtml2 .= "<option value=\"{$WeaponEffectValue}\" {$Selected}>{$WeaponEffectDesc}</option>";
    }
    
    return "
        <tr align=\"center\" bgcolor=\"{$BgColor}\" id=\"{$ArmyType}_{$role_id}\" style=\"background-color:{$BgColor};\">
                          <td><input name=\"{$ArmyType}_role_list[]\" type=\"checkbox\" value=\"{$role_id}\" onclick=\"choose(this, '{$ArmyType}_{$role_id}')\" {$IsChk} /></td>
                          <td><input id=\"{$role_name_text_name}\" name=\"{$role_name_text_name}\" type =\"text\" value=\"{$role_name}\" onclick=\"this.select();\"  size=\"10\" /></td>
                          <td align=\"left\">
                              <select name=\"{$role_sign_text_name}\">
                                  {$RoleHtml}
                              </select>
                          </td>						 					

                          <td>
                              <select name=\"{$attack_range_text_name}\">
                                  {$AttackRangeHtml}
                              </select>
                          </td>

                          <td>
                              <select name=\"{$role_stunt_text_name}\">
                                  {$RoleStuntHtml}
                              </select>
                          </td>

                          <td>
                              <select name=\"{$role_stunt_attack_range_text_name}\">
                                  {$RoleStuntAttackRangeHtml}
                              </select>
                          </td>
                          <td><input id=\"{$role_level_text_name}\" name=\"{$role_level_text_name}\" onclick=\"this.select();\" type =\"text\" value=\"{$role_level}\" size=\"2\" /></td>
                          <td><input style=\"color:blue\" id=\"{$health_text_name}\" name=\"{$health_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$health}\" size=\"3\" /></td>
                          <td><input style=\"color:blue\" id=\"{$attack_text_name}\" name=\"{$attack_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$attack}\" size=\"3\" /></td>
                          <td><input style=\"color:blue\" id=\"{$defense_text_name}\" name=\"{$defense_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$defense}\" size=\"3\" /></td>
                          <td><input style=\"color:blue\" id=\"{$magic_attack_text_name}\" name=\"{$magic_attack_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$magic_attack}\" size=\"3\" /></td>
                          <td><input style=\"color:blue\" id=\"{$magic_defense_text_name}\" name=\"{$magic_defense_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$magic_defense}\" size=\"3\" /></td>
                          <td><input style=\"color:blue\" id=\"{$stunt_attack_text_name}\" name=\"{$stunt_attack_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$stunt_attack}\" size=\"3\" /></td>
                          <td><input style=\"color:blue\" id=\"{$stunt_defense_text_name}\" name=\"{$stunt_defense_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$stunt_defense}\" size=\"3\" /></td>
                          <td><input style=\"color:blue\" id=\"{$hit_text_name}\" name=\"{$hit_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$hit}\" size=\"2\" /></td>
                          <td><input style=\"color:blue\" id=\"{$base_hit_text_name}\" name=\"{$base_hit_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$base_hit}\" size=\"2\" /></td>
                          <td><input style=\"color:blue\" id=\"{$block_text_name}\" name=\"{$block_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$block}\" size=\"2\" /></td>
                          <td><input style=\"color:blue\" id=\"{$base_block_text_name}\" name=\"{$base_block_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$base_block}\" size=\"2\" /></td>
                          <td><input style=\"color:blue\" id=\"{$break_block_text_name}\" name=\"{$break_block_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$break_block}\" size=\"2\" /></td>
                          <td><input style=\"color:blue\" id=\"{$base_break_block_text_name}\" name=\"{$base_break_block_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$base_break_block}\" size=\"2\" /></td>
                          <td><input style=\"color:blue\" id=\"{$dodge_text_name}\" name=\"{$dodge_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$dodge}\" size=\"2\" /></td>
                          <td><input style=\"color:blue\" id=\"{$base_dodge_text_name}\" name=\"{$base_dodge_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$base_dodge}\" size=\"2\" /></td>
                          <td><input style=\"color:blue\" id=\"{$critical_text_name}\" name=\"{$critical_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$critical}\" size=\"2\" /></td>
                          <td><input style=\"color:blue\" id=\"{$base_critical_text_name}\" name=\"{$base_critical_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$base_critical}\" size=\"2\" /></td>
                          <td><input style=\"color:blue\" id=\"{$break_critical_text_name}\" name=\"{$break_critical_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$break_critical}\" size=\"2\" /></td>
                          <td><input style=\"color:blue\" id=\"{$base_break_critical_text_name}\" name=\"{$base_break_critical_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$base_break_critical}\" size=\"2\" /></td>
                          <td><input style=\"color:blue\" id=\"{$kill_text_name}\" name=\"{$kill_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$kill}\" size=\"2\" /></td>
                          
                          <td>
                              <select name=\"{$position_text_name}\">
                                  {$PositionHtml}
                              </select>
                          </td>
                          
                          <td><input style=\"color:blue\" id=\"{$momentum_text_name}\" name=\"{$momentum_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$momentum}\" size=\"3\" /></td>
						  <td><input style=\"color:blue\" id=\"{$full_momentum_text_name}\" name=\"{$full_momentum_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$full_momentum}\" size=\"3\" /></td>
                          <td><input style=\"color:blue\" id=\"{$speed_text_name}\" name=\"{$speed_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$speed}\" size=\"2\" /></td>
                          <td><input style=\"color:blue\" id=\"{$armor_text_name}\" name=\"{$armor_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$armor}\" size=\"2\" /></td>
						  
						  <td>
                              <select name=\"{$is_boss_text_name}\">
                                  {$IsBossHtml}
                              </select>
                          </td>
						  
						  <td>
                              <select name=\"{$is_main_role_text_name}\">
                                  {$RoleTypeHtml}
                              </select>
                          </td>
						  
						  <td><input style=\"color:blue\" id=\"{$main_role_hurt_text_name}\" name=\"{$main_role_hurt_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$main_role_hurt}\" size=\"2\" /></td>
						  <td><input style=\"color:blue\" id=\"{$save_momentum_text_name}\" name=\"{$save_momentum_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$save_momentum}\" size=\"2\" /></td>
                          
						  <td>
                              <select name=\"{$element_text_name}\">
                                  {$ElementTypeHtml}
                              </select>
                          </td>
						  
						  <td>
                              <select name=\"{$blood_pet_stunt_text_name}\">
                                  {$BloodPetStuntHtml}
                              </select>
                          </td>
						  
						  <td><input style=\"color:blue\" id=\"{$normal_attack_text_name}\" name=\"{$normal_attack_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$normal_attack}\" size=\"2\" /></td>
                          <td><input style=\"color:blue\" id=\"{$dec_kill_text_name}\" name=\"{$dec_kill_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$dec_kill}\" size=\"2\" /></td>
						  
						  <td>
                              <select name=\"{$passivity_stunt_text_name}\">
                                  {$PassivityStuntHtml}
                              </select>
                          </td>
						  
						  <td><input style=\"color:blue\" id=\"{$passivity_stunt_lv_text_name}\" name=\"{$passivity_stunt_lv_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$passivity_stunt_lv}\" size=\"2\" /></td>
                          <td><input style=\"color:blue\" id=\"{$passivity_param1_text_name}\" name=\"{$passivity_param1_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$passivity_param1}\" size=\"2\" /></td>
						  <td><input style=\"color:blue\" id=\"{$passivity_param2_text_name}\" name=\"{$passivity_param2_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$passivity_param2}\" size=\"2\" /></td>
						  
						  <td>
                              <select name=\"{$weapon_effect_text_name}\">
                                  {$WeaponEffectHtml1}
                              </select>
                          </td>
						  						  
						  <td><input style=\"color:blue\" id=\"{$effect_value_text_name}\" name=\"{$effect_value_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$effect_value}\" size=\"2\" /></td>	
						  <td><input style=\"color:blue\" id=\"{$effect_param_text_name}\" name=\"{$effect_param_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$effect_param}\" size=\"2\" /></td>
						  
						  <td>
                              <select name=\"{$weapon_effect2_text_name}\">
                                  {$WeaponEffectHtml2}
                              </select>
                          </td>
						  						  
						  <td><input style=\"color:blue\" id=\"{$effect_value2_text_name}\" name=\"{$effect_value2_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$effect_value2}\" size=\"2\" /></td>	
						  <td><input style=\"color:blue\" id=\"{$effect_param2_text_name}\" name=\"{$effect_param2_text_name}\" onchange=\"fill_data('{$ArmyType}', '{$role_id}', this.id);\" onclick=\"this.select();\" type =\"text\" value=\"{$effect_param2}\" size=\"2\" /></td>
					  </tr>
    ";
}


/*
 * 模板数据
 */
class TemplateDb {
    
    /*
     * 职业角色
     */
    static function GetRole () {
        global $db;

        $Result = array();
        $Rows = $db->queryAll('select sign, name from role');
        
        foreach ($Rows as $Row) {
            $Result[$Row['sign']] = $Row['name'];
        }

        return $Result;
    }

    /*
     * 获取职业类型
     */
    static function GetJob ($RoleSign) {
        global $db;

        $Row = $db->queryOne("
                select rj.id, rj.sign from role_job rj left join role r
                    on rj.id = r.role_job_id where r.sign = '{$RoleSign}'
        ");

         return $Row;
    }
    

    /*
     * 战法
     */
    static function GetStunt () {
        global $db;

        $Result = array();
        $Rows = $db->queryAll('select id, sign, name from role_stunt order by id desc');

        foreach ($Rows as $Row) {
            $Result[$Row['sign']] = $Row['name'] . "({$Row['id']})";
        }

        return $Result;
    }

    /*
     * 阵形
     */
    static function GetGridType () {
        global $db;

        $Result = array();
        $Rows = $db->queryAll('select `name`, `desc` from `deploy_grid_type`');

        foreach ($Rows as $Row) {
            $Result[$Row['name']] = $Row['desc'];
        }

        return $Result;
    }
	
	/*
     * 神兵效果
     */
    static function GetWeaponEffect1 () {
        global $db;

        $Result = array();
		$Result[0] = '无效果';
        $Rows = $db->queryAll('select `id`, `description` from `enhance_weapon_effect` where `id` < 5');

        foreach ($Rows as $Row) {
            $Result[$Row['id']] = $Row['description'];
        }

        return $Result;
    }
	
	static function GetWeaponEffect2 () {
        global $db;

        $Result = array();
		$Result[0] = '无效果';
        $Rows = $db->queryAll('select `id`, `description` from `enhance_weapon_effect` where `id` > 4');

        foreach ($Rows as $Row) {
            $Result[$Row['id']] = $Row['description'];
        }

        return $Result;
    }
	
	/*
     * 是否boss
     */
	static function GetIsBoss () {
		$Result = array();
		$Result[0] = '否';
		$Result[1] = '是';
		
		return $Result;
	}
	
	/*
     * 角色类型
     */
	static function GetRoleType () {
		$Result = array();
		$Result[0] = '伙伴';
		$Result[1] = '主角';
		
		return $Result;
	}
	
	/*
     * 元素类型
     */
	static function GetElementType () {
		$Result = array();
		$Result[0] = '无';
		$Result[1] = '火';
		$Result[2] = '水';
		$Result[3] = '木';
		
		return $Result;
	}

	/*
     * 被动技能类型
     */
	static function GetPassivityStunt () {
		global $db;

        $Result = array();
		$Result[0] = '无';
        $Rows = $db->queryAll('select `id`, `name` from `passivity_stunt`');

        foreach ($Rows as $Row) {
            $Result[$Row['id']] = $Row['name'];
        }

        return $Result;
	}
	
	/*
     * 血契灵兽技能类型
     */
	static function GetBloodPetStunt () {
		global $db;

        $Result = array();
		$Result[0] = '无';
        $Rows = $db->queryAll('select `id`, `name` from `blood_pet_stunt`');

        foreach ($Rows as $Row) {
            $Result[$Row['id']] = $Row['name'];
        }

        return $Result;
	}
	
    /*
     * 攻击范围
     */
    static function GetAttackRange () {
        global $db;

        $Result = array();
        $Rows = $db->queryAll('select sign, name from role_attack_range');

        foreach ($Rows as $Row) {
            $Result[$Row['sign']] = $Row['name'];
        }

        return $Result;
    }

    /*
     * 获取角色属性
     */
    static function GetRoleAttribute ($RoleId) {
        return array(
            'role_id'               => $RoleId,
            'role'                  => 1,
            'role_sign'             => 'JianLingNan',
            'role_name'             => '剑灵男',
            'role_level'            => 1,
            'role_max_health'       => 100,
            'role_job_id'           => 0,
            'health'                => 50,
            'attack'                => 10,
            'defense'               => 9,
            'magic_attack'          => 0,
            'magic_defense'         => 0,
            'stunt_attack'          => 15,
            'stunt_defense'         => 14,
            'hit'                   => 0,
            'base_hit'              => 0,
            'block'                 => 0,
            'base_block'            => 0,
            'break_block'           => 0,
            'base_break_block'      => 0,
            'break_critical'        => 0,
            'base_break_critical'   => 0,
            'dodge'                 => 0,
            'base_dodge'            => 0,
            'critical'              => 0,
            'base_critical'         => 0,
            'kill'                  => 0,
            'attack_range'          => 'A',
            'role_stunt_type'       => 'ZhanFa',
			'role_base_stunt'       => 'YuJianShu',
            'role_stunt'            => 'YuJianShu',
            'role_stunt_attack_range' => 'A',
            'position'                => 1,
            'momentum'                => 50,
            'is_boss'                 => 0,
            'first_attack'            => 0,
            'is_monster'              => false,
            'speed'                   => 0,
			'src_speed'				  => 0,
            'normal_attack'           => 0,
            'dec_kill'                => 0,
            'inc_jiangxing_injure'    => 0,
            'inc_jianxiu_injure'      => 0,
            'inc_wudao_injure'        => 0,
            'inc_lieshou_injure'      => 0,
			'passivity_stunt'	=> 0,
			'passivity_stunt_lv'=> 0,
			'passivity_param1'	=> 0,
			'passivity_param2'	=> 0,
			'weapon_effect'		=> 0,
			'weapon_level'		=> 0,
			'effect_prob'		=> 0,
			'effect_value'		=> 0,
			'effect_param'		=> 0,
			'weapon_effect2'	=> 0,
			'weapon_level2'		=> 0,
			'effect_prob2'		=> 0,
			'effect_value2'		=> 0,
			'effect_param2'		=> 0,
			'blood_pet_stunt'   => 0,
            'blood_pet_id'      => 0,
			'is_main_role'		=> 0,
			'main_role_hurt' 	=> 0,
			'save_momentum'		=> 0,
			'element'			=> 0,
			'full_momentum'		=> 100,
            'armor'             => 0
        );
        
    }


    /*
     * 获取战法类型
     */
    static function GetStuntType ($StuntSign) {
        global $db;
        
        $Row = $db->queryOne("
                select rst.sign from role_stunt_type rst
                    left join role_stunt rs on rst.id = rs.role_stunt_type_id
                    where rs.sign='{$StuntSign}'
        ");

         return $Row['sign'];
    }


    /*
     * 获取副本列表
     */
    static function GetMissionList () {
        global $db;

        $MissionList = array();
        $SectionList = $db->queryAll('select id, name from mission_section');
        foreach($SectionList as $Section) {
            $Rows = $db->queryAll('select id from mission where mission_section_id = ' . $Section['id'] . ' order by `lock` asc');
            $I = 1;
            foreach($Rows as $Row) {
                array_push($MissionList, array('id' => $Row['id'], 'name' => $Section['name'] . "({$I})"));
                $I++;
            }
        }
        return $MissionList;
    }

    /*
     *  获取怪物团怪物
     */
    static function GetSceneMonsterTeam ($MissionId) {
        global $db;

        $scene_list = $db->queryAll("select id from mission_scene where mission_id = {$MissionId} order by `lock` asc");

        $scene_monster_team = array();

        foreach ($scene_list as $scene) {

            $monster_team_list = $db->queryAll("select id from mission_monster_team where mission_scene_id = {$scene['id']}");
            foreach ($monster_team_list as $monster_team) {

                $monster_attribute = GetMonsterTeamParam($monster_team['id']);

                array_push($scene_monster_team, $monster_attribute);
            }

        }

        return $scene_monster_team;
    }
    
    
    static function GetMonsterTeamParam ($MonsterTeamId) {
        global $db;
        
        $monster_attribute = array ();
        $monster_list = $db->queryAll("select distinct m.*, mmt.id as mmt_id, mm.id as mm_id, mm.`speed`, mmt.`lock` as mmt_lock, rs.sign as rs_sign, rs.name as rs_name, rj.id as rj_id, rj.sign as rj_sign, rst.sign as rst_sign, rar.sign as rar_sign, dgt.name as dgt_name
            from mission_monster_team mmt
            left join mission_monster mm on mmt.id = mm.mission_monster_team_id
            left join deploy_grid dg on dg.id = mm.deploy_grid_id
            left join deploy_grid_type dgt on dgt.id = dg.deploy_grid_type_id
            left join monster m on m.id = mm.monster_id
            left join role_job rj on m.role_job_id = rj.id
            left join role_stunt rs on rs.id = m.role_stunt_id
            left join role_stunt_type rst on rst.id = rs.role_stunt_type_id
            left join role_attack_range rar on rar.id = rs.role_attack_range_id
            where mmt.id = {$MonsterTeamId} order by mmt_lock asc;");

        foreach ($monster_list as $monster) {
            $attribute = array(
                'role_id'       => $monster['mm_id'],
                'role'          => $monster['id'],
                'role_sign'     => $monster['sign'],
                'role_name'     => $monster['name'],
                'role_level'    => $monster['level'],
                'role_max_health' => $monster['health'],
                'role_job_id'   => $monster['rj_id'],
                'role_job_sign' => $monster['rj_sign'],
                'health'        => $monster['health'],
                'attack'        => $monster['attack'],
                'defense'       => $monster['defense'],
                'magic_attack'  => $monster['magic_attack'],
                'magic_defense' => $monster['magic_defense'],
                'stunt_attack'  => $monster['stunt_attack'],
                'stunt_defense' => $monster['stunt_defense'],
                'hit'                => 0,
                'base_hit'           => $monster['hit'] * 100,
                'block'              => 0,
                'base_block'         => $monster['block'] * 100,
                'break_block'        => 0,
                'base_break_block'   => $monster['break_block'] * 100,
                'break_critical'     => 0,
                'base_break_critical'   => $monster['break_critical'] * 100,
                'kill'               => $monster['kill'] * 100,
                'dodge'              => 0,
                'base_dodge'         => $monster['dodge'] * 100,
                'critical'           => 0,
                'base_critical'      => $monster['critical'] * 100,
                'attack_range'       => 'A',
                'role_stunt_type'    => $monster['rst_sign'],
				'role_base_stunt'    => $monster['rs_sign'],
                'role_stunt'         => $monster['rs_sign'],
                'role_stunt_attack_range' => $monster['rar_sign'],
                'position'         => $monster['dgt_name'],
                'momentum'         => 50,
                'is_boss'          => $monster['type'] === 2? 1 : 0,
                'is_monster'       => true,
                'speed'            => $monster['speed'] * 100,
				'src_speed'		   => 0,
                'normal_attack'    => 0,
                'dec_kill'                => 0,
                'inc_jiangxing_injure'    => 0,
                'inc_jianxiu_injure'      => 0,
                'inc_wudao_injure'        => 0,
                'inc_lieshou_injure'      => 0,
				'passivity_stunt'	=> 0,
				'passivity_stunt_lv'=> 0,
				'passivity_param1'	=> 0,
				'passivity_param2'	=> 0,
				'weapon_effect'		=> 0,
				'weapon_level'		=> 0,
				'effect_prob'		=> 0,
				'effect_value'		=> 0,
				'effect_param'		=> 0,
				'weapon_effect2'	=> 0,
				'weapon_level2'		=> 0,
				'effect_prob2'		=> 0,
				'effect_value2'		=> 0,
				'effect_param2'		=> 0,
				'blood_pet_stunt'   => 0,
                'blood_pet_id'      => 0,
				'is_main_role'		=> 0,
				'main_role_hurt' 	=> 0,
				'save_momentum'		=> 0,
				'element'			=> 0,
				'full_momentum'		=> 100,
                'armor'             => 0
            );
            $monster_attribute[$attribute['role_id']] = $attribute;
        }
        
        return $monster_attribute;
    }
    
    
    static function CheckPlayerId ($PlayerId) {
        global $db;
        
        return count($db->queryAll("select 1 from player where id='{$PlayerId}'")) > 0;
    }
    
    
    static function CheckMonsterTeamId ($MonsterTeamId) {
        global $db;
        
        return count($db->queryAll("select 1 from mission_monster_team where id='{$MonsterTeamId}'")) > 0;
    }
    
    static function GetId ($Table, $Sign) {
        global $db;
        
        $row = $db->queryOne("select id from {$Table} where sign = '{$Sign}'");
        return $row['id'];
    }
    
    static function GetSign ($Table, $Id) {
        global $db;
        
        $row = $db->queryOne("select sign from {$Table} where id = '{$Id}'");
        return $row['sign'];
    }
}
?>
