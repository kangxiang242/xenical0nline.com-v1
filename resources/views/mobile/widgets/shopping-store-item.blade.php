@foreach($data as $key=>$v)
<div class="form-radio">
    <input type="radio" {{ $key==0?"checked":"" }} id="store-{{ $v['shop_no'] }}" name="store_id" value="{{ $v['shop_no'] }}">
    <label class="radio-label" for="store-{{ $v['shop_no'] }}">
        <span class="ana"></span>
        <p class="text bg-711">{{ $v['shop_name'] }}<br><i>{{ str_replace(request()->city_name.request()->county_name,"",$v['shop_address']) }}</i></p>
    </label>
</div>
@endforeach
