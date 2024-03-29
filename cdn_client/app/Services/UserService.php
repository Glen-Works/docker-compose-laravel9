<?php

namespace App\Services;

use App\Dto\InputPageDto;
use App\Dto\InputUserDto;
use App\Dto\InputUserSelfDto;
use App\Dto\InputUserPasswordDto;
use App\Dto\InputUserInfoDto;
use App\Dto\OutputPageDto;
use App\Dto\OutputUserInfoDto;
use App\Enums\ListType;
use App\Enums\UserType;
use App\Repositories\UserRepository;
use App\Repositories\RoleUserRepository;
use App\Exceptions\ParameterException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use stdClass;

class UserService
{
    protected $userRepository;
    protected $roleUserRepository;
    protected $utilService;

    public function __construct(
        UserRepository $userRepository,
        RoleUserRepository $roleUserRepository,
        UtilService $utilService
    ) {
        $this->userRepository = $userRepository;
        $this->roleUserRepository = $roleUserRepository;
        $this->utilService = $utilService;
    }

    public function createUser($userDto)
    {
        DB::transaction(function () use ($userDto) {
            $id = $this->userRepository->createUser($userDto);
            $roleUserList = $this->utilService->getStoreKeyValue($id,  $userDto->roleUser,  "user_id", "role_id");

            if (count($roleUserList) > 0) {
                $this->roleUserRepository->createRoleUserList($roleUserList);
            }
        });
    }

    public function updateUser($userDto, int $id)
    {
        DB::transaction(function () use ($userDto, $id) {
            $this->userRepository->updateUser($userDto, $id);
            $this->roleUserRepository->deleteRoleUserByUserIds([$id]);
            $roleUserList = $this->utilService->getStoreKeyValue($id,  $userDto->roleUser,  "user_id", "role_id");

            if (count($roleUserList) > 0) {
                $this->roleUserRepository->createRoleUserList($roleUserList);
            }
        });
    }

    public function updateUserSelf(InputUserSelfDto $userDto, int $id)
    {
        $this->userRepository->updateUserSelf($userDto, $id);
    }

    public function updateUserPassword(InputUserPasswordDto $userDto, int $id)
    {
        // 確認新密碼
        if (($userDto->newPassword != $userDto->checkPassword)) {
            throw new ParameterException(trans('error.password'), Response::HTTP_BAD_REQUEST);
        }

        $this->userRepository->updateUserPassword($userDto->newPassword, $id);
    }

    public function getUserById(int $id)
    {
        $data = $this->userRepository->getUserById($id);

        if (empty($data)) {
            throw new ParameterException(trans('error.user_not_found'), Response::HTTP_BAD_REQUEST);
        }

        // return gettype($data->updated_at);

        $userInfo = new OutputUserInfoDto(
            $data->id,
            $data->name,
            $data->email,
            $data->status,
            $data->user_type,
            $data->login_ip ?? "",
            $data->password_update_time,
            $data->login_time,
            $data->created_at,
            $data->updated_at,
            $data->remark ?? "",
        );

        return $userInfo;
    }

    public function getUserList(InputPageDto $pageManagement): Collection
    {
        $data = $this->userRepository->getUserListByPage($pageManagement, ListType::ListData);

        $data->transform(function ($item) {
            $user = new stdClass();
            $user->id = $item->id;
            $user->name = $item->name;
            $user->email = $item->email;
            $user->status = $item->status;
            $user->userType = $item->user_type;
            $user->loginIp = $item->login_ip;
            $user->loginTime = $item->login_time;
            $user->createdAt = $item->created_at;
            $user->updatedAt = $item->updated_at;
            return $user;
        });

        return  $data;
    }

    public function getUserPage(InputPageDto $pageManagement): OutputPageDto
    {
        $count = $this->userRepository->getUserListByPage($pageManagement, ListType::ListCount);
        $pageCount = ceil($count / $pageManagement->getLimit());
        $pageManagement->setCount($count);
        $pageManagement->setPageCount($pageCount);

        $page = $this->utilService->setOutputPageDto($pageManagement);
        return $page;
    }

    public function deleteUserByIds(array $ids)
    {
        DB::transaction(function () use ($ids) {
            $this->roleUserRepository->deleteRoleUserByUserIds($ids);
            $this->userRepository->deleteUserByIds($ids);
        });
    }

    public function getRoleUserByUserId(int $id)
    {
        $data = $this->roleUserRepository->getRoleUserByUserId($id);

        if (empty($data)) {
            return [];
        }

        return  $data->pluck("role_id")->all();
    }
}
