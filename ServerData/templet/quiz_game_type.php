<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### guessRiddles

$list = $dbh->query("
	select
		`id`, `question`, `answer_1`,`answer_2`,`answer_3`,`answer_4`,`answer`
	from
		`quiz_game_question`
");

$hash = "";
for ($i = 0; $i < count($list); $i++) {
	$item = $list[$i];
	
	if ($hash != "") {
		$hash .= ",\n";
	}

	$question = $item["question"];
	$question = str_replace('"', '\"', $question);
	
	$hash .= "			".$item["id"]." : [\"".$question
		
	."\",\"".$item["answer_1"]."\",\"".$item["answer_2"]."\",\"".$item["answer_3"]."\",\"".$item["answer_4"]."\",".$item["answer"]."]";
}


### 类

$str = "package com.assist.server
{
	public class GuessRiddlesType
	{
		// 谜题id ：[题目 答案1 答案2 答案3 答案4 答案序号]
		private static const Question : Object = {
".$hash."
		};
		
		/**
		 * 谜题id获取题目
		 */
		public static function getQuestion(id : int) : String
		{
			return Question[id] ? Question[id][0] : \"\";
		}
		
		/**
		 * 谜题id获取答案1
		 */
		public static function getAnswer1(id : int) : String
		{
			return Question[id] ? Question[id][1] : \"\";
		}
		
		/**
		 * 谜题id获取答案2
		 */
		public static function getAnswer2(id : int) : String
		{
			return Question[id] ? Question[id][2] : \"\";
		}
		
		/**
		 * 谜题id获取答案3
		 */
		public static function getAnswer3(id : int) : String
		{
			return Question[id] ? Question[id][3] : \"\";
		}
		
		/**
		 * 谜题id获取答案4
		 */
		public static function getAnswer4(id : int) : String
		{
			return Question[id] ? Question[id][4] : \"\";
		}
		
		/**
		 * 谜题id获取答案序号
		 */
		public static function getAnswerId(id : int) : int
		{
			return Question[id] ? Question[id][5] : 0;
		}
		
	}
}
";

file_put_contents($desc_dir."GuessRiddlesType.as", addons().$str);

echo "[data] guessRiddlesType [Done]\n";
?>