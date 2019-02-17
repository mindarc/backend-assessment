<?php
/**
 * Mindarc Pty Ltd.
 *
 * @category    Mindarc
 * @package     Mindarc_GoogleFeed
 * @author      Mindarc Team <hello@mindarc.com.au>
 * @copyright   Copyright (c) 2019 Mindarc Pty Ltd. (https://www.mindarc.com.au/)
 */

namespace Mindarc\GoogleFeed\Ui\Component\Listing\Profile\Columns\Renderer;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Class Actions
 * @package Mindarc\GoogleFeed\Ui\Component\Listing\Profile\Columns\Renderer
 */
class Actions extends Column
{
    /** Url path */
    const URL_PATH_GENERATE = 'google/profile/generate';
    const URL_PATH_PREVIEW = 'google/profile/preview';
    const URL_PATH_EDIT = 'google/profile/edit';
    const URL_PATH_DELETE = 'google/profile/delete';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $authorization;

    /**
     * Actions constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        \Magento\Framework\AuthorizationInterface $authorization,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->authorization = $authorization;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');

                // if saving profiles allowed
                if ($this->authorization->isAllowed('Mindarc_GoogleFeed::form')) {
                    $item[$name]['edit'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::URL_PATH_EDIT,
                            ['id' => $item['entity_id']]
                        ),
                        'label' => __('Edit')
                    ];
                }

                // if viewing profiles allowed
                if ($this->authorization->isAllowed('Mindarc_GoogleFeed::profiles')) {
                    $item[$name]['preview'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::URL_PATH_PREVIEW,
                            ['id' => $item['entity_id']]
                        ),
                        'label' => __('Preview'),
                    ];
                }

                // if generation allowed and profile is not disabled
                if ($this->authorization->isAllowed('Mindarc_GoogleFeed::generate')
                    && $item['status'] != \Mindarc\GoogleFeed\Model\Profile\Source\Status::STATUS_DISABLED) {
                    $item[$name]['generate'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::URL_PATH_GENERATE,
                            ['id' => $item['entity_id']]
                        ),
                        'label' => __('Generate'),
                        'confirm' => [
                            'title' => __('Generate'),
                            'message' => __('Are you sure you want generate the feed now?')
                        ],
                    ];
                }

                // if deleting profiles allowed
                if ($this->authorization->isAllowed('Mindarc_GoogleFeed::delete')) {
                    $item[$name]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::URL_PATH_DELETE,
                            ['id' => $item['entity_id']]
                        ),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete'),
                            'message' => __('Are you sure to delete this profile?')
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
