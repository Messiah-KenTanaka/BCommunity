@extends('app')

@section('title', config('app.name') . ' | ユーザー登録')

@section('content')
  @include('nav')
  <div class="container">
    <div class="row">
      <div class="mx-auto col col-12 col-sm-11 col-md-9 col-lg-7 col-xl-6 mt-5 mb-20">
        <h1 class="text-center"><a class="text-dark main-font-family" href="/">BASSER</a></h1>
        <div class="card mt-3">
          <div class="card-body text-center">
            <h2 class="h3 card-title text-center mt-2">ユーザー登録</h2>

            <a href="{{ route('login.google') }}" class="btn btn-block btn-danger">
              <i class="fab fa-google mr-1"></i>Googleで登録
            </a>
            <a href="{{ route('login.apple') }}" class="btn btn-block btn-dark mt-3">
              <i class="fab fa-apple mr-1"></i>Appleで登録
            </a>            

            @include('error_card_list')

            <div class="card-text">
              <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="md-form">
                  <label for="name">ユーザー名</label>
                  <input class="form-control" type="text" id="name" name="name" required value="{{ old('name') }}">
                  <small><span class="text-danger">ユーザー名は登録後の変更はできません</span></small><br>
                  <small class="extra-small">※3〜16文字以内で入力してください</small><br>
                  <small class="extra-small">※漢字、ひらがな、カタカナ、及び英字（大文字・小文字）のみ可</small>
                </div>
                <div class="md-form">
                  <label for="email">メールアドレス</label>
                  <input class="form-control" type="text" id="email" name="email" required value="{{ old('email') }}" >
                  <small class="text-danger">メールアドレスは登録後の変更はできません</small>
                </div>
                <div class="md-form">
                  <label for="password">パスワード</label>
                  <input class="form-control" type="password" id="password" name="password" required>
                </div>
                <div class="md-form">
                  <label for="password_confirmation">パスワード(確認)</label>
                  <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" required>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="1" id="agreement" name="agreement" required>
                  <label class="form-check-label" for="agreement">
                    利用規約に同意する
                  </label>
                </div>
                <div class="text-center">
                  <a href="#" data-toggle="modal" data-target="#registerModalId">利用規約を表示</a>
                </div>
                <button class="btn btn-block bg-primary text-white mt-2 mb-2" type="submit" disabled>ユーザー登録</button>
              </form>

              <div class="mt-0">
                <a href="{{ route('login') }}" class="card-text">ログインはこちら</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @component('components.terms-modal', ['id' => 'registerModalId'])
  @endcomponent

@endsection
