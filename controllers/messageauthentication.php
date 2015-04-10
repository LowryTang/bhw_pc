<?php
/**
* @brief 消息模块
* @class Messageauthentication
* @note  后台
* @author Clark
*/
class Messageauthentication extends IController{
	public $checkRight  = 'all';
	/*
	* @var 设置验证码失效时间 20min
	*/
	public static $expireTime = 1200;

	/*
	* @var 设置验证码位数  6位
	*/
	public static $codeCount = 6;

	/*
	* @var 设置session前缀,注册时使用的session前缀
	*/
	public static $prefix = 'sessionTelephonePrefix_';
	
	/*
	* @var 设置session前缀，忘记密码，进行重置密码时使用；
	*/
	public static $forgetPasswordPrefix = 'sessionForgetPasswordPrefix_';
	
	/*
	* @var 设置短信平台私钥
	*/
	public static $apiKey = 'dc84787276291d36a313b774471daa76';

	/*
	* @var 设置短信平台api
	*/
	public static $messageApi = 'http://yunpian.com/v1/sms/tpl_send.json';
	
	/*
	* @var 设置短信模板
	*/
	public static $tplId = 2;

	/*
	* @brief 初始化，验证权限
	*/
	public function init(){
		//IInterceptor::reg('CheckRights@onCreateAction');
	}
	
	/*
	* @brief 发送短信验证码,注册时调用此接口
	* @param $tel String 电话号码
	* @return Boolean
	*/
	public function sendCodeToValidate(){
		//$tel = IFilter::act(IReq::get('tel'),'int');
		$tel = IReq::get("tel");
		if(!$tel){
			return false;
		}
		//生成六位的数字验证码，发短信且发邮件来验证        
		$arrRand = array();
		for($count = self::$codeCount; $count > 0; $count--){
			$arrRand[] = mt_rand(0,9); 
		}
		$strRand = implode('',$arrRand);
		$arrCode = array(
			"tel" => $tel,
			"class" => 'registration',
			"code" => $strRand,
			"create_time" => strtotime('now')
		);
		$objCode   = new IModel("verification_code");
		$objCode->setData($arrCode);
		$intId = $objCode->add();
		if(!$intId){
			$result = array(
					"success" => false,
					"message" => array(
						"code" => 1,
						"msg" => "生成验证码失败"
					)
			);
			$result = json_encode($result);
			echo $result;
			return $result;
		}
		//将手机验证码存入session
		//$key = self::$prefix.$tel;
		//$ret = ISession::set($key,$arrCode);
		//$strMessage = "您的验证码是#".$strRand."#。如非本人操作，请忽略本短信";
		$siteConfigObj = new Config("site_config");
		$site_config   = $siteConfigObj->getInfo();
		$company = isset($site_config['message_company_conf']) ? $site_config['message_company_conf'] : '百花味';
		$strMessage = "#company#=".$company."&#code#=".$strRand;
		//发送验证码到手机
		$result = $this->sendMessageUsingTpl(self::$apiKey,self::$tplId,$strMessage,$tel);
		echo $result;
		return $result;

	}

	/*
	* @brief 验证短信验证码，注册时调用此接口
	* @param $tel Integer 电话号码
	* @param $code Integer 验证码
	* @return array("result"=>"0","message"=>"验证码正确")；
	*/
	public function validateCode($tel,$code){
		//$tel = IFilter::act(IReq::get('tel'),'int');
		//$code = IFilter::act(IReq::get('code'),'int');
		//$tel = IReq::get("tel");
		//$code = IReq::get("code");
		if(!$code || !$tel){
			$str = json_encode(array("result" => "1","message" => "手机号码或验证码为空"));
			return $str;
		}
		//$arrCode = ISession::get(self::$prefix.$tel);
		$objCode = new IModel('verification_code');
		$strSql = "tel ='".$tel."' and class = 'registration' ORDER BY `id` DESC";
		$arrCode = $objCode->query($strSql);
		if(empty($arrCode) || !is_array($arrCode)){
			$str = json_encode(array("result" => "1","message" => "该手机尚未发送验证码"));
			return $str;
		}
		$arrCode = $arrCode[0];
		//验证手机号码、手机验证码、验证码有效期
		$interval = strtotime('now') - $arrCode['create_time'];
		if(($tel == $arrCode['tel']) && ($code == $arrCode['code']) && ($interval <= self::$expireTime)){
			//验证通过后删除session，防止多次验证
			//ISession::clear(self::$prefix.$tel);
			$str = json_encode(array("result" => "0","message" => "验证码正确"));
			return $str;
		}elseif(($tel == $arrCode['tel']) && ($code == $arrCode['code'])){
			//清除过期的session
			//ISession::clear(self::$prefix.$tel);
			$str = json_encode(array("result" => "1","message" => "验证码超时"));
			return $str;
		}else{
			$str = json_encode(array("result" => "1","message" => "验证码错误"));
			return $str;
		}
	}
	
