<?php

namespace App\Http\Controllers\Web\Admin;

use App\Models\Admin\VehicleType;
use App\Models\Admin\ServiceLocation;
use App\Models\Master\CarMake;
use App\Models\Admin\Company;
use App\Models\User;
use App\Models\Admin\Driver; 
use App\Models\Request\RequestPlace;
use App\Models\Country;
use App\Base\Filters\Admin\RequestFilter;
use App\Base\Filters\Master\CommonMasterFilter;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Http\Controllers\Controller;
use App\Models\Request\Request as RequestRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index()
    {
//        dd($created_request);
        $page = trans('pages_names.request');
        $main_menu = 'trip-request';
        $sub_menu = 'request';

        return view('admin.request.index', compact('page', 'main_menu', 'sub_menu'));
    }

    public function getAllRequest(QueryFilterContract $queryFilter)
    {
//        dd(Carbon::now());
        $query = RequestRequest::companyKey()->whereIsCompleted(true);

        $results = $queryFilter->builder($query)->customFilter(new RequestFilter)->defaultSort('-created_at')->paginate();

        return view('admin.request._request', compact('results'));
    }
    
    public function indexPending()
    {
        $page = trans('pages_names.request');
        $main_menu = 'trip-request';
        $sub_menu = 'pendingrequests';
        
        $newdate =  date('Y-m-d H:i:s', strtotime(' -1 day'));
        $resultsCount = RequestRequest::whereNull('driver_id')->where('created_at','>',$newdate)->get();
        
        return view('admin.request.indexpending', compact('page', 'main_menu', 'sub_menu','resultsCount'));
    }

    public function getAllPendingRequest(QueryFilterContract $queryFilter)
    {
        $newdate =  date('Y-m-d H:i:s', strtotime(' -1 day'));
        $query = RequestRequest::companyKey()->whereNull('driver_id')->where('created_at','>',$newdate);
        $results = $queryFilter->builder($query)->customFilter(new RequestFilter)->defaultSort('-created_at')->paginate();
        $nearest_drivers = Driver::where('active', 1)->where('approve', 1)->where('available', 1)->get();
        $getDatapickArr = array();
        $getDatadropArr = array();
        foreach($results as $resultsval){
            $queryval = RequestPlace::where('request_id','=',$resultsval->id)->first();
            $getDatapickArr[$resultsval->id] = $queryval->pick_address;
            $getDatadropArr[$resultsval->id] = $queryval->drop_address;
        }
        return view('admin.request._requestpending', compact('results','nearest_drivers','getDatapickArr','getDatadropArr'));
    }
    

    public function retrieveSingleRequest(RequestRequest $request){
        $item = $request;

        return view('admin.request._singlerequest', compact('item'));
    }

    public function getSingleRequest(RequestRequest $request)
    {
        $page = trans('pages_names.request');
        $main_menu = 'trip-request';
        $sub_menu = 'request';

        $item = $request;

        return view('admin.request.requestview', compact('page', 'main_menu', 'sub_menu', 'item'));
    }

    public function fetchSingleRequest(RequestRequest $request){
        return $request;
    }

     public function requestDetailedView(RequestRequest $request){
        $item = $request;
        $page = trans('pages_names.request');
         $main_menu = 'trip-request';
        $sub_menu = 'request';

        return view('admin.request.trip-request',compact('item','page', 'main_menu', 'sub_menu'));
    }

     public function indexScheduled()
    {
        $page = trans('pages_names.request');
        $main_menu = 'trip-request';
        $sub_menu = 'scheduled-rides';

        return view('admin.scheduled-rides.index', compact('page', 'main_menu', 'sub_menu'));
    }
    
    
    public function ScheduledNewRequest()
    {
        $page = trans('pages_names.add_driver');
        $services = ServiceLocation::companyKey()->whereActive(true)->get();
        $types = VehicleType::whereActive(true)->get();
        $countries = Country::all();
        $carmake = CarMake::active()->get();

        $users = User::active()->join('role_user', 'users.id', '=', 'role_user.user_id')->where('role_user.role_id',2)->get();
        
        $nearest_drivers = Driver::where('active', 1)->where('approve', 1)->where('available', 1)->get();

        $main_menu = 'trip-request';
        $sub_menu = 'scheduled-rides';
        //echo "<pre>"; print_r($users); die();
        return view('admin.scheduled-rides.create', compact('users', 'types', 'page', 'countries', 'main_menu', 'sub_menu', 'carmake','nearest_drivers'));
        
        // $page = trans('pages_names.request');
        // $main_menu = 'trip-request';
        // $sub_menu = 'scheduled-rides';

        // return view('admin.scheduled-rides.create', compact('page', 'main_menu', 'sub_menu'));
    }
    

     public function getAllScheduledRequest(QueryFilterContract $queryFilter)
    {
        $query = RequestRequest::companyKey()->whereIsCompleted(false)->whereIsCancelled(false)->whereIsLater(true);
        $results = $queryFilter->builder($query)->customFilter(new RequestFilter)->defaultSort('-created_at')->paginate();

        return view('admin.scheduled-rides._scheduled', compact('results'));
    }

    /**
     * View Invoice
     *
     * */
    public function viewCustomerInvoice(RequestRequest $request_detail){

        $data = $request_detail;

        return view('email.invoice',compact('data'));

    }
    /**
     * View Invoice
     *
     * */
    public function viewDriverInvoice(RequestRequest $request_detail){

        $data = $request_detail;

        return view('email.driver_invoice',compact('data'));

    }
    public function getCancelledRequest(RequestRequest $request)
    {
        $page = trans('pages_names.request');
        $main_menu = 'cancelled-request';
        $sub_menu = 'request';

        $item = $request;
        // dd($item->cancelReason);

        return view('admin.request.Cancelledview', compact('page', 'main_menu', 'sub_menu', 'item'));
    }

}
