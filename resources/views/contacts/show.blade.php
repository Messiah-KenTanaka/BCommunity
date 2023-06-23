@extends('app')

@section('title', config('app.name') . ' | お問合せ内容')

@section('content')
  @include('nav')
  <div class="container">
    <div class="row">
      @include('sidemenu')
      <div class="col">
        @if(in_array(Auth::id(), [1, 2, 3]))
          <h2 class="text-center main-ja-font-family mt-5">お問合せ内容</a></h2>
          @foreach($contactContent as $contact)
            @include('contacts.card')
          @endforeach
          @if ($contactContent->hasMorePages())
            <p class="text-center my-3"><a href="{{ $contactContent->nextPageUrl() }}">もっと見る</a></p>
          @endif
        @endif
        @include('floatingButton')
      </div>
    </div>
  </div>
  @include('bottomNav')
@endsection