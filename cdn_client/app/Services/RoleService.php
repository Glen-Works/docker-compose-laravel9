<?php

namespace App\Services;

use App\Dto\InputPageDto;
use App\Dto\InputRoleDto;
use App\Dto\OutputPageDto;
use App\Dto\OutputRoleListDto;
use App\Enums\ListType;
use App\Repositories\RoleRepository;
use App\Repositories\RoleUserRepository;
use App\Repositories\RoleMenuRepository;
use App\Exceptions\ParameterException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use stdClass;

class RoleService
{

    protected $roleRepository;
    protected $roleUserRepository;
    protected $roleMenuRepository;
    protected $utilService;

    public function __construct(
        RoleRepository $roleRepository,
        RoleUserRepository $roleUserRepository,
        RoleMenuRepository $roleMenuRepository,
        UtilService $utilService
    ) {
        $this->roleRepository = $roleRepository;
        $this->roleUserRepository = $roleUserRepository;
        $this->roleMenuRepository = $roleMenuRepository;
        $this->utilService = $utilService;
    }

    public function createRole(InputRoleDto $roleDto)
    {
        $this->roleRepository->createRole($roleDto);
    }

    public function updateRole(InputRoleDto $roleDto, int $id)
    {
        $this->roleRepository->updateRole($roleDto, $id);
    }

    public function getRoleById(int $id)
    {
        $data = $this->roleRepository->getRoleById($id);

        if (empty($data->toArray())) {
            throw new ParameterException(trans('error.user_not_found'), Response::HTTP_BAD_REQUEST);
        }

        $data->transform(function ($item) {
            $role = new stdClass();
            $role->id = $item->id;
            $role->name = $item->name;
            $role->key = $item->key;
            $role->status = $item->status;
            $role->weight = $item->weight;
            $role->remark = $item->remark;
            $role->createdAt = $item->created_at;
            $role->updatedAt = $item->updated_at;
            return $role;
        });
        return $data;
    }

    public function getRoleList(InputPageDto $pageManagement)
    {
        $data = $this->roleRepository->getRoleListByPage($pageManagement, ListType::ListData);

        $data->transform(function ($item) {
            $role = new stdClass();
            $role->id = $item->id;
            $role->name = $item->name;
            $role->key = $item->key;
            $role->status = $item->status;
            $role->weight = $item->weight;
            $role->createdAt = $item->created_at;
            $role->updatedAt = $item->updated_at;
            return $role;
        });

        return  $data;
    }

    public function getRolePage(InputPageDto $pageManagement): OutputPageDto
    {
        $count = $this->roleRepository->getRoleListByPage($pageManagement, ListType::ListCount);
        $pageCount = ceil($count / $pageManagement->getLimit());
        $pageManagement->setCount($count);
        $pageManagement->setPageCount($pageCount);

        $page = $this->utilService->setOutputPageDto($pageManagement);
        return $page;
    }

    public function deleteRoleById(int $id)
    {
        DB::transaction(function () use ($id) {
            $this->roleUserRepository->deleteRoleUserByRoleId($id);
            $this->roleMenuRepository->deleteRoleMenuByRoleId($id);
            $this->roleRepository->deleteRoleById($id);
        });
    }
}
