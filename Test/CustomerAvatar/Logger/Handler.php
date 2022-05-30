<?php

namespace Test\CustomerAvatar\Logger;

use Magento\Framework\Logger\Handler\Base;

class Handler extends Base
{
    const FILE_NAME = '/var/log/customer_avatar.log';

    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::INFO;

    /**
     * File name
     * @var string
     */
    protected $fileName = HANDLER::FILE_NAME;
}
