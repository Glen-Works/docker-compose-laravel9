<?php

namespace App\Doc;

class SwaggerUser
{
    /**
     * @OA\Examples(
     *   summary="ShowUserById",
     *   example="ShowUserById",
     *   value={
     *     "data": {
     *       "userInfo": {
     *          "id": 2,
     *          "name": "Gary",
     *          "email": "gary.shih@wvt.com.tw",
     *          "status": 1,
     *          "userType": "2",
     *          "loginIp": "172.20.0.1",
     *          "passwordUpdateTime": null,
     *          "loginTime": "2022-12-13 10:01:29",
     *          "createdAt": null,
     *          "updatedAt": "2022-12-13T02:01:44.000000Z",
     *          "remark": "",
     *       },
     *       "roleUser": {
     *          1,2
     *       }
     *     },
     *     "message": "",
     *     "success": true
     *     }
     * )
     */
    public $ShowUserById;


    /**
     * @OA\Examples(
     *   summary="ShowUserList",
     *   example="ShowUserList",
     *   value={
     *      "data": {
     *        "userList": {
     *          {
     *            "id": 3,
     *            "name": "test",
     *            "email": "testqwq@gmail.com",
     *            "status": 1,
     *            "userType": 2,
     *            "loginIp": null,
     *            "loginTime": null,
     *            "createdAt": "2022-09-26T06:59:17.000000Z",
     *            "updatedAt": "2022-09-26T06:59:17.000000Z"
     *          },
     *          {
     *            "id": 4,
     *            "name": "test",
     *            "email": "tesqwtqwq@gmail.com",
     *            "status": 1,
     *            "userType": 2,
     *            "loginIp": null,
     *            "loginTime": null,
     *            "createdAt": "2022-09-26T07:04:12.000000Z",
     *            "updatedAt": "2022-09-26T07:04:12.000000Z"
     *          },
     *        },
     *        "pageManagement": {
     *            "page": 1,
     *            "pageCount": 1,
     *            "count": 5,
     *            "limit": 10,
     *            "search": {},
     *            "sort": "asc",
     *            "sortColumn": "id"
     *        }
     *      },
     *      "message": "",
     *      "success": true
     *    }
     * )
     */
    public $ShowUserList;

    /**
     * @OA\Schema(
     *      schema="CreateUser",
     *      @OA\Property(
     *          property="name",
     *          description="name",
     *          type="string",
     *          format="string",
     *          example="Gary"
     *      ),
     *      @OA\Property(
     *          property="email",
     *          description="email",
     *          type="string",
     *          format="string",
     *          example="gary.shih@wvt.com.tw"
     *      ),
     *      @OA\Property(
     *          property="password",
     *          description="password",
     *          type="string",
     *          format="string",
     *          example="gary123456"
     *      ),
     *      @OA\Property(
     *          property="status",
     *          description="status",
     *          type="bool",
     *          format="bool",
     *          example="true",
     *      ),
     *      @OA\Property(
     *          property="userType",
     *          description="userType",
     *          type="integer",
     *          format="integer",
     *          example="true",
     *      ),
     *      @OA\Property(
     *          property="remark",
     *          description="remark",
     *          type="string",
     *          format="string",
     *          example="管理者帳號",
     *      ),
     *      @OA\Property(
     *          property="roleUser",
     *          description="roleUser",
     *          example={1,2},
     *      ),
     * )
     */
    public $CreateUser;

    /**
     * @OA\Schema(
     *      schema="UpdateUser",
     *      @OA\Property(
     *          property="name",
     *          description="name",
     *          type="string",
     *          format="string",
     *          example="Gary"
     *      ),
     *      @OA\Property(
     *          property="email",
     *          description="email",
     *          type="string",
     *          format="string",
     *          example="gary.shih@wvt.com.tw"
     *      ),
     *      @OA\Property(
     *          property="status",
     *          description="status",
     *          type="bool",
     *          format="bool",
     *          example="true",
     *      ),
     *      @OA\Property(
     *          property="userType",
     *          description="userType",
     *          type="integer",
     *          format="integer",
     *          example="true",
     *      ),
     *      @OA\Property(
     *          property="remark",
     *          description="remark",
     *          type="string",
     *          format="string",
     *          example="管理者帳號",
     *      ),
     *      @OA\Property(
     *          property="roleUser",
     *          description="roleUser",
     *          example={1,2},
     *      ),
     * )
     */
    public $UpdateUser;

    /**
     * @OA\Schema(
     *      schema="UpdateSelfProfile",
     *      @OA\Property(
     *          property="name",
     *          description="name",
     *          type="string",
     *          format="string",
     *          example="Gary"
     *      ),
     *      @OA\Property(
     *          property="email",
     *          description="email",
     *          type="string",
     *          format="string",
     *          example="gary.shih@wvt.com.tw"
     *      ),
     * )
     */
    public $UpdateSelfProfile;

    /**
     * @OA\Schema(
     *      schema="UpdateUserPassword",
     *      @OA\Property(
     *          property="newPassword",
     *          description="new Passord",
     *          type="string",
     *          format="string",
     *          example="@WSX1qaz"
     *      ),
     *      @OA\Property(
     *          property="checkPassword",
     *          description="check Passord",
     *          type="string",
     *          format="string",
     *          example="@WSX1qaz"
     *      ),
     * )
     */
    public $UpdateUserPassword;
}
