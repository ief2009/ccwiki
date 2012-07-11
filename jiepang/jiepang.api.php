<?php
//  app id / app secret / redirect uri / language
define('APP_ID', 100479);
define('APP_SECRET', '8a4e25850e0375397d7fbf9d7c70f1e3');
define('REDIRECT_URI', 'http://' . $_SERVER['HTTP_HOST']. rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/JiepangLocationSearch.php');
define('LANG', 'CHS');

define('JIEPANG_OAUTH_AUTHORIZE_URL', 'https://jiepang.com/oauth/authorize');
define('JIEPANG_OAUTH_TOKEN_URL', 'http://jiepang.com/oauth/token');
define('JIEPANG_API_URL', 'http://api.jiepang.com/v1/');
    
class JiepangOAuthError extends Exception { }
class JiepangApiError extends Exception  { }

class JiepangApi {
	private $_code;
	private $_client_id;
	private $_client_secret;
	private $_access_token;
	private $ch;
	private $fixed_params;
	
	public function __construct($config = array())
	{
		if (isset($config['access_token']))
		{
			$this->_access_token = $config['access_token'];
		}
		else
		{
			if (isset($_SESSION['access_token']))
			{
				$this->_access_token = $_SESSION['access_token'];
			}
		}
		
		if (isset($config['code']))
		{
			$this->_code = $config['code'];
		}
		
		if (isset($config['client_id']))
		{
			$this->_client_id = $config['client_id'];
		}
		else
		{
			$this->_client_id = APP_ID;
		}
		
		if (isset($config['client_secret']))
		{
			$this->_client_secret = $config['client_secret'];
		}
		else
		{
			$this->_client_secret = APP_SECRET;
		}
		
		$this->ch = curl_init();
		curl_setopt($this->ch, CURLOPT_POST, TRUE);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		
		$this->fixed_params = array
		(
			'lang' => LANG,
		);
	}
	
	public function get_authorize_url($redirect_uri = REDIRECT_URI)
	{
		$url = JIEPANG_OAUTH_AUTHORIZE_URL . '?' . http_build_query(array
		(
			'response_type' => 'code',
			'client_id' => $this->_client_id,
			'redirect_uri' => $redirect_uri,
		));
		
		return $url;
	}
	
	public function request_access_token($redirect_uri = REDIRECT_URI)
	{
		if (isset($_GET['error']))
		{
			throw new JiepangOAuthError($_GET['error']);
		}
		$params = array
		(
			'client_id' => $this->_client_id,
			'client_secret' => $this->_client_secret,
			'redirect_uri' => $redirect_uri,
			'grant_type' => 'authorization_code',
			'code' => $_GET['code'],
		);
		$ret = $this->oauth_call(JIEPANG_OAUTH_TOKEN_URL, $params);
		
		return $ret;
	}
	
	public function set_access_token($access_token)
	{
		$this->_access_token = $access_token;
	}
	
	private function request($url, $postFields = null)
	{
		if ($postFields != null)
		{
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postFields);
		}
		
		curl_setopt($this->ch, CURLOPT_URL, $url);
		do
		{
			$response = curl_exec($this->ch);
		}
		while (strlen($response) <= 0);
		
		$obj = json_decode($response, true);
		if ($obj === NULL)
		{
			throw new Exception('Invalid response.');
		}
		return $obj;
	}
	
	private function oauth_call($url, $params = null)
	{
		$res = $this->request($url, $params);
		if (isset($res->error))
		{
			throw new JiepangOAuthError($res->error);
		}
		return $res;
	}
	
	public function api($api, $params = null)
	{
		$url = JIEPANG_API_URL . $api;
		if (empty($this->_access_token))
		{
        if (isset($_SESSION['access_token']))
			  {
		 		   $this->_access_token = $_SESSION['access_token'];
			  }
			  else
			  {
				  $params['source'] = $this->_client_id;
			 }
		}
		if ($params == null)
		{
			$params = array();
		}
		$params['access_token'] = $this->_access_token;
		
		$res = $this->request($url, array_merge($this->fixed_params, $params));
		if (isset($res->error))
		{
			throw new JiepangApiError($res->error->message, $res->error->code);
		}
		return $res;
	}
}

?>