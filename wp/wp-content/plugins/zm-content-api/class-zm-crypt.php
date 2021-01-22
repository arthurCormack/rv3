<?php
define("ZMID_CRYPT_ENCRYPTKEY", 'MySecretKey12345');
define("ZMID_CRYPT_IV", '1234567890123456');
define("ZMID_CRYPT_BLOCKSIZE", 16);
class Crypto {

    private $encryptKey = ZMID_CRYPT_ENCRYPTKEY;
    private $iv = ZMID_CRYPT_IV;
    private $blocksize = ZMID_CRYPT_BLOCKSIZE;
    
    public function decrypt($data) {
    	//die("\$data to hex2bin():" .$data);
        return $this->unpad(mcrypt_decrypt(MCRYPT_BLOWFISH, 
            $this->encryptKey, 
            hex2bin($data),
            MCRYPT_MODE_ECB, $this->iv), $this->blocksize);
    }
    
    public function encrypt($data) {
        //don't use default php padding which is '\0'
        $pad = $this->blocksize - (strlen($data) % $this->blocksize);
        $data = $data . str_repeat(chr($pad), $pad);
        return bin2hex(mcrypt_encrypt(MCRYPT_RIJNDAEL_128,
            $this->encryptKey,
            $data, MCRYPT_MODE_CBC, $this->iv));
    }
    
    private function unpad($str, $blocksize) {
        $len = mb_strlen($str);
        $pad = ord( $str[$len - 1] );
        if ($pad && $pad < $blocksize) {
            $pm = preg_match('/' . chr($pad) . '{' . $pad . '}$/', $str);
            if( $pm ) {
                return mb_substr($str, 0, $len - $pad);
            }
        }
        return $str;
    }
}
?>