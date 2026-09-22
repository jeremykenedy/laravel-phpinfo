<?php

namespace jeremykenedy\LaravelPhpInfo\View;

use Illuminate\Contracts\View\View;
use jeremykenedy\LaravelPhpInfo\PhpInfo;

class PhpInfoComposer
{
    private $settings;

    private $phpInfo;

    public function __construct(Settings $settings, PhpInfo $phpInfo)
    {
        $this->settings = $settings;
        $this->phpInfo = $phpInfo;
    }

    public function compose(View $view)
    {
        $view->with('phpInfo', $this->phpInfo->capture());
        $view->with('phpInfoClasses', $this->settings->classes());
        $view->with('phpInfoTheme', $this->settings->theme());
        $view->with('phpInfoLayout', $this->settings->view() === 'standalone'
            ? 'laravelPhpInfo::layouts.standalone'
            : config('laravelPhpInfo.laravelPhpInfoBladeExtended'));
        $view->with('phpInfoAssetVersion', filemtime(__DIR__.'/../resources/assets/css/php-info.css').'-'.filemtime(__DIR__.'/../resources/assets/js/php-info.js'));
    }
}