	/*
	* @brief 验证短信验证码，注册时调用此接口
	* @param $tel Integer 电话号码
	* @param $code Integer 验证码
	* @return array("result"=>"0","message"=>"验证码正确")；
	*/
	public static function validateMobileCode($tel,$code){
		if(!$code || !$tel){
			$str = json_encode(array("result" => "1","message" => "手机号码或验证码为空"));
			return $str;
		}
		//$arrCode = ISession::get(self::$prefix.$tel);
		$objCode = new IModel('verification_code');
		$strSql = "tel ='".$tel."' and class = 'registration' ORDER BY `id` DESC";
		$arrCode = $objCode->query($strSql);
		if(empty($arrCode) || !is_array($arrCode)){
			$str = json_encode(array("result" => "1","message" => "该手机尚未发送验证码"));
			return $str;
		}
		$arrCode = $arrCode[0];
		//验证手机号码、手机验证码、验证码有效期
		$interval = strtotime('now') - $arrCode['create_time'];
		if(($tel == $arrCode['tel']) && ($code == $arrCode['code']) && ($interval <= self::$expireTime)){
			//验证通过后删除session，防止多次验证
			//ISession::clear(self::$prefix.$tel);
			$str = json_encode(array("result" => "0","message" => "验证码正确"));
			return $str;
		}elseif(($tel == $arrCode['tel']) && ($code == $arrCode['code'])){
			//清除过期的session
			//ISession::clear(self::$prefix.$tel);
			$str = json_encode(array("result" => "1","message" => "验证码超时"));
			return $str;
		}else{
			$str = json_encode(array("result" => "1","message" => "验证码错误"));
			return $str;
		}
	}

	/*
	* @brief 定时清除session过期的手机验证码
	* @param null
	* @return null
	*/
	public function clearExpiredSessionCode(){
		if(!isset($_SESSION) || empty($_SESSION)){
			return;
		}
		foreach($_SESSION as $key => $value){
			if(stristr($key,self::$prefix)){
				$arrKey = explode("_",$key);
				$tel = $arrKey[2];
				ISession::clear(self::$prefix.$tel);
			}
			if(stristr($key,self::$forgetPasswordPrefix)){
				$arrKey = explode("_",$key);
				$tel = $arrKey[2];
				ISession::clear(self::$forgetPasswordPrefix.$tel);
			}
		}
	}

	/**
	* 普通接口发短信
	* @param $apikey 为短信平台分配的apikey
	* @param $text 为短信内容
	* @param $mobile 为接受短信的手机号
	*/
	public function sendMessage($apikey,$text,$mobile){
		$url = self::$messageApi;
		$encoded_text = urlencode("$text");
		$post_string="apikey=$apikey&text=$encoded_text&mobile=$mobile";
		return $this->sock_post($url, $post_string);
	}

	/**
	* 模板接口发短信
	* @param $apikey 为短信平台分配的apikey
	* @param $tpl_id 为模板id
	* @param $tpl_value 为模板值
	* @param $mobile 为接受短信的手机号
	*/
	public function sendMessageUsingTpl($apikey,$tpl_id,$tpl_value,$mobile){
		$url = self::$messageApi;
		$encoded_tpl_value = urlencode("$tpl_value");
		$post_string = "apikey=$apikey&tpl_id=$tpl_id&tpl_value=$encoded_tpl_value&mobile=$mobile";
		return $this->sock_post($url, $post_string);
	}

