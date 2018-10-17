<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: VAIO
 * @date: 2018-04-06 23:21
 */

namespace App\Http\Controllers\Auth;

use App\Http\Common\ResponseUtils;
use App\Http\Controllers\Base\BaseRestController;
use App\Http\Services\Auth\AuthorityService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class AuthorityController extends BaseRestController
{
    /**
     * @var AuthorityService
     */
    private $authorityService;

    /**
     * AuthorityController constructor.
     * @param AuthorityService $authorityService
     */
    public function __construct(AuthorityService $authorityService)
    {
        $this->authorityService = $authorityService;
    }

    public function index(Request $request)
    {
        $page = $this->authorityService->getAuthorityList();

        if ($page->getTotal() > 0) {
            return ResponseUtils::ok("获取数据成功", $page->toArray());
        }

        return ResponseUtils::notFound("暂无数据");
    }

    public function store(Request $request)
    {
        $response = parent::store($request);

        $this->authorityService->clearAuthorityCache();

        return $response;
    }

    public function update(Request $request, ...$pathVariables)
    {
        $response = parent::update($request, ...$pathVariables);

        $this->authorityService->clearAuthorityCache();

        return $response;
    }

    public function destroy(...$pathVariables)
    {
        $bool = $this->authorityService->deleteSelfAndChildren(end($pathVariables));

        if ($bool) {
            $this->authorityService->clearAuthorityCache();
            return ResponseUtils::noContent();
        }

        return ResponseUtils::unprocesableEntity("删除失败");
    }
}
