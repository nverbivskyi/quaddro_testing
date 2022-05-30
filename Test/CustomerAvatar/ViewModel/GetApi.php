<?php

namespace Test\CustomerAvatar\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Test\CustomerAvatar\Model\Api;

class GetApi implements ArgumentInterface
{
    private Api $api;

    /**
     * @param Api $api
     */
    public function __construct(
        Api $api
    ){
        $this->api = $api;
    }

    public function getRandomImage()
    {
        return $this->api->getRandomImage();
    }
}
