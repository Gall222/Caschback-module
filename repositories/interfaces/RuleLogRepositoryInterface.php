<?php

declare(strict_types=1);

namespace common\modules\cashback\repositories\interfaces;

use common\modules\cashback\models\RuleLog;

/**
 * Репозиторий для логирования правил модуля "Кэшбэк за закрытие займа"
 */
interface RuleLogRepositoryInterface
{
    public function save(RuleLog $ruleLog): bool;
}
