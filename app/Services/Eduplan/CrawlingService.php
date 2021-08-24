<?php

declare(strict_types=1);

namespace App\Services\Eduplan;

use Goutte\Client;
use Exception;
use Log;

class CrawlingService
{
    private const TRY_COUNT = 3;
    private $client;

    public function __construct(
        Client $client
    ) {
        $this->client = $client;
    }

    public function getPageUrlByClickAdvertisementUrl(string $url)
    {
        for ($i = 0; $i < self::TRY_COUNT; $i++) {
            try {
                $result = $this->client->request('GET', $url);
                break;
            } catch (Exception $e) {
                Log::error('광고 클릭 시 오류 try count: '.($i+1).' code:'.$e->getCode().' message:'.$e->getMessage());
            }
        }

        return $result ? $result->getUri() : '';
    }

    public function getNaverSearchViewAllPageResult(string $keyword)
    {
        $url = 'https://search.naver.com/search.naver?where=view&sm=tab_jum&query='.urlencode($keyword);
        
        for ($i = 0; $i < self::TRY_COUNT; $i++) {
            try {
                $result = $this->client->request('GET', $url);
                break;
            } catch (Exception $e) {
                Log::error('키워드 검색 전체 뷰 크롤링 오류:'.$keyword.' try count:'.($i+1).' code:'.$e->getCode().' message:'.$e->getMessage());
            }
        }

        return $result ?? '';
    }

    public function getNaverSearchViewCafePageResult(string $keyword)
    {
        $url = 'https://search.naver.com/search.naver?query='.urlencode($keyword).'&nso=&where=article&sm=tab_opt';

        for ($i = 0; $i < self::TRY_COUNT; $i++) {
            try {
                $result = $this->client->request('GET', $url);
                break;
            } catch (Exception $e) {
                Log::error('키워드 검색 카페 뷰 크롤링 오류:'.$keyword.' try count:'.($i+1).' code:'.$e->getCode().' message:'.$e->getMessage());
            }
        }

        return $result ?? '';
    }
}