	/**
	* @param $url 为服务的url地址
	* @param $query 为请求字符串
	*/
	private function sock_post($url,$query){
		$data = "";
		$info = parse_url($url);
		$fp = fsockopen($info["host"],80,$errno,$errstr,30);
		if(!$fp){
			return $data;
		}
		$head = "POST ".$info['path']." HTTP/1.0\r\n";
		$head .= "Host: ".$info['host']."\r\n";
		$head .= "Referer: http://".$info['host'].$info['path']."\r\n";
		$head .= "Content-type: application/x-www-form-urlencoded\r\n";
		$head .= "Content-Length: ".strlen(trim($query))."\r\n";
		$head .= "\r\n";
		$head .= trim($query);
		$write = fputs($fp,$head);
		$header = "";
		while($str = trim(fgets($fp,4096))){
			$header .= $str;
		}
		while(!feof($fp)){
			$data .= fgets($fp,4096);
		}
		return $data;
	}
	
	/*
	* @brief 发送短信验证码,忘记密码时调用此接口
	* @param $tel String 电话号码
	* @return Boolean
	*/
	public function sendCodeToValidateWhenForgotPassword(){
		$tel = IReq::get("tel");
		//$tel = IFilter::act(IReq::get('tel'),'int');
		if(!$tel){
			return false;
		}
		//生成4位的数字验证码，发短信且发邮件来验证        
		$arrRand = array();
		for($count = self::$codeCount - 2; $count > 0; $count--){
			$arrRand[] = mt_rand(0,9); 
		}
		$strRand = implode('',$arrRand);
		$arrCode = array(
			"tel" => $tel,
			"class" => 'forget_password',
			"code" => $strRand,
			"create_time" => strtotime('now')
		);
		//将手机验证码存入session
		//$key = self::$forgetPasswordPrefix.$tel;
		//ISession::set($key,$arrCode);
		//$strMessage = "您的验证码是#".$strRand."#。如非本人操作，请忽略本短信";
		$objCode   = new IModel("verification_code");
		$objCode->setData($arrCode);
		$intId = $objCode->add();
		if(!$intId){
			$result = array(
				"success" => false,
				"message" => array(
					"code" => 1,
					"msg" => "生成验证码失败"
				)
			);
			$result = json_encode($result);
			echo $result;
			return $result;
		}
		$site_config=new Config("site_config");
		$site_config=$site_config->getInfo();
		$company = isset($site_config['message_company_conf']) ? $site_config['message_company_conf'] : '百花味';
		$strMessage = "#company#=".$company."&#code#=".$strRand;
		//发送验证码到手机
		$result = $this->sendMessageUsingTpl(self::$apiKey,self::$tplId,$strMessage,$tel);
		echo $result;
		return $result;
	}
	
	/*
	* @brief 验证短信验证码，忘记密码时调用此接口
	* @param $tel Integer 电话号码
	* @param $code Integer 验证码
	* @return array("result"=>"0","message"=>"验证码正确")；
	*/
	public function validateCodeWhenForgotPassword($tel,$code){
		if(!$code || !$tel){
			$str = json_encode(array("result" => "1","message" => "手机号码或验证码为空"));
			return $str;
		}
		//$arrCode = ISession::get(self::$forgetPasswordPrefix.$tel);
		$objCode = new IModel('verification_code');
		$strSql = "tel ='".$tel."' and class = 'forget_password' ORDER BY `id` DESC";
		$arrCode = $objCode->query($strSql);
		if(empty($arrCode) || !is_array($arrCode)){
			$str = json_encode(array("result" => "1","message" => "该手机尚未发送验证码"));
			return $str;
		}
		$arrCode = $arrCode[0];
		//验证手机号码、手机验证码、验证码有效期
		$interval = strtotime('now') - $arrCode['create_time'];
		if(($tel == $arrCode['tel']) && ($code == $arrCode['code']) && ($interval <= self::$expireTime)){
			//验证通过后删除session，防止多次验证
			//ISession::clear(self::$forgetPasswordPrefix.$tel);
			$str = json_encode(array("result" => "0","message" => "验证码正确"));
			return $str;
		}elseif(($tel == $arrCode['tel']) && ($code == $arrCode['code'])){
			//清除过期的session
			//ISession::clear(self::$forgetPasswordPrefix.$tel);
			$str = json_encode(array("result" => "1","message" => "验证码超时"));
			return $str;
		}else{
			$str = json_encode(array("result" => "1","message" => "验证码错误"));
			return $str;
		}
	}
}
