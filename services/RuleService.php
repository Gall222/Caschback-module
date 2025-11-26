<?php

declare(strict_types=1);

namespace common\modules\cashback\services;

use common\modules\cashback\models\Rule;
use common\modules\cashback\repositories\interfaces\RuleRepositoryInterface;
use common\modules\cashback\services\interfaces\RuleLogServiceInterface;
use common\modules\cashback\services\interfaces\RuleServiceInterface;
use common\modules\cashback\transformers\interfaces\RuleCreditProductTransformerInterface;
use common\modules\core\exceptions\ApplicationException;
use common\modules\core\exceptions\entity\EntityNotFoundException;
use common\modules\core\repositories\interfaces\TransactionManagerInterface;
use Yii;
use yii\widgets\ActiveForm;

/**
 * @inheritdoc
 */
final class RuleService implements RuleServiceInterface
{
    private const DAYS_COUNT_FOR_VALIDATION = 9999;

    private RuleRepositoryInterface $ruleRepository;
    private RuleCreditProductTransformerInterface $ruleTransformer;
    private RuleLogServiceInterface $ruleLogService;
    private TransactionManagerInterface $transactionManager;

    public function __construct(
        RuleRepositoryInterface $ruleRepository,
        RuleCreditProductTransformerInterface $ruleTransformer,
        RuleLogServiceInterface $ruleLogService,
        TransactionManagerInterface $transactionManager
    ) {
        $this->ruleRepository = $ruleRepository;
        $this->ruleTransformer = $ruleTransformer;
        $this->ruleLogService = $ruleLogService;
        $this->transactionManager = $transactionManager;
    }

    /**
     * @inheritdoc
     *
     * @throws EntityNotFoundException
     * @throws ApplicationException
     */
    public function save(array $newModelData): void
    {
        $rule = $this->createOrFindModel((int)$newModelData['id']);
        $oldRuleData = clone $rule;
        $rule = $this->loadDataToModel($rule, $newModelData);

        $this->transactionManager->begin();
        try {
            if (!$this->ruleRepository->save($rule)) {
                throw new ApplicationException(Yii::t('app', 'Ошибка при сохранении'));
            }
            if (!$this->ruleLogService->save(
                $this->ruleTransformer->arrayToJsonInModel($oldRuleData),
                $rule
            )) {
                throw new ApplicationException(Yii::t('app', 'Ошибка при логировании'));
            }
            $this->transactionManager->commit();
        } catch (\Throwable $e) {
            $this->transactionManager->rollBack();

            throw new ApplicationException($e->getMessage());
        }
    }

    /**
     * @inheritdoc
     *
     * @throws EntityNotFoundException
     */
    public function validate(array $newModelData): array
    {
        $model = $this->createOrFindModel((int)$newModelData['id']);
        $model = $this->loadDataToModel($model, $newModelData);

        if ($model->end_days_count === '') {
            $model->end_days_count = self::DAYS_COUNT_FOR_VALIDATION;
        }

        return ActiveForm::validate($model);
    }

    /**
     * @inheritdoc
     */
    public function findAll(): array
    {
        return $this->ruleRepository->findAll();
    }

    /**
     * @throws EntityNotFoundException
     * @throws ApplicationException
     */
    public function changeActive(int $ruleId): void
    {
        $rule = $this->ruleRepository->getById($ruleId);
        $rule->is_active = !$rule->is_active;
        $this->save($rule->attributes);
    }

    /**
     * @throws EntityNotFoundException
     * @throws ApplicationException
     */
    public function delete(int $ruleId): void
    {
        $rule = $this->ruleRepository->getById($ruleId);
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->ruleRepository->delete($rule);
            $this->ruleLogService->recordOfDelete($rule);
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();

            throw new ApplicationException($e->getMessage());
        }
    }

    /**
     * Загружает новые данные в модель
     *
     * @param array{
     *    id:string,
     *    name:string,
     *    loan_percent:string,
     *    credit_products_id:array<string>,
     *    close_loan_count:string,
     *    start_days_count:string,
     *    end_days_count:string,
     *    is_active:string} $newData
     */
    private function loadDataToModel(Rule $rule, array $newData): Rule
    {
        $newData = $this->ruleTransformer->arrayToJson($newData);
        $rule->load(['Rule' => $newData]);

        return $rule;
    }

    /**
     * Создает или находит модель в зависимости от наличия id
     * @throws EntityNotFoundException
     */
    public function createOrFindModel(?int $modelId): Rule
    {
        $rule = ($modelId === null || $modelId === 0) ? new Rule() : $this->ruleRepository->getById($modelId);

        return $this->ruleTransformer->jsonToArray($rule);
    }

    /**
     * {@inheritdoc}
     */
    public function findActiveRules(): array
    {
        return $this->ruleRepository->findActiveRules();
    }
}
