<?php

declare(strict_types=1);

namespace common\modules\cashback\transformers\interfaces;

use common\modules\cashback\models\Rule;

/**
 * Трансформер для изменения формата данных по кредитным продуктам правила кэшбэка за закрытие займа
 */
interface RuleCreditProductTransformerInterface
{
    /**
     * @param array{
     *     id:string,
     *     name:string,
     *     loan_percent:string,
     *     credit_products_id:array<string>,
     *     close_loan_count:string,
     *     start_days_count:string,
     *     end_days_count:string,
     *     is_active:string} $modelData
     *
     * @return array{
     *      id:string,
     *      name:string,
     *      loan_percent:string,
     *      credit_products_id:string,
     *      close_loan_count:string,
     *      start_days_count:string,
     *      end_days_count:string,
     *      is_active:string}
     */
    public function arrayToJson(array $modelData): array;

    public function jsonToArray(Rule $rule): Rule;

    public function arrayToJsonInModel(Rule $rule): Rule;
}
