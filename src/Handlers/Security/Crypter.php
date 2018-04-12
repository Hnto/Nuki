<?php
namespace Nuki\Handlers\Security;

use \Nuki\Models\IO\Input\Encrypted;

class Crypter {
    
    /**
     * Contains the cipher method used
     */
    const CIPHER_METHOD = 'aes-256-cbc';
    
    /**
     * Contains the encrypted iv seperator
     */
    const ENCRYPTED_IV_SEPERATOR = '--==--';
    
    /**
     * Contains the application key
     * 
     * @var string 
     */
    private $appKey;
    
    /**
     * Set application key
     */
    public function __construct() {
        $this->appKey = \Nuki\Handlers\Core\Assist::getAppKey();
    }
    
    /**
     * Encrypt data by key
     * 
     * @param string $data
     * @param string $key
     * @return Encrypted
     */
    public function encrypt($data, $key) {
      $this->validateData($data);
      $this->validateKey($key);

      $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::CIPHER_METHOD));

      $encrypted = openssl_encrypt($data, self::CIPHER_METHOD, $key, 0, $iv);
      $encoded = base64_encode($encrypted . self::ENCRYPTED_IV_SEPERATOR . $iv);

      return new Encrypted($encoded);
    }
    
    /**
     * Decrypt data by key
     * 
     * @param mixed $data
     * @param string $key
     * @return string
     */
    public function decrypt($data, $key) : string {
      $this->validateData($data);
      $this->validateKey($key);
     
      if (empty($data)) {
          return '';
      }
 
      list($encrypted, $iv) = explode(self::ENCRYPTED_IV_SEPERATOR, base64_decode($data), 2);

      return openssl_decrypt($encrypted, self::CIPHER_METHOD, $key, 0, $iv);
    }
    
    /**
     * Validate the crypter application key against the used key
     * 
     * @param string $key
     * @return boolean
     * @throws \Nuki\Exceptions\Base
     */
    private function validateKey($key) : bool {
        if (!hash_equals($this->appKey, $key)) {
            throw new \Nuki\Exceptions\Base('Used key does not equal the known application key');
        }
        
        return true;
    }
    
    /**
     * Validate input datat
     * 
     * @param string $data
     * @return string|null
     */
    private function validateData($data) : bool {
      if (!is_string($data) || empty($data)) {
        return false;
      }
            
      return true;
    }
}
