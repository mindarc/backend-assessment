<?php
/**
 * Copyright © pmk, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mindarc\Feed\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class FeedGenerator
 * @package Mindarc\Feed\Console\Command
 */
class FeedGenerator extends \Symfony\Component\Console\Command\Command
{
    const INPUT_KEY_FEEDS = 'feed';

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
     * FeedGenerator constructor.
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
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('mindarc:feed')
            ->setDescription('Generates the feeds.')
            ->setDefinition($this->getInputList());
    }

    /**
     * @return array
     */
    public function getInputList() : array
    {
        return [
            new InputArgument(
                self::INPUT_KEY_FEEDS,
                InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
                'Add feed name.'
            ),
        ];
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->appState->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
        foreach ($this->getFeedsArgs($input) as $feedId) {
            $feedConfig = $this->config->get($feedId);
            if (!empty($feedConfig)) {
                $feedGenerator = $this->getFeedGenerator($feedConfig['action_class']);
                $feedGenerator->generate($feedConfig['feedprefix']);
            } else {
                throw new \InvalidArgumentException(
                    "The requested feed types are not supported: '"
                );
            }
        }
    }

    /**
     * @param InputInterface $input
     * @return array
     */
    protected function getFeedsArgs(InputInterface $input) : array
    {
        $feedTypes = [];
        if ($input->getArgument(self::INPUT_KEY_FEEDS)) {
            $requestedTypes = $input->getArgument(self::INPUT_KEY_FEEDS);
            $feedTypes = array_filter(array_map('trim', $requestedTypes), 'strlen');
        }

        return $feedTypes;
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