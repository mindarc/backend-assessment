<?php
namespace Mindarc\GoogleFeed\Block;

use Mindarc\GoogleFeed\Model\FeedGenerator;

class Feed extends \Magento\Framework\View\Element\Template
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Xml\Parser $parser,
        \Magento\Framework\View\Element\Template\Context $context,
        FeedGenerator $feedGenerator,
        array $data = []
    ) {
        $this->parser     = $parser;
        $this->feedGenerator = $feedGenerator;
        parent::__construct($context, $data);
    }
    
    /**
     * readXmlDataByParser
     *
     * @return void
     */
    public function readXmlDataByParser()
    {
        $filePath = $this->feedGenerator->getSavePath();
        $parsedArray = $this->parser->load($filePath)->xmlToArray();
        return $parsedArray;
    }

}
