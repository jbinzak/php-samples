<?php

namespace App\Http\Middleware;

use App\Http\Model\Internal\InternalAdminUser;
use Closure;

class InternalAdminMiddleware
{

    // declare
    const DEFAULT_NO_PAGE = 1;
    const DEFAULT_PER_PAGE = 25;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        // check for user
        $user = InternalAdminUser::currentUser($request);
        if(empty($user)){
            if(strpos($request->fullUrl(), 'administrator-dashboard') !== false){
                return response()->json(['header' => 'UnAuthorized', 'message' => 'UnAuthorized, please login.'], 401);
            }
            return redirect("/admin/logout");
        }
        $request->user = $user;


        // pagination
        $request->page = ($request->has('page')) ? (int)$request->input('page') : self::DEFAULT_NO_PAGE;
        $request->per_page = ($request->has('per_page')) ? (int)$request->input('per_page') : self::DEFAULT_PER_PAGE;
        if(empty($request->page) || $request->page <= 0){
            $request->page = self::DEFAULT_NO_PAGE;
            $request->limit = 0;
            $request->offset = ($request->has('offset')) ? (int)$request->input('offset') : 0;
        }else{
            $request->limit = $request->per_page;
            $request->offset = ($request->has('offset')) ? (int)$request->input('offset') : ($request->page - 1) * $request->limit;
        }

        return $next($request);
    }
}
