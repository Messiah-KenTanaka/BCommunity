@extends('app')

@section('title', config('app.name') . ' | 投稿')

@include('nav')

@section('content')
    <div class="container">
        <div class="row">
            @include('sidemenu')
            <div class="col col-xl-9 no-padding-margin">
                <div class="mt-3 mb-20">
                    <div class="card-body pt-0">
                        @include('error_card_list')
                        <div class="card-text">
                            <form method="POST" action="{{ route('articles.store') }}" enctype="multipart/form-data">
                                @include('articles.form')
                                <button type="submit" id="submit-btn"
                                    class="btn rounded-pill bg-primary text-white btn-block">
                                    <i class="fa-solid fa-paper-plane pr-2"></i>
                                    <span id="submit-text">投稿する</span>
                                    <div class="spinner-border spinner-border-sm ml-2 d-none" role="status">
                                        <span class="sr-only">読み込み中...</span>
                                    </div>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('bottomNav')
@endsection
