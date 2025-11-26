<?php

declare(strict_types=1);

namespace common\modules\cashback\dto;

/**
 * Данные для сохранения лога правил
 */
final class RuleLogDto
{
    private int $ruleId;
    private string $previousValue;
    private string $newValue;
    private int $userId;

    public function __construct(
        int $ruleId,
        string $previousValue,
        string $newValue,
        int $userId
    ) {
        $this->ruleId = $ruleId;
        $this->previousValue = $previousValue;
        $this->newValue = $newValue;
        $this->userId = $userId;
    }

    public function getRuleId(): int
    {
        return $this->ruleId;
    }

    public function getPreviousValue(): string
    {
        return $this->previousValue;
    }

    public function getNewValue(): string
    {
        return $this->newValue;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
