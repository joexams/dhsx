<?php
$path = dirname(dirname(__FILE__))."/";
require_once($path."config.php");

if (! isset($dbh)) {
	$dbh = new DBI();
	$dbh->connect();
}

### server_viewpoint
$list = $dbh->query("select `id`, `question`, `answer_a`, `answer_b` from `server_point_question`");

$hash = "";
for($i = 0; $i < count($list); $i++)
{
	$item = $list[$i];
	if($hash != "")
	{
		$hash .= ",\n";
	}

	$hash .= "			".$item["id"].":["."\"".$item["question"]."\", \"".$item["answer_a"]."\", \"".$item["answer_b"]."\"]";
}

### 类
$str = "package com.assist.server{
	public class ServerViewpointType{
		private static const QuestionBank : Object = {
".$hash."
		};

		/**
		*根据题目id获取该题信息
		*/
		public static function getQuestionInfo(id : int) : Object
		{
			var obj : Object = {};
			obj.question = QuestionBank[id] ? QuestionBank[id][0] : \"\";
			obj.answerA = QuestionBank[id] ? QuestionBank[id][1] : \"\";
			obj.answerB = QuestionBank[id] ? QuestionBank[id][2] : \"\";
			return obj;
		}
	}
}";
file_put_contents($desc_dir."ServerViewpointType.as", addons().$str);