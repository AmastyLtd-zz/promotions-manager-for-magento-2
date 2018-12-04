<?php

namespace Amasty\Rgrid\Model;

use Amasty\Rules\Helper\Data;
use Magento\Framework\Option\ArrayInterface;

class RuleActions implements ArrayInterface
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    public function __construct(\Magento\Framework\Module\Manager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            'by_percent' => __('Percent of product price discount'),
            'by_fixed' => __('Fixed amount discount'),
            'cart_fixed' => __('Fixed amount discount for whole cart'),
            'buy_x_get_y' => __('Buy N products, and get next products with discount')
        ];

        if ($this->moduleManager->isEnabled('Amasty_Promo')) {
            $amastyPromoOptions = [
                'ampromo_items' => __('Auto add promo items with products'),
                'ampromo_cart' => __('Auto add promo items for the whole cart'),
                'ampromo_product' => __('Auto add the same product'),
                'ampromo_spent' => __('Auto add promo items for every $X spent')
            ];

            $options = array_merge($options, $amastyPromoOptions);
        }

        if ($this->moduleManager->isEnabled('Amasty_Rules')) {
            $options = array_merge($options, Data::staticGetDiscountTypes());
        }

        return $options;
    }
}
