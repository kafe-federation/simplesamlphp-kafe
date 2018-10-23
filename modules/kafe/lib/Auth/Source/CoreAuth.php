<?php
class sspmod_kafe_Auth_Source_CoreAuth extends sspmod_core_Auth_UserPassBase {

	/* The database DSN.
	* See the documentation for the various database drivers for information about the syntax:
	*     http://www.php.net/manual/en/pdo.drivers.php
	*/
	private $dsn;

	/* The database username & password. */
	private $username;
	private $password;

	private $idp_host = '/simplesaml/';
	private $user_table = 'your_user_table';
	private $blk_table  = 'your_blacklist_log';
	private $logged_table = 'your_logged_table';

	private $logon_try = '5'; // 5회만 로그인 허용
	private $block_min = '60'; // 60분 동안 차단

	 // 대칭키 salt
	 private $aes_key_string = 'yourAESKeyHeRE';

    public function __construct($info, $config) {
        parent::__construct($info, $config);

        if (!is_string($config['dsn'])) {
            throw new Exception('Missing or invalid dsn option in config.');
        }
        $this->dsn = $config['dsn'];
        if (!is_string($config['username'])) {
            throw new Exception('Missing or invalid username option in config.');
        }
        $this->username = $config['username'];
        if (!is_string($config['password'])) {
            throw new Exception('Missing or invalid password option in config.');
        }
        $this->password = $config['password'];
    }


	// 비밀번호 암호화
	private function _generateHash($plainText, $salt = null)
	{
		$salt = "Your SoLt KeY Here";  

		if ($salt === null)
		{
			$salt = substr(md5(uniqid(rand(), true)), 0, 25);
		}
		else
		{
			$salt = substr($salt, 0, 25);
		}

		$salt = sha1($salt);
		$digest = $salt . hash('sha256', $salt . $plainText);
		return $salt . hash('sha256', $salt . $plainText);
	}

	// 문자열 암호화
	private function _aes_encrypt($value, $secret = '')
	{
		if (empty($secret)) {
			$secret = $this->aes_key_string;
		}

		 return rtrim(
			  base64_encode(
					mcrypt_encrypt(
						 MCRYPT_RIJNDAEL_256,
						 $secret, $value,
						 MCRYPT_MODE_ECB,
						 mcrypt_create_iv(
							  mcrypt_get_iv_size(
									MCRYPT_RIJNDAEL_256,
									MCRYPT_MODE_ECB
							  ),
							  MCRYPT_RAND)
						 )
					), "\0"
			  );
	}

	// 문자열 복호화
	private function _aes_decrypt($value, $secret = '')
	{
		if (empty($secret)) {
			$secret = $this->aes_key_string;
		}

		 return rtrim(
			  mcrypt_decrypt(
					MCRYPT_RIJNDAEL_256,
					$secret,
					base64_decode($value),
					MCRYPT_MODE_ECB,
					mcrypt_create_iv(
						 mcrypt_get_iv_size(
							  MCRYPT_RIJNDAEL_256,
							  MCRYPT_MODE_ECB
						 ),
						 MCRYPT_RAND
					)
			  ), "\0"
		 );
	}

	protected function login($username, $password) 
	{
		if ($this->isInBlackList($username)) {
			unset($_SESSION['try_times']);
			header('Location: '.$this->idp_host.'block.php');
			exit;
		}

		/* Connect to the database. */
		$db = new PDO($this->dsn, $this->username, $this->password);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		/* Ensure that we are operating with UTF-8 encoding.
		* This command is for MySQL. Other databases may need different commands.
		*/
		$db->exec("SET NAMES 'utf8'");

		/* With PDO we use prepared statements. This saves us from having to escape
		* the username in the database query.
		*/
		$st = $db->prepare('SELECT * FROM '.$this->user_table.' WHERE user_name=:username');

		if (! $st->execute(array('username' => $this->_aes_encrypt($username)))) {
			throw new Exception('Failed to query database for user.');
		}

		/* Retrieve the row from the database. */
		$row = $st->fetch(PDO::FETCH_ASSOC);
		if (! $row) {
			/* User not found. */
			SimpleSAML_Logger::warning('CoreAuth: Could not find user ' . var_export($username, TRUE) . '.');
			$this->error_syslog();
			$this->writeBlackList($username, $_SERVER['REMOTE_ADDR'], 0);
			throw new SimpleSAML_Error_Error('WRONGUSERPASS');
		}

		/* Check the password. */
		if ($row['password'] !== $this->_generateHash($password)) {
			/* Invalid password. */
			SimpleSAML_Logger::warning('CoreAuth: Wrong password for user ' . var_export($username, TRUE) . '.');
			$this->error_syslog();
			$this->writeBlackList($username, $_SERVER['REMOTE_ADDR'], 1);
			throw new SimpleSAML_Error_Error('WRONGUSERPASS');
		}

		/* Check the activation. */
		if (empty($row['active'])) {
			/* No activation is carried */
			SimpleSAML_Logger::warning('CoreAuth: Account activation is required for user ' . var_export($username, TRUE) . '.');
			throw new SimpleSAML_Error_Error('NOACTIVATION');
		}

		$display_name = $row['display_name'];
		$display_name = $this->_aes_decrypt($display_name);
		$mail = $row['email'];
		$mail = $this->_aes_decrypt($mail);
		$affiliation = $row['title'];
		$schahomeorg = $row['schahomeorg'];
		$organization = $row['affiliation'];

		$last_name = $row['last_name'];
		if(!empty($row['last_name'])){
//	            $last_name = $row['last_name'];
		    $last_name = $this->_aes_decrypt($last_name);
		}else {
		    $last_name = "";
		}
	        $first_name = $row['first_name'];

		if(!empty($row['first_name'])) {
//		$first_name = $row['first_name'];
		    $first_name = $this->_aes_decrypt($first_name);
		}else{
		    $first_name = "";
		}
		$eduppn = $username.'@coreen.or.kr';

		if ($affiliation == "staff") {
			$refined_affi = array('staff', 'member');
			$refined_scopedaffi = array('staff@coreen.or.kr');
		}else{
			$refined_affi = array('affiliate');
			$refined_scopedaffi = array('affiliate@coreen.or.kr');
		}

		/* Create the attribute array of the user. */
		$attributes = array(
			'uid' => array($username),
			'displayName' => array($display_name),
			'mail' => array($mail),
			'eduPersonAffiliation' => $refined_affi,
			'eduPersonScopedAffiliation' => $refined_scopedaffi, 
			'organizationName' => array($organization),
			'eduPersonPrincipalName' => array($eduppn), 
			'schacHomeOrganization' => array($schahomeorg),
			'givenName' => array($first_name),
			'sn' => array($last_name),
			'eduPersonEntitlement' => array('urn:mace:dir:entitlement:common-lib-terms'),
			'schacHomeOrganizationType' => array('urn:schac:homeOrganizationType:int:vho'),
		);

		$this->clearBlackList($username);
		$this->writeUserLoggedin($username, $_SERVER['REMOTE_ADDR'], 0);

		/* Return the attributes. */
		return $attributes;
	}

