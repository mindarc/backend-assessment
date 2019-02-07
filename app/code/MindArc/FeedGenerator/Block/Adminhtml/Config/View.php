<?php

namespace MindArc\FeedGenerator\Block\Adminhtml\Config;

use Magento\Backend\Block\Template\Context;

class View
    extends \Magento\Config\Block\System\Config\Form\Field
{

    const BUTTON_TEMPLATE = 'system/config/view.phtml';

    public function __construct(Context $context, $data = array())
    {
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate(static::BUTTON_TEMPLATE);
        }

        return $this;
    }

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();

        return parent::render($element);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->addData(
            [
                'url'     => $this->getUrl(),
                'html_id' => $element->getHtmlId(),
            ]
        );

        return $this->_toHtml();
    }

    public function getUrl($route = '', $params = [])
    {
        return parent::getUrl('generate/feed/view');
    }

}