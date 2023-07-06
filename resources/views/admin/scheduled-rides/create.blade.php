@extends('admin.layouts.app')
@section('title', 'Main page')


@section('content')

<!-- Start Page content -->
<div class="content">
<div class="container-fluid">

<div class="row">
<div class="col-sm-12">
    <div class="box">

        <div class="box-header with-border">
            <a href="{{ url('drivers') }}">
                <button class="btn btn-danger btn-sm pull-right" type="submit">
                    <i class="mdi mdi-keyboard-backspace mr-2"></i>
                    @lang('view_pages.back')
                </button>
            </a>
        </div>

<div class="col-sm-12">

<form  method="post" class="form-horizontal" action="https://taxi.atyarealtors.com/api/v1/admincreate" enctype="multipart/form-data">
{{csrf_field()}}
<div class="row" style="margin-top:20px;">
<div class="col-6">
<div class="form-group">
<label for="admin_id">Pickup Location
    <span class="text-danger">*</span>
</label>
<input class="form-control" type="text" id="autocomplete" name="pick_address" value="" required="" placeholder="@lang('view_pages.enter_address')">

</div>
</div>

<div class="col-6">
<div class="form-group">
<label for="gender">Drop Location
    <span class="text-danger">*</span>
</label>
<input class="form-control" type="text" id="dropautocomplete" name="drop_address" value="" required="" placeholder="@lang('view_pages.enter_address')">

</div>
</div>



<div class="row">

    </div>
<!--  <div class="col-sm-6">
            <div class="form-group">
            <label for="address">@lang('view_pages.address')</label>
            <input class="form-control" type="text" id="address" name="address" value="{{old('address')}}" required="" placeholder="@lang('view_pages.enter_address')">
            <span class="text-danger">{{ $errors->first('address') }}</span>

        </div>
    </div> -->
</div>

<div class="row">
    <div class="col-sm-6">
    <div class="form-group">
    <label for="name">Select Customer <span class="text-danger">*</span></label>
    <select name="user_id" id="user_id" onchange="getypesAndCompanys()" class="form-control" required>
        <option value="">Select Customer</option>
        @foreach($users as $key=>$type)
        <option value="{{$type->id}}">{{$type->name}} - {{$type->email}} - {{$type->mobile}}</option>
        @endforeach
    </select>
    <span class="text-danger">{{ $errors->first('name') }}</span>

</div>

</div>
<div class="col-sm-6">
        <div class="form-group">
            <label for="type">Select Driver
                <span class="text-danger">*</span>
            </label>
            <select name="driver_id" id="type" class="form-control" required>
                <option value="">Select Driver</option>
                @foreach($nearest_drivers as $key=>$type)
                <option value={{$type->id}}>{{$type->name}} - {{$type->mobile}}</option>
                @endforeach
            </select>
            </div>
        </div>


</div>

<div class="row">
       <div class="col-sm-6">
        <div class="form-group">
            <label for="type"> Guaranteed Ride</label>
            <select name="is_laterdata" class="form-control" onchange="showridetype(this)">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
            <input class="form-control" type="hidden" id="name" name="name" value="">
            <input class="form-control" type="hidden" id="email" name="email" value="">
            <input class="form-control" type="hidden" id="mobile" name="mobile" value="">
            <input class="form-control" type="hidden" name="ride_type" value="1">
            
            <input class="form-control" type="hidden" name="payment_opt" value="1">
            <input class="form-control request_eta_amount" type="hidden" name="request_eta_amount" value="200">
            <input class="form-control pickuplat" type="hidden" name="pick_lat" value="">
            <input class="form-control pickuplng" type="hidden" name="pick_lng" value="">
            <input class="form-control dropuplat" type="hidden" name="drop_lat" value="">
            <input class="form-control dropuplng" type="hidden" name="drop_lng" value="">
            <input class="form-control vehicle_type" type="hidden" name="vehicle_type" value="18580f24-f331-494e-8af6-dcba4267edd9">
        </div>
        <div id="showlater">
            
        </div>
    </div>
   
    
</div>


<div class="row showgauranteedride">
    <div class="col-sm-6">
        <div class="form-group">
            <label for="name">Date</label>
            <input class="form-control" type="date" id="date" name="date" value="" placeholder="Please select date">
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label for="name">Time</label>
            <input class="form-control" type="time" id="time" name="time" value="" placeholder="Please select time">
        </div>
    </div>
</div>

</div>


<div class="form-group">
        <div class="col-6">

</div>


<div class="form-group">
        <div class="col-12">
            <button class="btn btn-primary btn-sm m-5 pull-right" type="submit">
                Create Request
            </button>
        </div>
    </div>

</form>

            </div>
        </div>


    </div>
</div>
</div>

</div>
<!-- container -->

</div>
<!-- content -->
<!-- jQuery 3 -->

    <script src="{{asset('assets/vendor_components/jquery/dist/jquery.js')}}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyDm0q0FoMepI_RO4GOgcNcMp65d_6pv9Mo"></script>

<script>
    function showridetype(obj){
        var ridetype = $(obj).val();
        if(ridetype=='1'){
            //$('.is_later').val(1);
            $('#showlater').append('<input class="form-control is_later" type="hidden" name="is_later" value="1">');
            $('.showgauranteedride').show();
        }else {
            $('#showlater').html('');
            $('.showgauranteedride').hide();
        }
    }
    
    $(document).ready(function(){ 
        $('.showgauranteedride').hide();
        var autocomplete = new google.maps.places.Autocomplete(
            document.getElementById("autocomplete")
        );
        autocomplete.addListener('place_changed', function () {
              var place = autocomplete.getPlace();
              // place variable will have all the information you are looking for.
              $('.pickuplat').val(place.geometry['location'].lat());
              $('.pickuplng').val(place.geometry['location'].lng());
        });
    
        var autocomplete1 =new google.maps.places.Autocomplete(
            document.getElementById("dropautocomplete")
        );
        
        autocomplete1.addListener('place_changed', function () {
            var place1 = autocomplete1.getPlace();
            $('.dropuplat').val(place1.geometry['location'].lat());
            $('.dropuplng').val(place1.geometry['location'].lng());
             var picklat =  $('.pickuplat').val();
             var pickuplng = $('.pickuplng').val();
            $.ajax({
                type: "POST",
                url: "https://taxi.atyarealtors.com/api/v1/admineta",
                data: { pick_lat:picklat,pick_lng:pickuplng,drop_lat:place1.geometry['location'].lat(),drop_lng:place1.geometry['location'].lng(),ride_type:1,vehicle_type:'18580f24-f331-494e-8af6-dcba4267edd9' }, 
                success: function( msg ) {
                    console.log('msg',msg)
                    $('.request_eta_amount').val(msg.data.total);
                    //alert("SMS Sent Successfully");
                }
            });
        });
        
    })

    function getypesAndCompanys(){
        var userdata = $( "#user_id option:selected").text();
        console.log('getvalues',userdata);
        if(userdata){
            var getvalues = userdata.split(' - ')
            console.log('getvalues',getvalues);
            $('#name').val(getvalues[0]);
            $('#email').val(getvalues[1]);
            $('#mobile').val(getvalues[2]);
        }
        
    }
</script>

@endsection
