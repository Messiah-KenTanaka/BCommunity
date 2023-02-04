@extends('app')

@section('title', $pref . 'ランキング')

@section('content')
  @include('nav')
  <div class="container">
    <div class="row">
      @include('sidemenu')
      <div class="col">
        @include('ranking.pref')
        <h2 class="text-center my-3 main-ja-font-family">ランキング</h2>
        <h2 class="text-center my-3 main-ja-font-family">{{ $pref }}</h2>
        @foreach($ranking as $key => $article)
          <div class="text-center my-4 main-ja-font-family h3">
            @switch(++$key)
              @case(1)
                <span class="first-gold">{{ $key }}位</span>
                  @break
              @case(2)
                <span class="second-silver">{{ $key }}位</span>
                  @break
              @case(3)
                <span class="third-copper">{{ $key }}位</span>
                  @break
              @default
                <span class="">{{ $key }}位</span>
            @endswitch
          </div>
          @include('articles.card')
        @endforeach
      </div>
      @include('sidemenuRight')
    </div>
  </div>
  @include('bottomNav')
@endsection