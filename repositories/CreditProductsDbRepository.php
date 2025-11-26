<?php

declare(strict_types=1);

namespace common\modules\cashback\repositories;

use backend\modules\crmfo_kernel_modules\Entity\CreditProducts;
use common\modules\cashback\repositories\interfaces\CreditProductsRepositoryInterface;

/**
 * {@inheritDoc}
 */
final class CreditProductsDbRepository implements CreditProductsRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function findAll(): array
    {
        return CreditProducts::find()->all();
    }
}
