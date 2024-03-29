<div class="card-bottom">
    {{-- リツイートされた投稿の場合 --}}
    @if ($article->isRetweet ?? false)
        <div class="card-body pt-2 pb-0">
            <span class="text-muted">
                <i class="fa-solid fa-retweet"></i>
                リツイートされました
            </span>
        </div>
    @endif
    <div class="card-body d-flex flex-row pb-0">
        <a href="{{ route('users.show', ['name' => $article->user->name]) }}" class="text-dark">
            @if ($article->user->image)
                <img src="{{ $article->user->image }}" class="rounded-circle mb-1 mr-1"
                    style="width: 50px; height: 50px; object-fit: cover;">
            @else
                <img src="{{ asset('images/noimage02.jpg') }}" class="rounded-circle mb-1 mr-1"
                    style="width: 50px; height: 50px; object-fit: cover;">
            @endif
        </a>
        <div>
            <div class="font-weight-bold pl-2">
                <a href="{{ route('users.show', ['name' => $article->user->name]) }}" class="text-dark">
                    {{ $article->user->nickname }}
                </a>
                @if (!empty($article->user->achievementImage))
                    <img src="{{ asset($article->user->achievementImage) }}" style="width: 25px; height: 25px;"
                        class="cursor-pointer" data-toggle="modal" data-target="#modal-degree">
                    @include('degree_modal')
                @endif
            </div>
            <div class="text-muted small pl-2">
                {{ $article->created_at->diffForHumans() }}
            </div>
        </div>

        {{-- ログインユーザーの投稿の場合 --}}
        @if (Auth::id() === $article->user_id)
            <!-- dropdown -->
            <div class="ml-auto card-text">
                <div class="dropdown">
                    <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <button type="button" class="btn btn-link text-muted m-0 p-2">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        {{--  更新はできないようにコメントアウト
              <a class="dropdown-item" href="{{ route("articles.edit", ['article' => $article]) }}">
                <i class="fas fa-pen mr-1"></i>投稿を更新する
              </a>
              <div class="dropdown-divider"></div>  --}}
                        <a class="dropdown-item text-danger" data-toggle="modal"
                            data-target="#modal-delete-{{ $article->id }}">
                            <i class="fas fa-trash-alt mr-1"></i>投稿を削除
                        </a>
                    </div>
                </div>
            </div>
            <!-- dropdown -->

            <!-- modal -->
            <div id="modal-delete-{{ $article->id }}" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="demoModalTitle">確認</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form method="POST" action="{{ route('articles.delete', ['article' => $article]) }}">
                            @csrf
                            @method('PATCH')
                            <div class="modal-body">
                                {{--  {{ $article->title }}を削除します。よろしいですか？  --}}
                                削除します。よろしいですか？
                            </div>
                            <div class="border-maintenance-modal modal-footer justify-content-between">
                                <a class="btn btn-outline-grey" data-dismiss="modal">キャンセル</a>
                                <button type="submit" class="btn btn-danger loading-btn">削除する</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- modal -->
        @endif

        {{-- 投稿したユーザー以外の場合 --}}
        @if (!(Auth::id() === $article->user_id))
            {{-- 未ログインは何も表示しない --}}
            @auth
                <!-- dropdown -->
                <div class="ml-auto card-text">
                    <div class="dropdown">
                        <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <button type="button" class="btn btn-link text-muted m-0 p-2">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <form method="POST" action="{{ route('report.index', ['userId' => Auth::id()]) }}">
                                @csrf
                                @method('POST')
                                <input type="hidden" name="article_id" value="{{ $article->id }}">
                                <input type="hidden" name="article_user_id" value="{{ $article->user->id }}">
                                <button class="dropdown-item" type="submit">
                                    <i class="fa-regular fa-flag"></i> この投稿を報告
                                </button>
                            </form>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" data-toggle="modal"
                                data-target="#modal-user-block-{{ $article->user->id }}">
                                <i class="fa-solid fa-ban"></i> {{ $article->user->nickname }}さんをブロック
                            </a>
                        </div>
                    </div>
                </div>
                <!-- dropdown -->
                <!-- modal ユーザーブロック-->
                <div id="modal-user-block-{{ $article->user->id }}" class="modal fade" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="demoModalTitle">確認</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form method="POST" action="{{ route('users.userBlock', ['userId' => Auth::id()]) }}">
                                @csrf
                                @method('POST')
                                <input type="hidden" name="article_user_id" value="{{ $article->user->id }}">
                                <div class="modal-body">
                                    {{ $article->user->nickname }}さんをブロックします。よろしいですか？
                                </div>
                                <div class="border-maintenance-modal modal-footer justify-content-between">
                                    <a class="btn btn-outline-grey" data-dismiss="modal">キャンセル</a>
                                    <button type="submit" class="btn btn-danger loading-btn">OK</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- modal end -->
            @endauth
        @endif
    </div>
    <div class="card-body pt-0 pb-1">

        {{-- <div class="card-text">
      <a class="text-dark" href="{{ route('articles.show', ['article' => $article]) }}">
        {!! nl2br(Functions::makeLink(e( $article->body ))) !!}
      </a>
    </div> --}}

        <article-preview :article="{{ $article }}"></article-preview>

    </div>
    @foreach ($article->tags as $tag)
        @if ($loop->first)
            <div class="card-body pt-0 pb-1 pl-3">
                <div class="card-text line-height">
        @endif
        <a href="{{ route('tags.show', ['name' => $tag->name]) }}" class="mr-1">
            {{ Functions::getNameFifteenEllipsis($tag->hashtag) }}
        </a>
        @if ($loop->last)
