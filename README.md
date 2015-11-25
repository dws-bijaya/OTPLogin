 _____ ___________ _                 _         __   _____  _____ 
|  _  |_   _| ___ \ |               (_)       /  | |  _  ||  _  |
| | | | | | | |_/ / |     ___   __ _ _ _ __   `| | | |/' || |/' |
| | | | | | |  __/| |    / _ \ / _` | | '_ \   | | |  /| ||  /| |
\ \_/ / | | | |   | |___| (_) | (_| | | | | | _| |_\ |_/ /\ |_/ /
 \___/  \_/ \_|   \_____/\___/ \__, |_|_| |_| \___(_)___(_)\___/ 
                                __/ |                            
                               |___/        


OTPLogin 1.0.0
@desc  This light weight class can be used to add one more step secure after login using OTP from ALL pre registered Machine using MAC Addresss
@author Bijaya Kumar Behera <it.bijaya@gmail.com> +91 9911033016

How to implement OTPLogin 
---------------------------------------------------
1. Create a database "test" if not already exists
2. Import OTPLogin.sql to "test" database
3. Get a MAC Address from your system
	For Window User :
		*. Click the Run button in the Windows Start Menu.
		*. Type cmd in the Open prompt of the Run menu and click OK to launch a command prompt window.
		*. maximised the window
		*. Type ipconfig /all at the command prompt to check the network card settings.  
		*. The MAC address is listed by ipconfig under Physical Address.
		below sample markked between '==>' and '<==' is the mac addresss
		-----------------------------------------------------------------
		|Physical Address. . . . . . . . . : ==>70-54-D2-19-78-C0<==
		----------------------------------------------------------------- 
	For Linux Use
		*. Open terminal
		*. Type ifconfig and hit enter
		*. The MAC address is listed by ipconfig under HWaddr.
		below sample markked between '==>' and '<==' is the mac addresss
		----------------------------------------------------
		|Link encap:Ethernet  HWaddr ==>00:26:18:25:6E:0E<==
		----------------------------------------------------
	For Mac OS Use
		*. Open terminal
		*. Type ifconfig and hit enter
		*. The MAC address is listed by ipconfig under ether.
		below sample markked between '==>' and '<==' is the mac addresss
		----------------------------------------------------
		|ether ==>e0:34:f5:ef:d4:a0<==
		----------------------------------------------------
4. Add MAC Address record to table otplogin_macs
   Example: Suppose MAC Address is 00-C0-26-C1-2D-6C
   remove '-' or ':' & changed it UPPERCASE 
   Final MAC Address: 00C026C12D6C	
   Execute SQL Query
   INSERT INTO otplogin_macs (macadd, name, parent, active ) VALUES ( '00C026C12D6C', 'My Machine', 0, '1' ) ;
   Continue the above steps for ALl MAC Address
3. create mysql connection 
	$DB=new mysqli("localhost", "root", "", "test");
Or
	$DB=mysql_connect("localhost", "root", "");mysql_select_db("test", $DB);
Or 
	$DB= new PDO("mysql:host=localhost;dbname=test", "root", "");
4. create OTPLogin object
	Params are
		1. $DB  mySQL Connection   		   [resourece/object]
		2. $prefix  Table prefix  text     [String]
		3. $cookiename  OTP Cookie Name    [String]
		4. $otpcookietimeout OTP Cookie Timeout in sec  [Integer]
		5. $otpsessiotimeout OTP Session Timeout 	   [Integer]
	Return 
		$oOTPLogin   [Object]
	$oOTPLogin=new OTPLogin($DB, $prefix, $cookiename, $otpcookietimeout, $otpsessiotimeout );
5.  check valid cookie session
	Params are:
		1. $userid Logged User Id [Integer]
	Return
		true/false  [boolean]
	$oOTPLogin->isValid($userid)  [true/false]
6.  Initialise OTP if not started  
	Params are :
		1. $userid Logged user id  [Integer]
	Return :
		true/false  [bollean]
	$initStatus = $oOTPLogin->initOTP($userid);
7. Download OTPGenerator of your OS
	Params are :
		1. $otpurl OTP URL   [String]
		2. $userid User ID   [Integer]
		3. $info   Info      [Array]
	Reruen :
		Void				 [null]
	$oOTPLogin->downloadOTPGenFile($otpurl, $userid, $info); 
8. verify OTP
	Params are :
		1. $userid Logged user ID [Integer]
		2. $otp    User OTP       [Integer]
	Return :
		true/false 				  [bollean]		
	$oOTPLogin->verifyOTP($userid, $otp)