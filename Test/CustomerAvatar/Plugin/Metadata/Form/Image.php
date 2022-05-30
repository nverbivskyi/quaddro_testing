<?php

namespace Test\CustomerAvatar\Plugin\Metadata\Form;

use Magento\Customer\Model\Metadata\Form\Image as ImageMetadata;
use Magento\Framework\Exception\LocalizedException;
use Test\CustomerAvatar\Model\Source\Validation\Image as ImageModel;

class Image
{
    /**
     * @var ImageModel
     */
    private ImageModel $validImage;

    /**
     * Image constructor.
     * @param ImageModel $validImage
     */
    public function __construct(ImageModel $validImage)
    {
        $this->validImage = $validImage;
    }

    /**
     * @param ImageMetadata $subject
     * @param $value
     * @return array
     * @throws LocalizedException
     */
    public function beforeExtractValue(ImageMetadata $subject, $value): array
    {
        $attrCode = $subject->getAttribute()
            ->getAttributeCode();

        if ($this->validImage->isImageValid('tmp_name', $attrCode) === false) {
            unset($_FILES[$attrCode]['tmp_name']);
        }

        return [$value];
    }
}
