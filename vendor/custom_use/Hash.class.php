<?php
/*
*
* Base 62

Hashes are base 62 encoded base 10 integers. 1=1, 10=a, 36=Z, 61=z, 62=10, 72=1a, etc.
62, 3844, 238328, 14776336, 916132832, 56800235584, 3521614606208

1 character = 62 permutations, 2 characters = 3844 permutations, etc.
41, 2377, 147299, 9132313, 566201239, 35104476161, 2176477521929

41 = next highest prime from golden mean of 62.
2377 = next highest prime from golden mean of 3844.
Uniqueness

I chose to use primes to ensure hash uniqueness. Any prime greater than one half 62^n will do, but if you use a prime near 62^n or 62^n/2 or 2*62^n/3 etc, you will detect a linearity in the sequence at certain points in the ring.
Appearance of randomness

I chose primes near the golden ratio to maximize the appearance of randomness. Given a small set of hashes (even with the associated id) it would be difficult for anyone to guess the next hash.
This is a minimum security technique.

Keep your primes a secret and limit the number of hashes a user can get his hands on to make it harder for script kiddies to reverse engineer your algo. This is a thin rotation and base re-encoding obfuscation algorithm, not an encryption algorithm. Don't use this to crypt sensitive info. Use it to obfuscate integer IDs.
*
*
*
*
*/

class Hash
{

	###########################Start of the unique golden prime hash randomness simple function############################
	//using the integer only to generate the hash
	/* Next prime greater than 62 ^ n / 1.618033988749894848 */

	private static $golden_primes = array(
		1,41,2377,147299,9132313,566201239,35104476161,2176477521929
	);
	/* Ascii : 0  9, A  Z,  a  z */
	/* $chars = array_merge(range(48,57), range(65,90), range(97,122)) */
	private static $chars = array(
		0 => 48,1 => 49,2 => 50,3 => 51,4 => 52,5 => 53,6 => 54,7 => 55,8 => 56,9 => 57,10=> 65,
		11=> 66,12=> 67,13=> 68,14=> 69,15=> 70,16=> 71,17=> 72,18=> 73,19=> 74,20=> 75,
		21=> 76,22=> 77,23=> 78,24=> 79,25=> 80,26=> 81,27=> 82,28=> 83,29=> 84,30=> 85,
		31=> 86,32=> 87,33=> 88,34=> 89,35=> 90,36=> 97,37=> 98,38=> 99,39=> 100,40=> 101,
		41=> 102,42=> 103,43=> 104,44=> 105,45=> 106,46=> 107,47=> 108,48=> 109,49=> 110,
		50=> 111,51=> 112,52=> 113,53=> 114,54=> 115,55=> 116,56=> 117,57=> 118,58=> 119,
		59=> 120,60=> 121,61=> 122
	);
	private static
	function pad($text)
	{
		// Add a single 0x80 byte and let PHP pad with 0x00 bytes.
		return pack("a*H2", $text, "80");
	}
	private static
	function unpad($text)
	{
		// Return all but the trailing 0x80 from text that had the 0x00 bytes removed
		return substr(rtrim($text, "\0"), 0, - 1);
	}
	private static
	function base62($int)
	{
		$key = "";
		while($int > 0)
		{
			$mod = $int - (floor($int / 62) * 62);
			$key .= chr(self::$chars[$mod]);
			$int = floor($int / 62);
		}
		return strrev($key);
	}
	###########################end of the unique golden prime hash randomness simple function############################

	//1 way unique randomness hash - use golden primes
	//don't set $len > 7 , only can set between 1 - 7 else will return all 0 value,will need to expand this algo if gt time

	//1 way hashing algo - method 1
	public static
	function udiHash($num, $len = 7)
	{
		$ceil = pow(62, $len);
		$prime= self::$golden_primes[$len];
		$dec  = ($num * $prime) - floor($num * $prime / $ceil) * $ceil;
		$hash = self::base62($dec);
		return str_pad($hash, $len, "0", STR_PAD_LEFT);
	}
	//1 way hashing algo - method 2
	public static
	function udiLongUniqueHash()
	{
		//adding true inside uniqid will make 23 chacracter,now is 13
		$udiLongUniqueHash = uniqid(self::udiHash(mt_rand(1, getrandmax())));
		return $udiLongUniqueHash;
	}
	//1 way hashing algo - method 3
	public static
	function udiRandomHash()
	{
		$randomHash = self::udiHash(mt_rand(1, getrandmax()));
		return $randomHash;
	}
	//1 way hashing algo - method 4
	public static
	function md5HmacHash($id, $saltkey)
	{
		return hash_hmac('md5', $id, $saltkey);
	}
	//1 way hashing algo - method 5
	public static
	function uniqueSha512Hash()
	{
		$hash = uniqid(hash("sha512", rand()), TRUE);
		return $hash;
	}
	//1 way hashing algo - method 6
	public static
	function apiCodeHash($salt)
	{
		$salt = '88!@#uqnie123@Passw0rd)_+913';
		return sha1($salt . time() . substr($salt, - floor(strlen($salt) / 2)));
	}

