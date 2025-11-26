<?php

declare(strict_types=1);

namespace common\modules\cashback\repositories\interfaces;

use backend\modules\crmfo_kernel_modules\Entity\CreditProducts;

/**
 * Репозиторий для работы с кредитными продуктами
 */
interface CreditProductsRepositoryInterface
{
    /**
     * @return array<CreditProducts>
     */
    public function findAll(): array;
}
