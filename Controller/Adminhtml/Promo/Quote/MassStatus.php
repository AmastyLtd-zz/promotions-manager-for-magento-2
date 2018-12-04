<?php

namespace Amasty\Rgrid\Controller\Adminhtml\Promo\Quote;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Amasty\Rgrid\Model\SalesRuleProvider;

class MassStatus extends Action
{
    /**
     * @var SalesRuleProvider
     */
    private $ruleProvider;

    public function __construct(
        Action\Context $context,
        SalesRuleProvider $ruleProvider
    ) {
        parent::__construct($context);

        $this->ruleProvider = $ruleProvider;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var int[]|null $ids */
        $ids = $this->getRequest()->getParam('ids');
        /** @var string|null $status */
        $status = $this->getRequest()->getParam('status');

        if (is_array($ids)) {
            try {
                /** @var \Magento\SalesRule\Api\Data\RuleSearchResultInterface $rules */
                $rules = $this->ruleProvider->getByRuleIds($ids);

                /** @var \Magento\SalesRule\Model\Rule $rule */
                foreach ($rules->getItems() as $rule) {
                    $rule->setIsActive($status);
                    $this->ruleProvider->getRepository()->save($rule);
                }

                $this->messageManager->addSuccessMessage(
                    __('A total of %1 record(s) have been updated.', $rules->getTotalCount())
                );

                return $this->_redirect('sales_rule/*/');
            } catch (LocalizedException $exception) {
                $this->messageManager->addExceptionMessage($exception);
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while updating the rule(s) status.')
                );
            }

            return $this->_redirect('sales_rule/*/');
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a rule to update its status.'));

        return $this->_redirect('sales_rule/*/');
    }
}
