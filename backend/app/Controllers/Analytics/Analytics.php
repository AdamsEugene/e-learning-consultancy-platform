<?php 

namespace App\Controllers\Analytics;

use App\Libraries\Routing;
use App\Controllers\BaseController;
use App\Models\AnalyticsModel;

class Analytics extends BaseController {

    /**
     * Log a count
     * 
     * @param string $record
     * @return array
     */
    public function logCount($record, $trend = 'increment') {

        $analyticsModel = new AnalyticsModel();

        // convert the record to a string
        $record = ucwords($record);

        // check if the record exists
        $recordItem = $analyticsModel->getByRecordType($record);

        if($recordItem) {
            $totalCount = ($trend == 'increment') ? $recordItem['totalCount'] + 1 : $recordItem['totalCount'] - 1;
            $analyticsModel->updateRecord($recordItem['id'], ['totalCount' => $totalCount]);
        } else {
            $analyticsModel->createRecord(['recordType' => $record, 'totalCount' => 1]);
        }

        return Routing::success("Record logged successfully");
    }

    /**
     * Get the count of a record
     * 
     * @param string $record
     * @return int
     */
    public function counters() {

        $analyticsModel = new AnalyticsModel();
        $records = $analyticsModel->getRecords();

        return Routing::success($records);

    }
}
?>