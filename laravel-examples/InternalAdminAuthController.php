<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Http\Model\Error\InvalidRequestParameterException;
use App\Http\Model\Error\InvalidUserCredentialsException;
use App\Http\Model\Internal\InternalAdminUser;
use App\Http\Model\Internal\InternalAdminUserLoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class InternalAdminAuthController extends Controller
{

    public $data = [];



    /**
     * @OA\Get(
     *     path="/admin/login",
     *     operationId="VIEW /admin/login",
     *     tags={"Admin Authentication"},
     *     @OA\Response(
     *         response="200",
     *         description="Displays login form."
     *     ),
     *     @OA\Response(
     *         response="302",
     *         description="User is already logged in, redirects to dashboard."
     *     )
     * )
     *
     * @OA\Post(
     *     path="/admin/login",
     *     operationId="SUBMIT /admin/login",
     *     tags={"Admin Authentication"},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(ref="#/components/schemas/InternalAdminUserLoginRequest"),
     *          ),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Redirects to dashboard."
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error: Bad request. When invalid parameters were supplied.",
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Error: Bad request. When invalid parameters were supplied.",
     *     )
     * )
     *
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){

        // already logged in
        if(!empty(InternalAdminUser::currentUser($request))){
            return redirect("/admin/app");
        }

        // post
        if ($request->isMethod('post')) {

            $logged_in = true;

            // get params
            try{
                $loginRequest = new InternalAdminUserLoginRequest($request);


                // get token/user info
                try{
                    $loginResponse = InternalAdminUser::verifyLogin($loginRequest);
                }catch (InvalidUserCredentialsException $e){
                    $logged_in = false;
                    flash($e->responseArray()['message'])->error();
                }

            }catch (InvalidRequestParameterException $e){
                $logged_in = false;
                flash($e->responseArray()['message'])->error();
            }

            if($logged_in){
                return redirect("/admin/app")->withCookie(cookie(InternalAdminUser::COOKIE_USER_SESSION, $loginResponse->user_session_token, 60*24*2, '/', null, null, false));
            }

        }

        // return view & data
        return view('internal/login', $this->data);
    }

    /**
     * @OA\Get(
     *     path="/admin/logout",
     *     operationId="/admin/logout",
     *     tags={"Admin Authentication"},
     *     @OA\Response(
     *         response="302",
     *         description="Removes cookie and redirects to login."
     *     )
     * )
     *
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request){
        Cookie::queue(
            cookie()->forget(InternalAdminUser::COOKIE_USER_SESSION)
        );
        return redirect('/admin/login');
    }
}
