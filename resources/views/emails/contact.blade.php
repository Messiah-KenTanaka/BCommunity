<p>{{ $name }} 様</p>
<p>お問い合わせありがとうございます。</p>
<p>以下の内容でお問い合わせを受け付けました。</p>
<hr>
<p>お名前： {{ $name }}</p>
<p>メールアドレス： {{ $email }}</p>
<p>お問い合わせ内容：</p>
<p>{{ $data['message'] }}</p>
<hr>
<p>このメールに心当たりがない場合は、お手数ですがこのメールを破棄してください。</p>