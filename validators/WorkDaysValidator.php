<?php

declare(strict_types=1);

namespace common\modules\cashback\validators;

use Yii;
use yii\validators\Validator;

/**
 * Валидатор дней работы правила
 */
final class WorkDaysValidator extends Validator
{
    // phpcs:ignore
    public function validateAttribute($model, $attribute): void
    {
        if ($model->end_days_count === '' || $model->end_days_count === null) {
            return;
        }

        if ($model->end_days_count < $model->start_days_count) {
            $this->addError(
                $model,
                $attribute,
                Yii::t('app', 'Дата окончания работы правила должна быть больше даты начала')
            );
        }
    }
}
