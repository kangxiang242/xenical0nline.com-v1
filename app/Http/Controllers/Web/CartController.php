<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

use App\Services\CartService;
use Illuminate\Http\Request;


class CartController extends Controller
{

    public function get(Request $request){
        $cart = new CartService();
        $data = $cart->getCart();

        return view('web.widgets.ajax-cart',$data);
    }




}
