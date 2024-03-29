<?php

namespace App\Http\Controllers;

use App\Dto\InputRoleMenuDto;
use App\Dto\OutputRoleListDto;
use App\Dto\OutputRoleInfoMenuDto;
use App\Services\ResponseService;
use App\Services\RoleService;
use App\Services\UtilService;
use App\Services\CacheMamageService;

use Illuminate\Http\Request;

class RoleController extends BaseController
{
    private $roleService;
    private $utilService;
    private $responseService;
    protected $cacheMamageService;

    public function __construct(
        RoleService $roleService,
        UtilService $utilService,
        ResponseService $responseService,
        CacheMamageService $cacheMamageService
    ) {
        $this->roleService = $roleService;
        $this->utilService = $utilService;
        $this->responseService = $responseService;
        $this->cacheMamageService = $cacheMamageService;
    }

    /**
     * @OA\Get(
     *  tags={"Role"},
     *  path="/api/v1/role",
     *  summary="角色清單 (Role List)",
     *  security={{"Authorization":{}}},
     *  @OA\Parameter(parameter="page",in="query",name="page",description="頁數",@OA\Schema(type="integer",default="1")),
     *  @OA\Parameter(parameter="pageCount",in="query",name="pageCount",description="總頁數",@OA\Schema(type="integer")),
     *  @OA\Parameter(parameter="count",in="query",name="count",description="總筆數",@OA\Schema(type="integer")),
     *  @OA\Parameter(parameter="limit",in="query",name="limit",description="每頁筆數",@OA\Schema(type="integer",default="10")),
     *  @OA\Parameter(parameter="search",in="query",name="search[name]",description="搜尋條件",@OA\Schema(type="string")),
     *  @OA\Parameter(parameter="sort",in="query",name="sort",description="排序", explode=true,
     *      @OA\Schema(type="string",enum = \App\Enums\ListOrderByType::class)),
     *  @OA\Parameter(parameter="sortColumn",in="query",name="sortColumn",description="排序欄位", explode=true,
     *      @OA\Schema(type="string",enum = {"id","name","status","weight","createdAt","updatedAt"})),
     *  @OA\Response(response=200,description="OK",@OA\JsonContent(examples={"myname":@OA\Schema(ref="#/components/examples/ShowRoleList", example="ShowRoleList")})),
     *  @OA\Response(response=401,description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ResponseUnauthorized")),
     *  @OA\Response(response=500,description="Server Error",@OA\JsonContent(ref="#/components/schemas/responseError")),
     * )
     * @return OutputRoleListDto
     */
    public function index(Request $request)
    {
        //取得api data
        $data = $request->query();

        //頁數初始化
        $pageManagement = $this->utilService->initPage($data ?? null);

        //取得List, page
        $roleList = $this->roleService->getRoleList($pageManagement);
        $rolePage = $this->roleService->getRolePage($pageManagement);

        $outputRoleListDto = new OutputRoleListDto($roleList, $rolePage);
        return $this->responseService->responseJson($outputRoleListDto);
    }


    /**
     * @OA\Post(
     *  tags={"Role"},
     *  path="/api/v1/role",
     *  summary="新增權限(Role Create)",
     *  security={{"Authorization":{}}},
     *  @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/CreateRole")),
     *  @OA\Response(response=200,description="OK",@OA\JsonContent(ref="#/components/schemas/ResponseSuccess")),
     *  @OA\Response(response=401,description="Unauthorized",@OA\JsonContent(ref="#/components/schemas/ResponseUnauthorized")),
     *  @OA\Response(response=500,description="Server Error",@OA\JsonContent(ref="#/components/schemas/responseError")),
     * )
     */
    public function store(Request $request)
    {
        //取得api data
        $data = $request->all();

        //驗證
        $this->utilService->ColumnValidator($data, [
            'name' => 'required|unique:roles|max:100',
            'key' => 'required|unique:roles|max:150',
            'status' => 'required|boolean',
            'weight' => 'integer|nullable',
            'remark' => 'string|max:5000|nullable',
            'roleMenu' => 'array|nullable',
        ]);

        $roleDto = new InputRoleMenuDto(
            $data["name"],
            $data["key"],
            $data["status"],
            $data["weight"] ?? 0,
            $data["remark"] ?? "",
            $data["roleMenu"] ?? []
        );

        $this->roleService->createRole($roleDto);
        return $this->responseService->responseJson();
    }

    /**
     * @OA\Get(
     *  tags={"Role"},
     *  path="/api/v1/role/{id}",
     *  summary="權限資料 (Role Info)",
     *  security={{"Authorization":{}}},
     *  @OA\Parameter(parameter="page",in="path",name="id",required=true,description="id",@OA\Schema(type="integer")),
     *  @OA\Response(response=200,description="OK",@OA\JsonContent(examples={"myname":@OA\Schema(ref="#/components/examples/ShowRoleById", example="ShowRoleById")})),
     *  @OA\Response(response=401,description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ResponseUnauthorized")),
     *  @OA\Response(response=500,description="Server Error",@OA\JsonContent(ref="#/components/schemas/responseError")),
     * )
     */
    public function show(Request $request, $id)
    {
        parent::show($request, $id);
        $roleInfo = $this->roleService->getRoleById($id);
        $roleMenu = $this->roleService->getRoleMenuByRoleId($id);

        $data = new OutputRoleInfoMenuDto($roleInfo, $roleMenu);
        return $this->responseService->responseJson($data);
    }

