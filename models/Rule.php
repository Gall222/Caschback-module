<?php

declare(strict_types=1);

namespace common\modules\cashback\models;

use common\modules\cashback\validators\CloseLoanDaysValidator;
use common\modules\cashback\validators\CreditProductValidator;
use common\modules\cashback\validators\WorkDaysValidator;
use Yii;
use yii\db\ActiveRecord;
use yii\validators\NumberValidator;
use yii\validators\RequiredValidator;
use yii\validators\SafeValidator;
use yii\validators\StringValidator;
use yii\validators\TrimValidator;

/**
 * @property int $id
 * @property string $name Название
 * @property float $loan_percent Процент от суммы общего долга на момент закрытия
 * @property string $credit_products_id Кредитные продукты, на которые будет распространяться данное правило
 * @property int $close_loan_count_start Количество закрытых займов (нижний порог)
 * @property int $close_loan_count_end Количество закрытых займов (верхний порог)
 * @property int $start_days_count Начало действия правила - количество дней от DueDate
 * (при отрицательном числе - до DueDate, при положительном - после DueDate)
 * @property int $end_days_count Конец действия правила - количество дней от DueDate
 * @property bool $is_active Включено ли правило
 * @property string $created_at Момент создания записи
 */
final class Rule extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%cashback_rules}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'loan_percent', 'close_loan_count_start',
                'start_days_count', 'credit_products_id'],
                RequiredValidator::class],
            [['name',], TrimValidator::class],
            [['name'], StringValidator::class, 'max' => 255],
            ['loan_percent', NumberValidator::class, 'max' => 99.99],
            [['close_loan_count_start', 'close_loan_count_end', 'start_days_count',
                'end_days_count'], NumberValidator::class],
            [['created_at', 'is_active'], SafeValidator::class],
            [['start_days_count', 'end_days_count'], WorkDaysValidator::class],
            [['close_loan_count_start', 'close_loan_count_end'], CloseLoanDaysValidator::class],
            [['credit_products_id', 'start_days_count', 'end_days_count', 'close_loan_count_start',
                'close_loan_count_end'], CreditProductValidator::class],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Название'),
            'loan_percent' => Yii::t('app', 'Процент от суммы общего долга на момент закрытия'),
            'credit_products_id' => Yii::t('app', 'Кредитные продукты, 
            на которые будет распространяться данное правило'),
            'close_loan_count_start' => Yii::t('app', 'Количество закрытых займов (нижний порог)'),
            'close_loan_count_end' => Yii::t('app', 'Количество закрытых займов (верхний порог)'),
            'start_days_count' => Yii::t('app', 'Начало действия правила - количество дней от DueDate
                (при отрицательном числе - до DueDate, при положительном - после DueDate)'),
            'end_days_count' => Yii::t('app', 'Конец действия правила'),
            'is_active' => Yii::t('app', 'Включено ли правило'),
        ];
    }
}
