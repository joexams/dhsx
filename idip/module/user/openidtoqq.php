<?php

class openidtoqq extends Module_DefaultAbstract
{
	public function onPost() {
		$params = $this->getRequestHeaderParams();
		$openid = isset($params['szOpenId']) ? trim($params['szOpenId']) : '';

		$body['szOpenId'] = $openid;
		$result_code = 0;
		echo json_encode($this->setResponse($body, '', $result_code));
	}
}