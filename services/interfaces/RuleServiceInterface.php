<?php

declare(strict_types=1);

namespace common\modules\cashback\services\interfaces;

use common\modules\cashback\models\Rule;

/**
 * Сервис для работы с правилами для модуля "Кэшбэк за закрытие займа"
 */
interface RuleServiceInterface
{
    /**
     * @param array{
     *    id:string,
     *    name:string,
     *    loan_percent:string,
     *    credit_products_id:array<string>,
     *    close_loan_count:string,
     *    start_days_count:string,
     *    end_days_count:string,
     *    is_active:string} $newModelData
     */
    public function save(array $newModelData): void;

    /**
     * Возвращает модель с новыми данными
     *
     * @param array{
     *    id:string,
     *    name:string,
     *    loan_percent:string,
     *    credit_products_id:array<string>,
     *    close_loan_count:string,
     *    start_days_count:string,
     *    end_days_count:string,
     *    is_active:string} $newModelData
     *
     * @return array<mixed>
     */
    public function validate(array $newModelData): array;

    /**
     * @return array<Rule>
     */
    public function findAll(): array;

    public function changeActive(int $ruleId): void;

    public function delete(int $ruleId): void;

    public function createOrFindModel(?int $modelId): Rule;

    /**
     * @return array<Rule>
     */
    public function findActiveRules(): array;
}
