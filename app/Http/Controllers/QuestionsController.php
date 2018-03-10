<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use IU\PHPCap\RedCapProject;

class QuestionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        $categories = array('Informations sur la maladie', 'Informations sur l\'accompagnement', 'Compétences d\'accompagnement', 'Possibilités de soutien', 'Besoin de souffler', 'Possibilités de répit',
            'Qualité du répit', 'Soutien émotionnel ou social formel', 'Soutien émotionnel ou social informel', 'Soutien pratique', 'Soutien financier ou légal');


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

        $size = sizeof($input);

        $values = array_values($input);

        $average = 0;
        $sum = 0;

        for($i = 2; $i<$size; $i++){
            $sum += $values[$i];
        }

        $average = round($sum/($size-1));


        $input['record_id'] = $input['_token'];
        $input['avg'.$idChapter] = $average;
        unset($input['_token']);
        unset($input['id']);
        $input['survey_complete']='2';


         $test = '['.json_encode($input).']';


        $apiUrl = Config::get('app.aliases.api_url');  # replace this URL with your institution's # REDCap API URL.

        $apiToken = Config::get('app.aliases.api_token');    # replace with your actual API token

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

        $projectInfo = $project->exportMetadata();

        $str     = str_replace('\u','u',$projectInfo);
        $strJSON = preg_replace('/u([\da-fA-F]{4})/', '&#x\1;', $str);

        $questions = json_decode($strJSON);


      return view('survey.chart');



    }


}
