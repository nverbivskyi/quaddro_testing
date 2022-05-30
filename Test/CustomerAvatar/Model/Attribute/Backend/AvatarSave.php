<?php

namespace Test\CustomerAvatar\Model\Attribute\Backend;

use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Test\CustomerAvatar\Model\Source\Validation\Image;

class AvatarSave extends AbstractBackend
{
    /**
     * @param DataObject $object
     * @return $this
     * @throws LocalizedException
     */
    public function beforeSave($object): AvatarSave
    {
        $validation = new Image();
        $attrCode = $this->getAttribute()
            ->getAttributeCode();

        if ($attrCode == 'profile_picture') {
            if (!$validation->isImageValid('tmpp_name', $attrCode)) {
                throw new LocalizedException(
                    __('The profile picture is not a valid image.')
                );
            }
        }

        return parent::beforeSave($object);
    }
}
