<?php

declare(strict_types=1);

namespace common\modules\cashback\validators;

use common\modules\cashback\dto\RuleValidateDto;
use common\modules\cashback\models\Rule;
use common\modules\cashback\services\interfaces\RuleServiceInterface;
use Yii;
use yii\validators\Validator;

/**
 * Валидатор проверяет пересечение правил для кредитных продуктов по периоду доступности
 */
final class CreditProductValidator extends Validator
{
    // phpcs:ignore
    public function validateAttribute($model, $attribute): void
    {
        /** @var Rule $model */
        $ruleService = Yii::$container->get(RuleServiceInterface::class);

        if ($this->isRuleHasEmptyValidFields($model)) {
            return;
        }

        $rules = $ruleService->findAll();
        foreach ($rules as $rule) {
            if ($rule->id === $model->id) {
                continue;
            }
            $this->checkRule(new RuleValidateDto($rule, json_decode($model->credit_products_id), $model, $attribute));
        }
    }

    /**
     * Есть ли пустые поля кредитных продуктов или даты начала действия правила
     */
    private function isRuleHasEmptyValidFields(Rule $rule): bool
    {
        $modelCreditProducts = json_decode($rule->credit_products_id);

        return ($modelCreditProducts === '' || $modelCreditProducts === null) ||
            $rule->start_days_count === '' || $rule->close_loan_count_start === '';
    }

    /**
     * Проверяет правило на пересечение срока действия
     */
    private function checkRule(RuleValidateDto $dto): void
    {
        $ruleForCompare = $dto->getRuleForCompare();
        $currentRule = $dto->getCurrentRule();
        $creditProducts = json_decode($ruleForCompare->credit_products_id);
        foreach ($creditProducts as $productId) {
            /** Если кредитные продукты совпали */
            if (!in_array($productId, $dto->getModelCreditProducts())) {
                continue;
            }

            if (($this->isNewRuleStartInAnotherRulePeriod($currentRule, $ruleForCompare) ||
                    $this->isNewRuleAcrossRulePeriod($currentRule, $ruleForCompare)) &&
                ($this->isCloseDayStartInAnotherRuleDays($currentRule, $ruleForCompare) ||
                    $this->isCloseDayAcrossRulePeriod($currentRule, $ruleForCompare))
            ) {
                $this->addError(
                    $currentRule,
                    $dto->getAttribute(),
                    Yii::t('app', 'Период действия правила пересекается с уже существующим: ')
                    . $ruleForCompare->name
                );
            }
        }
    }

    /** Новое правило начинает работать в период работы существующего правила */
    private function isNewRuleStartInAnotherRulePeriod(Rule $currentRule, Rule $ruleForCompare): bool
    {
        return $currentRule->start_days_count >= $ruleForCompare->start_days_count &&
            ($ruleForCompare->end_days_count === null ||
                $currentRule->start_days_count <= $ruleForCompare->end_days_count);
    }

    /** Новое правило начинает работать до начала существующего, но пересекает его */
    private function isNewRuleAcrossRulePeriod(Rule $currentRule, Rule $ruleForCompare): bool
    {
        return $currentRule->start_days_count < $ruleForCompare->start_days_count &&
            ($currentRule->end_days_count === null ||
                $currentRule->end_days_count >= $ruleForCompare->start_days_count);
    }

    /** Начинается ли количество закрытых займов в периоде количества закрытых займов другого правила */
    private function isCloseDayStartInAnotherRuleDays(Rule $currentRule, Rule $ruleForCompare): bool
    {
        return $currentRule->close_loan_count_start >= $ruleForCompare->close_loan_count_start &&
            ($ruleForCompare->close_loan_count_end === null ||
                $currentRule->close_loan_count_start <= $ruleForCompare->close_loan_count_end);
    }

    /** Пересекает ли количество закрытых займов в периоде количества закрытых займов другого правила, */
    private function isCloseDayAcrossRulePeriod(Rule $currentRule, Rule $ruleForCompare): bool
    {
        return $currentRule->close_loan_count_start < $ruleForCompare->close_loan_count_start &&
            ($currentRule->close_loan_count_end === null ||
                $currentRule->close_loan_count_end >= $ruleForCompare->close_loan_count_start);
    }
}