	################################
	#
	# BlackList 대응 함수
	#
	#################################

	// record successfully logged user	    
	private function writeUserLoggedin($cn, $ui, $opt) 
	{
		$db = new PDO($this->dsn, $this->username, $this->password);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$db->exec("SET NAMES 'utf8'");

		//logged_table
		$st = $db->prepare("INSERT INTO $this->logged_table (user_id, user_ip, access_stamp) VALUES ('$cn', '$ui', now())");
		if (! $st->execute()) {
			throw new Exception('Failed to query database for login_users.');
		}

		//update last sign in 
		$st = $db->prepare('update '.$this->user_table.' set last_sign_in_stamp=:now where user_name=:username');
		$res = $st->execute(array(
			'now' => time(),
			'username' => $this->_aes_encrypt($cn),
		));
		if (!$res) {
			throw new Exception('Failed to query database for login_users.');
		}

		unset($db);
		unset($st);
	}

	// record syslog
	private function error_syslog() 
	{
		//format: Oct 21 11:01:01 clog CROND[16822]: (root) CMD (run-parts /etc/cron.hourly)

		list($h_name) = explode(".", gethostname());
		$access = date("M d H:i:s") . " " . $h_name . " SAFE";
		openlog($access, LOG_PERROR, LOG_LOCAL7);

		syslog(LOG_INFO, "Web brute-force attempt from {$_SERVER['REMOTE_ADDR']} ({$_SERVER['HTTP_USER_AGENT']})");

		closelog();
	}

	// delete black user
	private function clearBlackList($cn) 
	{
		$db = new PDO($this->dsn, $this->username, $this->password);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->exec("SET NAMES 'utf8'");
		$st = $db->prepare("DELETE FROM $this->blk_table WHERE user_id = '$cn'");
		if (! $st->execute()) {
			throw new Exception('Failed to query database for user.');
		}
		unset($db);
		unset($st);

		unset($_SESSION['try_times']);
	}

	// write black user
	private function writeBlackList($cn, $ui, $opt) 
	{
		$db = new PDO($this->dsn, $this->username, $this->password);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$db->exec("SET NAMES 'utf8'");

		if ($opt === 0) {
			$rsn = "Unauthorized user";
		} else {
			$rsn = "Password failed";
		}

		$st = $db->prepare("INSERT INTO $this->blk_table (user_id, user_ip, access_stamp, user_task)	VALUES ('$cn', '$ui', now(), '$rsn')");

		if (! $st->execute()) {
			throw new Exception('Failed to query database for user.');
		}

		unset($db);
		unset($st);
	}

	//check if your user_id is stuck in the blacklist
	private function isInBlackList($ui) 
	{
		$db = new PDO($this->dsn, $this->username, $this->password);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$db->exec("SET NAMES 'utf8'");

		// x분 동안 접근 차단수
		$st = $db->prepare("SELECT count(*) as trials FROM $this->blk_table  WHERE user_id = '$ui' AND access_stamp > (NOW() - INTERVAL ".$this->block_min." MINUTE)");
		if (! $st->execute()) {
			throw new Exception('Failed to query database for user.');
		}

		$row = $st->fetch(PDO::FETCH_ASSOC);

		unset($db);
		unset($st);

		// failed x times
		if ($row['trials'] >= $this->logon_try) return true;
		else $_SESSION['try_times'] = ($row['trials']+1);
		
		return false;
	}
	############################

}
