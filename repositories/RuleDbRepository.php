<?php

declare(strict_types=1);

namespace common\modules\cashback\repositories;

use common\modules\cashback\models\Rule;
use common\modules\cashback\repositories\interfaces\RuleRepositoryInterface;
use common\modules\core\exceptions\entity\EntityNotFoundException;
use common\modules\core\exceptions\RepositoryException;
use Yii;
use yii\db\StaleObjectException;

/**
 * @inheritdoc
 */
final class RuleDbRepository implements RuleRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function findAll(): array
    {
        return Rule::find()->all();
    }

    public function save(Rule $rule): bool
    {
        return $rule->save();
    }

    /**
     * @inheritdoc
     */
    public function getById(int $ruleId): Rule
    {
        /** @var Rule $rule */
        $rule = Rule::findOne($ruleId);

        if ($rule === null) {
            throw new EntityNotFoundException(Yii::t('app', 'Правило не найдено: ' . $ruleId));
        }

        return $rule;
    }

    /**
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function delete(Rule $rule): void
    {
        $rule->delete();
    }

    /**
     * @inheritdoc
     */
    public function findActiveRules(): array
    {
        return Rule::find()->where(['is_active' => true])->all();
    }

    /**
     * @inheritDoc
     */
    public function update(Rule $rule): bool
    {
        if ($rule->save() === true) {
            return true;
        }

        throw new RepositoryException('Не удалось добавить продукт в правило кэшбэка ' .
            $rule->getErrorSummary(false)[0]);
    }
}
