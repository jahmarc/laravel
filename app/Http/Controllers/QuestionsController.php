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

        //To restart the test
        session_start();
        if (session_status() == PHP_SESSION_ACTIVE){

            session_reset();
            session_unset();
            session_destroy();
            session_write_close();

            //New form token
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
        //Get the history object in the database by userID
        $histories = History::where('userID','=',Auth::id())->get();

        //If he is empty, we create an new history object and we assign it the userID
        if($histories->isEmpty()) {
            $history = new History();
            $history->userID = Auth::id();
        }
        //If he isn't empty, we get back the first row, that contains object
        else{
            $history = $histories[0];
        }
        session_start();

        //We get back the datas inserted in the form of view category.blade.php
        $input = $request->all();

        //We get back the ID of chapter
        $idChapter = $input['id'];

        //Total number of questions in a category
        $idQuestion = $input['cptQuestions'];

        //Boolean used to determine the progression of the filling of a category
        $isEmpty = false;
        $isNotEmpty = false;

        //Initialization for average process
        $values = array_values($input);
        $sum = 0;
        $input['category'.$idChapter.'bool'] = 1;
        $questionNotEmpty = 0;

        //Remove the first and the second element of the array (token, idChapter)
        array_splice($values,0,2);
        
        //Remove the last element of the array (avg)
        array_pop($values);

        //fill the array to store it in the session
        $array = array($input);

        //creation of the session
        $_SESSION["array{$idChapter}"] = $array;

        //calculating the average
        for($i=0;$i<=$idQuestion;$i++){
            if (isset($values[$i])){
                if($values[$i]!=null){
                    if($values[$i]!=7){
                        $sum+=$values[$i];
                        $questionNotEmpty++;
                    }
                }
            }
        }

        //Prevent the division by 0
        if ($questionNotEmpty===0){
            $average = 0;
        }else{
            $average=round($sum/$questionNotEmpty);
        }

        //we change the name token by record_id for redcap api
        $input['record_id'] = $input['_token'];

        //We add the average of the chapter, the userID and we unset token, id and cptQuestions for redcap api
        $input['avg'.$idChapter] = $average;
        $input['iduser'] = Auth::id();
        unset($input['_token']);
        unset($input['id']);
        unset($input['cptQuestions']);
        $input['survey_complete']='2';

        //Loop on all questions of a category
        for($j=1;$j<=$idQuestion;$j++){
            //Test on each questions to know if the var it's set and not null
            if(isset($input['q'.$idChapter.'_'.$j])){
                //The question is filled
                $isNotEmpty = true;
                //Increment this var to one to calculate after how much the user answered
                $arrayCptPourcent[$j-1] = 1;
            }else{
                //The question is not filled
                $isEmpty = true;
                //Increment this var to zero to calculate after how much the user answered
                $arrayCptPourcent[$j-1] = 0;
            }
        }

        //Result of the filling of a category
        $pourcentage=(array_sum($arrayCptPourcent))*100/$idQuestion;

        //Set the result, this var have to exist in REDCap
        $input['pourcent'.$idChapter]=$pourcentage;


        //we encode the survey in json for redcap api
        $survey = '['.json_encode($input).']';

        //We get back from config/app.php the variable api_url and api_token to use redcap
        $apiUrl = Config::get('app.aliases.api_url');
        $apiToken = Config::get('app.aliases.api_token');


        try {
            //I create a redcap Project (vendor\phpcap)
            $project = new RedCapProject($apiUrl, $apiToken);

            //I import the record into redcap
            $id = $project->importRecords($survey,$format = 'php', $type = 'flat', $overwriteBehavior = 'normal', $dateFormat = 'YMD', $returnContent = 'ids');

            //Fill the session variable id, with the id of the current category
            $_SESSION["id"] = $id;

            //I check if history->survey1 is null to fill it
            if($history->survey1==null) {
                $history->survey1=$id[0];
            }
            //If not I check if it's same value
            elseif ($history->survey1!=$id[0]){
                //Then if not I check if survey2 is null to fill it
                if($history->survey2==null) {
                    $history->survey2=$history->survey1;
                    $history->survey1=$id[0];
                }
                //If not I check if it's same value
                elseif($history->survey2!=$id[0]){
                    //Then if not I check if survey3 is null to fill it
                    if($history->survey3==null) {
                        $history->survey3=$history->survey2;
                        $history->survey2=$history->survey1;
                        $history->survey1=$id[0];
                    }
                    //If not I check if it's same value and if not I delete survey3 value to get the 3 recent histories
                    elseif($history->survey3!=$id[0]){
                        $temp1 = $history->survey1;
                        $temp2 = $history->survey2;
                        $history->survey3=$temp2;
                        $history->survey2=$temp1;
                        $history->survey1=$id[0];
                    }
                }
            }
            //I save the history on database
            $history->save();

            //I get back the record from redcap
            $records = $project->exportRecords('json', 'flat', $_SESSION["id"]);
            $str     = str_replace('\u','u',$records);
            $strJSON = preg_replace('/u([\da-fA-F]{4})/', '&#x\1;', $str);
            $datas = json_decode($strJSON);

            //Array of category names to display it in the resume table
            $categories = array('Informations sur la maladie', 'Informations sur l\'accompagnement', 'Compétences d\'accompagnement', 'Possibilités de soutien', 'Besoin de souffler', 'Possibilités de répit',
                'Qualité du répit', 'Soutien émotionnel ou social formel', 'Soutien émotionnel ou social informel', 'Soutien pratique', 'Soutien financier ou légal');


            return view('survey.resume', array(\Auth::user(), 'categories' => $categories, 'id' => $input['record_id'], 'incomplete' =>$isEmpty, 'complete' =>$isNotEmpty, 'idChapter'=>$idChapter, 'idQuestion'=>$idQuestion, 'data'=>$datas, 'arrayCptPourcent' =>$arrayCptPourcent));
        } catch (\Exception $e) {
            echo($e->getMessage());
        }
    }



    public function back()
    {

        session_start();

        //Get back the id store in session
        $ids = $_SESSION["id"];
        $id = $ids[0];

        //We get back from config/app.php the variable api_url and api_token to use redcap
        $apiUrl = Config::get('app.aliases.api_url');
        $apiToken = Config::get('app.aliases.api_token');
        try {
            //I create a redcap Project (vendor\phpcap)
            $project = new RedCapProject($apiUrl, $apiToken);

            //I get back the record from redcap
            $records = $project->exportRecords('json', 'flat', $ids);
            $str = str_replace('\u', 'u', $records);
            $strJSON = preg_replace('/u([\da-fA-F]{4})/', '&#x\1;', $str);
            $datas = json_decode($strJSON);

            //Array of category names to display it in the resume table
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

        //We get back from config/app.php the variable api_url and api_token to use redcap
        $apiUrl = Config::get('app.aliases.api_url');
        $apiToken = Config::get('app.aliases.api_token');
        try {
            //Creation of a new RedCapProject
            $project = new RedCapProject($apiUrl, $apiToken);
        } catch (\Exception $e) {
            echo($e->getMessage());
        }
        //Exportation from Reccap of metadatas (contains questions of the survey)
        $projectInfo = $project->exportMetadata();
        $str     = str_replace('\u','u',$projectInfo);
        $strJSON = preg_replace('/u([\da-fA-F]{4})/', '&#x\1;', $str);
        $questions = json_decode($strJSON);

        //Array of category names to display it in the Title of the page
        $categories = array('Informations sur la maladie', 'Informations sur l\'accompagnement', 'Compétences d\'accompagnement', 'Possibilités de soutien', 'Besoin de souffler', 'Possibilités de répit',
            'Qualité du répit', 'Soutien émotionnel ou social formel', 'Soutien émotionnel ou social informel', 'Soutien pratique', 'Soutien financier ou légal');

        return view('survey.category', array(\Auth::user(), 'questions' => $questions, 'id' => $id, 'categories' => $categories));
    }

    //Function to build the chart
    public function chart(){

        //I call the APIUrl and the API Token saved in config/app.php
        //Need them for creating redcapAPI
        $apiUrl = Config::get('app.aliases.api_url');
        $apiToken = Config::get('app.aliases.api_token');


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

        //I check if history is empty
        if($histories->isEmpty()) {
        }

        //If not I fill the recordIDs to get back the sruveys from redcap
        else {

            $history = $histories[0];

            //3 different var because of order in exportation, the order of the 1st in history isn't necessary the 1st after export
            //So I made 3 different var of IDS and 3 exportations
            $recordIds[0] = $history->survey1;
            $recordId2[0] = $history->survey2;
            $recordId3[0] = $history->survey3;

            //3 Exportations in 3 var
            $record1 = $project->exportRecords('json', 'flat', $recordIds);



            if($recordId2[0]!=null) {
                $record2 = $project->exportRecords('json', 'flat', $recordId2);
                if($recordId3[0]!=null) {
                    $record3 = $project->exportRecords('json', 'flat', $recordId3);
                    //I delete the last char (]) of record1
                    $record1 = substr($record1, 0, -1);
                    //I delete the  first([) and last char (]) of record2
                    $record2 = substr($record2, 1, -1);
                    //I delete the  first char ([) of record3
                    $record3 = substr($record3,1);

                    // I make one simple var for everything in good format (,) betweeen the surveys
                    $records = $record1.','.$record2.','.$record3;
                }
                else{
                    //I delete the last char (]) of record1
                    $record1 = substr($record1, 0, -1);
                    //I delete the  first([) and last char (]) of record2
                    $record2 = substr($record2, 1);

                    // I make one simple var for everything in good format (,) betweeen the surveys
                    $records = $record1.','.$record2;
                }
            }
            else{

                $records = $record1;
            }




            $str = str_replace('\u', 'u', $records);
            $strJSON = preg_replace('/u([\da-fA-F]{4})/', '&#x\1;', $str);

            $datas = json_decode($strJSON);


                //I get the size of $datas to know if there is 1 survey, 2 or 3 surveys
            $size = sizeof($datas);
            //var to store the checks if empty or not
            $bool = array();

            //Gett all the averages from redcap and get all the checks
            for($x=0; $x<$size; $x++) {
                if (property_exists($datas[$x], 'avg1')) {
                    $avg1 = $datas[$x]->avg1;
                    $bool[$x][1] = $datas[$x]->category1bool;
                } else {
                    $avg1 = 0;
                    $bool[$x][1] = $datas[$x]->category1bool;
                }
                if (property_exists($datas[$x], 'avg2')) {
                    $avg2 = $datas[$x]->avg2;
                    $bool[$x][2] = $datas[$x]->category2bool;
                } else {
                    $avg2 = 0;
                    $bool[$x][2] = $datas[$x]->category2bool;
                }
                if (property_exists($datas[$x], 'avg3')) {
                    $avg3 = $datas[$x]->avg3;
                    $bool[$x][3] = $datas[$x]->category3bool;
                } else {
                    $avg3 = 0;
                    $bool[$x][3] = $datas[$x]->category3bool;
                }
                if (property_exists($datas[$x], 'avg4')) {
                    $avg4 = $datas[$x]->avg4;
                    $bool[$x][4] = $datas[$x]->category4bool;
                } else {
                    $avg4 = 0;
                    $bool[$x][4] = $datas[$x]->category4bool;
                }
                if (property_exists($datas[$x], 'avg5')) {
                    $avg5 = $datas[$x]->avg5;
                    $bool[$x][5] = $datas[$x]->category5bool;
                } else {
                    $avg5 = 0;
                    $bool[$x][5] = $datas[$x]->category5bool;
                }
                if (property_exists($datas[$x], 'avg6')) {
                    $avg6 = $datas[$x]->avg6;
                    $bool[$x][6] = $datas[$x]->category6bool;
                } else {
                    $avg6 = 0;
                    $bool[$x][6] = $datas[$x]->category6bool;
                }
                if (property_exists($datas[$x], 'avg7')) {
                    $avg7 = $datas[$x]->avg7;
                    $bool[$x][7] = $datas[$x]->category7bool;
                } else {
                    $avg7 = 0;
                    $bool[$x][7] = $datas[$x]->category7bool;
                }
                if (property_exists($datas[$x], 'avg8')) {
                    $avg8 = $datas[$x]->avg8;
                    $bool[$x][8] = $datas[$x]->category8bool;
                } else {
                    $avg8 = 0;
                    $bool[$x][8] = $datas[$x]->category8bool;
                }
                if (property_exists($datas[$x], 'avg9')) {
                    $avg9 = $datas[$x]->avg9;
                    $bool[$x][9] = $datas[$x]->category9bool;
                } else {
                    $avg9 = 0;
                    $bool[$x][9] = $datas[$x]->category9bool;
                }
                if (property_exists($datas[$x], 'avg10')) {
                    $avg10 = $datas[$x]->avg10;
                    $bool[$x][10] = $datas[$x]->category10bool;
                } else {
                    $avg10 = 0;
                    $bool[$x][10] = $datas[$x]->category10bool;
                }
                if (property_exists($datas[$x], 'avg11')) {
                    $avg11 = $datas[$x]->avg11;
                    $bool[$x][11] = $datas[$x]->category11bool;
                } else {
                    $avg11 = 0;
                    $bool[$x][11] = $datas[$x]->category11bool;
                }

                //We build an array of averages for the view
                $averages[$x] = array($avg1, $avg2, $avg3, $avg4, $avg5, $avg6, $avg7, $avg8, $avg9, $avg10, $avg11);

            }



            //Get all the categories name to display them
            $categories = array('Informations sur la maladie', 'Informations sur l\'accompagnement', 'Compétences d\'accompagnement', 'Possibilités de soutien', 'Besoin de souffler', 'Possibilités de répit',
                'Qualité du répit', 'Soutien émotionnel ou social formel', 'Soutien émotionnel ou social informel', 'Soutien pratique', 'Soutien financier ou légal');


            //Get all the associations informations to fill the Helps buttons
            $associationsInfo = $project->exportMetadataAss();
            $str = str_replace('\u', 'u', $associationsInfo);
            $strJSON = preg_replace('/u([\da-fA-F]{4})/', '&#x\1;', $str);
            $associations = json_decode($strJSON);


            return view('survey.chart', array(\Auth::user(), 'averages' => $averages, 'categories' => $categories, 'associations' => $associations, 'bool' => $bool));
        }
    }
}