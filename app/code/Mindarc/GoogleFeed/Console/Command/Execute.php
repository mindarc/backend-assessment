<?php
/**
 * Mindarc Pty Ltd.
 *
 * @category    Mindarc
 * @package     Mindarc_GoogleFeed
 * @author      Mindarc Team <hello@mindarc.com.au>
 * @copyright   Copyright (c) 2019 Mindarc Pty Ltd. (https://www.mindarc.com.au/)
 */

namespace Mindarc\GoogleFeed\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Execute
 * @package Mindarc\GoogleFeed\Console\Command
 */
class Execute extends Command
{
    /**
     * console command
     */
    const COMMAND_GENERATE_FEED = 'google-feed:generate';

    /**
     * @var \Mindarc\GoogleFeed\Model\FeedGenerator
     */
    private $feedGenerator;

    /**
     * Execute constructor.
     * @param \Mindarc\GoogleFeed\Model\FeedGenerator $feedGenerator
     */
    public function __construct(
        \Mindarc\GoogleFeed\Model\FeedGenerator $feedGenerator
    ) {
        parent::__construct();
        $this->feedGenerator = $feedGenerator;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_GENERATE_FEED)
            ->setDescription('Generate google feeds according the profiles');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $result = $this->feedGenerator->generate();

        if ($result) { // result is error
            $output->writeln('<error>Error(s):</error>');
            $output->writeln('<error>' . implode("\n", $result) . '</error>');
        }

        $output->writeln('<info>' . __('Finished') . '</info>');
    }
}