	//
	//2 way hashing algo - method 1
	public static
	function encryptBase64($var)
	{
		$result = base64_encode(serialize($var));
		return $result;
	}
	public static
	function decryptBase64($var)
	{
		$result = unserialize(base64_decode($var));
		return $result;
	}

	//not allow arrray encrypy due to md5 issue,need improve soon!
	public static
	function encryptData($decrypted, $password, $salt = '!yZz*fF3pXe1Kbm%9')
	{
		// Build a 256 - bit $key which is a SHA256 hash of $salt and $password.
		$key = hash('SHA256', $salt . $password, true);
		// Build $iv and $iv_base64.  We use a block size of 128 bits (AES compliant) and CBC mode.  (Note: ECB mode is inadequate as IV is not used.)
		srand();
		//for windows use
		// $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND);
		$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_DEV_RANDOM);
		//             echo 'this is iv' . $iv . ' < br/>';
		if(strlen($iv_base64 = rtrim(base64_encode($iv), '=')) != 22)
		return false;
		// Encrypt $decrypted and an MD5 of $decrypted using $key.  MD5 is fine to use here because it's just to verify successful decryption.
		$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $decrypted . md5($decrypted), MCRYPT_MODE_CBC, $iv));
		// We're done!
		return $iv_base64 . $encrypted;
	}

	public static
	function decryptData($encrypted, $password, $salt = '!yZz*fF3pXe1Kbm%9')
	{
		// Build a 256 - bit $key which is a SHA256 hash of $salt and $password.
		$key = hash('SHA256', $salt . $password, true);
		// Retrieve $iv which is the first 22 characters plus == , base64_decoded.
		$iv        = base64_decode(substr($encrypted, 0, 22) . '==');
		// Remove $iv from $encrypted.
		$encrypted = substr($encrypted, 22);
		// Decrypt the data.  rtrim won't corrupt the data because the last 32 characters are the md5 hash; thus any \0 character has to be padding.
		$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, base64_decode($encrypted), MCRYPT_MODE_CBC, $iv), "\0\4");
		// Retrieve $hash which is the last 32 characters of $decrypted.
		$hash      = substr($decrypted, - 32);
		// Remove the last 32 characters from $decrypted.
		$decrypted = substr($decrypted, 0, - 32);
		// Integrity check.  If this fails, either the data is corrupted, or the password / salt was incorrect.
		if(md5($decrypted) != $hash)
		return false;
		// Yay!
		return $decrypted;
	}

	//start encryptCredential function
	public static
	function encryptDataMode2($data)
	{
		$key       = '9cqkTFHOfOmKn8kt&NSlIK*XMRWWx*tNY$azRdEvm2to*AQOll%8tP18g35H!zNg9l85pgnww$&q6y@1WrWZhKhx&23acq^*FWf*xdnmI%7aWwM6JQLm%tzYG^*8PIh1zD@D5QKa98Gg';
		$cipher    = mcrypt_module_open(MCRYPT_blowfish, '', 'cbc', '');
		mcrypt_generic_init($cipher, substr($key, 8, 56), substr($key, 32, 8));
		$encrypted = mcrypt_generic($cipher, self::pad($data));
		mcrypt_generic_deinit($cipher);
		return base64_encode($encrypted);
	}

	//end encryptCredential function
	//start decryptCredential function
	public static
	function decryptDataMode2($data)
	{
		$encryptedData = base64_decode($data);
		$key           = '9cqkTFHOfOmKn8kt&NSlIK*XMRWWx*tNY$azRdEvm2to*AQOll%8tP18g35H!zNg9l85pgnww$&q6y@1WrWZhKhx&23acq^*FWf*xdnmI%7aWwM6JQLm%tzYG^*8PIh1zD@D5QKa98Gg';
		$cipher        = mcrypt_module_open(MCRYPT_blowfish, '', 'cbc', '');
		mcrypt_generic_init($cipher, substr($key, 8, 56), substr($key, 32, 8));
		$decrypted     = self::unpad(mdecrypt_generic($cipher, $encryptedData));
		mcrypt_generic_deinit($cipher);
		return $decrypted;
	}
	//end decryptCredential function

	//allow array encryting
	public static
	function encrypt($string,$key)
	{

		$result = '';
		for($i = 0; $i < strlen($string); $i++)
		{
			$char    = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key)) - 1, 1);
			$char    = chr(ord($char) + ord($keychar));
			$result .= $char;
		}
		return base64_encode($result);
	}

	public static
	function decrypt($string,$key)
	{

		$result = '';
		$string = base64_decode($string);
		for($i = 0; $i < strlen($string); $i++)
		{
			$char    = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key)) - 1, 1);
			$char    = chr(ord($char) - ord($keychar));
			$result .= $char;
		}
		return $result;
	}

}

?>