<?php

declare(strict_types=1);

namespace common\modules\cashback\services\interfaces;

use common\modules\cashback\models\Rule;

/**
 * Сервис для логирования правил модуля "Кэшбэк за закрытие займа"
 */
interface RuleLogServiceInterface
{
    /**
     * Сохраняет измененные поля в json формате
     */
    public function save(Rule $oldRuleData, Rule $newRuleData): bool;

    /**
     * Запись об удалении правила, в new_value - deleted
     */
    public function recordOfDelete(Rule $rule): bool;
}
