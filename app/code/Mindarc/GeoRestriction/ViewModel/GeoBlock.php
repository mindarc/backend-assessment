<?php
/**
 * Copyright © pmk, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mindarc\GeoRestriction\ViewModel;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class GeoBlock
 * @package Mindarc\GeoRestriction\ViewModel
 */
class GeoBlock implements ArgumentInterface
{
    /**
     * @var \Mindarc\GeoRestriction\GeoService\ServiceHelper
     */
    protected $serviceHelper;

    /**
     * @var BlockRepositoryInterface
     */
    protected $blockRepository;

    /**
     * @var string
     */
    protected $userCountry;

    /**
     * GeoBlock constructor.
     * @param \Mindarc\GeoRestriction\GeoService\ServiceHelper $serviceHelper
     * @param BlockRepositoryInterface $blockRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        \Mindarc\GeoRestriction\GeoService\ServiceHelper $serviceHelper,
        BlockRepositoryInterface $blockRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->serviceHelper = $serviceHelper;
        $this->blockRepository = $blockRepository;
    }

    /**
     * @return string
     */
    public function getUserCountry() : string
    {
        if (!$this->userCountry) {
            $this->userCountry = $this->serviceHelper->getUserCountry();
        }
        return $this->userCountry;
    }
}