<?php

namespace App\Services;

use App\Dto\InputPageDto;
use App\Dto\InputMenuDto;
use App\Dto\InputUserInfoDto;
use App\Dto\OutputPageDto;
use App\Dto\OutputMenuInfoDto;
use App\Enums\ListType;
use App\Enums\UserType;
use App\Repositories\MenuRepository;
use App\Repositories\RoleMenuRepository;
use App\Exceptions\ParameterException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use stdClass;

class MenuService
{
    protected $menuRepository;
    protected $roleMenuRepository;
    protected $utilService;

    public function __construct(
        MenuRepository $menuRepository,
        RoleMenuRepository $roleMenuRepository,
        UtilService $utilService
    ) {
        $this->menuRepository = $menuRepository;
        $this->roleMenuRepository = $roleMenuRepository;
        $this->utilService = $utilService;
    }

    public function createMenu(InputMenuDto $menuDto)
    {
        $this->menuRepository->createMenu($menuDto);
    }

    public function updateMenu(InputMenuDto $menuDto, int $id)
    {
        $this->menuRepository->updateMenu($menuDto, $id);
    }

    public function getMenuById(int $id)
    {
        $data = $this->menuRepository->getMenuById($id);

        if (empty($data->toArray())) {
            throw new ParameterException(trans('error.user_not_found'), Response::HTTP_BAD_REQUEST);
        }

        $outputMenuInfoDto = new OutputMenuInfoDto(
            $data->id,
            $data->name,
            $data->key,
            $data->url,
            $data->feature,
            $data->status,
            $data->parent,
            $data->weight,
            $data->remark ?? "",
            $data->created_at,
            $data->updated_at,
        );

        return $outputMenuInfoDto;
    }

    public function getMenuList(InputPageDto $pageManagement)
    {
        $data = $this->menuRepository->getMenuListByPage($pageManagement, ListType::ListData);

        $data->transform(function ($item) {
            $menu = new stdClass();
            $menu->id = $item->id;
            $menu->name = $item->name;
            $menu->key = $item->key;
            $menu->url = $item->url;
            $menu->feature = $item->feature;
            $menu->status = $item->status;
            $menu->parent = $item->parent;
            $menu->weight = $item->weight;
            $menu->createdAt = $item->created_at;
            $menu->updatedAt = $item->updated_at;
            return $menu;
        });

        return  $data;
    }

    public function getMenuPage(InputPageDto $pageManagement): OutputPageDto
    {
        $count = $this->menuRepository->getMenuListByPage($pageManagement, ListType::ListCount);
        $pageCount = ceil($count / $pageManagement->getLimit());
        $pageManagement->setCount($count);
        $pageManagement->setPageCount($pageCount);

        $page = $this->utilService->setOutputPageDto($pageManagement);
        return $page;
    }

    public function getMenuAll()
    {
        return $this->menuRepository->getMenuAllList();
    }

    public function deleteMenuByIds(array $ids)
    {
        DB::transaction(function () use ($ids) {
            $this->roleMenuRepository->deleteRoleMenuByMenuIds($ids);
            $this->menuRepository->deleteMenuByIds($ids);
        });
    }
}
