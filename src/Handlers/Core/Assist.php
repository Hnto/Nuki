<?php
namespace Nuki\Handlers\Core;

use \Nuki\{
    Models\IO\Input\Encrypted,
    Handlers\Security\Crypter,
    Application\Application
};

class Assist {
  
    /**
     * Returns class methods 
     * 
     * @param string|object $class
     *
     * @return array
     */
    public static function classMethods($class) : array {
        $classMethods = get_class_methods($class);

        if (is_null($classMethods)) {
          $classMethods = [];
        }

        return $classMethods;
    }

    /**
     * Returns class name with full namespace path
     * 
     * @param object $class
     * @return string
     */
    public static function className($class) {
        return get_class($class);
    }

    /**
     * Returns only the class name
     * 
     * @param object $class
     * @return string
     */
    public static function classNameShort($class) {
        $className = self::className($class);

        $position = strrpos($className, '\\');
        $name = substr($className, $position + 1);

        return $name;
    }

    /**
     * Check if class has method
     * 
     * @param string $method
     * @param object|string $class
     * @return boolean
     */
    public static function classHasMethod($method, $class) {
        $methods = self::classMethods($class);

        if (!in_array($method, $methods)) {
          return false;
        }

        return true;
    }

    /**
     * @param string $string
     * @param string $needle
     * @return string
     */
    public static function extractBeforeNeedle(string $string, string $needle)
    {
        if (strpos($string, $needle) === false) {

            return $string;
        }

        return strstr($string, $needle, true);
    }

    /**
     * @param array $data
     * @param mixed $key
     * @return array
     */
    public static function extractSingleFromMultiArray(array $data = [], $key)
    {
        $return = [];

        foreach($data as $item) {
            if (isset($item[$key])) {

                $return[] = $item[$key];
            }
        }

        return $return;
    }
    
    /**
     * Get application name
     * 
     * @return string
     */
    public static function getAppName() : string {
        if (empty(getenv('APP_NAME'))) {
            return null;
        }
        
        return getenv('APP_NAME');
    }
    
    /**
     * Get application algorithm
     * Used for encryption, decryption, hashing etc.
     * 
     * @return string
     * @throws \Nuki\Exceptions\Base
     */
    public static function getAppAlgorithm() : string {
      if (empty(getenv('APP_ALGORITHM'))) {
          throw new \Nuki\Exceptions\Base('Application algorithm is not set');
      }

      return getenv('APP_ALGORITHM');
    }
    
    /**
     * Get application key
     * Used for encryption, decryption, hashing etc.
     * 
     * @return string
     * @throws \Nuki\Exceptions\Base
     */
    public static function getAppKey() : string {
      if (empty(getenv('APP_KEY'))) {
          throw new \Nuki\Exceptions\Base('Application key is not set');
      }

      return getenv('APP_KEY');
    }

    /**
     * Get application storage driver
     * 
     * @throws \Nuki\Exceptions\Base
     */
    public static function getAppStorageDriver() : string {
      if (empty(getenv('APP_STORAGEDRIVER'))) {
        throw new \Nuki\Exceptions\Base('Application storage driver is not set');
      }
      
      return getenv('APP_STORAGEDRIVER');
    }

    /**
     * Get application environment
     * 
     * @throws \Nuki\Exceptions\Base
     */
    public static function getAppEnv() : string {
      if (empty(getenv('APP_ENV'))) {
        throw new \Nuki\Exceptions\Base('Application environment is not set');
      }
      
      return getenv('APP_ENV');
    }

    /**
     * Hash by data or filename
     *
     * @param $data
     * @param bool $file
     *
     * @return bool|string
     *
     * @throws \Nuki\Exceptions\Base
     */
    public static function hash($data, $file = false) {
      $hasher = new \Nuki\Handlers\Security\Hasher(self::getAppAlgorithm(), self::getAppKey());

      return $hasher->hash($data, $file);
    }

    /**
     * Verify hash
     *
     * @param $hashed
     * @param $hash
     *
     * @return bool
     *
     * @throws \Nuki\Exceptions\Base
     */
    public static function hashVerify($hashed, $hash) : bool {
      $hasher = new \Nuki\Handlers\Security\Hasher(
          self::getAppAlgorithm(),
          self::getAppKey()
      );
      
      return $hasher->verify($hashed, $hash);
    }

    /**
     * Encrypt data and return an encrypter object
     *
     * @param $data
     *
     * @return Encrypted
     *
     * @throws \Nuki\Exceptions\Base
     */
    public static function encrypt($data) : Encrypted {
      $crypter = new Crypter();

      return $crypter->encrypt($data, self::getAppKey());
    }

    /**
     * Decrypt data and return the decrypted value
     *
     * @param $data
     *
     * @return string
     *
     * @throws \Nuki\Exceptions\Base
     */
    public static function decrypt($data) : string {
      $crypter = new Crypter();

      return $crypter->decrypt($data, self::getAppKey());
    }

    /**
     * Generate random string
     *
     * @param int $length
     *
     * @return string
     *
     * @throws \Exception
     */
    public static function randomString(int $length = 6) {
      return bin2hex(random_bytes($length));
    }
    
    /**
     * Generate random integers
     * 
     * @param int $min
     * @param int $max
     * @return int
     */
    public static function randomInt($min = 0, $max = 10) {
      return mt_rand($min, $max);
    }
    
    /**
     * Load the contents of a core view file
     * 
     * @param string $name
     * @return string
     * @throws \Nuki\Exceptions\Base
     */
    public static function loadCoreView(string $name) {
      $base = __DIR__ . '/../../Views/';
      if (!file_exists($base . $name . '.view')) {
        throw new \Nuki\Exceptions\Base('The view file "' . $name . '" does not exist.');
      }
      
      return file_get_contents($base . $name . '.view');
    }
}
