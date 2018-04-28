<?php
namespace App\Http\Controllers;
use App\History;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
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
        session_start();
        if (session_status() == PHP_SESSION_ACTIVE){

            session_reset();
            session_unset();
            session_destroy();
            session_write_close();
            \Session::regenerateToken();
        }

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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Creation on an object History to save the ID of survey into database
        $histories = History::where('userID','=',Auth::id())->get();
        if($histories->isEmpty()) {
            $history = new History();
            $history->userID = Auth::id();
        }
        else{
            $history = $histories[0];
        }
        session_start();
        /*session_unset();
        session_destroy();*/
        //
        $input = $request->all();
        $idChapter = $input['id'];
        $idQuestion = $input['cptQuestions'];
        $isEmpty = false;
        $isNotEmpty = false;
        $size = sizeof($input);
        $questionNotEmpty=0;
        $values = array_values($input);
        $average = 0;
        $sum = 0;
        $input['category'.$idChapter.'bool'] = 1;



        //remplis l'array pour store dans la session
        $array = array($input);

        //créé toutes les sessions et switch case pour remplir

        $_SESSION["array{$idChapter}"] = $array;


/*
        echo "<pre>";
        print_r($array[$idChapter]);
        echo "/<pre>";
   */

        //calculating the average
        for($i=1;$i<$size;$i++){
            if($values[$i]!=null){
                if($values[$i]!=7){
                    $questionNotEmpty++;
                    $sum+=$values[$i];
                }
            }
        }

        $average=round($sum/$questionNotEmpty);

        $input['record_id'] = $input['_token'];
        $input['avg'.$idChapter] = $average;
        $input['iduser'] = Auth::id();
        unset($input['_token']);
        unset($input['id']);
        unset($input['cptQuestions']);
        $input['survey_complete']='2';
        for($j=1;$j<=$idQuestion;$j++){
            if(isset($input['q'.$idChapter.'_'.$j])){
                $isNotEmpty = true;
                $ar[$j-1] = 1; //use for %progression
            }else{
                $isEmpty = true;
                $ar[$j-1] = 0; //use for %progression
            }
        }
        $pourcentage=(array_sum($ar))*100/$idQuestion;
        $input['pourcent'.$idChapter]=$pourcentage;
        $test = '['.json_encode($input).']';
        $apiUrl = Config::get('app.aliases.api_url');  # replace this URL with your institution's # REDCap API URL.
        $apiToken = Config::get('app.aliases.api_token');    # replace with your actual API token
        try {
            //I create a redcap Project (vendor\phpcap)
            $project = new RedCapProject($apiUrl, $apiToken);
            //I import the record into redcap
            $id = $project->importRecords($test,$format = 'php', $type = 'flat', $overwriteBehavior = 'normal', $dateFormat = 'YMD', $returnContent = 'ids');
            $_SESSION["id"] = $id;
            if($history->survey1==null) {
                $history->survey1=$id[0];
            }
            elseif ($history->survey1!=$id[0]){
                if($history->survey2==null) {
                    $history->survey2=$id[0];
                }
                elseif($history->survey2!=$id[0]){
                    if($history->survey3==null) {
                        $history->survey3=$id[0];
                    }
                    elseif($history->survey3!=$id[0]){
                        $temp1 = $history->survey1;
                        $temp2 = $history->survey2;
                        $history->survey3=$temp2;
                        $history->survey2=$temp1;
                        $history->survey1=$id[0];
                    }
                }
            }
            $history->save();
            $records = $project->exportRecords('json', 'flat', $_SESSION["id"]);
            $str     = str_replace('\u','u',$records);
            $strJSON = preg_replace('/u([\da-fA-F]{4})/', '&#x\1;', $str);
            $datas = json_decode($strJSON);
            $categories = array('Informations sur la maladie', 'Informations sur l\'accompagnement', 'Compétences d\'accompagnement', 'Possibilités de soutien', 'Besoin de souffler', 'Possibilités de répit',
                'Qualité du répit', 'Soutien émotionnel ou social formel', 'Soutien émotionnel ou social informel', 'Soutien pratique', 'Soutien financier ou légal');
            return view('survey.resume', array(\Auth::user(), 'categories' => $categories, 'id' => $input['record_id'], 'incomplete' =>$isEmpty, 'complete' =>$isNotEmpty, 'idChapter'=>$idChapter, 'idQuestion'=>$idQuestion, 'data'=>$datas, 'ar' =>$ar));
        } catch (\Exception $e) {
            echo($e->getMessage());
        }
    }



    public function back()
    {

        session_start();
        /*session_unset();
        session_destroy();*/
        //

        $ids = $_SESSION["id"];
        $id = $ids[0];
        $apiUrl = Config::get('app.aliases.api_url');  # replace this URL with your institution's # REDCap API URL.
        $apiToken = Config::get('app.aliases.api_token');    # replace with your actual API token
        try {
            //I create a redcap Project (vendor\phpcap)
            $project = new RedCapProject($apiUrl, $apiToken);

            $records = $project->exportRecords('json', 'flat', $ids);
            $str = str_replace('\u', 'u', $records);
            $strJSON = preg_replace('/u([\da-fA-F]{4})/', '&#x\1;', $str);
            $datas = json_decode($strJSON);
            $categories = array('Informations sur la maladie', 'Informations sur l\'accompagnement', 'Compétences d\'accompagnement', 'Possibilités de soutien', 'Besoin de souffler', 'Possibilités de répit',
                'Qualité du répit', 'Soutien émotionnel ou social formel', 'Soutien émotionnel ou social informel', 'Soutien pratique', 'Soutien financier ou légal');
            return view('survey.resume', array(\Auth::user(), 'categories' => $categories, 'data'=>$datas, 'id' => $id));
        }
        catch (\Exception $e) {
            echo($e->getMessage());
        }


    }
    /**
     * Display the specified resource.
     *
     * @param  \App\History  $history
     * @return \Illuminate\Http\Response
     */
    public function show(History $history)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\History  $history
     * @return \Illuminate\Http\Response
     */
    public function edit(History $history)
    {
        //
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\History  $history
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, History $history)
    {
        //
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\History  $history
     * @return \Illuminate\Http\Response
     */
    public function destroy(History $history)
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
        return view('survey.category', array(\Auth::user(), 'questions' => $questions, 'id' => $id, 'categories' => $categories));
    }
    public function chart(){
        //I call the APIUrl and the API Token saved in config/app.php
        //Need them for creating redcapAPI
        $apiUrl = Config::get('app.aliases.api_url');  # replace this URL with your institution's # REDCap API URL.
        $apiToken = Config::get('app.aliases.api_token');    # replace with your actual API token
        //I try to create a redcap Project
        try {
            $project = new RedCapProject($apiUrl, $apiToken);
        }
            //I catch the redcapException
        catch (\Exception $e) {
            echo($e->getMessage());
        }
        //I store the userID I need to get all survey he got in history (max.3)
        $userID = Auth::id();
        //get the histories of the User
        $histories = History::where('userID','=',$userID)->get();
        if($histories->isEmpty()) {
        }
        else {
            $history = $histories[0];
            $recordIds[0] = $history->survey1;
            $recordIds[1] = $history->survey2;
            $recordIds[2] = $history->survey3;
            $records = $project->exportRecords('json', 'flat', $recordIds);
            $str = str_replace('\u', 'u', $records);
            $strJSON = preg_replace('/u([\da-fA-F]{4})/', '&#x\1;', $str);
            $datas = json_decode($strJSON);
            $size = sizeof($datas);
            $bool = array();
            for($x=0; $x<$size; $x++) {
                if (property_exists($datas[0], 'avg1')) {
                    $avg1 = $datas[$x]->avg1;
                    $bool[1] = $datas[0]->category1bool;
                } else {
                    $avg1 = 0;
                    $bool[1] = $datas[0]->category1bool;
                }
                if (property_exists($datas[0], 'avg2')) {
                    $avg2 = $datas[$x]->avg2;
                    $bool[2] = $datas[0]->category2bool;
                } else {
                    $avg2 = 0;
                    $bool[2] = $datas[0]->category2bool;
                }
                if (property_exists($datas[0], 'avg3')) {
                    $avg3 = $datas[$x]->avg3;
                    $bool[3] = $datas[0]->category3bool;
                } else {
                    $avg3 = 0;
                    $bool[3] = $datas[0]->category3bool;
                }
                if (property_exists($datas[0], 'avg4')) {
                    $avg4 = $datas[$x]->avg4;
                    $bool[4] = $datas[0]->category4bool;
                } else {
                    $avg4 = 0;
                    $bool[4] = $datas[0]->category4bool;
                }
                if (property_exists($datas[0], 'avg5')) {
                    $avg5 = $datas[$x]->avg5;
                    $bool[5] = $datas[0]->category5bool;
                } else {
                    $avg5 = 0;
                    $bool[5] = $datas[0]->category5bool;
                }
                if (property_exists($datas[0], 'avg6')) {
                    $avg6 = $datas[$x]->avg6;
                    $bool[6] = $datas[0]->category6bool;
                } else {
                    $avg6 = 0;
                    $bool[6] = $datas[0]->category6bool;
                }
                if (property_exists($datas[0], 'avg7')) {
                    $avg7 = $datas[$x]->avg7;
                    $bool[7] = $datas[0]->category7bool;
                } else {
                    $avg7 = 0;
                    $bool[7] = $datas[0]->category7bool;
                }
                if (property_exists($datas[0], 'avg8')) {
                    $avg8 = $datas[$x]->avg8;
                    $bool[8] = $datas[0]->category8bool;
                } else {
                    $avg8 = 0;
                    $bool[8] = $datas[0]->category8bool;
                }
                if (property_exists($datas[0], 'avg9')) {
                    $avg9 = $datas[$x]->avg9;
                    $bool[9] = $datas[0]->category9bool;
                } else {
                    $avg9 = 0;
                    $bool[9] = $datas[0]->category9bool;
                }
                if (property_exists($datas[0], 'avg10')) {
                    $avg10 = $datas[$x]->avg10;
                    $bool[10] = $datas[0]->category10bool;
                } else {
                    $avg10 = 0;
                    $bool[10] = $datas[0]->category10bool;
                }
                if (property_exists($datas[0], 'avg11')) {
                    $avg11 = $datas[$x]->avg11;
                    $bool[11] = $datas[0]->category11bool;
                } else {
                    $avg11 = 0;
                    $bool[11] = $datas[0]->category11bool;
                }
                $averages[$x] = array($avg1, $avg2, $avg3, $avg4, $avg5, $avg6, $avg7, $avg8, $avg9, $avg10, $avg11);
            }
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
            $str = str_replace('\u', 'u', $associationsInfo);
            $strJSON = preg_replace('/u([\da-fA-F]{4})/', '&#x\1;', $str);
            $associations = json_decode($strJSON);
           return view('survey.chart', array(\Auth::user(), 'averages' => $averages, 'categories' => $categories, 'associations' => $associations, 'bool' => $bool));
        }
    }
}