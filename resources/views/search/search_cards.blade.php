<div class="container">
    <div class="row">
        <div class="col-6 mb-4">
            <div class="card card-box-shadow text-center">
                <a href="{{ route('ranking.index') }}" class="card-body text-decoration-none text-dark">
                    <i class="fas fa-crown fa-5x mb-3"></i>
                    <h5 class="card-title">ランキング<span class="d-none d-sm-inline">から探す</span></h5>
                    <p class="card-text">一番釣れている魚が見つかる！</p>
                </a>
            </div>
        </div>
        <div class="col-6 mb-4">
            <!-- dropup -->
            <div class="card card-box-shadow text-center">
                <a class="card-body" data-toggle="modal" data-target="#trendModal">
                    <i class="fas fa-bolt fa-5x mb-3"></i>
                    <h5 class="card-title">トレンド<span class="d-none d-sm-inline">から探す</span></h5>
                    <p class="card-text">今の流行りを見つけよう！</p>
                </a>
            </div>

            <div class="modal fade" id="trendModal" tabindex="-1" role="dialog" aria-labelledby="trendModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title font-weight-bold" id="trendModalLabel"><i
                                    class="fas fa-bolt mr-2"></i>最近のトレンド</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            @foreach ($tags as $i => $tag)
                                <button class="dropdown-item d-flex align-items-start" type="button"
                                    onclick="location.href='{{ route('tags.show', ['name' => $tag->name]) }}'">
                                    <div>
                                        {{ ++$i }}.
                                    </div>
                                    <div>
                                        <span
                                            class="ml-1 font-weight-bold">{{ '#' . Functions::getNameFifteenEllipsis($tag->name) }}</span><br>
                                        <span class="text-muted">{{ $tag->count }}件</span>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <!-- dropup -->
        </div>
        <div class="col-6 mb-4">
            <div class="card card-box-shadow text-center">
                <a href="{{ route('map.index') }}" class="card-body text-decoration-none text-dark">
                    <i class="fas fa-map-marker-alt fa-5x mb-3"></i>
                    <h5 class="card-title">釣り場<span class="d-none d-sm-inline">から探す</span></h5>
                    <p class="card-text">行きたい釣り場を見つける！</p>
                </a>
            </div>
        </div>
        <div class="col-6 mb-4">
            <div class="card card-box-shadow text-center">
                <a href="{{ route('weather.index') }}" class="card-body text-decoration-none text-dark">
                    <i class="fas fa-cloud-sun fa-5x mb-3"></i>
                    <h5 class="card-title">天気<span class="d-none d-sm-inline">を調べる</span></h5>
                    <p class="card-text">天気をチェックしよう！</p>
                </a>
            </div>
        </div>
        <div class="col-6 mb-4">
            <div class="card card-box-shadow text-center">
                <a href="{{ route('searchUsers') }}" class="card-body text-decoration-none text-dark">
                    <i class="fa-solid fa-users fa-5x mb-3"></i>
                    <h5 class="card-title">釣り人<span class="d-none d-sm-inline">を探す</span></h5>
                    <p class="card-text">気になるバサーを見つける！</p>
                </a>
            </div>
        </div>
        {{--  <div class="col-6 mb-4">
            <div class="card card-box-shadow text-center">
                <a href="https://enchannel-9b79709345f9.herokuapp.com/"
                    class="card-body text-decoration-none text-dark">
                    <i class="fas fa-yin-yang fa-5x mb-3"></i>
                    <h5 class="card-title">89<span class="d-none d-sm-inline">ちゃんねる</span></h5>
                    <p class="card-text">スレッドで語り合おう！</p>
                </a>
            </div>
        </div>  --}}
    </div>
</div>
