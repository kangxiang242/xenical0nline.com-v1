<?php


namespace App\Http;


class JsonResponse
{
    protected $status = true;

    protected $statusCode = 200;

    protected $message = '';

    protected $title = '';

    protected $redirect = '';

    protected $flush = false;

    protected $data = [];

    protected $options = [];

    public function title($title){
        $this->title = $title;
        return $this;
    }

    public function message($message){
        $this->message = $message;
        return $this;
    }

    public function status($status){
        $this->status = $status;
        return $this;
    }

    public function statusCode($code){
        $this->statusCode = $code;
        return $this;
    }

    public function data(array $data){
        $this->data = $data;
        return $this;
    }

    public function redirect($redirect){
        $this->redirect = $redirect;
        return $this;
    }

    public function options($options = []){
        $this->options = $options;
    }

    public function flash(){
        $this->flush  = true;
        $send = [
            'status'=>$this->status,
            'title'=>$this->title,
            'message'=>$this->message,
            'data'=>$this->data,
            'redirect'=>$this->redirect,
            'options'=>$this->options,
            '_token'=>csrf_token(),
        ];
        session()->flash('flash',json_encode($send,JSON_UNESCAPED_UNICODE));
        return $this;
    }



    public function send(){
        $send = [
            'status'=>$this->status,
            'title'=>$this->title,
            'message'=>$this->message,
            'data'=>$this->data,
            'redirect'=>$this->redirect,
            'flush'=>$this->flush,
            'options'=>$this->options,
            '_token'=>csrf_token(),
        ];

        return response()->json($send)->setStatusCode($this->statusCode);
    }

    public static function make(){
        return new self();
    }
}
