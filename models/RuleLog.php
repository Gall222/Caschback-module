<?php

declare(strict_types=1);

namespace common\modules\cashback\models;

use yii\db\ActiveRecord;
use yii\validators\NumberValidator;
use yii\validators\RequiredValidator;
use yii\validators\SafeValidator;
use yii\validators\StringValidator;

/**
 * @property int $id
 * @property int $rule_id Id правила
 * @property string|null $previous_value Предыдущее значение
 * @property string|null $new_value Новое значение
 * @property int $user_id Id пользователя
 * @property string $created_at Момент создания записи
 */
final class RuleLog extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%cashback_rules_logs}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['rule_id',], RequiredValidator::class],
            [['created_at'], SafeValidator::class],
            [['user_id', 'rule_id'], NumberValidator::class],
            [['previous_value', 'new_value'], StringValidator::class],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'rule_id' => 'Id правила',
            'previous_value' => 'Предыдущее значение',
            'new_value' => 'Новое значение',
            'user_id' => 'Id пользователя',
            'created_at' => 'Момент создания записи',
        ];
    }
}
