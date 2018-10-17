<?php
/**
 * Created by PhpStorm.
 * @description: RestFull api基类
 * @author: FS
 * @date: 2018-04-10 12:27
 */

namespace App\Http\Controllers\Base;

use App\Http\Common\ResponseUtils;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


abstract class BaseRestController extends Controller
{
    use SelectServiceProvider, Transferable;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $selectService = $this->getSelectService($request);

        $page = $selectService->selectPageList();

        if ($page->getTotal() > 0) {

            $page->setList($this->transform($page->getList()));

            return ResponseUtils::ok("获取数据成功", $page->toArray());
        }

        return ResponseUtils::notFound("暂无数据");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $model = $this->getModel();

        $model->fill($request->post());

        $bool = $model->save();

        if ($bool) {
            return ResponseUtils::created("创建成功", $model);
        }

        return ResponseUtils::unprocesableEntity("创建失败", $model);
    }

    /**
     * Display the specified resource.
     *
     * @param array $pathVariables
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(...$pathVariables)
    {
        $model = $this->getModel()->find(end($pathVariables));

        if ($model != null) {
            return ResponseUtils::created("获取数据成功", $model);
        }

        return ResponseUtils::notFound("暂无数据");
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param array $pathVariables
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, ...$pathVariables)
    {
        $lastId = end($pathVariables);

        $model = $this->getModel()->find($lastId);

        if ($model == null) {
            return ResponseUtils::notFound("该记录不存在", $lastId);
        }

        $model->fill($request->post());

        $bool = $model->update();

        if ($bool) {
            return ResponseUtils::created("更新成功", $model);
        }

        return ResponseUtils::unprocesableEntity("更新失败", $model);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param array $pathVariables
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(...$pathVariables)
    {
        $lastId = end($pathVariables);

        if (strpos($lastId, ',')) {
            $bool = $this->getModel()::destroy(explode(',', $lastId)) > 0;
        } else {
            $model = $this->getModel()->find($lastId);

            if ($model == null) {
                return ResponseUtils::notFound("该记录不存在", $lastId);
            }

            $bool = $model->delete();
        }

        if ($bool) {
            return ResponseUtils::noContent();
        }

        return ResponseUtils::unprocesableEntity("删除失败");
    }

}
