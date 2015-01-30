<?php
defined('IN_G') or exit('No permission resources.');
//common::load_class('session');
if(param::get_cookie('ho_lang')) {
	define('L_STYLE',param::get_cookie('ho_lang'));
} else {
	define('L_STYLE','zh-cn');
}
class admin {
	public $userid;
	public $username;
	
	public function __construct() {
		self::check_admin();
	}
	
	private static function exitHtml($isLogout) 
	{
		if ($isLogout > 0) {
			$outHtml = '<div class="error-main">
			<div class="error-wrap">
			    <div class="error-text-title-wrap clearfix"><div class="error-number">500，</div><div class="error-text-title"></div></div>
			    <p class="error-text-content">登录状态已过期，请重新登录</p>
			    <ul class="handle-way-list">
			        <li class="handle-way-item"><a href="'.WEB_URL.INDEX.'?m=manage&c=index&v=login">重新登录</a></li>
			    </ul>
			</div>
			</div>';
		}else {
			$outHtml = '<div class="error-main">
			<div class="error-wrap">
			    <div class="error-text-title-wrap clearfix"><div class="error-number">500，</div><div class="error-text-title"></div></div>
			    <p class="error-text-content">没有权限，请联系管理员</p>
			    <ul class="handle-way-list">
			        <li class="handle-way-item">1.您可以<a href="javascript:history.go(-1);">返回上一个页面</a></li>
			        <li class="handle-way-item">2.您可以<a href="'.WEB_URL.INDEX.'">回到首页</a></li>
			        <li class="handle-way-item">3.您可以<a href="javascript:location.reload();">尝试刷新</a></li>
			    </ul>
			</div>
			</div>';
		}
		echo $outHtml;
		exit;
	}

	/**
	 * 判断用户是否已经登陆
	 */
	final public function check_admin() {
		return true;
	}
}
