<?php

declare(strict_types=1);

namespace common\modules\cashback\dto;

use common\modules\cashback\models\Rule;

/** Данные для валидации кредитных продуктов правил модуля "Кэшбэк за закрытие займа" */
final class RuleValidateDto
{
    /** Правило, с которым происходит сравнение */
    private Rule $ruleForCompare;

    /**
     * Массив выбранных кредитных продуктов
     * @var array<string> $modelCreditProducts
     */
    private array $modelCreditProducts;

    /** Проверяемое (текущее) правило */
    private Rule $currentRule;

    /** Атрибут валидатора */
    private string $attribute;

    /**
     * @param array<string> $modelCreditProducts
     */
    public function __construct(
        Rule $ruleForCompare,
        array $modelCreditProducts,
        Rule $currentRule,
        string $attribute
    ) {
        $this->ruleForCompare = $ruleForCompare;
        $this->modelCreditProducts = $modelCreditProducts;
        $this->currentRule = $currentRule;
        $this->attribute = $attribute;
    }

    public function getRuleForCompare(): Rule
    {
        return $this->ruleForCompare;
    }

    /**
     * @return array<string>
     */
    public function getModelCreditProducts(): array
    {
        return $this->modelCreditProducts;
    }

    public function getCurrentRule(): Rule
    {
        return $this->currentRule;
    }

    public function getAttribute(): string
    {
        return $this->attribute;
    }
}
