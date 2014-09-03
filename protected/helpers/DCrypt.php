<?php

class DCrypt
{
	public static function encrypt($encrypt, $key){
		$secureKey = hash('sha256', $key, true);
		return  base64_encode(mcrypt_encrypt(MCRYPT_BLOWFISH, $secureKey, serialize($encrypt), MCRYPT_MODE_ECB));
	}

	public static function decrypt($decrypt, $key){
		$secureKey = hash('sha256', $key, true);
		$decrypted = mcrypt_decrypt(MCRYPT_BLOWFISH, $secureKey, base64_decode($decrypt), MCRYPT_MODE_ECB);
		$result = @unserialize($decrypted);
		if ($result && self::encrypt($result, $key) != $decrypt) $result = false;
		return $result;
	}
}
