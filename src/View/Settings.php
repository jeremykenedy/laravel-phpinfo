<?php

namespace jeremykenedy\LaravelPhpInfo\View;

use Illuminate\Contracts\Config\Repository;

class Settings
{
    private $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function get($key, $default = null)
    {
        return $this->config->get('laravelPhpInfoUi.'.$key, $this->config->get('laravelPhpInfo.'.$key, $default));
    }

    public function css()
    {
        $css = $this->get('cssFramework');

        if (in_array($css, ['bootstrap3', 'bootstrap4', 'bootstrap5', 'tailwind'], true)) {
            return $css;
        }

        return (string) $this->config->get('laravelPhpInfo.bootstapVersion') === '4' ? 'bootstrap4' : 'bootstrap3';
    }

    public function view()
    {
        return $this->get('view') === 'standalone' ? 'standalone' : 'app';
    }

    public function theme()
    {
        $theme = $this->get('theme', 'system');

        return in_array($theme, ['system', 'light', 'dark'], true) ? $theme : 'system';
    }

    public function classes()
    {
        if ($this->css() === 'tailwind') {
            return [
                'container' => 'mx-auto max-w-7xl px-4 py-8',
                'row' => '', 'column' => 'w-full',
                'card' => 'overflow-hidden rounded-2xl border border-slate-200 shadow-sm',
                'header' => 'px-6 py-5', 'body' => 'px-6 py-4',
            ];
        }

        $legacy = $this->css() === 'bootstrap3';

        return [
            'container' => 'container', 'row' => 'row', 'column' => 'col-md-12',
            'card' => $legacy ? 'panel panel-default' : 'card',
            'header' => $legacy ? 'panel-heading' : 'card-header',
            'body' => $legacy ? 'panel-body' : 'card-body',
        ];
    }
}
