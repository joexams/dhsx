<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### qi_xi

$list = $dbh->query("
	select
		`id`, `question`, `answer_a`,`answer_b`,`answer_right`
	from
		`st_qi_xi_quiz_base`
");


$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}
	
	$hash .= "			".$item["id"]." : [\"".$item["question"]."\",\"".$item["answer_a"]."\",\"".$item["answer_b"]."\",\"".$item["answer_right"]."\"]";
}



### 类

$str = "package com.assist.server
{
	public class QiXiQuestionType
	{
		// [题目id :  题目 答案a 答案b 正确答案]
		private static const Question : Object = {
".$hash."
		};
		
		/**
		 * 获取物品列表数量
		 */
		public static function getQuestion(id : int) : Object
		{
			
			if(Question[id])
			{
				var obj : Object = {};
				obj.question = Question[id][0];
				obj.answerA = Question[id][1];
				obj.answerB = Question[id][2];
				obj.rightAnswer = Question[id][3];
				return obj;
			}
			return null;
		}
	}
}
";

file_put_contents($desc_dir."QiXiQuestionType.as", addons().$str);

echo "[data] qiXiQuestionType [Done]\n";
?>