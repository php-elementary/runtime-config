<?php

namespace elementary\config\Runtime;

use elementary\cache\Runtime\RuntimeCache;
use elementary\core\Singleton\SingletonTrait;
use Psr\SimpleCache\CacheInterface;

/**
 * @package elementary\config\Runtime
 */
class RuntimeConfig
{
    use SingletonTrait;

    /**
     * @var CacheInterface
     */
    protected $cache = null;

    /**
     * @var string
     */
    protected $separate = '/';

    /**
     * @param string $key 'services/service/version'
     * @param mixed  $value
     *
     * @return $this
     */
    public function set($key, $value)
    {
        $path = $this->getPath($key);
        $data = $this->getFromCache();
        $node = &$data;
        $cnt  = count($path) - 1;

        for ($ii=0; $ii<=count($path); $ii++) {
            if ($ii == $cnt) {
                $node[$path[$ii]] = $value;
                break;
            } elseif (!array_key_exists($path[$ii], $node)) {
                $node[$path[$ii]] = [];
            }
            if (!is_array($node[$path[$ii]])) {
                $node[$path[$ii]] = [];
            }

            $node = &$node[$path[$ii]];
        }

        $this->setToCache($data);

        return $this;
    }

    /**
     * @param string    $key 'services/service/version'
     * @param mixed     $defaultValue
     *
     * @return mixed
     */
    public function get($key, $defaultValue = null)
    {
        $keyExists = true;
        $parts     = $this->getPath($key);
        $node      = $this->getFromCache();

        foreach($parts as $part) {
            $part = trim($part);

            if (!is_array($node) || !array_key_exists($part, $node)) {
                $keyExists = false;
                break;
            }

            $node = &$node[$part];
        }

        if ($keyExists) {
            return $node;
        } else {
            return $defaultValue;
        }
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        $parts     = $this->getPath($key);
        $keyExists = true;
        $node      = $this->getFromCache();

        foreach($parts as $part) {
            $part = trim($part);

            if (!is_array($node) || !array_key_exists($part, $node)) {
                $keyExists = false;
                break;
            }

            $node = &$node[$part];
        }

        return ($keyExists);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function delete($key)
    {
        $retunValue = false;
        $path = $this->getPath($key);
        $data = $this->getFromCache();
        $node = &$data;
        $cnt  = count($path) - 1;

        for ($ii=0; $ii<=count($path); $ii++) {
            if ($ii == $cnt) {
                unset($node[$path[$ii]]);
                $this->setToCache($data);
                $retunValue = true;
                break;
            }

            $node = &$node[$path[$ii]];
        }

        return $retunValue;
    }

    /**
     * @param array $cache
     *
     * @return $this
     */
    public function setAll(array $cache)
    {
        $this->setToCache($cache);

        return $this;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->getFromCache();
    }

    /**
     * @return string
     */
    public function getSeparate()
    {
        return $this->separate;
    }

    /**
     * @param string $separate
     *
     * @return $this
     */
    public function setSeparate($separate)
    {
        $this->separate = $separate;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return array
     */
    protected function getPath($key)
    {
        return explode($this->getSeparate(), $key);
    }

    /**
     * @param array $value
     */
    public function setToCache(array $value) {
        $this->getCache()->set('', $value);
    }

    /**
     * @return array
     */
    public function getFromCache()
    {
        return $this->getCache()->get('', []);
    }

    /**
     * @return CacheInterface
     */
    public function getCache()
    {
        if ($this->cache === null) {
            $this->setCache(new RuntimeCache());
        }

        return $this->cache;
    }

    /**
     * @param CacheInterface $cache
     *
     * @return $this
     */
    public function setCache(CacheInterface $cache)
    {
        $this->cache = $cache;

        return $this;
    }
}