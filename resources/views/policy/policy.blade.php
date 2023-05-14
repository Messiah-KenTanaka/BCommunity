@extends('app')

@section('title', config('app.name') . ' | プライバシーポリシー')

@include('nav')

@section('content')
  <div class="container">
    <div class="row">
      @include('sidemenu')
      <div class="col mt-4">
        <h2 class="text-center main-ja-font-family">プライバシーポリシー</a></h2>
          <div>
            <p>当アプリケーションは、利用者のプライバシー保護を重視し、利用者情報の取り扱いには細心の注意を払っています。以下のプライバシーポリシーは、当アプリケーションで取り扱う個人情報について、どのように収集・利用・管理されるかについて示したものです。</p>
            <p>1. 収集する情報の範囲</p>
              <p>当アプリケーションでは、以下のような方法で個人情報を収集する場合があります。
              利用者が自ら入力する情報（例：氏名、メールアドレス、住所など）
              利用者がサービスを利用する際に自動的に収集される情報（例：IPアドレス、利用時間帯、OS種類など）
              その他、サービス提供に必要な情報</p>
            <p>2. 収集した情報の利用目的</p>
              <p>当アプリケーションは、以下のような目的で利用者の個人情報を利用します。
              サービス提供のため
              お問い合わせ対応のため
              キャンペーン・イベント等の案内のため
              サービス改善のための分析、アンケートの実施等</p>
            <p>3. 個人情報の管理</p>
              <p>当アプリケーションは、利用者の個人情報を適切に管理し、紛失・漏洩・改ざんなどの防止に努めます。また、個人情報を外部に委託する場合がある場合は、個人情報保護法に基づき、委託先に対し適切な契約を締結し、管理を徹底します。</p>
            <p>4. 個人情報の第三者提供について</p>
              <p>当アプリケーションは、利用者の個人情報を適切に管理し、法律に基づく場合以外で、利用者の個人情報を第三者に提供することはありません。</p>
            <p>5. Cookie等の利用について</p>
              <p>当アプリケーションでは、利用者の利便性向上のため、Cookieを使用しています。Cookieは、利用者のブラウザに保存され、Webサイトの閲覧時に利用されます。なお、Cookieの使用によって、利用者の個人情報が収集されることはありません。また、利用者は、ブラウザの設定によりCookieの受け取りを拒否することができますが、その場合、当アプリケーションの一部の機能が制限される場合があります。
              当アプリケーションでは、Google Analyticsを利用して、利用者の利便性向上のための情報を収集しています。Google Analyticsは、Cookieを利用して、利用者の情報を収集しています。収集された情報は、当アプリケーションの利便性向上以外の目的には使用されません。また、Google Analyticsの利用によって、利用者の個人情報が収集されることはありません。なお、Google Analyticsの利用については、Googleのプライバシーポリシーをご確認ください。</p>
          </div>
      </div>
    </div>
  </div>
  @include('bottomNav')
@endsection
