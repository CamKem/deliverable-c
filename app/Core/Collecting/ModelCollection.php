<?php

namespace App\Core\Collecting;

use app\Core\Database\Model;
use app\Core\Database\Relations\BelongsTo;
use Override;

class ModelCollection extends Collection
{

    // recursively convert the collection to an array
    #[Override]
    public function toArray($data = null): array
    {
        $items = $data ?? $this->items;

        if (empty($items)) {
            return [];
        }

        $array = [];
        foreach ($items as $key => $value) {
            if ($value instanceof Model) {
                $array[$key] = $value->toArray();
                $relations = $value->getRelated();
                if (!empty($relations)) {
                    foreach ($relations as $relation => $models) {
                        if ($models instanceof Model) {
                            $array[$key][$relation] = $models->toArray();
                        } elseif (is_array($models)) {
                            // if the relation is belongsTo, there will only be 1 item
                            if (count($models) === 1 && $value->$relation() instanceof BelongsTo) {
                                // get the first item in the array (keyed by id so used array_values)
                                $array[$key][$relation] = array_values($models)[0]->toArray();
                                $belongsToRelations = array_values($models)[0]->getRelated();
                                if (!empty($belongsToRelations)) {
                                    $array[$key][$relation] = $this->toArray($models);
                                }
                            } else {
                                // NOTE: work out if we want this or not:
                                // if there is only 1 item in the collection,
                                // ensure no further nesting & convert to an array
                                //if ((count($models) === 1) && empty($models[0]->getRelated())) {
                                //    $array[$key][$relation] = $models[0]->toArray();
                                //} else {
                                //    $array[$key][$relation] = $this->toArray($models);
                                //}
                                $array[$key][$relation] = $this->toArray($models);
                            }
                        }
                    }
                }
            }
        }
        return $array;
    }

    // if there is only 1 item in the collection, allow property access
    public function __get($name)
    {
        if (count($this->items) === 1) {
            return $this->items[0]->$name;
        }
    }

}