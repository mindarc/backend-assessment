<?php

namespace MindArc\GeoIP\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class GeoIP
    extends \Magento\Framework\App\Helper\AbstractHelper
{

    const DATABASE_PATH = 'interactive_geoip/setting/file';
    const SIMULATION_MODE = 'interactive_geoip/setting/simulationmode';
    const MY_IP = 'interactive_geoip/setting/myip';

    protected $_directoryList;

    public function __construct(
        DirectoryList $directoryList,
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager
    )
    {
        $this->_directoryList = $directoryList;
        parent::__construct($context, $objectManager, $storeManager);
    }

    public function getDataBaseFile()
    {
        return $this->scopeConfig->getValue(self::DATABASE_PATH);
    }

    public function simulationMode()
    {
        return $this->scopeConfig->getValue(self::SIMULATION_MODE);
    }

    public function myIP()
    {
        return $this->scopeConfig->getValue(self::MY_IP);
    }

    public function checkDB()
    {
        $path = $this->_directoryList->getPath('base') . '/' . $this->getDataBaseFile();
        if (!file_exists($path)) {
            return false;
        }
        $folder = scandir($path, true);
        $pathFile = $path . '/' . $folder[0];
        if (!file_exists($pathFile)) {
            return false;
        }

        return $pathFile;
    }

    public function getCountryData($ip)
    {
        try {
            $libPath = $this->checkDB();
            if ($libPath && class_exists('GeoIp2\Database\Reader')) {
                $geoIp = new \GeoIp2\Database\Reader($libPath);
                $record = $geoIp->country($ip);
                $geoIpData = [
                    'name' => $record->country->name,
                    'iso'  => $record->country->isoCode,
                ];
            } else {
                $geoIpData = [];
            }
        } catch (\Exception $e) {
            $geoIpData = [
                'name' => 'NONE',
                'iso'  => 'NONE',
            ];
        }

        return $geoIpData;
    }

    public function getIpAddress()
    {
        if ($this->simulationMode()) {
            return $this->myIP();
        }

        $server = $this->_getRequest()->getServer();
        if (!empty($server['HTTP_CLIENT_IP'])) {
            $ip = $server['HTTP_CLIENT_IP'];
        } else if (!empty($server['HTTP_X_FORWARDED_FOR'])) {
            $ip = $server['HTTP_X_FORWARDED_FOR'];
        } else if (!empty($server['REMOTE_ADDR'])) {
            $ip = $server['REMOTE_ADDR'];
        } else {
            $ip = $this->getIpFromMagento();
        }
        $ips = explode(',', $ip);
        $ip = $ips[count($ips) - 1];

        return trim($ip);
    }

    protected function getIpFromMagento()
    {
        return $this->_remoteAddress->getRemoteAddress();
    }
}