    /**
     * @OA\Get(
     *  tags={"Role"},
     *  path="/api/v1/role/all",
     *  summary="Role所有清單 (Role All List)",
     *  security={{"Authorization":{}}},
     *  @OA\Response(response=200,description="OK",@OA\JsonContent(examples={"myname":@OA\Schema(ref="#/components/examples/AllRole", example="AllRole")})),
     *  @OA\Response(response=401,description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ResponseUnauthorized")),
     *  @OA\Response(response=500,description="Server Error",@OA\JsonContent(ref="#/components/schemas/responseError")),
     * )
     * @return OutputMenuListDto
     */
    public function getRoleAllList(Request $request)
    {
        $data["roleList"] = $this->roleService->getRoleAll();
        return $this->responseService->responseJson($data);
    }

    /**
     * @OA\Put(
     *  tags={"Role"},
     *  path="/api/v1/role/{id}",
     *  summary="修改權限(Role Update)",
     *  security={{"Authorization":{}}},
     *  @OA\Parameter(parameter="page",in="path",name="id",required=true,description="id",@OA\Schema(type="integer")),
     *  @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/UpdateRole")),
     *  @OA\Response(response=200,description="OK",@OA\JsonContent(ref="#/components/schemas/ResponseSuccess")),
     *  @OA\Response(response=401,description="Unauthorized",@OA\JsonContent(ref="#/components/schemas/ResponseUnauthorized")),
     *  @OA\Response(response=500,description="Server Error",@OA\JsonContent(ref="#/components/schemas/responseError")),
     * )
     */
    public function update(Request $request, $id)
    {
        parent::update($request, $id);

        //取得api data
        $data = $request->all();

        //驗證
        $this->utilService->ColumnValidator($data, [
            'name' => 'required|max:100|unique:roles,name,' . $id, //當id不存在,在debug模式會顯示name 已經存在
            'key' => 'required|max:150|unique:roles,key,' . $id, //當id不存在,在debug模式會顯示key 已經存在
            'status' => 'required|boolean',
            'weight' => 'integer|nullable',
            'remark' => 'string|max:5000|nullable',
            'roleMenu' => 'array|nullable',
        ]);

        $roleDto = new InputRoleMenuDto(
            $data["name"],
            $data["key"],
            $data["status"],
            $data["weight"] ?? 0,
            $data["remark"] ?? "",
            $data["roleMenu"] ?? [],
        );

        $this->roleService->updateRole($roleDto, $id);

        //刪除 所有人的menu cache
        $this->cacheMamageService->removeCacheMenuAllUser();

        return $this->responseService->responseJson();
    }

    /**
     * @OA\Delete(
     *  tags={"Role"},
     *  path="/api/v1/role/{id}",
     *  summary="刪除權限(Role Delete)",
     *  security={{"Authorization":{}}},
     *  @OA\Parameter(parameter="page",in="path",name="id",required=true,description="id",@OA\Schema(type="integer")),
     *  @OA\Response(response=200,description="OK",@OA\JsonContent(ref="#/components/schemas/ResponseSuccess")),
     *  @OA\Response(response=401,description="Unauthorized",@OA\JsonContent(ref="#/components/schemas/ResponseUnauthorized")),
     *  @OA\Response(response=500,description="Server Error",@OA\JsonContent(ref="#/components/schemas/responseError")),
     * )
     */
    public function destroy(Request $request, $id)
    {
        parent::destroy($request, $id);
        $this->roleService->deleteRoleByIds([$id]);

        //刪除 所有人的menu cache
        $this->cacheMamageService->removeCacheMenuAllUser();

        return $this->responseService->responseJson();
    }

    /**
     * @OA\Delete(
     *  tags={"Role"},
     *  path="/api/v1/role/multiple/ids",
     *  summary="刪除多個權限(Role Delete)",
     *  security={{"Authorization":{}}},
     *  @OA\Parameter(parameter="id[]",in="query",name="id[]",description="id",@OA\Schema(type="array",@OA\Items(type="integer"))),
     *  @OA\Response(response=200,description="OK",@OA\JsonContent(ref="#/components/schemas/ResponseSuccess")),
     *  @OA\Response(response=401,description="Unauthorized",@OA\JsonContent(ref="#/components/schemas/ResponseUnauthorized")),
     *  @OA\Response(response=500,description="Server Error",@OA\JsonContent(ref="#/components/schemas/responseError")),
     * )
     */
    public function destroyMultiple(Request $request)
    {
        $data = $request->all();

        //驗證
        $this->utilService->ColumnValidator($data, [
            'id' => 'required|array',
        ]);

        $deleteIds = $data["id"];
        $this->roleService->deleteRoleByIds($deleteIds);

        //刪除 所有人的menu cache
        $this->cacheMamageService->removeCacheMenuAllUser();

        return $this->responseService->responseJson();
    }
}
