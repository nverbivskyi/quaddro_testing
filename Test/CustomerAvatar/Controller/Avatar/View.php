<?php

namespace Test\CustomerAvatar\Controller\Avatar;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Filesystem;
use Magento\Framework\Url\DecoderInterface;
use Magento\MediaStorage\Helper\File\Storage;

class View extends Action
{
    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var DecoderInterface
     */
    protected $urlDecoder;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @param Context $context
     * @param RawFactory $resultRawFactory
     * @param DecoderInterface $urlDecoder
     * @param FileFactory $fileFactory
     */
    public function __construct(
        Context $context,
        RawFactory $resultRawFactory,
        DecoderInterface $urlDecoder,
        FileFactory $fileFactory
    ) {
        $this->resultRawFactory = $resultRawFactory;
        $this->urlDecoder = $urlDecoder;
        $this->fileFactory = $fileFactory;
        return parent::__construct($context);
    }

    /**
     * @return Raw|void
     * @throws NotFoundException
     */
    public function execute()
    {
        if ($this->getRequest()->getParam('image')) {
            $file = $this->urlDecoder->decode(
                $this->getRequest()->getParam('image')
            );
        } else {
            throw new NotFoundException(__('Page not found.'));
        }

        /** @var Filesystem $filesystem */
        $filesystem = $this->_objectManager->get(Filesystem::class);
        $directory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $fileName = CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER . '/' . ltrim($file, '/');
        $path = $directory->getAbsolutePath($fileName);

        if (!$directory->isFile($fileName)
            && !$this->_objectManager->get(Storage::class)->processStorageFile($path)
        ) {
            throw new NotFoundException(__('Page not found.'));
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        switch (strtolower($extension)) {
            case 'gif':
                $contentType = 'image/gif';
                break;
            case 'jpg':
                $contentType = 'image/jpeg';
                break;
            case 'png':
                $contentType = 'image/png';
                break;
            default:
                $contentType = 'application/octet-stream';
                break;
        }
        $stat = $directory->stat($fileName);
        $contentLength = $stat['size'];
        $contentModify = $stat['mtime'];

        $resultRaw = $this->resultRawFactory->create();
        $resultRaw->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Content-type', $contentType, true)
            ->setHeader('Content-Length', $contentLength)
            ->setHeader('Last-Modified', date('r', $contentModify));
        $resultRaw->setContents($directory->readFile($fileName));

        return $resultRaw;
    }
}
