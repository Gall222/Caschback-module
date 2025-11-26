<?php

declare(strict_types=1);

namespace common\modules\cashback\services\interfaces;

use backend\modules\crmfo_kernel_modules\Entity\Questionnaire;

/**
 * Сервис модуля "Кэшбэк за закрытие займа"
 */
interface ServiceInterface
{
    public function getBonuses(Questionnaire $questionnaire, int $totalDebt): ?float;

    public function addBonuses(Questionnaire $questionnaire, int $totalDebt): void;

    /**
     * Получение суммы кешбека для переменной
     *
     * @throws \Exception
     */
    public function getCashbackAmount(Questionnaire $questionnaire): float;
}
