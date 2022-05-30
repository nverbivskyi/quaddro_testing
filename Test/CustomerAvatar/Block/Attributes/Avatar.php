<?php

namespace Test\CustomerAvatar\Block\Attributes;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\MediaStorage\Helper\File\Storage;
use Test\CustomerAvatar\Model\Api;
use Magento\Framework\Filesystem;

class Avatar extends Template
{
    /**
     * @var Api
     */
    private Api $avatarApi;

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @var Storage
     */
    private Storage $storage;

    public function __construct(
        Context $context,
        Api $avatarApi,
        Filesystem $filesystem,
        Storage $storage
    ) {
        $this->avatarApi = $avatarApi;
        $this->filesystem = $filesystem;
        $this->storage = $storage;
        parent::__construct($context);
    }

    /**
     * Check the file is already exist in the path.
     *
     * @param $file
     * @return bool
     */
    public function checkImageFile($file)
    {
        $file = base64_decode($file);
        $directory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $fileName = CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER . '/' . ltrim($file, '/');
        $path = $directory->getAbsolutePath($fileName);
        if (!$directory->isFile($fileName)
            && !$this->storage->processStorageFile($path)
        ) {
            return false;
        }
        return true;
    }

    /**
     * Get the avatar of the customer is already logged in
     *
     * @param $file
     * @return string
     */
    public function getAvatarCurrentCustomer($file): string
    {
        if ($this->checkImageFile(base64_encode($file)) === true) {
            return $this->getUrl('viewfile/avatar/view/', ['image' => base64_encode($file)]);
        }
        return $this->avatarApi->getRandomImage();
    }
}
