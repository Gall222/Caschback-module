<?php

declare(strict_types=1);

namespace common\modules\cashback\repositories;

use common\modules\cashback\models\RuleLog;
use common\modules\cashback\repositories\interfaces\RuleLogRepositoryInterface;

/**
 * {@inheritdoc}
 */
final class RuleLogDbRepository implements RuleLogRepositoryInterface
{
    public function save(RuleLog $ruleLog): bool
    {
        return $ruleLog->save();
    }
}
