<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use jcobhams\NewsApi\NewsApi;

class NewsController extends Controller
{
    private NewsApi $news_api;

    public function __construct()
    {
        $this->news_api = new NewsApi(env('NEWSAPI_KEY'));
    }


}
