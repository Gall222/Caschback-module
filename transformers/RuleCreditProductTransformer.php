<?php

declare(strict_types=1);

namespace common\modules\cashback\transformers;

use common\modules\cashback\models\Rule;
use common\modules\cashback\transformers\interfaces\RuleCreditProductTransformerInterface;

/**
 * {@inheritdoc}
 */
final class RuleCreditProductTransformer implements RuleCreditProductTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function arrayToJson(array $modelData): array
    {
        if (!is_array($modelData['credit_products_id'])) {
            return $modelData;
        }
        $modelData['credit_products_id'] = json_encode($modelData['credit_products_id']);

        return $modelData;
    }

    public function jsonToArray(Rule $rule): Rule
    {
        if ($rule->credit_products_id === null) {
            return $rule;
        }
        $rule->credit_products_id = json_decode($rule->credit_products_id);

        return $rule;
    }

    public function arrayToJsonInModel(Rule $rule): Rule
    {
        if (!is_array($rule->credit_products_id)) {
            return $rule;
        }
        $rule->credit_products_id = json_encode($rule->credit_products_id);

        return $rule;
    }
}
