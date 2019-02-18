<?php
/**
 * Mindarc Pty Ltd.
 *
 * @category    Mindarc
 * @package     Mindarc_GoogleFeed
 * @author      Mindarc Team <hello@mindarc.com.au>
 * @copyright   Copyright (c) 2019 Mindarc Pty Ltd. (https://www.mindarc.com.au/)
 */

namespace Mindarc\GoogleFeed\Block\Adminhtml\Profile;

/**
 * Class Preview
 * @package Mindarc\GoogleFeed\Block\Adminhtml\Profile
 */
class Preview implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var mixed
     */
    private $profile;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * Preview constructor.
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\UrlInterface $urlBuilder
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->registry = $registry;
        $this->urlBuilder = $urlBuilder;
        $this->profile = $this->registry->registry('current_profile');
    }

    /**
     * get feed url of the profile
     *
     * @return mixed
     */
    public function getFeedUrl()
    {
        return $this->profile->getUrl();
    }

    /**
     * Get trimmed xml content for preview
     *
     * @param bool $limit
     * @return bool|mixed|string
     */
    public function getFeed($limit = true)
    {
        $feedUrl = $this->getFeedUrl();
        if ($feedUrl) {
            try {
                // getting xml content from the feed url
                $xml = file_get_contents($feedUrl);
                // trim to 10000 chars
                if ($limit) {
                    $xml = substr($xml, 0, 10000);
                }
                // replace double quotes with single quotes, and remove line breaks to work with the js function.
                $xml = str_replace(PHP_EOL, '', str_replace('"', '\'', trim(preg_replace('/\s\s+/', '~~',
                    $xml))));

            } catch (\Exception $e) {
                return false;
            }
            return $xml;
        }

        return false;
    }

    /**
     * build and return back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->urlBuilder->getUrl(
            'google/profile/'
        );
    }
}
