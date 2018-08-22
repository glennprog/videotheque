<?php

namespace AppBundle\Service;

class GuidGenerator
{
    protected $guid;

    public function __construct(){
        $this->setGuid(null);
    }

    public function setGuid($guid){
        $this->guid = $guid;
    }

    public function getGuid($guid){
        $this->guid = $guid;
    }

    /**
    * Returns a GUIDv4 string
    *
    * Uses the best cryptographically secure method 
    * for all supported pltforms with fallback to an older, 
    * less secure version.
    *
    * @param bool $trim
    * @return string
    */
    function GUIDv4 ($trim = true)
    {
        // Windows
        if (function_exists('com_create_guid') === true) {
            if ($trim === true)
                return trim(com_create_guid(), '{}');
            else
                return com_create_guid();
        }

        // OSX/Linux
        if (function_exists('openssl_random_pseudo_bytes') === true) {
            $data = openssl_random_pseudo_bytes(16);
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        }

        // Fallback (PHP 4.2+)
        mt_srand((double)microtime() * 10000);
        $charid = strtolower(md5(uniqid(rand(), true)));
        $hyphen = chr(45);                  // "-"
        $lbrace = $trim ? "" : chr(123);    // "{"
        $rbrace = $trim ? "" : chr(125);    // "}"
        $guidv4 = $lbrace.
                substr($charid,  0,  8).$hyphen.
                substr($charid,  8,  4).$hyphen.
                substr($charid, 12,  4).$hyphen.
                substr($charid, 16,  4).$hyphen.
                substr($charid, 20, 12).
                $rbrace;
        return $guidv4;
    }


    // Less secure
    function createGUID() { 
    
        // Create a token
        $token      = $_SERVER['HTTP_HOST'];
        $token     .= $_SERVER['REQUEST_URI'];
        $token     .= uniqid(rand(), true);
        
        // GUID is 128-bit hex
        $hash        = strtoupper(md5($token));
        
        // Create formatted GUID
        $guid        = '';
        
        // GUID format is XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX for readability    
        $guid .= substr($hash,  0,  8) . 
             '-' .
             substr($hash,  8,  4) .
             '-' .
             substr($hash, 12,  4) .
             '-' .
             substr($hash, 16,  4) .
             '-' .
             substr($hash, 20, 12);
                
        return $guid;
    
    }
}