<?php

namespace Amasty\Rgrid\Controller\Adminhtml\Promo\Quote;

use Magento\Backend\App\Action;
use Magento\Framework\Data\Collection as DataCollection;
use Magento\Framework\Exception\LocalizedException;
use Amasty\Rgrid\Model\SalesRuleProvider;
use Amasty\Rgrid\Model\ResourceModel\Rule\Collection;
use Amasty\Rgrid\Model\ResourceModel\Rule\CollectionFactory;

class MassPriority extends Action
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var SalesRuleProvider
     */
    private $ruleProvider;

    public function __construct(
        Action\Context $context,
        CollectionFactory $collectionFactory,
        SalesRuleProvider $ruleProvider
    ) {
        parent::__construct($context);

        $this->collectionFactory = $collectionFactory;
        $this->ruleProvider = $ruleProvider;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var int[]|null $ids */
        $ids = $this->getRequest()->getParam('ids');
        $rulePriority = $this->getRulePriority();

        if (is_array($ids)) {
            try {
                /** @var \Magento\SalesRule\Api\Data\RuleSearchResultInterface $rules */
                $rules = $this->ruleProvider->getByRuleIds($ids);

                /** @var \Magento\SalesRule\Model\Rule $rule */
                foreach ($rules->getItems() as $rule) {
                    $rule->setSortOrder($rulePriority);
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
                    __('Something went wrong while updating the rule(s) priority.')
                );
            }

            return $this->_redirect('sales_rule/*/');
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a rule to update its priority.'));

        return $this->_redirect('sales_rule/*/');
    }

    /**
     * @return int
     */
    private function getRulePriority()
    {
        /** @var string|null $priority */
        $priority = $this->getRequest()->getParam('priority');
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();

        if ($priority === 'low') {
            $rulePriority = $collection->getSortOrder();
            $rulePriority++;
        } else {
            $rulePriority = $collection->getSortOrder(DataCollection::SORT_ORDER_ASC);

            if ($rulePriority != 0) {
                $rulePriority--;
            }
        }

        return $rulePriority;
    }
}
