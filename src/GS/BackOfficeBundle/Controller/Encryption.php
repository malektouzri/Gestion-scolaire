<?php
/**
 * Created by PhpStorm.
 * User: ACER
 * Date: 07/09/2018
 * Time: 14:04
 */

namespace GS\BackOfficeBundle\Controller;


class Encryption {
    var $skey = "dabado";
    function pad_key($key){
        // key is too large
        if(strlen($key) > 32) return false;

        // set sizes
        $sizes = array(16,24,32);

        // loop through sizes and pad key
        foreach($sizes as $s){
            while(strlen($key) < $s) $key = $key."\0";
            if(strlen($key) == $s) break; // finish if the key matches a size
        }

        // return
        return $key;
    }


    public  function safe_b64encode($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }

    public function safe_b64decode($string) {
        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    public  function encode($value){
        if(!$value){return false;}
        $text = $value;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->pad_key($this->skey), $text, MCRYPT_MODE_ECB, $iv);
        return trim($this->safe_b64encode($crypttext));
    }

    public function decode($value){
        if(!$value){return false;}
        $crypttext = $this->safe_b64decode($value);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->pad_key($this->skey), $crypttext, MCRYPT_MODE_ECB, $iv);
        return trim($decrypttext);
    }
}