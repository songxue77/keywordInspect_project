<?php

declare(strict_types=1);

namespace App\Traits\Eduplan;

trait StatisticsDataTransformer
{
    public function transformViewTopRankCountToText($viewTopRankCount, $keywordCount)
    {
        return $viewTopRankCount === 0 ?
            '노출없음' : $viewTopRankCount.'('.sprintf('%0.1f', $viewTopRankCount / $keywordCount * 100).'%)';
    }
    
    public function transformViewTotalCountToText($viewTotalCount, $searchTotalResultCount)
    {
        return $viewTotalCount === 0 ?
            '노출없음' : $viewTotalCount.'('.sprintf('%0.1f', $viewTotalCount / $searchTotalResultCount * 100).'%)';
    }
    
    public function transformViewTotalCafeCountToText($viewTotalCafeCount, $searchTotalResultCount)
    {
        return $viewTotalCafeCount === 0 ?
            '노출없음' : $viewTotalCafeCount.'('.sprintf('%0.1f', $viewTotalCafeCount / $searchTotalResultCount * 100).'%)';
    }
    
    public function transformViewTotalPostCountToText($viewTotalPostCount, $searchTotalResultCount)
    {
        return $viewTotalPostCount === 0 ?
            '노출없음' : $viewTotalPostCount.'('.sprintf('%0.1f', $viewTotalPostCount / $searchTotalResultCount * 100).'%)';
    }
    
    public function transformViewTotalBlogCountToText($viewTotalBlogCount, $searchTotalResultCount)
    {
        return $viewTotalBlogCount === 0 ?
            '노출없음' : $viewTotalBlogCount.'('.sprintf('%0.1f', $viewTotalBlogCount / $searchTotalResultCount * 100).'%)';
    }
    
    public function transformCafeTopRankCountToText($cafeTopRankCount, $keywordCount)
    {
        return $cafeTopRankCount === 0 ?
            '노출없음' : $cafeTopRankCount.'('.sprintf('%0.1f', $cafeTopRankCount / $keywordCount * 100).'%)';
    }
    
    public function transformCafeTotalCountToText($cafeTotalRankCount, $searchTotalResultCount)
    {
        return $cafeTotalRankCount === 0 ? 
            '노출없음' : $cafeTotalRankCount.'('.sprintf('%0.1f', $cafeTotalRankCount / $searchTotalResultCount * 100).'%)';
    }
    
    public function transformCompareViewTopRankToText($statisticsResult)
    {
        $isOwnViewTopRankCount = $statisticsResult['isOwn']['viewTopRankCount'] ?? 0;
        $isNotOwnViewTopRankCount = $statisticsResult['isNotOwn']['viewTopRankCount'] ?? 0;
        
        $isOwnViewTotalCount = $statisticsResult['isOwn']['viewTotalCount'] ?? 0;
        $isNotOwnViewTotalCount = $statisticsResult['isNotOwn']['viewTotalCount'] ?? 0;
        
        $viewTopRankCompareText = $isOwnViewTopRankCount !== 0 && $isNotOwnViewTopRankCount !== 0 ?
            '1 : ' . sprintf('%0.1f', $isNotOwnViewTopRankCount / $isOwnViewTopRankCount) : '-';
        
        $viewTotalCompareText = $isOwnViewTotalCount !== 0 && $isNotOwnViewTotalCount !== 0 ?
            '1 : ' . sprintf('%0.1f', $isNotOwnViewTotalCount / $isOwnViewTotalCount) : '-';
        
        return [
            'viewTopRankCompareText' => $viewTopRankCompareText,
            'viewTotalCompareText' => $viewTotalCompareText
        ];
    }
}