@extends('web::layout.layout')

@section('style')
    @parent
@stop

@section('script')
@stop

@section('content')
<main class="page-faq">
    @include('web.widgets.head-banner')
    @include('web.widgets.breadcrumb', ['itemsHtml' => '<li class="breadcrumb__item">減肥常見疑問解答Q&A</li>'])
    <!-- <p class="en-title">Q&A</p>
    <h1 class="title">減肥常見疑問解答</h1> -->

    @include('web.widgets.qa', ['faqs' => $faq])

  
</main>
@include('web.widgets.update-box')
@endsection

