<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use IU\PHPCap\RedCapProject;
use function Sodium\add;

class QuestionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        //Array of category names to display it in the resume table

        $categories = array('Informations sur la maladie', 'Informations sur l\'accompagnement', 'Compétences d\'accompagnement', 'Possibilités de soutien', 'Besoin de souffler', 'Possibilités de répit',
            'Qualité du répit', 'Soutien émotionnel ou social formel', 'Soutien émotionnel ou social informel', 'Soutien pratique', 'Soutien financier ou légal');


        //Return view of summary table with categories array and User Authentificated

        return view('survey.start', array(\Auth::user(), 'categories' => $categories));


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \IU\PHPCap\PhpCapException
     */



    public function store(Request $request)
    {

        //
        $input = $request->all();

        $idChapter = $input['id'];
        $idQuestion = $input['cptQuestions'];
        $isEmpty = false;

        $size = sizeof($input);

        $values = array_values($input);

        $average = 0;
        $sum = 0;

        $input['category'.$idChapter.'bool'] = 1;

        for($i = 2; $i<$size; $i++){
            $sum += $values[$i];
        }

        $average = round($sum/($size-1));


        $input['record_id'] = $input['_token'];
        $input['avg'.$idChapter] = $average;
        $input['iduser'] = Auth::id();
        unset($input['_token']);
        unset($input['id']);
        unset($input['cptQuestions']);
        $input['survey_complete']='2';

        $test = '['.json_encode($input).']';

        $apiUrl = Config::get('app.aliases.api_url');  # replace this URL with your institution's # REDCap API URL.

        $apiToken = Config::get('app.aliases.api_token');    # replace with your actual API token

        for($j=1;$j<$idQuestion;$j++){
            if(isset($input['q'.$idChapter.'_'.$j])){
                echo $input['q'.$idChapter.'_'.$j];
            }else{
                $isEmpty = true;
            }
        }
        if ($isEmpty == true) {
            echo '<script language="javascript">';
            echo 'alert("Le formulaire est imcomplet !")';
            echo '</script>';
        }else{
            echo '<script language="javascript">';
            echo 'alert("Le formulaire est complètement rempli !")';
            echo '</script>';
        }

        try {
            $project = new RedCapProject($apiUrl, $apiToken);


            $id = $project->importRecords($test,$format = 'php', $type = 'flat', $overwriteBehavior = 'normal', $dateFormat = 'YMD', $returnContent = 'ids');

            $categories = array('Informations sur la maladie', 'Informations sur l\'accompagnement', 'Compétences d\'accompagnement', 'Possibilités de soutien', 'Besoin de souffler', 'Possibilités de répit',
                'Qualité du répit', 'Soutien émotionnel ou social formel', 'Soutien émotionnel ou social informel', 'Soutien pratique', 'Soutien financier ou légal');


            return view('survey.resume', array(\Auth::user(), 'categories' => $categories, 'id' => $input['record_id']));
        } catch (\Exception $e) {
            echo($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function category($id){

        $apiUrl = Config::get('app.aliases.api_url');  # replace this URL with your institution's # REDCap API URL.

        $apiToken = Config::get('app.aliases.api_token');    # replace with your actual API token

        try {
            $project = new RedCapProject($apiUrl, $apiToken);
        } catch (\Exception $e) {
            echo($e->getMessage());
        }

        $projectInfo = $project->exportMetadata();




        $str     = str_replace('\u','u',$projectInfo);
        $strJSON = preg_replace('/u([\da-fA-F]{4})/', '&#x\1;', $str);

        $questions = json_decode($strJSON);



        $categories = array('Informations sur la maladie', 'Informations sur l\'accompagnement', 'Compétences d\'accompagnement', 'Possibilités de soutien', 'Besoin de souffler', 'Possibilités de répit',
            'Qualité du répit', 'Soutien émotionnel ou social formel', 'Soutien émotionnel ou social informel', 'Soutien pratique', 'Soutien financier ou légal');


        //print_r($projectInfo);


        return view('survey.category', array(\Auth::user(), 'questions' => $questions, 'id' => $id, 'categories' => $categories));

    }


    public function chart($id){


        $apiUrl = Config::get('app.aliases.api_url');  # replace this URL with your institution's # REDCap API URL.

        $apiToken = Config::get('app.aliases.api_token');    # replace with your actual API token

        try {
            $project = new RedCapProject($apiUrl, $apiToken);
        } catch (\Exception $e) {
            echo($e->getMessage());
        }

        $recordIds = [$id];
        $records = $project->exportRecords('json', 'flat', $recordIds);

        $str     = str_replace('\u','u',$records);
        $strJSON = preg_replace('/u([\da-fA-F]{4})/', '&#x\1;', $str);

        $datas = json_decode($strJSON);


        $size = sizeof($datas);



        $bool =array();

        if(property_exists($datas[0],'avg1')) {
            $avg1 = $datas[0]->avg1;
            $bool[1] = $datas[0]->category1bool;

        }
        else{
            $avg1 =0;
            $bool[1] = $datas[0]->category1bool;
        }

        if(property_exists($datas[0],'avg2')) {
            $avg2 = $datas[0]->avg2;
            $bool[2] = $datas[0]->category2bool;
        }
        else{
            $avg2 =0;
            $bool[2] = $datas[0]->category2bool;
        }
        if(property_exists($datas[0],'avg3')) {
            $avg3 = $datas[0]->avg3;
            $bool[3] = $datas[0]->category3bool;
        }
        else{
            $avg3 =0;
            $bool[3] = $datas[0]->category3bool;
        }
        if(property_exists($datas[0],'avg4')) {
            $avg4 = $datas[0]->avg4;
            $bool[4] = $datas[0]->category4bool;
        }
        else{
            $avg4 =0;
            $bool[4] = $datas[0]->category4bool;
        }
        if(property_exists($datas[0],'avg5')) {
            $avg5 = $datas[0]->avg5;
            $bool[5] = $datas[0]->category5bool;
        }
        else{
            $avg5 =0;
            $bool[5] = $datas[0]->category5bool;
        }
        if(property_exists($datas[0],'avg6')) {
            $avg6 = $datas[0]->avg6;
            $bool[6] = $datas[0]->category6bool;
        }
        else{
            $avg6 =0;
            $bool[6] = $datas[0]->category6bool;
        }
        if(property_exists($datas[0],'avg7')) {
            $avg7 = $datas[0]->avg7;
            $bool[7] = $datas[0]->category7bool;
        }
        else{
            $avg7 =0;
            $bool[7] = $datas[0]->category7bool;
        }
        if(property_exists($datas[0],'avg8')) {
            $avg8 = $datas[0]->avg8;
            $bool[8] = $datas[0]->category8bool;
        }
        else{
            $avg8 =0;
            $bool[8] = $datas[0]->category8bool;
        }
        if(property_exists($datas[0],'avg9')) {
            $avg9 = $datas[0]->avg9;
            $bool[9] = $datas[0]->category9bool;
        }
        else{
            $avg9 =0;
            $bool[9] = $datas[0]->category9bool;
        }
        if(property_exists($datas[0],'avg10')) {
            $avg10 = $datas[0]->avg10;
            $bool[10] = $datas[0]->category10bool;
        }
        else{
            $avg10 =0;
            $bool[10] = $datas[0]->category10bool;
        }
        if(property_exists($datas[0],'avg11')) {
            $avg11 = $datas[0]->avg11;
            $bool[11] = $datas[0]->category11bool;
        }
        else{
            $avg11 =0;
            $bool[11] = $datas[0]->category11bool;
        }







        $averages = array($avg1,$avg2,$avg3,$avg4,$avg5,$avg6,$avg7,$avg8,$avg9,$avg10,$avg11);





        $categories = array('Informations sur la maladie', 'Informations sur l\'accompagnement', 'Compétences d\'accompagnement', 'Possibilités de soutien', 'Besoin de souffler', 'Possibilités de répit',
            'Qualité du répit', 'Soutien émotionnel ou social formel', 'Soutien émotionnel ou social informel', 'Soutien pratique', 'Soutien financier ou légal');

        $apiUrl = Config::get('app.aliases.api_url');  # replace this URL with your institution's # REDCap API URL.

        $apiToken = Config::get('app.aliases.api_token');    # replace with your actual API token

        try {
            $project = new RedCapProject($apiUrl, $apiToken);
        } catch (\Exception $e) {
            echo($e->getMessage());
        }


        $associationsInfo = $project->exportMetadataAss();


        $str     = str_replace('\u','u',$associationsInfo);
        $strJSON = preg_replace('/u([\da-fA-F]{4})/', '&#x\1;', $str);

        $associations = json_decode($strJSON);


        return view('survey.chart',array(\Auth::user(),'averages' => $averages, 'categories' => $categories, 'associations' => $associations, 'bool' => $bool));
    }

}
