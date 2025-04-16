<?php

/**
 * Features List
 * 
 * @return array
 */
function features_list() {
    // default features list
    return [
        "page_vitals" => [
            "name" => "Page Vitals",
            "initial" => "pv",
            "description" => "This is the full details of the site"
        ],
        "ai_insight" => [
            "name" => "AI Insight",
            "initial" => "ai",
            "description" => "This feature allows heatmap.com to use Generative AI to give insight on how your pages element are performing"
        ],
        "comparison_mode" => [
            "name" => "Comparison Mode",
            "initial" => "cm",
            "description" => "This feature enables users to compare heatmaps using available filters."
        ],
        "industry_benchmark" => [
            "name" => "Industry Benchmark",
            "initial" => "ib",
            "description" => "This is the industry benchmark rating sites to the industry"
        ],
        "interactive_mode" => [
            "name" => "Interactive Mode",
            "initial" => "im",
            "description" => "This is the interactive mode feature available for the viewing of an heatmap"
        ],
        "system_of_records" => [
            "name" => "System of Records",
            "initial" => "sr",
            "description" => "This is the System of Records feature available for the viewing of an heatmap"
        ],
        "ads_platform" => [
            "name" => "Ads Platform",
            "initial" => "ap",
            "description" => "This is the Ads Platform feature available for the viewing of an heatmap"
        ],
        "group_heatmaps" => [
            "name" => "Group Heatmaps",
            "initial" => "gh",
            "description" => "This is the Group Heatmaps feature available for the viewing of an heatmaps"
        ]
    ];
}

/**
 * Re format the features list of the websites
 * 
 * @param array $list
 * @param array $loadedFeatures
 * 
 * @return array
 */
function reformat_features($list, $loadedFeatures = []) {
    
    if(empty($loadedFeatures)) return $list;
    
    $keyValues = [];
    foreach($loadedFeatures as $key => $value) {
        $keyValues[$value['initial']] = strtolower(str_ireplace(" ", "_", $value['name']));
    }

    foreach($list as $item) {
        $websiteFeatures[] = $keyValues[$item] ?? $item;
    }

    return $websiteFeatures ?? [];
}

/**
 * Heatmap Features
 * 
 * @param array $features
 * 
 * @return array
 */
function heatmap_features($heatmapArray = []) {

    $list = [
        "page_vitals" => "allowPageVitals",
        "ai_insight" => "enableAiInsight",
        "comparison_mode" => "isCompatibleWithInteractiveMode",
        "industry_benchmark" => "allowIndustryBenchmark",
        "interactive_mode" => "isCompatibleWithInteractiveMode",
        "system_of_records" => "systemOfRecords",
        "ads_platform" => "adsPlatform",
        "group_heatmaps" => "showGroupHeatmaps"
    ];

    // set all the features to false
    foreach($list as $feature) {
        $heatmapArray[$feature] = false;
    }

    // set the features to true
    foreach($heatmapArray['features'] as $feature) {
        $heatmapArray[$list[$feature]] = true;
    }

    // set the freemium page
    $heatmapArray['isFreemiumPage'] = in_array("freemium", $heatmapArray['features']);

    return $heatmapArray ?? [];
}