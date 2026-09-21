<?php

namespace jeremykenedy\LaravelPhpInfo\App\Http\Controllers;

if (class_exists('App\Http\Controllers\Controller') && method_exists('App\Http\Controllers\Controller', 'middleware')) {
    abstract class Controller extends \App\Http\Controllers\Controller
    {
    }
} else {
    abstract class Controller extends \Illuminate\Routing\Controller
    {
    }
}
