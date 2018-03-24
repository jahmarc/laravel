<?php

namespace Tests\Feature;

use IU\PHPCap\RedCapProject;
use Tests\TestCase;
use Illuminate\Support\Facades\Config;

class QuestionsTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    /*
     * A basic Test with an addition
     *
     * @return void
     */
    public function testBasic(){
        $data = [10, 20, 30];
        $result = array_sum($data);
        $this->assertEquals(60, $result);
    }

    /**
     * test importing data on redcap.
     *
     * @return void
     * @throws \IU\PHPCap\PhpCapException
     */
    public function testStore()
    {
        #insertion of answers to questions
        $test1['q1_1'] = '0';
        $test1['q1_2'] = '1';
        $test1['q1_3'] = '3';
        $test1['q1_4'] = '1';
        $test1['q1_5'] = '6';
        $test1['q1_6'] = '7';

        $test1['record_id'] = 'IYfV0VsAtcTRY6ZPuNSSaGSBZlQJetcOBubWaiTn';
        $test1['avg'.'1'] = '3'; #chapter indication with average
        $test1['iduser'] = '6';
        $test1['survey_complete']='2';

        $test2 = '['.json_encode($test1).']';

        $apiUrl = Config::get('app.aliases.api_url');  # replace this URL with your institution's # REDCap API URL.
        $apiToken = Config::get('app.aliases.api_token');    # replace with your actual API token

        $projectTest = new RedCapProject($apiUrl, $apiToken); #creation of a redcap project for the test

        #import the data and storage the record's id
        $array = $projectTest->importRecords($test2,$format = 'php', $type = 'flat', $overwriteBehavior = 'normal', $dateFormat = 'YMD', $returnContent = 'ids');

        $idTest = 'IYfV0VsAtcTRY6ZPuNSSaGSBZlQJetcOBubWaiTn';
        $id = $array[0];

        #if it's Equals import successfully
        $this->assertEquals($id, $idTest);
    }


}
