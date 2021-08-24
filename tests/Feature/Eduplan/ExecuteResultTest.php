<?php

namespace Eduplan;

use App\Entities\Eduplan\KeywordGroup;
use App\Entities\Eduplan\KeywordGroupKeyword;
use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;
use App\Repositories\Eduplan\SiteRepository;
use App\Repositories\Eduplan\ExecuteResultRepository;
use App\Entities\Admin;
use App\Entities\Eduplan\ExecuteResult;
use App\Exports\ExecuteResultExport;

class ExecuteResultTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;
    use InteractsWithSession;

    private $admin;
    private $executeResult;
    private $siteRepository;
    private $executeResultRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();
        $this->admin = factory(Admin::class)->create();
        $this->executeResult = $this->app->make(ExecuteResult::class);
        $this->siteRepository = $this->app->make(SiteRepository::class);
        $this->executeResultRepository = $this->app->make(ExecuteResultRepository::class);
    }

    /** @test */
    public function adminCanViewExecuteIndexPage()
    {
        // action
        $test = $this->actingAs($this->admin)->get('/eduplan/execute');

        // assert
        $test->assertStatus(200);
    }

    /** @test */
    public function adminGetRedirectionWhenViewNotExistShowPage()
    {
        // action
        $test = $this->actingAs($this->admin)->get('/eduplan/execute/show/999999');

        // assert
        $test->assertStatus(302);
    }

    /** @test */
    public function showProcessResult()
    {
        $siteTypeText = [
            'Blog' => '블로그',
            'Cafe' => '카페',
            'Post' => '포스트'
        ];

        // create a ExecuteResult
        $executeResult = factory(ExecuteResult::class)->create();

        // action
        $test = $this->actingAs($this->admin)->get('/eduplan/execute/show/'.$executeResult['ExecuteResultIdx']);

        // data
        $executeResultData = json_decode($executeResult['ExecuteResult'], true);
        $processResult = $executeResultData['ProcessResult'];

        // assert
        $test->assertStatus(200);
        foreach ($processResult as $processData) {
            $test->assertSee($processData['inspectDatetime']);
            $test->assertSee($processData['result']['keyword']);
            $test->assertSee(number_format($processData['result']['monthlyPcQcCnt']));
            $test->assertSee(number_format($processData['result']['monthlyMobileQcCnt']));
            $test->assertSee(number_format($processData['result']['monthlyTotalQcCnt']));
            $test->assertSee($processData['result']['aHeadSiteType']);
            $test->assertSee($processData['result']['sectionRank']);
            $test->assertSee($processData['result']['cafeTopRank'] ?? '미노출');
            $test->assertSee($processData['result']['isAdShowTop'] === 'true' ? 'O' : 'X');
            foreach ($processData['result']['siteLink'] as $siteLink) {
                $test->assertSee($siteLink['Name']);
                $test->assertSee($siteLink['WriterId']);
                $test->assertSee($siteTypeText[$siteLink['SiteType']]);
            }
            foreach ($processData['result']['cafeLink'] as $cafeLink) {
                $test->assertSee($cafeLink['Name']);
                $test->assertSee($cafeLink['WriterId']);
                $test->assertSee($siteTypeText[$cafeLink['SiteType']]);
            }
        }
    }

    /** @test */
    public function processKeywordGroup()
    {
        // random으로 생성하면 크롤링에 영향을 주기 때문에 고정값 사용
        $keywordPreset = [
            '삼성채용',
            '공기업전용',
            '현대채용',
            '전기기사필기',
            '자기소개서예시',
            'SK하이닉스채용',
            '정보처리기사',
            '건강보험공단',
            '전기기사인강',
            '전기기사필기'
        ];

        // create a keyword group
        $keywordGroup = factory(KeywordGroup::class)->create();
        $keywordCnt = $keywordGroup->KeywordCnt;

        // create keyword group keywords
        $keywords = array_slice($keywordPreset, 0, $keywordCnt);
        for ($i = 0; $i < $keywordCnt; $i++) {
            $keywords[] = factory(KeywordGroupKeyword::class)->create([
                'KeywordGroupIdx' => $keywordGroup->KeywordGroupIdx,
                'KeywordGroupSortNo' => $i + 1,
                'Keyword' => $keywords[$i]
            ]);
        }

        // action
        $test = $this->actingAs($this->admin)->withSession([
            'adminIdx' => 1,
            'adminID' => 'devtest'
        ])->get('/eduplan/execute/process/?keywordGroupIdx='.$keywordGroup->KeywordGroupIdx);

        $test->assertStatus(200);
        $test->assertJson([
            'code' => '0000',
            'message' => '검수 성공'
        ]);
    }

    /** @test */
    public function processSingleKeyword()
    {
        // action
        $test = $this->actingAs($this->admin)->withSession([
            'adminIdx' => 1,
            'adminID' => 'devtest'
        ])->get('/eduplan/execute/process/?keywordName=삼성채용');

        $test->assertStatus(200);
        $test->assertJson([
            'code' => '0000',
            'message' => '검수 성공'
        ]);
    }

    /** @test */
    public function executeResultStatistics()
    {
        // action
        $test = $this->actingAs($this->admin)->withSession([
            'adminIdx' => 1,
            'adminID' => 'devtest'
        ])->get('/eduplan/execute/process/?keywordName=삼성채용');

        $executeResultIdx = $test['data']['executeResultIdx'];

        $statisticsTestIsOwn = $this->actingAs($this->admin)->withSession([
            'adminIdx' => 1,
            'adminID' => 'devtest'
        ])->get('/eduplan/execute/statistics/?executeResultIdx='.$executeResultIdx.'&cafeName=독취사&isOwn=1');

        $statisticsTestIsNotOwn = $this->actingAs($this->admin)->withSession([
            'adminIdx' => 1,
            'adminID' => 'devtest'
        ])->get('/eduplan/execute/statistics/?executeResultIdx='.$executeResultIdx.'&cafeName=공취사&isOwn=0');

        $executeResult = $this->executeResultRepository->find($executeResultIdx);
        $executeResultArray = json_decode($executeResult['data']['ExecuteResult'], true);

        $this->assertArrayHasKey('StatisticsResult', $executeResultArray);
        $this->assertEquals('독취사', $executeResultArray['StatisticsResult']['isOwn']['cafeName']);
        $this->assertArrayHasKey('cafeName', $executeResultArray['StatisticsResult']['isOwn']);
        $this->assertArrayHasKey('viewTotalCount', $executeResultArray['StatisticsResult']['isOwn']);
        $this->assertArrayHasKey('cafeTopRankCount', $executeResultArray['StatisticsResult']['isOwn']);
        $this->assertArrayHasKey('viewTopRankCount', $executeResultArray['StatisticsResult']['isOwn']);
        $this->assertArrayHasKey('cafeTotalRankCount', $executeResultArray['StatisticsResult']['isOwn']);
        $this->assertArrayHasKey('viewTotalBlogCount', $executeResultArray['StatisticsResult']['isOwn']);
        $this->assertArrayHasKey('viewTotalCafeCount', $executeResultArray['StatisticsResult']['isOwn']);

        $this->assertArrayHasKey('cafeName', $executeResultArray['StatisticsResult']['isNotOwn']);
        $this->assertEquals('공취사', $executeResultArray['StatisticsResult']['isNotOwn']['cafeName']);
        $this->assertArrayHasKey('viewTotalCount', $executeResultArray['StatisticsResult']['isNotOwn']);
        $this->assertArrayHasKey('cafeTopRankCount', $executeResultArray['StatisticsResult']['isNotOwn']);
        $this->assertArrayHasKey('viewTopRankCount', $executeResultArray['StatisticsResult']['isNotOwn']);
        $this->assertArrayHasKey('cafeTotalRankCount', $executeResultArray['StatisticsResult']['isNotOwn']);
        $this->assertArrayHasKey('viewTotalBlogCount', $executeResultArray['StatisticsResult']['isNotOwn']);
        $this->assertArrayHasKey('viewTotalCafeCount', $executeResultArray['StatisticsResult']['isNotOwn']);
    }

    /** @test */
    public function adminCanViewExecuteResultList()
    {
        // action
        $test = $this->actingAs($this->admin)->get('/eduplan/execute/list');

        // assert
        $test->assertStatus(200);
    }
    
    /** @test */
    public function adminCanExcelOutput()
    {
        // action
        $test = $this->actingAs($this->admin)->withSession([
            'adminIdx' => 1,
            'adminID' => 'devtest'
        ])->get('/eduplan/execute/process/?keywordName=자기소개서');

        $executeResultIdx = $test['data']['executeResultIdx'];

        Excel::fake();
        
        // excel export action
        $test = $this->actingAs($this->admin)->get('/eduplan/execute/excelOutput?executeResultIdx='.$executeResultIdx);

        Excel::assertDownloaded('execute_result_'.date('Ymd').'.xlsx', function(ExecuteResultExport $export) {
            return array_key_exists('executeResultIdx', $export->view()->getData());
        });

        Excel::assertDownloaded('execute_result_'.date('Ymd').'.xlsx', function(ExecuteResultExport $export) {
            return array_key_exists('executeResult', $export->view()->getData());
        });

        Excel::assertDownloaded('execute_result_'.date('Ymd').'.xlsx', function(ExecuteResultExport $export) {
            return array_key_exists('executeResultArray', $export->view()->getData());
        });
    }
}
