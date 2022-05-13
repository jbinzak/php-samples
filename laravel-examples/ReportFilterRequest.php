<?php

namespace App\Http\Model\Internal;

use App\Http\Model\Error\InvalidRequestParameterException;
use App\Http\Model\Util\RequestHelper;
use Illuminate\Http\Request;

/**
 *
 * @OA\Schema(
 *     schema="ReportFilterRequest",
 *     @OA\Property(property="start_date", type="string"),
 *     @OA\Property(property="end_date", type="string"),
 *     @OA\Property(property="clubs", type="string"),
 *     @OA\Property(property="utm_codes", type="string"),
 * )
 */
class ReportFilterRequest{

    public $start_date;
    public $end_date;
    public $clubs;
    public $teams;
    public $utm_codes;
    public $download_csv;


    /**
     * ReportFilterRequest constructor.
     * @param Request $request
     */
    public function __construct(Request $request){

        // get args
        $this->start_date = RequestHelper::getArgSafely($request, 'start_date', null);
        $this->end_date = RequestHelper::getArgSafely($request, 'end_date', null);
        $this->clubs = RequestHelper::getArgSafely($request, 'clubs', []);
        if(!is_array($this->clubs)){
            $this->clubs = explode(',', $this->clubs);
        }
        $this->teams = RequestHelper::getArgSafely($request, 'teams', []);
        if(!is_array($this->teams)){
            $this->teams = explode(',', $this->teams);
        }
        $this->download_csv = RequestHelper::getArgSafely($request, 'download_csv', 0);

    }
}
