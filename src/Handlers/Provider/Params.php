<?php
namespace Nuki\Handlers\Provider;

use Nuki\Exceptions\Base;
use Nuki\Models\IO\Input\Param;

class Params implements \Nuki\Skeletons\Handlers\BaseHandler {
  
  /**
   * Contains params
   * 
   * @var array 
   */
  private $params;

    /**
     * Params constructor.
     * @param array $params
     * @throws \Nuki\Exceptions\Base
     */
      public function __construct(array $params = []) {
        rsort($params);

        if (isset($params[0]) && is_array($params[0])) {
          throw new Base('Params handler does not accept multidimensional arrays');
        }

        $this->params = $params;
      }

    /**
     * @param mixed $key
     * @param mixed $value
     * @throws \Nuki\Exceptions\Base
     */
      public function add($key, $value) {
        if (is_array($key)) {
          throw new Base('Param key may not be an array');
        }

        $this->params[$key] = $value;
      }

    /**
     * Get param value by key or false for all params
     * Optional set default if not found
     *
     * @param bool $key
     * @param bool $default
     * @return Param|array
     */
      public function get($key = false, $default = false) {
        if ($key === false) {
          return $this->params;
        }

        if (!$this->has($key) && $default === false) {
          return new Param($key, null);
        }

        if (!$this->has($key) && $default !== false) {
          return new Param($key, $default);
        }

        return new Param($key, $this->params[$key]);
      }

    /**
     * {@inheritdoc}
     */
  public function has($key): bool {
    if (!isset($this->params[$key])) {
      return false;
    }
    
    return true;
  }
  
  /**
   * {@inheritdoc}
   */
  public function remove($key) {
    unset($this->params[$key]);
  }
}
