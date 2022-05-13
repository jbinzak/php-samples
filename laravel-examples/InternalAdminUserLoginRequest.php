<?php

namespace App\Http\Model\Internal;

use App\Http\Model\Error\InvalidRequestParameterException;
use App\Http\Model\Util\RequestHelper;
use Illuminate\Http\Request;

/**
 *
 * @OA\Schema(
 *     schema="InternalAdminUserLoginRequest",
 *     @OA\Property(property="email", type="string"),
 *     @OA\Property(property="password", type="string"),
 * )
 */
class InternalAdminUserLoginRequest{

    public $email;
    public $password;

    /**
     * InternalAdminUserLoginRequest constructor.
     * @param Request $request
     * @throws InvalidRequestParameterException
     */
    public function __construct(Request $request){

        // get email
        $this->email = RequestHelper::getArgSafely($request, 'email', null);
        if(empty($this->email)){
            throw new InvalidRequestParameterException('Please enter a valid email address.');
        }

        // get password
        $this->password = RequestHelper::getArgSafely($request, 'password', null);
        if(empty($this->password)){
            throw new InvalidRequestParameterException('Please enter a valid password.');
        }

    }
}
