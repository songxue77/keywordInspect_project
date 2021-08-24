<?php

namespace Tests\Feature\Eduplan;

use App\Services\Eduplan\ExternalApiCallService;
use App\Entities\Admin;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NaverApiCallTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    private $admin;
    private $externalApiCallService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();
        $this->admin = factory(Admin::class)->create();
        $this->externalApiCallService = $this->app->make(ExternalApiCallService::class);
    }

    /** @test */
    public function getKeywordStatistics()
    {
        // 5개씩 보낼 수 있음
        $keywords = [
            '삼성채용',
            '공기업전용',
            '현대채용',
            '전기기사필기',
            '자기소개서예시'
        ];
        
        $apiResult = $this->externalApiCallService->getKeywordQCByAPI($keywords);
        
        $this->assertEquals(5, count($apiResult));
        foreach ($apiResult as $key => $result) {
            $this->assertEquals(true, in_array($key, $keywords));
        }
    }
}
