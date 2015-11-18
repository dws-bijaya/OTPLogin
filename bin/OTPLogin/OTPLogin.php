<?php
/**
 * @class OTPLogin 
 * @file  OTPLogin.php
 * @desc  This light weight class can be used to add one more step secure after login using OTP from ALL pre registered Machine using MAC Addresss
 * @author Bijaya Kumar Behera <it.bijaya@gmail.com> +91 9911033016
 */
class OTPLogin {
	var $DB  = null;
	var $COOKIE_NAME = 'OTPLogin_Sess';
	var $DB_PREFIX = 'otplogin';
	var $ACTIVATION_EXPIRE_DAYS = 2592000 ; // 30 Days
	var $macid = 0 ;
	const VERSION = "1.0.0";
	function OTPLogin($db, $DB_PREFIX, $COOKIE_NAME, $ACTIVATION_EXPIRE_DAYS, $OPT_EXPIRE) {
		$this->DB =  $db ;
		$this->DB_PREFIX = $DB_PREFIX;
		$this->COOKIE_NAME = $COOKIE_NAME;
		$this->ACTIVATION_EXPIRE_DAYS = $ACTIVATION_EXPIRE_DAYS;
		$this->OPT_EXPIRE=$OPT_EXPIRE;
	}
	/*
		download executable file to get OTP for window or linux
	*/
	public function downloadOTPGenFile($otpurl, $userid, $info = array(), $filename = null , $os = null) {
		$filename = empty($filename) ? 'OTPLoginGenerator' : $filename;
		$os       = ( empty($os) ? stripos($_SERVER['HTTP_USER_AGENT'], 'window') !== FALSE ? 'window' : (stripos($_SERVER['HTTP_USER_AGENT'], 'Linux') !== FALSE ? 'linux': 'window' ) : (!in_array($os, array('window', 'linux') ) ? 'window': $os )) ;
		$filedata = $os == 'window' ? $this->_filedataWin($otpurl, $userid, $filename, $info) : $this->_filedataLinux($otpurl, $userid, $filename, $info);
		header('Content-Type: ' . $filedata['Content-Type'] );
		header('Content-Disposition: attachment; filename="' . $filedata['filename']  . '"');
		header('Expires: 0');
		if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE"))
		{
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		
		} else
			header('Pragma: no-cache');
		header("Content-Length: ". strlen($filedata['content']));
		echo $filedata['content'];
		ob_flush();
		flush();
		return true;
	}
	/*
	   download executable file for Linux
	*/
	private function _filedataLinux($otpurl, $userid, $filename, $info) {
		$otpurl =  $otpurl  . (stripos($otpurl, "?") === FALSE ? '?': ''); 
		$filename ="{$filename}-UID-{$userid}.sh" ;
		$infoout = array();
		$infoout1 = array();
		$infoout[] = "echo 'OTPLoginGenerator V" . self::VERSION . "'";
		$infoout1[]= "#OTPLoginGenerator V" . self::VERSION;
		$infoout1[]= "#File:" . "$filename";
		$infoout1[]= "#Platform:" . "Linux";
		$infoout[] = "echo 'Generated On: " . gmdate('D, d M Y H:i:s T' . "'", time()) ; 
		$infoout1[]= "#Generated On: " . gmdate('D, d M Y H:i:s T', time()); 
		$info      = (array) $info;
		foreach ($info as $key=>$inf) {
			$infoout[] = ("echo '" . ucwords($key) . ": " . str_replace(array(chr(13),chr(10), "'"), array('','',"''") , $inf) . "'" );
			$infoout1[]= ("#" . ucwords($key) . ": " . str_replace(array(chr(13),chr(10)), array('',''), $inf));
		}
		$infoout[] = ("echo 'UserID:" . $userid . "'");
		$infoout1[]= ("#UserID:" . $userid);
		$infoout   = join(chr(13).chr(10), $infoout);
		$infoout1   = join(chr(13).chr(10), $infoout1);
		$bin=<<<LINUXBIN
$infoout1
echo '####################################################'
$infoout
echo '####################################################'
echo ''
#Global config
###########################################################
USERID=$userid 
REQURi="$otpurl"
###########################################################
ifconfigbin=`which ifconfig`
awkbin=`which awk 2>null`
curlbin=`which curl 2>null`
wgetbin=`which wget 2>null`
MACADD=$(\$ifconfigbin -a |\$awkbin '/^[a-z]/ { iface=$1; mac=\$NF; next }/inet addr:/ { print mac; exit }')
REQURi="\${REQURi}userid=\$USERID&macadd=\$MACADD"
OUTPUT=""
if [ -e "\$curlbind" ]; then
  OUTPUT=$(\$curlbin -s \$REQURi)
fi
if [ -e "\$wgetbin" ]; then
  OUTPUT=$(\$wgetbin  -q -O - "$@" \$REQURi)
fi
echo "Requesting to server . .. ... " 
echo "Request completed."
echo "Redirecting response to console . .. ..." 
echo "+++++++++++++++++++++++[OUTPUT]+++++++++++++++++++++++++++++" 
echo "$OUTPUT													 "
echo "+++++++++++++++++++++++[OUTPUT]+++++++++++++++++++++++++++++" 
echo "Press any key to continue . . ."
read
LINUXBIN;
		$ret = array( 'content' => $bin, 'filename' => $filename, 'Content-Type' => 'application/x-sh' );
		return $ret ;		
	}
	/*
	   download executable file for Window
	*/
	private function _filedataWin ($otpurl, $userid, $filename, $info) {
		$otpurl =  $otpurl  . (stripos($otpurl, "?") === FALSE ? '?': ''); 
		$filename ="{$filename}-UID-{$userid}.cmd" ;
		$infoout = array();
		$infoout1 = array();
		$infoout[] = "echo OTPLoginGenerator V" . self::VERSION;
		$infoout1[]= "REM OTPLoginGenerator V" . self::VERSION;
		$infoout1[]= "REM File:" . "$filename";
		$infoout1[]= "REM Platform:" . "Window NT";
		$infoout[] = "echo Generated On: " . gmdate('D, d M Y H:i:s T', time()); 
		$infoout1[]= "REM Generated On: " . gmdate('D, d M Y H:i:s T', time()); 
		$info      = (array) $info;
		foreach ($info as $key=>$inf) {
			$infoout[] = ("echo " . ucwords($key) . ": " . str_replace(array(chr(13),chr(10), '(', ')'), array('','','^(', '^)'), $inf));
			$infoout1[]= ("REM " . ucwords($key) . ": " . str_replace(array(chr(13),chr(10), '(', ')'), array('','','', ''), $inf));
		}
		$infoout[] = ("echo UserID:" . $userid);
		$infoout1[]= ("REM UserID:" . $userid);
		$infoout   = join(chr(13).chr(10). chr(9), $infoout);
		$infoout1   = join(chr(13).chr(10), $infoout1);
		$bin=<<<WINDOWBIN
$infoout1


set TMPVBS=%TMP%\mytempfile-%RANDOM%-%TIME:~6,5%.vbs
echo off
(
	echo[
	echo ####################################################
    $infoout
    echo ####################################################
    echo[   
) 
echo off
(
echo 'Global config
echo '###########################################################
echo Dim MACADD, REQURi , USERID
echo USERID = {$userid} 
echo REQURi = "{$otpurl}page=getotp&"
echo '###########################################################
echo[
echo WScript.Echo "Please wait .... Requesting  to server ... " 
echo dim WMI:  set WMI = GetObject(^"winmgmts:\\\\.\\root\cimv2")
echo dim Nads: set Nads = WMI.ExecQuery(^"Select * from Win32_NetworkAdapter where physicaladapter=true") 
echo dim nad
echo for each Nad in Nads
echo	if not isnull^(Nad^.MACAddress^) then MACADD=Nad.MACAddress   
echo next 
echo 'Create an HTTP object
echo Set objHTTP = CreateObject(^"WinHttp.WinHttpRequest.5.1" )
echo REQURi = REQURi  ^& ^"macadd^=" & MACADD & "^&userid=" & USERID
echo objHTTP.Open "GET", REQURi, False
echo WScript.Echo ^"Requesting to server . .. ... "
echo objHTTP.Send
echo intStatus = objHTTP.Status
echo WScript.Echo ^"Request completed with status "  & intStatus  
echo WScript.Echo "Redirecting response to console . .. ..." 
echo WScript.Echo "" 
echo WScript.Echo ^"+++++++++++++++++++++++[OUTPUT]+++++++++++++++++++++++++++++" 
echo If intStatus = 200 Then
echo	WScript.Echo objHTTP.responseText
echo Else
echo	WScript.Echo "OOPS" +REQURi
echo End If
echo WScript.Echo ^"+++++++++++++++++++++++[OUTPUT]+++++++++++++++++++++++++++++"
)>%TMPVBS%
cscript %TMPVBS%
pause
del %TMPVBS%
WINDOWBIN;
		$ret = array( 'content' => $bin, 'filename' => $filename, 'Content-Type' => 'application/cmd' );
		return $ret ;
	}
	/*
	   verify OTP from user 
	*/
	public function verifyOTP($userid, $otp) {
		$otp  = (int) $otp;
		$time =  time();
		//
		$ip= addslashes($this->getIpAdd());
		$qry="SELECT macid FROM " . $this->DB_PREFIX . "_otp_session WHERE userid = $userid AND $time <= expire AND ip = '$ip' AND active ='2' AND otp = $otp LIMIT 1";
		$ret = $this->dbExec($qry);
		if ( empty($ret) ) {
			return false;
		}
		$macid = (int) $ret[0]['macid'];
		$this->macid = $macid;
		$activation_code = md5($userid . $otp . $time . $ip . '2' );
		$activation_cookie_code = array_chunk(str_split($activation_code), 8) ;
		for($c=0;$c<=3;$c++) {
			$ind=array();
			foreach ( $activation_cookie_code[$c] as $key => $value) {
				if ( ord($value) >=48 && ord($value) <=87 )
					continue;
				$ind[] = $key;
			}
			if ( empty($ind)) {
				$activation_cookie_code[$c] = join("", $activation_cookie_code[$c]);
				continue;
			}
			shuffle($ind);
			$r = rand (1, count($ind));
			$ind2 = (array) array_rand($ind, $r) ;
			foreach ($ind2 as $key => $value) {
				$activation_cookie_code[$c][$ind[$value]] = strtoupper($activation_cookie_code[$c][$ind[$value]]);
			}
			$activation_cookie_code[$c] =  join("", $activation_cookie_code[$c]);
		}
		$activation_cookie_code =  join("-", $activation_cookie_code);
		$created_on = time();
		$expire     = $created_on + $this->ACTIVATION_EXPIRE_DAYS;
		$ins="INSERT INTO " . $this->DB_PREFIX . "_activations (userid, activation_code, created_on, macid, expire) VALUES ($userid, '{$activation_code}', $created_on, $macid, $expire) ";
		$this->dbExec($ins);

		$upd="UPDATE " . $this->DB_PREFIX . "_otp_session SET active = '3' WHERE userid = $userid AND ip = '$ip' AND active ='2' AND otp = $otp";
		$this->dbExec($upd);

		// Clean 
		$qry="SELECT COUNT(*) as t FROM " . $this->DB_PREFIX . "_activations WHERE userid = $userid"; 
		$ret=$this->dbExec($qry);
		if ( !empty($ret)) {
			$t = (int) $ret[0]['t'];
			if ( $t > 10 ) {
				$limit = $t - 10;
				$del="DELETE FROM " . $this->DB_PREFIX . "_activations WHERE userid = $userid ORDER BY created_on ASC LIMIT $limit"; 
				$this->dbExec($del);
			} 
		}
		@setcookie($this->COOKIE_NAME, $activation_cookie_code , time() + $expire);
		return true;
	}

	/*
		get new OTP 	
	*/
	public function getOTP ($userid, $macadd) {
		$userid = (int) $userid;
		$macadd = strtoupper(preg_replace('/[^a-z0-9]/i', '', $macadd));
		// Check Mac Address exists ?
		$qry="SELECT macid FROM " . $this->DB_PREFIX . "_macs WHERE macadd = '{$macadd}' AND active = '1' LIMIT 1";
		$ret=$this->dbExec($qry);
		// valid mac address? 
		if ( empty($ret) ) {
			return false;
		}
		//
		$time =  time();
		$macid = (int) $ret[0]['macid'];
		//
		$ip= addslashes($this->getIpAdd());
		$qry="SELECT otp FROM " . $this->DB_PREFIX . "_otp_session WHERE userid = $userid AND $time <= expire AND ip = '$ip' AND active ='1' LIMIT 1";
		$ret = $this->dbExec($qry);
		if ( empty($ret) ) {
			return false;
		}
		$qry="UPDATE " . $this->DB_PREFIX . "_otp_session SET active = '2', macid = $macid WHERE userid = $userid AND $time <= expire AND ip = '$ip' AND active ='1' LIMIT 1";
		$this->dbExec($qry);
		return (int) $ret[0]['otp'];
	}
	/*
		get Mac id
	*/
	public function getMacId () {
		return $this->macid;
	}
	/*
		Initiliase OTP for logged user
	*/
	public function initOTP ($userid) {
		if ( !$userid )
			return $this->OPT_EXPIRE;

		//
		$ip= addslashes($this->getIpAdd());

		//
		$time = time();
		$qry="SELECT expire FROM " . $this->DB_PREFIX . "_otp_session WHERE $time < expire AND ip = '$ip'  AND 	( active = '1' OR active = '2') LIMIT 1";
		$res = $this->dbExec($qry);
		$Elapsed = -1 ;
		if ( !empty($res)) {
			$Elapsed = $res[0]['expire'] - time();
		}
		if ( $Elapsed >0 )
			return $Elapsed;
		$del="DELETE FROM  " . $this->DB_PREFIX . "_otp_session WHERE userid = $userid";
 		$this->dbExec($del);

		$x = 5; // Amount of digits
		$min = pow(10,$x);
		$max = pow(10,$x+1)-1;
		$opt = rand($min, $max);
		$expire = time() + $this->OPT_EXPIRE;
		$ins="INSERT INTO " . $this->DB_PREFIX . "_otp_session (userid, macid, expire, ip, otp) VALUES ($userid, 0, $expire, '$ip', $opt)"; 
		$this->dbExec($ins);
		return $this->OPT_EXPIRE;
	}

	/*
		Get IP Address from user is being request
	*/
	function getIpAdd() {
    	// check for shared internet/ISP IP
    	if (!empty($_SERVER['HTTP_CLIENT_IP']) && validate_ip($_SERVER['HTTP_CLIENT_IP'])) {
        	return $_SERVER['HTTP_CLIENT_IP'];
    	}

    	// check for IPs passing through proxies
    	if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        	// check if multiple ips exist in var
        	if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
            	$iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            	foreach ($iplist as $ip) {
                	if (validate_ip($ip))
                    	return $ip;
            	}
        	} else {
            	if (validate_ip($_SERVER['HTTP_X_FORWARDED_FOR']))
                	return $_SERVER['HTTP_X_FORWARDED_FOR'];
        	}
    	}
    	if (!empty($_SERVER['HTTP_X_FORWARDED']) && validate_ip($_SERVER['HTTP_X_FORWARDED']))
        	return $_SERVER['HTTP_X_FORWARDED'];
    	if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && validate_ip($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
        	return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
    	if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && validate_ip($_SERVER['HTTP_FORWARDED_FOR']))
        	return $_SERVER['HTTP_FORWARDED_FOR'];
    	if (!empty($_SERVER['HTTP_FORWARDED']) && validate_ip($_SERVER['HTTP_FORWARDED']))
        	return $_SERVER['HTTP_FORWARDED'];

	    // return unreliable ip since all else failed
    	return $_SERVER['REMOTE_ADDR'];
	}

	/*
		Check validity of Activation Code from COOKIE 
	*/
	public function isValid () {
		$activation_code = isset($_COOKIE[$this->COOKIE_NAME]) ? $_COOKIE[$this->COOKIE_NAME] : "";
		$activation_code = addslashes(preg_replace('/[^a-z0-9]/', '', strtolower($activation_code)));
		$chk="SELECT macid FROM " . $this->DB_PREFIX . "_activations WHERE activation_code = '{$activation_code}' ";
		$res=$this->dbExec($chk);
		if ( empty($res))
			return false;
		$this->macid = $res[0]['macid'];
		$expire = time () + $this->ACTIVATION_EXPIRE_DAYS;
		$update="UPDATE " . $this->DB_PREFIX . "_activations SET expire = $expire WHERE activation_code = '{$activation_code}'";
		$this->dbExec($update);
		return true;	
	}
	/*
		Do execute MySQL Query
	*/
	private function dbExec($qry) {
		static $cleaned ;
		$restype = $this->canDoDBQry();
		if ( !$restype )
			return false;
		$res = array();
		$time =  time();
		switch ($restype) {
			case 1:
					//Old data clean up
					if ( !$cleaned ) {
						mysql_query("DELETE FROM " . $this->DB_PREFIX . "_otp_session WHERE  $time > expire ", $this->DB);
						mysql_query("DELETE FROM " . $this->DB_PREFIX . "_activations WHERE  $time > expire ", $this->DB);
						$cleaned = true ;
					}

					$r=@mysql_query($qry, $this->DB);
					if (!$r)
						break;
					/* fetch object array */
					while(($a=mysql_fetch_assoc($r))) {
						$res[]=$a;
					}
					/* free result set */
					mysql_free_result($r);
					break;
			case "mysqli":
					//Old data clean up
					if ( !$cleaned ) {
						$this->DB->query("DELETE FROM " . $this->DB_PREFIX . "_otp_session WHERE  $time > expire ");
						$this->DB->query("DELETE FROM " . $this->DB_PREFIX . "_activations WHERE  $time > expire ");
						$cleaned = true ;
					}

					if ($r = $this->DB->query($qry)) {
					    /* fetch object array */
    					while ($a = $r->fetch_assoc()) {
        					$res[]=$a;
    					}
				    	/* free result set */
    					$r->close();
					}
					break;
			case "pdo":
					//Old data clean up
					if ( !$cleaned ) {
						$this->DB->query("DELETE FROM " . $this->DB_PREFIX . "_otp_session WHERE  $time > expire ");
						$this->DB->query("DELETE FROM " . $this->DB_PREFIX . "_activations WHERE  $time > expire ");
						$cleaned = true ;
					}
					if ($r = $this->DB->query($qry)) {
					    /* fetch object array */
    					while ($a = $r->fetch(PDO::FETCH_ASSOC)) {
        					$res[]=$a;
    					}
				    	/* free result set */
    					$r->closeCursor();
					}
					break;
			default:
			        break;
		}
		return $res;
	}
	/*
		Can i do query to DAtabase server
	*/
	private function canDoDBQry() {
		static $restype; 
		if ( is_null($this->DB) || !( is_resource($this->DB) || is_object($this->DB)) )
		{ 
			return false;
		}
		if( !is_null($restype) )
			return $restype;

		$restype = is_resource($this->DB) && stripos(get_resource_type($this->DB), 'mysql')!==FALSES ? 1 : false;
		$restype = ( $restype == false) ? strtolower(get_class($this->DB)) : $restype;
		$restype = ( $restype == false || !in_array($restype, array(1,'mysqli','pdo')) ) ? false : $restype;	
		return $restype;
	}	
}