</div>
</div>
@endif
@endforeach
<div class="d-flex">
    @if ($article->fish_size)
        <div class="pt-0 pb-2 pl-3">
            <span class="font-black-ops-one pl-1">
                <i class="fa-solid fa-ruler-horizontal mr-1" style="color: #f91a01;"></i>
                {{ $article->fish_size }}
            </span>
            cm
        </div>
    @endif
    {{-- @if ($article->fish_size && $article->weight)
        <span class="lead pl-3">
          /
        </span>
    @endif --}}
    @if ($article->weight)
        <div class="card-body pt-0 pb-1 pl-3">
            <span class="font-black-ops-one pl-1">
                <i class="fa-solid fa-weight-scale mr-1" style="color: #41230e;"></i>
                {{ number_format($article->weight) }}
            </span>
            g
        </div>
    @endif
</div>

@if ($article->pref || $article->bass_field)
    <div class="card-body pt-0 pb-1 pl-3">
        <i class="fa-solid fa-location-dot mr-1" style="color: #33a853;"></i>
        @if ($article->pref)
            <a onclick="location.href='{{ route('ranking.pref', ['pref' => $article->pref]) }}'">
                <small class="border border-pref p-2 mr-2">
                    {{ $article->pref }}
                </small>
            </a>
        @endif
        @if ($article->bass_field)
            <a onclick="location.href='{{ route('ranking.field', ['field' => $article->bass_field]) }}'">
                <small class="border border-pref p-2">
                    {{ $article->bass_field }}
                </small>
            </a>
        @endif
    </div>
@endif
@if ($article->image)
    {{--  <a href="{{ route('articles.show', ['article' => $article]) }}">
      <img src="{{ $article->image }}" class="img-fluid border-image p-3">
    </a>  --}}
    <article-image :article="{{ $article }}"></article-image>
@endif

<div class="card-body pt-0 pb-1 pl-3">
    <div class="card-text d-flex">
        <article-like :initial-is-liked-by='@json($article->isLikedBy(Auth::user()))'
            :initial-count-likes='@json($article->count_likes)' :authorized='@json(Auth::check())'
            endpoint="{{ route('articles.like', ['article' => $article]) }}">
        </article-like>
        <div class="pl-3">
            <article-retweet :initial-is-retweeted-by='@json($article->isRetweetedBy(Auth::user()))'
                :initial-count-retweets='@json($article->count_retweets)' :authorized='@json(Auth::check())'
                endpoint="{{ route('articles.retweets', ['article' => $article]) }}">
            </article-retweet>
        </div>
        <div class="pl-3">
            <a class="btn m-0 p-1 shadow-none text-dark"
                href="{{ route('articles.show', ['article' => $article]) }}">
                <i class="fa-regular fa-message mr-1"></i>
            </a>
            {{ $article->comment_count }}
        </div>
    </div>
</div>
</div>
