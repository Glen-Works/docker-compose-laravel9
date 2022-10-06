<?php

namespace App\Http\Controllers;

use App\Dto\InputLoginDto;
use App\Dto\OutputJwtDto;
use App\Dto\OutputLoginDto;
use App\Enums\JwtType;
use App\Services\JwtService;
use App\Services\LoginService;
use App\Services\ResponseService;
use App\Services\UtilService;
use Illuminate\Http\Request;

class LoginController extends Controller
{

    private $utilService;
    private $loginService;
    private $responseService;
    protected $jwtService;

    public function __construct(
        UtilService $utilService,
        LoginService $loginService,
        ResponseService $responseService,
        JwtService $jwtService
    ) {
        $this->utilService = $utilService;
        $this->loginService = $loginService;
        $this->responseService = $responseService;
        $this->jwtService = $jwtService;
    }

    /**
     * @OA\Post(
     *  tags={"User"},
     *  path="/api/v1/user",
     *  summary="新增使用者(User Create)",
     *  security={{"Authorization":{}}},
     *  @OA\Response(response=200,description="OK",@OA\JsonContent(ref="#/components/schemas/ResponseSuccess")),
     *  @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/CreateUser")),
     *  @OA\Response(response=401,description="Unauthorized",@OA\JsonContent(ref="#/components/schemas/ResponseUnauthorized")),
     *  @OA\Response(response=500,description="Server Error",@OA\JsonContent(ref="#/components/schemas/responseError")),
     * )
     */
    public function login(Request $request)
    {
        //取得api data
        $data = $request->all();

        //驗證
        $this->utilService->ColumnValidator($data, [
            'account' => 'required|max:100|email:rfc,dns',
            'password' => 'required|max:50',
            'captcha' => 'max:50',
            'captchaId' => 'max:50'
        ]);

        $inputLoginDto = new InputLoginDto(
            $data["account"],
            $data["password"],
            $data["captcha"] ?? "",
            $data["captchaId"] ?? "",
        );

        $outputUserInfoDto = $this->loginService->login($inputLoginDto);

        $jwtToken = $this->jwtService->genJwtToken($outputUserInfoDto, JwtType::jwtToken);
        $refreshToken = $this->jwtService->genJwtToken($outputUserInfoDto, JwtType::jwtRefreshToken);
        //todo 有時間在做 驗證 captcha

        $outputJwtDto = new OutputJwtDto($jwtToken, $refreshToken);
        $outputLoginDto = new OutputLoginDto($outputUserInfoDto, $outputJwtDto);

        return $this->responseService->responseJson($outputLoginDto);
    }

    public function refreshJwtToken(Request $request)
    {
        //取得api data
        $data = $request->all();

        //驗證
        $this->utilService->ColumnValidator($data, [
            'refreshtoken' => 'required|max:800'
        ]);

        $data = (is_array($data)) ? (object)$data : $data;

        // $jwtToken = $this->jwtService->getJwtToken($data->refreshtoken);
        $userInfo = $this->jwtService->getUserInfoByRefreshJwtToken($data->refreshtoken);

        $jwtToken = $this->jwtService->genJwtToken($userInfo, JwtType::jwtToken);
        $refreshToken = $this->jwtService->genJwtToken($userInfo, JwtType::jwtRefreshToken);
        $outputJwtDto = new OutputJwtDto($jwtToken, $refreshToken);
        $outputLoginDto = new OutputLoginDto($userInfo, $outputJwtDto);
        return $this->responseService->responseJson($outputLoginDto);
    }

    public function test(Request $request)
    {
        $data = $this->jwtService->getUserInfoByRequest($request);
        return $this->responseService->responseJson($data);
    }
}
