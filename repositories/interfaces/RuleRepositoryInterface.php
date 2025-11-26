<?php

declare(strict_types=1);

namespace common\modules\cashback\repositories\interfaces;

use common\modules\cashback\models\Rule;
use common\modules\core\exceptions\entity\EntityNotFoundException;
use common\modules\core\exceptions\RepositoryException;

/**
 * Репозиторий для работы с правилами для модуля "Кэшбэк за закрытие займа"
 */
interface RuleRepositoryInterface
{
    /**
     * @return array<Rule>
     */
    public function findAll(): array;

    public function save(Rule $rule): bool;

    /**
     * @throws EntityNotFoundException
     */
    public function getById(int $ruleId): Rule;

    public function delete(Rule $rule): void;

    /**
     * @return array<Rule>
     */
    public function findActiveRules(): array;

    /**
     * Обновление правила
     *
     * @throws RepositoryException
     */
    public function update(Rule $rule): bool;
}
