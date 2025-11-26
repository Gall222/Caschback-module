<?php

declare(strict_types=1);

namespace common\modules\cashback\services;

use common\modules\cashback\dto\RuleLogDto;
use common\modules\cashback\models\Rule;
use common\modules\cashback\models\RuleLog;
use common\modules\cashback\repositories\interfaces\RuleLogRepositoryInterface;
use common\modules\cashback\services\interfaces\RuleLogServiceInterface;
use common\modules\core\exceptions\entity\EntityNotFoundException;
use common\modules\core\user\repositories\interfaces\UserRepositoryInterface;

/**
 * {@inheritDoc}
 */
final class RuleLogService implements RuleLogServiceInterface
{
    private RuleLogRepositoryInterface $logRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(
        RuleLogRepositoryInterface $logRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->logRepository = $logRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * {@inheritDoc}
     * @throws EntityNotFoundException
     */
    public function save(Rule $oldRuleData, Rule $newRuleData): bool
    {
        $user = $this->userRepository->getAuthUser();
        $changedParams = $this->getChangedParams($oldRuleData, $newRuleData);

        return $this->addRecord(new RuleLogDto(
            $newRuleData->id,
            json_encode($changedParams['oldAttributes']),
            json_encode($changedParams['newAttributes']),
            $user->id
        ));
    }

    /**
     * {@inheritDoc}
     * @throws EntityNotFoundException
     */
    public function recordOfDelete(Rule $rule): bool
    {
        $user = $this->userRepository->getAuthUser();

        return $this->addRecord(new RuleLogDto(
            $rule->id,
            json_encode($rule->attributes),
            json_encode('deleted'),
            $user->id
        ));
    }

    /**
     * Добавляет запись в таблицу логов
     */
    private function addRecord(RuleLogDto $dto): bool
    {
        $log = new RuleLog();
        $log->rule_id = $dto->getRuleId();
        $log->previous_value = $dto->getPreviousValue();
        $log->new_value = $dto->getNewValue();
        $log->user_id = $dto->getUserId();

        return $this->logRepository->save($log);
    }

    /**
     * @return array<array<string, mixed>, array<string, mixed>>
     */
    private function getChangedParams(Rule $oldRuleData, Rule $newRuleData): array
    {
        $oldAttributes = [];
        $newAttributes = [];

        foreach ($oldRuleData->attributes as $key => $oldValue) {
            if ((string)$oldValue !== (string)$newRuleData->attributes[$key]) {
                $oldAttributes[$key] = (string)$oldValue;
                $newAttributes[$key] = (string)$newRuleData->attributes[$key];
            }
        }

        return ['oldAttributes' => $oldAttributes, 'newAttributes' => $newAttributes];
    }
}
