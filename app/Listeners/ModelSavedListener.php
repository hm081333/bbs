<?php

namespace App\Listeners;

use App\Events\ModelSavedEvent;
use App\Models\BaseModel;
use App\Utils\Tools;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use MongoDB\Laravel\Relations\HasMany;

class ModelSavedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param ModelSavedEvent $event
     *
     * @return void
     */
    public function handle(ModelSavedEvent $event)
    {
        $model = $event->model;
        //region 保存关联模型
        $model
            ->getRelationData()
            ->each(function (array $data, string $relation) use ($model) {
                if (!empty($data)) {
                    if (str_starts_with(Tools::jsonEncode($data), '{')) $data = [$data];
                    /* @var HasOne|HasMany|BelongsTo|BelongsToMany $relation 模型关联类 */
                    $relation = $model->$relation();
                    // 关联模型主键，用于传递主键更新
                    $relationPrimaryKey = $relation->getModel()->getKeyName();
                    //region 获取关联查询条件，作为默认数据
                    $relationDefaultItem = [];
                    foreach ($relation->getQuery()->getQuery()->wheres as $where) {
                        if (isset($where['operator']) && $where['operator'] == '=' && strpos($where['column'], '.') === false) $relationDefaultItem[$where['column']] = $where['value'];
                    }
                    //endregion
                    collect($data)->each(function (array $item) use ($relation, $relationPrimaryKey, $relationDefaultItem) {
                        $item = array_merge($item, $relationDefaultItem);
                        if ($relation instanceof HasOne || $relation instanceof BelongsTo) {
                            /* @var BaseModel $relationModel 关联模型 */
                            $relationModel = $relation->select(['*'])->firstOrNew();
                        } else if (!empty($item[$relationPrimaryKey])) {
                            /* @var BaseModel $relationModel 关联模型 */
                            $relationModel = $relation->where($relationPrimaryKey, $item[$relationPrimaryKey])->select(['*'])->firstOrNew();
                            unset($item[$relationPrimaryKey]);
                        } else {
                            /* @var BaseModel $relationModel 关联模型 */
                            $relationModel = $relation->make();
                        }
                        $relationModel->saveData($item);
                    });
                }
            });
        $model->clearRelationData();
        // if ($model->isDirty()) {
        //     foreach ($model->getTouchedRelations() as $touchedRelation) {
        //         $model->$touchedRelation->touch();
        //     }
        // }
        //endregion
    }
}
