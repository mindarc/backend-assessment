<?php
/**
 * Mindarc Pty Ltd.
 *
 * @category    Mindarc
 * @package     Mindarc_GoogleFeed
 * @author      Mindarc Team <hello@mindarc.com.au>
 * @copyright   Copyright (c) 2019 Mindarc Pty Ltd. (https://www.mindarc.com.au/)
 */

namespace Mindarc\GoogleFeed\Model\Profile;

/**
 * Class DataProvider
 * @package Mindarc\GoogleFeed\Model\Profile
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Mindarc\GoogleFeed\Model\ResourceModel\Profile\CollectionFactory $dataCollectionFactory
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Mindarc\GoogleFeed\Model\ResourceModel\Profile\CollectionFactory $dataCollectionFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $dataCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var \Mindarc\GoogleFeed\Model\Profile $data */
        foreach ($items as $data) {
            $this->loadedData[$data->getId()] = $data->getData();
        }

        $data = $this->dataPersistor->get('current_profile');
        if (!empty($data)) {
            $data = $this->collection->getNewEmptyItem();
            $data->setData($data);
            $this->loadedData[$data->getId()] = $data->getData();
            $this->dataPersistor->clear('current_profile');
        }

        return $this->loadedData;
    }
}
