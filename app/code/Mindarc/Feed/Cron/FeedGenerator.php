<?php
/**
 * Copyright © pmk, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mindarc\Feed\Cron;

/**
 * Class FeedGenerator
 * @package Mindarc\Feed\Console\Command
 */
class FeedGenerator
{
    /**
     * @var \Mindarc\Feed\FeedAdapter\GeneratorFactory
     */
    protected $generatorFactory;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @var \Mindarc\Feed\Config\Data
     */
    protected $config;

    /**
     * GoogleFeedGenerator constructor.
     * @param \Mindarc\Feed\FeedAdapter\GeneratorFactory $generatorFactory
     * @param \Magento\Framework\App\State $appState
     * @param \Mindarc\Feed\Config\Data $config
     */
    public function __construct(
        \Mindarc\Feed\FeedAdapter\GeneratorFactory $generatorFactory,
        \Magento\Framework\App\State $appState,
        \Mindarc\Feed\Config\Data $config
    ) {
        $this->generatorFactory = $generatorFactory;
        $this->appState = $appState;
        $this->config = $config;
    }

    /**
     * @return null
     */
    protected function execute()
    {
        $this->appState->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
        $feedConfigs = $this->config->get();
        foreach ($feedConfigs as $feedConfig){
            $feedGenerator = $this->getFeedGenerator($feedConfig['action_class']);
            $feedGenerator->generate($feedConfig['feedprefix']);
        }
    }

    /**
     * @param string $class
     * @return \Mindarc\Feed\FeedAdapter\GeneratorInterface
     */
    protected function getFeedGenerator(string $class) : \Mindarc\Feed\FeedAdapter\GeneratorInterface
    {
        return $this->generatorFactory->create($class);
    }
}