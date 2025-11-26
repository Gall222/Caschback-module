<?php

declare(strict_types=1);

namespace common\modules\cashback\services;

use backend\modules\crmfo_kernel_modules\Entity\Questionnaire;
use common\modules\bonus_account\constants\BonusAccountReasons;
use common\modules\bonus_account\services\interfaces\AccountServiceInterface;
use common\modules\cashback\models\Rule;
use common\modules\cashback\services\interfaces\RuleServiceInterface;
use common\modules\cashback\services\interfaces\ServiceInterface;
use DateTimeImmutable;

/**
 * {@inheritdoc}
 */
final class Service implements ServiceInterface
{
    private RuleServiceInterface $ruleService;
    private AccountServiceInterface $bonusAccountService;

    public function __construct(
        RuleServiceInterface $ruleService,
        AccountServiceInterface $bonusAccountService
    ) {
        $this->ruleService = $ruleService;
        $this->bonusAccountService = $bonusAccountService;
    }

    /**
     * @throws \Exception
     */
    public function getBonuses(Questionnaire $questionnaire, int $totalDebt): ?float
    {
        $activeRules = $this->ruleService->findActiveRules();

        foreach ($activeRules as $rule) {
            if (!$this->isClosedLoanCountFit((int)$questionnaire->countPaidClosed(), $rule) ||
                !$this->isCreditProductInList($rule, $questionnaire->credit_product_id) ||
                !$this->isInRuleDiapason($questionnaire->getPayday(), $rule)
            ) {
                continue;
            }

            return round(($totalDebt * $rule->loan_percent) / 100, 2);
        }

        return null;
    }

    /**
     * @throws \Exception
     */
    public function addBonuses(Questionnaire $questionnaire, int $totalDebt): void
    {
        $bonuses = $this->getBonuses($questionnaire, $totalDebt);

        if ($bonuses === null) {
            return;
        }

        $this->bonusAccountService->increaseAmountBalance(
            $questionnaire->customer->defaultPhone->number_phone,
            $bonuses,
            BonusAccountReasons::CASHBACK
        );
    }

    /**
     * Получение суммы кешбека для переменной
     *
     * @throws \Exception
     */
    public function getCashbackAmount(Questionnaire $questionnaire): float
    {
        $amount = $this->getBonuses($questionnaire, (int)$questionnaire->account->getTotalDebtToday()) ?: 0;

        return $amount > 0 ? $amount : 0;
    }

    /**
     * Подходит ли количество закрытых займов клиента по правилу
     */
    private function isClosedLoanCountFit(int $questionnaireClosedLoansCount, Rule $rule): bool
    {
        return $questionnaireClosedLoansCount >= $rule->close_loan_count_start &&
            ($rule->close_loan_count_end === null ||
                $questionnaireClosedLoansCount <= $rule->close_loan_count_end);
    }

    /**
     * Находится ли кредитный продукт заявки в списке из правила
     */
    private function isCreditProductInList(Rule $rule, int $questionnaireCreditProductId): bool
    {
        $ruleCreditProducts = json_decode($rule->credit_products_id);

        return in_array($questionnaireCreditProductId, $ruleCreditProducts);
    }

    /**
     * Находится ли день оплаты в рабочем диапазоне правила
     */
    private function isInRuleDiapason(?DateTimeImmutable $payDay, Rule $rule): bool
    {
        if ($payDay === null) {
            return false;
        }
        $now = (new DateTimeImmutable())->setTime(0, 0, 0);
        $difference = date_diff($payDay, $now);
        $daysToDueDay = $difference->invert ? -$difference->days : $difference->days;

        return $rule->start_days_count <= $daysToDueDay &&
            ($rule->end_days_count === null ||
                $rule->end_days_count >= $daysToDueDay);
    }
}
