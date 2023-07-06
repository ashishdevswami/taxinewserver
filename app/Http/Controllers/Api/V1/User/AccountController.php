<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Models\User;
use App\Models\Admin\Driver;
use App\Base\Constants\Auth\Role;
use App\Http\Controllers\ApiController;
use App\Transformers\User\UserTransformer;
use App\Transformers\Driver\DriverProfileTransformer;
use App\Transformers\Owner\OwnerProfileTransformer;

class AccountController extends ApiController
{
    /**
     * Get the current logged in user.
     * @group User-Management
     * @return \Illuminate\Http\JsonResponse
    * @responseFile responses/auth/authenticated_driver.json
    * @responseFile responses/auth/authenticated_user.json
     */
    public function me()
    {
        //echo "lavlesh"; die();
        $user = auth()->user();
        
        // echo "<pre>"; print_r($user); die();
        if (auth()->user()->hasRole(Role::DRIVER)) {
            $getDriver = Driver::where('user_id',auth()->user()->id)->first();
            $driver_details = $user->driver;
             $driver_details['driver_type']=$getDriver->driver_type;
            // $driver_details['tax']=$getDriver->driver_type;
            $user = fractal($driver_details, new DriverProfileTransformer)->parseIncludes(['onTripRequest.userDetail','onTripRequest.requestBill','metaRequest.userDetail']);
            //echo "<pre>"; print_r($user); die();
        } else if(auth()->user()->hasRole(Role::USER)) {

            $user = fractal($user, new UserTransformer)->parseIncludes(['onTripRequest.driverDetail','onTripRequest.requestBill','metaRequest.driverDetail','favouriteLocations','laterMetaRequest.driverDetail']);
        }else{

            $owner_details = $user->owner;

            $user = fractal($owner_details, new OwnerProfileTransformer);
        }

        if(auth()->user()->hasRole(Role::DISPATCHER)){

            $user = User::where('id',auth()->user()->id)->first();
   
        }
        //$user->tax = '';
       // "<pre>"; print_r($user); die();
        return $this->respondOk($user);
    }
}
