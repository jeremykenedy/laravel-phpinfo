@extends($phpInfoLayout)

@section('title')
    {{ trans('laravelPhpInfo::laravel-phpinfo.title') }}
@endsection

@section('content')
    @if(config('laravelPhpInfo.usePHPinfoCSS'))
        <link rel="stylesheet" href="{{ route('laravelPhpInfo::asset', ['file' => 'php-info.css', 'v' => $phpInfoAssetVersion]) }}">
    @endif
    <script src="{{ route('laravelPhpInfo::asset', ['file' => 'php-info.js', 'v' => $phpInfoAssetVersion]) }}" defer></script>

    <div class="phpinfo-page {{ $phpInfoClasses['container'] }}" data-phpinfo data-theme="{{ $phpInfoTheme }}">
        <div class="{{ $phpInfoClasses['row'] }}">
            <div class="{{ $phpInfoClasses['column'] }}">
                <section class="phpinfo-card {{ $phpInfoClasses['card'] }} {{ config('laravelPhpInfo.bootstrapCardClasses') }}" aria-labelledby="phpinfo-title">
                    <header class="phpinfo-header {{ $phpInfoClasses['header'] }}">
                        <div>
                            <p class="phpinfo-eyebrow">{{ trans('laravelPhpInfo::laravel-phpinfo.runtime') }}</p>
                            <h1 id="phpinfo-title">{{ trans('laravelPhpInfo::laravel-phpinfo.title') }}</h1>
                            <p class="phpinfo-description">{{ trans('laravelPhpInfo::laravel-phpinfo.description') }}</p>
                        </div>
                        <span class="phpinfo-version">PHP {{ PHP_VERSION }}</span>
                    </header>
                    <div class="{{ $phpInfoClasses['body'] }}">
                        <div class="phpinfo-toolbar" data-phpinfo-controls hidden>
                            <div class="phpinfo-search">
                                <label for="phpinfo-search">{{ trans('laravelPhpInfo::laravel-phpinfo.search') }}</label>
                                <input id="phpinfo-search" type="search" data-phpinfo-search placeholder="{{ trans('laravelPhpInfo::laravel-phpinfo.search_placeholder') }}" aria-controls="phpinfo-output" autocomplete="off">
                            </div>
                            <div class="phpinfo-theme">
                                <label for="phpinfo-theme">{{ trans('laravelPhpInfo::laravel-phpinfo.theme') }}</label>
                                <select id="phpinfo-theme" data-phpinfo-theme>
                                    <option value="system">{{ trans('laravelPhpInfo::laravel-phpinfo.system') }}</option>
                                    <option value="light">{{ trans('laravelPhpInfo::laravel-phpinfo.light') }}</option>
                                    <option value="dark">{{ trans('laravelPhpInfo::laravel-phpinfo.dark') }}</option>
                                </select>
                            </div>
                        </div>
                        <p class="phpinfo-empty" data-phpinfo-empty role="status" hidden>{{ trans('laravelPhpInfo::laravel-phpinfo.no_results') }}</p>
                        <div id="phpinfo-output" class="php-info" tabindex="0" role="region" aria-label="{{ trans('laravelPhpInfo::laravel-phpinfo.title') }}">
                            @if(! $phpInfo['available'])
                                <p role="status">{{ trans('laravelPhpInfo::laravel-phpinfo.unavailable') }}</p>
                            @elseif($phpInfo['html'])
                                {!! $phpInfo['output'] !!}
                            @else
                                <pre>{{ $phpInfo['output'] }}</pre>
                            @endif
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
