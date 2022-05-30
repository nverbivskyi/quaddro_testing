<?php

namespace Test\CustomerAvatar\Plugin\CustomerData;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Helper\View;
use Test\CustomerAvatar\Block\Attributes\Avatar;

class Customer
{
    /**
     * @var CurrentCustomer
     */
    private CurrentCustomer $currentCustomer;

    /**
     * @var View
     */
    private View $customerViewHelper;

    /**
     * @var Avatar
     */
    private Avatar $customerAvatar;

    /**
     * @param CurrentCustomer $currentCustomer
     * @param View $customerViewHelper
     * @param Avatar $customerAvatar
     */
    public function __construct(
        CurrentCustomer $currentCustomer,
        View $customerViewHelper,
        Avatar $customerAvatar
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->customerViewHelper = $customerViewHelper;
        $this->customerAvatar = $customerAvatar;
    }

    /**
     * @return array
     */
    public function afterGetSectionData(): array
    {
        if (!$this->currentCustomer->getCustomerId()) {
            return [];
        }
        $customer = $this->currentCustomer->getCustomer();
        if (!empty($customer->getCustomAttribute('profile_picture'))) {
            $file = $customer->getCustomAttribute('profile_picture')->getValue();
        } else {
            $file = '';
        }
        return [
            'fullname' => $this->customerViewHelper->getCustomerName($customer),
            'firstname' => $customer->getFirstname(),
            'websiteId' => $customer->getWebsiteId(),
            'avatar' => $this->customerAvatar->getAvatarCurrentCustomer($file)
        ];
    }
}
