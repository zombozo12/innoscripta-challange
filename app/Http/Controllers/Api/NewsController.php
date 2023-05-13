<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Module\NewsApi\NewsApi;
use App\Http\Requests\News\NewsRequest;
use App\Http\Responses\ApiResponse;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class NewsController extends Controller
{

    /**
     * @throws Exception
     */
    public function GetAllNews(NewsRequest $request): JsonResponse
    {
        $response = new ApiResponse(now(), $request->fingerprint());

        $request->validated();

        $query = $request->query('q');
        $queryInTitle = $request->query('qInTitle');
        $sources = $request->query('sources');
        $country = $request->query('country');
        $domains = $request->query('domains');
        $exclude_domains = $request->query('exclude_domains');
        $category = $request->query('category');
        $from = $request->query('from');
        $to = $request->query('to');
        $language = $request->query('language');
        $sort_by = $request->query('sortBy');
        $page_size = $request->query('pageSize');
        $page = $request->query('page');

        $news_api = new NewsApi(env('NEWSAPI_KEY'));

        $news = $news_api->GetEverything($query, $queryInTitle, $sources, $domains, $exclude_domains, $from, $to, $language, $sort_by, $page_size, $page);

        $data = [
            'news' => $news
        ];

        return $response->setOKResponse($data);
    }
}
