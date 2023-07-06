<table class="table table-hover">
    <thead>
        <tr>
            <th> @lang('view_pages.s_no')</th>
            <th> @lang('view_pages.request_id')</th>
            <th> @lang('view_pages.date')</th>
            <th> Customer Name</th>
            <th> Pickup Location</th>
            <th> Drop Location</th>
            <th> @lang('view_pages.driver_name')</th>
            <th> @lang('view_pages.trip_status')</th>
            <th> @lang('view_pages.payment_option')</th>
        </tr>
    </thead>
    <tbody>


        @php $i= $results->firstItem(); @endphp

        @forelse($results as $key => $result)

        <tr>
            <td>{{ $i++ }} </td>
            <td>{{$result->request_number}}</td>
            <td>{{ $result->trip_start_time ?? $result->created_at }}</td>
            @if($result->user_id == null)
            <td>{{$result->adHocuserDetail ? $result->adHocuserDetail->name : '-'}}</td>
            @else
            <td>{{$result->userDetail ? $result->userDetail->name : '-'}}</td>
            @endif
            
            <td>{{$getDatapickArr[$result->id]}}</td>
            <td>{{$getDatadropArr[$result->id]}}</td>
            <td>
                <select name="driver_id" id="type" class="form-control" onchange="updatedriver('{{$result->id}}',this)">
                    <option value="">Select Driver</option>
                    @foreach($nearest_drivers as $key=>$type)
                    <option value={{$type->id}}>{{$type->name}} - {{$type->mobile}}</option>
                    @endforeach
                </select>
            </td>
            
            <td><span class="label label-warning">Pending</span></td>

            @if ($result->payment_opt == 0)
            <td><span class="label label-danger">@lang('view_pages.card')</span></td>
            @elseif($result->payment_opt == 1)
            <td><span class="label label-primary">@lang('view_pages.cash')</span></td>
            @elseif($result->payment_opt == 2)
            <td><span class="label label-warning">@lang('view_pages.wallet')</span></td>
            @else
            <td><span class="label label-info">@lang('view_pages.cash_wallet')</span></td>
            @endif

            
        </tr>
        @empty
        <tr>
            <td colspan="11">
                <p id="no_data" class="lead no-data text-center">
                    <img src="{{asset('assets/img/dark-data.svg')}}" style="width:150px;margin-top:25px;margin-bottom:25px;" alt="">
                    <h4 class="text-center" style="color:#333;font-size:25px;">@lang('view_pages.no_data_found')</h4>
                </p>
            </td>
        </tr>
        @endforelse

    </tbody>
</table>
<ul class="pagination pagination-sm pull-right">
    <li>
        <a href="#">{{$results->links()}}</a>
    </li>
</ul>
<script src="{{asset('assets/vendor_components/jquery/dist/jquery.js')}}"></script>
<script>
     function updatedriver(id, obj){
         alert(id)
     }
</script>