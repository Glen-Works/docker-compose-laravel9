<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Cache;

class AuthorizationService
{

    protected const CACHE_AUTH_USER_MENU = "CACHE_AUTH_USER_MENU";
    protected const CACHE_TIME = 7200;

    private const AUTU_ROUTE_MENU_COMPARISON = array(
        "UserController@index" => "user_list",
        "UserController@show" => "user_info",
        "UserController@store" => "user_create",
        "UserController@update" => "user_update",
        "UserController@destroy" => "user_delete",
        "RoleController@index" => "role_list",
        "RoleController@show" => "role_info",
        "RoleController@store" => "role_create",
        "RoleController@update" => "role_update",
        "RoleController@destroy" => "role_delete",
        "MenuController@index" => "menu_list",
        "MenuController@show" => "menu_info",
        "MenuController@store" => "menu_create",
        "MenuController@update" => "menu_update",
        "MenuController@destroy" => "menu_delete",
    );

    private $userRepository;
    private $cacheService;

    public function __construct(
        UserRepository $userRepository,
        CacheService $cacheService
    ) {
        $this->userRepository = $userRepository;
        $this->cacheService = $cacheService;
    }

    public function getAuthRouteMenuComparison()
    {
        return $this::AUTU_ROUTE_MENU_COMPARISON;
    }

    public function getUserMenusCacheNameById(int $id)
    {
        return $this::CACHE_AUTH_USER_MENU . "_$id";
    }

    public function getUserMenu(int $id)
    {
        $cacheNameUserMenu = $this->getUserMenusCacheNameById($id);

        $userMenu =  $this->cacheService->getByJson($cacheNameUserMenu);
        if (isset($userMenu)) {
            return  $userMenu;
        }

        $userMenu = $this->userRepository->getUserMenu($id);

        $this->cacheService->putByJson($cacheNameUserMenu, $userMenu, $this::CACHE_TIME);
        return $userMenu;
    }
}
