<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: VAIO
 * @date: 2018-04-07 16:34
 */

namespace App\Http\Controllers\Base;

use App\Http\Common\Constants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait ModelExtractor
{
    /**
     * @param array $attributes
     * @return Model
     */
    public function getModel($attributes = [])
    {
        $modelClass = $this->getModelClass();
        if (class_exists($modelClass)) {

            $model = new $modelClass($attributes);
            if ($model instanceof Model) {
                return $model;
            }

            throw new \RuntimeException('Class ' . $modelClass . ' not an instance of ' . Model::class);
        }

        throw new ModelNotFoundException('Class ' . $modelClass . ' not found');
    }

    /**
     * @return string
     */
    private function getModelClass()
    {
        $ControllerBaseClassName = class_basename(static::class);
        $ControllerSubNamespace = rtrim(static::class, $ControllerBaseClassName);

        $subNamespace = substr($ControllerSubNamespace, strpos(static::class, "Controllers") + strlen("Controllers"));
        $standardSubNamespace = (empty(trim($subNamespace, '\\')) ? '' : trim($subNamespace, '\\') . '\\');

        $modelBaseClassName = str_replace('Controller', '', $ControllerBaseClassName);

        return Constants::MODELS_NAMESPACE . '\\' . $standardSubNamespace . $modelBaseClassName;
    }

}
