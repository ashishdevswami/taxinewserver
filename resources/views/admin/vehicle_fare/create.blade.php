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
            <a href="{{ url('vehicle_fare') }}">
                <button class="btn btn-danger btn-sm pull-right" type="submit">
                    <i class="mdi mdi-keyboard-backspace mr-2"></i>
                    @lang('view_pages.back')
                </button>
            </a>
        </div>

<div class="col-sm-12">

<form  method="post" class="form-horizontal" action="{{url('vehicle_fare/store')}}" enctype="multipart/form-data">
{{csrf_field()}}
<div class="row">
<div class="col-6">
<div class="form-group">
<label for="admin_id">@lang('view_pages.select_zone')
    <span class="text-danger">*</span>
</label>
<select name="zone" id="zone" class="form-control" required>
    <option value="" selected disabled>@lang('view_pages.select_zone')</option>
    @foreach($zones as $key=>$zone)
    <option value="{{$zone->id}}" {{ old('zone') == $zone->id ? 'selected' : '' }}>{{$zone->name}}</option>
    @endforeach
</select>
</div>
</div>
<div class="col-sm-6">
        <div class="form-group">
            <label for="type">@lang('view_pages.select_type')
                <span class="text-danger">*</span>
            </label>
            <select name="type" id="type" class="form-control" required>
                <option value="" >@lang('view_pages.select_type')</option>
                @foreach($vechiclestypes as $key=>$vechicletype)
                    <option value="{{$vechicletype->id}}" {{ old('type') == $vechicletype->id ? 'selected' : '' }}>{{$vechicletype->name}}</option>
                @endforeach
            </select>
            </div>
    </div>

</div>
<div class="row">


<div class="col-sm-6">
<div class="form-group">
<label for="payment_type">@lang('view_pages.payment_type')
    <span class="text-danger">*</span>
</label>
@php
    $card = '';
    $cash = '';
    $wallet = '';
@endphp
@if (old('payment_type'))
    @foreach (old('payment_type') as $item)
        @if ($item == 'card')
            @php
                $card = 'selected';
            @endphp
        @elseif($item == 'cash')
            @php
                $cash = 'selected';
            @endphp
        @elseif($item == 'wallet')
            @php
                $wallet = 'selected';
            @endphp
        @endif
    @endforeach
@endif
<!-- <option value="card" {{ $card }}>@lang('view_pages.card')</option> -->
<select name="payment_type[]" id="payment_type" class="form-control select2" multiple="multiple" data-placeholder="@lang('view_pages.select') @lang('view_pages.payment_type')" required>
    <option value="cash" {{ $cash }}>@lang('view_pages.cash')</option>
    <option value="wallet" {{ $wallet }}>@lang('view_pages.wallet')</option>
</select>
</div>
</div>

</div>
    {{-- Ride now price --}}
