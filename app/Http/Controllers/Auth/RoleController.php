<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Base\BaseRestController;
use App\Http\Models\Auth\Authority;
use App\Http\Services\Auth\AuthorityService;
use App\Http\Services\Auth\RoleService;
use Illuminate\Http\Request;

class RoleController extends BaseRestController
{
    /**
     * @var RoleService
     */
    private $roleService;

    /**
     * @var AuthorityService
     */
    private $authorityService;

    /**
     *
     * /**
     * RoleController constructor.
     * @param RoleService $roleService
     * @param AuthorityService $authorityService
     */
    public function __construct(RoleService $roleService, AuthorityService $authorityService)
    {
        $this->roleService = $roleService;
        $this->authorityService = $authorityService;
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
        $response = parent::destroy(...$pathVariables);

        $this->authorityService->clearAuthorityCache();

        return $response;
    }


}
