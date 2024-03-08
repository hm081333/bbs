<?php

namespace App\Listeners;

use App\Events\ModelSavedEvent;
use App\Utils\Tools;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
     * @return void
     */
    public function handle(ModelSavedEvent $event)
    {
        $model = $event->model;
        //region 保存关联模型
        if ($relationData = $model->getRelationData()) {
            foreach ($relationData as $relation => $data) {
                if (empty($data)) continue;
                if (substr(Tools::jsonEncode($data), 0, 1) === '{') $data = [$data];
                foreach ($data as $item) {
                    $relation_model = $model->$relation();
                    $wheres = [];
                    foreach ($relation_model->getQuery()->getQuery()->wheres as $where) {
                        if (isset($where['operator']) && $where['operator'] == '=' && strpos($where['column'], '.') === false) $wheres[$where['column']] = $where['value'];
                    }
                    if ($relation_model instanceof HasOne) {
                        $relation_model = $relation_model->select(['*'])->firstOrNew();
                    } else if (!empty($item['id'])) {
                        $relation_model = $relation_model->where('id', $item['id'])->select(['*'])->first();
                        unset($item['id']);
                    } else if (!empty($item['where'])) {
                        $relation_model = $relation_model->where($item['where'])->select(['*'])->first();
                        unset($item['where']);
                    }
                    if (!($relation_model instanceof Model)) {
                        $relation_model = $model->$relation()->make();
                    }
                    $relation_model->saveData(array_merge($item, $wheres));
                    $relation_model->save();
                }
            }
            $model->clearRelationData();
            if ($model->isDirty()) {
                foreach ($model->getTouchedRelations() as $touchedRelation) {
                    $model->$touchedRelation->touch();
                }
            }
        }
        //endregion
    }
}
