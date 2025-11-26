<?php

declare(strict_types=1);

namespace common\modules\cashback\validators;

use Yii;
use yii\validators\Validator;

/**
 * Валидатор количества закрытых займов
 */
final class CloseLoanDaysValidator extends Validator
{
    // phpcs:ignore
    public function validateAttribute($model, $attribute): void
    {
        if ($model->close_loan_count_end === '' || $model->close_loan_count_end === null) {
            return;
        }

        if ($model->close_loan_count_end < $model->close_loan_count_start) {
            $this->addError(
                $model,
                $attribute,
                Yii::t('app', 'Верхний порог количества закрытых займов должен быть больше нижнего')
            );
        }
    }
}