<div class="row">
<div class="col-12">
    <div class="box box-solid box-info">
        <div class="box-header with-border">
        <h4 class="box-title">@lang('view_pages.ride_now')</h4>
        </div>

        <div class="box-body">
                <div class="row">
                        <div class="col-sm-6">
                                <div class="form-group">
                               <label for="base_price">@lang('view_pages.base_price')&nbsp (@lang('view_pages.kilometer')) <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" id="ride_now_base_price" name="ride_now_base_price" value="{{old('ride_now_base_price')}}" required="" placeholder="@lang('view_pages.enter') @lang('view_pages.base_price')">
                                <span class="text-danger">{{ $errors->first('ride_now_base_price') }}</span>
                            </div>
                        </div>

                          <div class="col-sm-6">
                            <div class="form-group">
                           <label for="price_per_distance">@lang('view_pages.price_per_distance')&nbsp (@lang('view_pages.kilometer')) <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" id="ride_now_price_per_distance" name="ride_now_price_per_distance" value="{{old('ride_now_price_per_distance')}}" required="" placeholder="@lang('view_pages.enter') @lang('view_pages.distance_price')">
                            <span class="text-danger">{{ $errors->first('ride_now_price_per_distance') }}</span>

                        </div>
                    </div>
                </div>

                <div class="row">
                <div class="col-sm-6">
                <div class="form-group">
                <label for="base_distance">@lang('view_pages.select_base_distance')
                    <span class="text-danger">*</span>
                </label>
                 <input class="form-control" type="number" id="ride_now_base_distance" name="ride_now_base_distance" value="{{old('ride_now_base_distance')}}" required="" placeholder="@lang('view_pages.base_distance')">
                
               
                </div>
                </div>

                <div class="col-sm-6">
                <div class="form-group">
                <label for="price_per_time">@lang('view_pages.price_per_time')<span class="text-danger">*</span></label>
                <input class="form-control" type="text" id="ride_now_price_per_time" name="ride_now_price_per_time" value="{{old('ride_now_price_per_time')}}" required="" placeholder="@lang('view_pages.enter') @lang('view_pages.price_per_time')">
                <span class="text-danger">{{ $errors->first('ride_now_price_per_time') }}</span>

                </div>
                </div>

                </div>

                <div class="row">
                 <div class="col-sm-6">
                <div class="form-group">
                <label for="cancellation_fee">@lang('view_pages.cancellation_fee')<span class="text-danger">*</span></label>
                <input class="form-control" type="text" id="ride_now_cancellation_fee" name="ride_now_cancellation_fee" value="{{old('ride_now_cancellation_fee')}}" required="" placeholder="@lang('view_pages.enter') @lang('view_pages.cancellation_fee')">
                <span class="text-danger">{{ $errors->first('ride_now_cancellation_fee') }}</span>

                </div>
                </div>


                <div class="col-sm-6">
                <div class="form-group">
                <label for="waiting_charge">@lang('view_pages.waiting_charge')<span class="text-danger">*</span></label>
                <input class="form-control" type="text" id="ride_now_waiting_charge" name="ride_now_waiting_charge" value="{{old('ride_now_waiting_charge')}}" required="" placeholder="@lang('view_pages.enter') @lang('view_pages.waiting_charge')">
                <span class="text-danger">{{ $errors->first('ride_now_waiting_charge') }}</span>

                </div>
                </div>

                <div class="col-sm-6">
                <div class="form-group">
                <label for="free_waiting_time_in_mins_before_trip_start">@lang('view_pages.free_waiting_time_in_mins_before_trip_start')<span class="text-danger">*</span></label>
                <input class="form-control" type="text" id="ride_now_free_waiting_time_in_mins_before_trip_start" name="ride_now_free_waiting_time_in_mins_before_trip_start" value="{{old('ride_now_free_waiting_time_in_mins_before_trip_start')}}" required="" placeholder="@lang('view_pages.enter') @lang('view_pages.free_waiting_time_in_mins_before_trip_start')">
                <span class="text-danger">{{ $errors->first('ride_now_free_waiting_time_in_mins_before_trip_start') }}</span>

                </div>
                </div>
                <div class="col-sm-6">
                <div class="form-group">
                <label for="free_waiting_time_in_mins_after_trip_start">@lang('view_pages.free_waiting_time_in_mins_after_trip_start')<span class="text-danger">*</span></label>
                <input class="form-control" type="text" id="ride_now_free_waiting_time_in_mins_after_trip_start" name="ride_now_free_waiting_time_in_mins_after_trip_start" value="{{old('ride_now_free_waiting_time_in_mins_after_trip_start')}}" required="" placeholder="@lang('view_pages.enter') @lang('view_pages.free_waiting_time_in_mins_after_trip_start')">
                <span class="text-danger">{{ $errors->first('ride_now_free_waiting_time_in_mins_after_trip_start') }}</span>

                </div>
                </div>
                
                <div class="col-12  col-lg-12 mt-12">
                    <h2 class="fw-medium fs-base me-auto">
                        Driver Commission
                    </h2>
                </div>
                <div class="col-12 col-lg-6 mt-4">
                    <div class="form-group">
                        <label for="free_waiting_time_in_mins_after_trip_start">Base Price (Kilometer)</label>
                        <input class="form-control" type="text" id="driver_base_price" name="driver_base_price" value="" required="" placeholder="Base Price">
                    </div>
                </div>
                <div class="col-12 col-lg-6 mt-4">
                    <div class="form-group">
                        <label for="free_waiting_time_in_mins_after_trip_start">Price Per Distance (Kilometer)</label>
                        <input class="form-control" type="text" id="driver_price_per_distance" name="driver_price_per_distance" value="" required="" placeholder="Price Per Distance">
                    </div>
                </div>
                   
                <div class="col-12 col-lg-6 mt-4">
                    <div class="form-group">
                        <label for="free_waiting_time_in_mins_after_trip_start">Base Distance</label>
                        <input class="form-control" type="text" id="driver_base_distance" name="driver_base_distance" value="" required="" placeholder="Base Distance">
                    </div>
                </div>
                <div class="col-12 col-lg-6 mt-4">
                    <div class="form-group">
                        <label for="free_waiting_time_in_mins_after_trip_start">Minimum waiting time before Cancellation</label>
                        <input class="form-control" type="text" id="driver_minimum_waiting_time" name="driver_minimum_waiting_time" value="" required="" placeholder="Minimum waiting time before auto Cancellation">
                    </div>
                </div>
                
                
                
        </div>
    </div>
