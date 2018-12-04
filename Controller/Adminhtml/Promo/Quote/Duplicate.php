<?php

namespace Amasty\Rgrid\Controller\Adminhtml\Promo\Quote;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Magento\SalesRule\Api\Data\RuleInterface;
use Magento\SalesRule\Api\RuleRepositoryInterface;

class Duplicate extends Action
{
    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    public function __construct(
        Action\Context $context,
        RuleRepositoryInterface $ruleRepository
    ) {
        parent::__construct($context);

        $this->ruleRepository = $ruleRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $ruleId = $this->getRequest()->getParam('id');

        if ($ruleId) {
            try {
                /** @var RuleInterface $rule */
                $rule = $this->ruleRepository->getById($ruleId);
                $rule->setRuleId(null);

                $rule = $this->ruleRepository->save($rule);

                $this->messageManager->addSuccessMessage(__('The rule has been duplicated.'));

                return $this->_redirect('sales_rule/*/edit', ['id' => $rule->getRuleId()]);
            } catch (LocalizedException $exception) {
                $this->messageManager->addExceptionMessage($exception);
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('We can\'t duplicate the rule right now. Please review the log and try again.')
                );
            }

            return $this->_redirect('sales_rule/*/');
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a rule to duplicate.'));

        return $this->_redirect('sales_rule/*/');
    }
}
