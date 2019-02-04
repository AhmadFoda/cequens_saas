<?php
/**
 * Created by PhpStorm.
 * User: karim
 * Date: 22/12/18
 * Time: 11:30 ص
 */

namespace CequensBundle\Service;


use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class CacheService
{

    /**
     * @var AdapterInterface
     */
    protected $cache;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    protected $prefix = 'calls';

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     */
    public function setPrefix(string $prefix)
    {
        $this->prefix = $prefix;
    }



    /**
     * CacheService constructor.
     * @param AdapterInterface $cache
     * @param LoggerInterface $logger
     */
    public function __construct(AdapterInterface $cache, LoggerInterface $logger)
    {
        $this->cache = $cache;
        $this->logger = $logger;
    }

    public function addCapturedDigitsToCache($key, $digits=null, $variableName=null)
    {
        $this->logger->debug('addCapturedDigitsToCache - Key => ', [$key]);
        $cacheObject = $this->initDataArrayCache($key);
        $cachedCallObjectArray = $cacheObject->get();
        if(!empty($variableName))
        {
            $cachedCallObjectArray['collected_inputs'][$variableName] = $digits;
        }
        /*else if(!empty($digits))
        {
            if(!array_key_exists('collected_inputs',$cachedCallObjectArray)) {
                $cachedCallObjectArray['collected_inputs'][] = $digits;
            }
        }
        else
        {
            if(!array_key_exists('collected_inputs',$cachedCallObjectArray))
            {
                $cachedCallObjectArray['collected_inputs'] = [];
            }
        }*/

        $cacheObject->set($cachedCallObjectArray);
        $this->cache->save($cacheObject);
        $this->logger->debug('addCapturedDigitsToCache - Cached Object for Key '.$key.' => ', array($cachedCallObjectArray));
        return $cacheObject->get();
    }

    public function getDataArrayFromCache($key, $data = array())
    {
        $cachedCallObject = $this->initDataArrayCache($key);
        $cachedCallObjectArray = $cachedCallObject->get();
        $this->logger->debug('getDataArrayFromCache - Cached Object for Key '.$key.' => ', array($cachedCallObjectArray));
        return $cachedCallObjectArray;
    }

    private function initDataArrayCache($key)
    {
        $cachedCallObject = $this->cache->getItem($this->prefix . $key);
        if (!$cachedCallObject) {


            if($this->prefix=='calls')
            {
                $cachedCallObject->expiresAfter('3600');
                $data = array(
                    'from' => '',
                    'to' => '',
                    'callSid' => $key,
                    'collected_inputs' => []
                );
            }
            elseif ($this->prefix=='sms')
            {
                $cachedCallObject->expiresAfter('7200');
                $data = array(
                    'from' => '',
                    'to' => '',
                    'collected_inputs' => [],
                    'currentStep'=>'',
                );
            }

            $cachedCallObject->set($data);
            $this->cache->save($cachedCallObject);
        }
        $this->logger->debug('INITTTTTTT CACHE ',[$cachedCallObject]);
        return $cachedCallObject;
    }

    public function get($key,$subKey)
    {
        $value = null;
        $cachedCallObject = $this->initDataArrayCache($key);
        if(array_key_exists($subKey,$cachedCallObject['collected_inputs']))
        {
            $value = $cachedCallObject['collected_inputs'][$subKey];
        }
        return $value;
    }

    public function getSubKey($key,$subKey)
    {
        $value = null;
        $cachedCallObject = $this->initDataArrayCache($key);
        if(array_key_exists($subKey,$cachedCallObject))
        {
            $value = $cachedCallObject[$subKey];
        }
        return $value;
    }

    public function setSubKey($key, $subKey, $value)
    {
        $cacheObject = $this->initDataArrayCache($key);
        $cachedCallObjectArray = $cacheObject->get();
        if(!empty($value))
        {
            $cachedCallObjectArray[$subKey] = $value;
        }
        $cacheObject->set($cachedCallObjectArray);
        $this->cache->save($cacheObject);
        $this->logger->debug('setSubKey - Cached Object => ', array($cachedCallObjectArray));
        return $cacheObject->get();
    }

    public function addToSubKey($key, $subKey, $subSubKey,$value)
    {
        $cacheObject = $this->initDataArrayCache($key);
        $cachedCallObjectArray = $cacheObject->get();
        if(!empty($value))
        {
            $cachedCallObjectArray[$subKey][$subSubKey] = $value;
        }
        $cacheObject->set($cachedCallObjectArray);
        $this->cache->save($cacheObject);
        $this->logger->debug('addToSubKey - Cached Object => ', array($cachedCallObjectArray));
        return $cacheObject->get();
    }

    public function removeKey($key)
    {

        $status = $this->cache->deleteItem($this->prefix . $key);
        $this->logger->debug('Deleting Status {Key,status} ',[$key,$status]);
    }
}