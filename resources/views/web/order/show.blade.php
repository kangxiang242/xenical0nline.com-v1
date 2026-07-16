@extends('web::layout.layout')

@section('style')
    @parent
@stop
@section('title','訂單詳情')
@section('script')
    <script src="{{ asset('static/js/sweetalert2.js') }}?ver={{ config('app.asset_version') }}"></script>
    <script>
        if(typeof flash_data !== 'undefined' && flash_data){
            Swal.fire({
                title: flash_data.msg,
                text: flash_data.sub_msg,
                icon: 'success',
                confirmButtonText: '我知道了'
            })
        }
    </script>
    <script>
        function initOrderPrivacyToggle(){
            var formatPhone = function(raw){
                var d = (raw || '').replace(/\D/g, '');
                if(d.length < 10) return raw || '';
                return d.slice(0,4) + ' ' + d.slice(4,7) + ' ' + d.slice(7,10);
            };
            var maskName = function(s){
                if(!s || s.length === 0) return '';
                return s.charAt(0) + '**';
            };
            var maskPhone = function(raw){
                var d = (raw || '').replace(/\D/g, '');
                if(d.length < 10) return '** ** ***';
                return '09** *** ' + d.slice(-3);
            };
            var maskEmail = function(s){
                if(!s) return '';
                var i = s.indexOf('@');
                if(i <= 0) return '****';
                return '****' + s.slice(i);
            };

            var trigger = document.querySelector('.ordertable .trigger');
            var contaName = document.querySelector('.ordertable .conta--name');
            var contaPhone = document.querySelector('.ordertable .conta--phone');
            var contaEmail = document.querySelector('.ordertable .conta--email');
            if(!trigger || !contaName || !contaPhone || !contaEmail) return;
            var hideBtn = trigger.querySelector('.hide');
            var showBtn = trigger.querySelector('.show');

            var isHidden = true;

            var applyState = function(){
                var nameFull = contaName.getAttribute('data-full') || '';
                var phoneFull = contaPhone.getAttribute('data-full') || '';
                var emailFull = contaEmail.getAttribute('data-full') || '';
                if(isHidden){
                    contaName.textContent = maskName(nameFull);
                    contaPhone.textContent = maskPhone(phoneFull);
                    contaEmail.textContent = maskEmail(emailFull);
                    if(hideBtn) hideBtn.style.display = 'none';
                    if(showBtn) showBtn.style.display = 'flex';
                } else {
                    contaName.textContent = nameFull;
                    contaPhone.textContent = formatPhone(phoneFull);
                    contaEmail.textContent = emailFull;
                    if(hideBtn) hideBtn.style.display = 'flex';
                    if(showBtn) showBtn.style.display = 'none';
                }
            };

            applyState();

            var toggle = function(e){
                if(e) e.preventDefault();
                isHidden = !isHidden;
                applyState();
            };
            trigger.addEventListener('click', function(e){ e.preventDefault(); e.stopPropagation(); toggle(); });
            trigger.addEventListener('keydown', function(e){ if(e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggle(); } });
        }
        if(document.readyState === 'loading'){
            document.addEventListener('DOMContentLoaded', initOrderPrivacyToggle);
        } else {
            initOrderPrivacyToggle();
        }
    </script>
@stop


@section('content')
<main>
    <div class="order">

        <div class="ordertable">
            <div class="item">
                <label><span>訂單編號</span></label>
                <div class="conta">{{ $order->no }}</div>
            </div>

            <div class="item">
                <label><span>訂單狀態</span></label>
                <div class="conta">{{ \Illuminate\Support\Arr::get(\App\Models\Order::STATUS_TXT,$order->status) }}</div>
            </div>

            <div class="item">
                <label><span>訂單提交時間</span></label>
                <div class="conta">{{ $order->created_at }}</div>
            </div>

            @php
                $phoneDigits = preg_replace('/\D/', '', $order->phone ?? '');
                $phoneFormatted = strlen($phoneDigits) >= 10 ? substr($phoneDigits, 0, 4) . ' ' . substr($phoneDigits, 4, 3) . ' ' . substr($phoneDigits, 7, 3) : ($order->phone ?? '');
                $nameMasked = $order->name ? mb_substr($order->name, 0, 1) . '**' : '';
                $phoneMasked = strlen($phoneDigits) >= 10 ? '09** *** ' . substr($phoneDigits, -3) : '** ** ***';
                $emailAt = $order->email ? strpos($order->email, '@') : false;
                $emailMasked = ($emailAt !== false && $emailAt > 0) ? '****' . substr($order->email, $emailAt) : '****';
            @endphp
            <div class="item item--name">
                <label><span>訂購人</span></label>
                <div class="conta conta--name" data-full="{{ $order->name }}">{{ $nameMasked }}</div>
                <div class="trigger" role="button" tabindex="0" aria-label="切換個人訊息顯示">
                    <div class="hide">
                        <svg class="hideicon" viewBox="0 0 1024 1024"><use href="#icon-eyehideicon"></use></svg>
                        隱藏個人訊息
                    </div>
                    <div class="show">
                        <svg class="showicon" viewBox="0 0 1024 1024"><use href="#icon-eyeshowicon"></use></svg>
                        顯示個人訊息
                    </div>
                </div>
            </div>

            <div class="item item--phone">
                <label><span>聯絡電話</span></label>
                <div class="conta conta--phone" data-full="{{ $order->phone }}">{{ $phoneMasked }}</div>
            </div>

            <div class="item item--email">
                <label><span>訂單信箱</span></label>
                <div class="conta conta--email" data-full="{{ $order->email }}">{{ $emailMasked }}</div>
            </div>

            <div class="item">
                <label><span>訂購商品</span></label>
                <div class="conta">
                    @foreach($order->products as $item)
                        <div class="shopitem">

                            <div class="shopMsg">
                                <span class="name">{{ $item->product_name }}</span>
                                <span>× {{ $item->number }}</span>
                                <span>NT${{ number_format(round($item->total_price)) }}</span>
                            </div>
                        </div>

                    @endforeach
                </div>
            </div>

            <div class="item">
                <label><span>配送方式</span></label>
                <div class="conta">{{ $order->delivery_type?"超商(7-11) 取貨付款":"快遞宅配 貨到付款" }}</div>
            </div>

            <div class="item">
                <label><span>訂單總額</span></label>
                <div class="conta">NT${{ number_format(round($order->total_price)) }}（{{ $order->freight>0?"含運費$".number_format(round($order->freight)):"免運費" }}）</div>
            </div>

            @if($order->delivery_type > 0)
                <div class="item">
                    <label><span>取貨門市</span></label>
                    <div class="conta">
                        {{ $order->shop_no }} {{ $order->shop_name }}門市
                    </div>
                </div>
            @endif
            <div class="item">

                @if($order->delivery_type > 0)
                    <label><span>門市地址</span></label>
                    <div class="conta">{{ $order->address }}</div>
                @else
                    <label><span>配送地址</span></label>
                    <div class="conta">{{ $order->city.$order->county.$order->street.$order->address }}</div>
                @endif
            </div>


            <div class="item">
                <label><span>訂單備註</span></label>
                <div class="conta">{{ $order->remarks?:"無" }}</div>
            </div>
        </div>

    </div>
</main>
@endsection
