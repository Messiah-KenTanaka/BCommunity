@extends('app')

@section('title', config('app.name') . ' | 全国ランキング | サイズ')

@section('content')
    @include('nav')
    <div class="container">
        <div class="row">
            @include('sidemenu')
            <div class="col">
                <div class="content-title">全国ランキング サイズ</div>
                @include('ranking.tabs', ['hasSize' => true, 'hasWeight' => false])
                @foreach ($ranking as $key => $article)
                    @include('ranking.card', ['rank' => ++$key])
                @endforeach
                {{-- @include('floatingButton') --}}
            </div>
        </div>
    </div>
    @include('bottomNav')
@endsection