</div>
</div>
</div>
{{-- Ride later price --}}
<div class="row">
    <div class="col-12">
        <div class="box box-solid box-info">
        <div class="box-header with-border">
        <h4 class="box-title">@lang('view_pages.ride_later')</h4>
        </div>

        <div class="box-body">
                <div class="row">
                        <div class="col-sm-6">
                                <div class="form-group">
                                <label for="base_price">@lang('view_pages.base_price')&nbsp (@lang('view_pages.kilometer')) <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" id="ride_later_base_price" name="ride_later_base_price" value="{{old('ride_later_base_price')}}" required="" placeholder="@lang('view_pages.enter') @lang('view_pages.base_price')">
                                <span class="text-danger">{{ $errors->first('ride_later_base_price') }}</span>

                            </div>
                        </div>

                            <div class="col-sm-6">
                            <div class="form-group">
                            <label for="price_per_distance">@lang('view_pages.price_per_distance')&nbsp (@lang('view_pages.kilometer')) <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" id="ride_later_price_per_distance" name="ride_later_price_per_distance" value="{{old('ride_later_price_per_distance')}}" required="" placeholder="@lang('view_pages.enter') @lang('view_pages.distance_price')">
                            <span class="text-danger">{{ $errors->first('ride_later_price_per_distance') }}</span>

                        </div>
                    </div>
                </div>

                <div class="row">
                <div class="col-sm-6">
                <div class="form-group">
                <label for="base_distance">@lang('view_pages.select_base_distance')
                    <span class="text-danger">*</span>
                </label>
                 <input class="form-control" type="number" id="ride_later_base_distance" name="ride_later_base_distance" value="{{old('ride_later_base_distance')}}" required="" placeholder="@lang('view_pages.base_distance')">

               
                </div>
                </div>

                <div class="col-sm-6">
                <div class="form-group">
                <label for="price_per_time">@lang('view_pages.price_per_time')<span class="text-danger">*</span></label>
                <input class="form-control" type="text" id="ride_later_price_per_time" name="ride_later_price_per_time" value="{{old('ride_later_price_per_time')}}" required="" placeholder="@lang('view_pages.enter') @lang('view_pages.price_per_time')">
                <span class="text-danger">{{ $errors->first('ride_later_price_per_time') }}</span>

                </div>
                </div>

                </div>

                <div class="row">
                    <div class="col-sm-6">
                <div class="form-group">
                <label for="cancellation_fee">@lang('view_pages.cancellation_fee')<span class="text-danger">*</span></label>
                <input class="form-control" type="text" id="ride_later_cancellation_fee" name="ride_later_cancellation_fee" value="{{old('ride_later_cancellation_fee')}}" required="" placeholder="@lang('view_pages.enter') @lang('view_pages.cancellation_fee')">
                <span class="text-danger">{{ $errors->first('ride_later_cancellation_fee') }}</span>

                </div>
                </div>


                <div class="col-sm-6">
                <div class="form-group">
                <label for="waiting_charge">@lang('view_pages.waiting_charge')<span class="text-danger">*</span></label>
                <input class="form-control" type="text" id="ride_later_waiting_charge" name="ride_later_waiting_charge" value="{{old('ride_later_waiting_charge')}}" required="" placeholder="@lang('view_pages.enter') @lang('view_pages.waiting_charge')">
                <span class="text-danger">{{ $errors->first('ride_later_waiting_charge') }}</span>

                </div>
                </div>

                <div class="col-sm-6">
                <div class="form-group">
                <label for="free_waiting_time_in_mins_before_trip_start">@lang('view_pages.free_waiting_time_in_mins_before_trip_start')<span class="text-danger">*</span></label>
                <input class="form-control" type="text" id="ride_later_free_waiting_time_in_mins_before_trip_start" name="ride_later_free_waiting_time_in_mins_before_trip_start" value="{{old('ride_later_free_waiting_time_in_mins_before_trip_start')}}" required="" placeholder="@lang('view_pages.enter') @lang('view_pages.free_waiting_time_in_mins_before_trip_start')">
                <span class="text-danger">{{ $errors->first('ride_later_free_waiting_time_in_mins_before_trip_start') }}</span>

                </div>
                </div>
                <div class="col-sm-6">
                <div class="form-group">
                <label for="free_waiting_time_in_mins_after_trip_start">@lang('view_pages.free_waiting_time_in_mins_after_trip_start')<span class="text-danger">*</span></label>
                <input class="form-control" type="text" id="ride_later_free_waiting_time_in_mins_after_trip_start" name="ride_later_free_waiting_time_in_mins_after_trip_start" value="{{old('ride_later_free_waiting_time_in_mins_after_trip_start')}}" required="" placeholder="@lang('view_pages.enter') @lang('view_pages.free_waiting_time_in_mins_after_trip_start')">
                <span class="text-danger">{{ $errors->first('ride_later_free_waiting_time_in_mins_after_trip_start') }}</span>

                </div>
                </div>
                
                <div class="col-12  col-lg-12 mt-12">
                    <h2 class="fw-medium fs-base me-auto">
                        Driver Commission
                    </h2>
                </div>
                <div class="col-12 col-lg-6 mt-4">
                    <div class="form-group">
                        <label for="free_waiting_time_in_mins_after_trip_start">Base Price (Kilometer)</label>
                        <input class="form-control" type="text" id="driver_base_price1" name="driver_base_price1" value="" required="" placeholder="Base Price">
                    </div>
                </div>
                <div class="col-12 col-lg-6 mt-4">
                    <div class="form-group">
                        <label for="free_waiting_time_in_mins_after_trip_start">Price Per Distance (Kilometer)</label>
                        <input class="form-control" type="text" id="driver_price_per_distance1" name="driver_price_per_distance1" value="" required="" placeholder="Price Per Distance">
                    </div>
                </div>
                   
                <div class="col-12 col-lg-6 mt-4">
                    <div class="form-group">
                        <label for="free_waiting_time_in_mins_after_trip_start">Base Distance</label>
                        <input class="form-control" type="text" id="driver_base_distance1" name="driver_base_distance1" value="" required="" placeholder="Base Distance">
                    </div>
                </div>
                <div class="col-12 col-lg-6 mt-4">
                    <div class="form-group">
                        <label for="free_waiting_time_in_mins_after_trip_start">Minimum waiting time before Cancellation</label>
                        <input class="form-control" type="text" id="driver_minimum_waiting_time1" name="driver_minimum_waiting_time1" value="" required="" placeholder="Minimum waiting time before auto Cancellation">
                    </div>
                </div>
                
        </div>
    </div>
</div>
</div>
</div>

<div class="form-group">
    <div class="col-12">
        <button class="btn btn-primary btn-sm pull-right mb-4" type="submit">
            @lang('view_pages.save')
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $('.select2').select2({
        placeholder : "Select ...",
    });

    $(document).on('change', '#zone', function() {
        let zone = $(this).val();

        $.ajax({
            url: "{{ url('vehicle_fare/fetch/vehicles') }}",
            type: 'GET',
            data: {
                '_zone': zone,
            },
            success: function(result) {
                var vehicles = result.data;
                var option = ''
                vehicles.forEach(vehicle => {
                    option += `<option value="${vehicle.id}">${vehicle.name}</option>`;
                });

                $('#type').html(option)
            }
        });
    });
</script>

@endsection

