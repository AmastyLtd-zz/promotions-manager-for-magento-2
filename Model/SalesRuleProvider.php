<?php

namespace Amasty\Rgrid\Model;

use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\SalesRule\Api\Data\RuleSearchResultInterface;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Magento\SalesRule\Model\Data\Rule;

class SalesRuleProvider
{
    /**
     * @var RuleRepositoryInterface
     */
    protected $ruleRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $criteriaBuilder;

    public function __construct(
        RuleRepositoryInterface $ruleRepository,
        SearchCriteriaBuilder $criteriaBuilder
    ) {
        $this->ruleRepository = $ruleRepository;
        $this->criteriaBuilder = $criteriaBuilder;
    }

    /**
     * @param array $ruleIds
     *
     * @return RuleSearchResultInterface
     */
    public function getByRuleIds($ruleIds)
    {
        $criteria = $this->getCriteriaBuilder($ruleIds);

        return $this->ruleRepository->getList($criteria);
    }

    /**
     * @param array $ruleIds
     *
     * @return SearchCriteria
     */
    private function getCriteriaBuilder($ruleIds)
    {
        return $this->criteriaBuilder->addFilter(
            Rule::KEY_RULE_ID,
            $ruleIds,
            'in'
        )->create();
    }

    /**
     * @return RuleRepositoryInterface
     */
    public function getRepository()
    {
        return $this->ruleRepository;
    }
}
