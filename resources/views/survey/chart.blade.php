<!doctype html>
<meta name="csrf-token" content="{{ csrf_token() }}">

@extends('layouts.app')
<?php //Get averages, nuber of categories and categoriesName
$arrayAverage = $averages;

$numberOfCategories  = count($arrayAverage, null);
$arrayCategoriesName = $categories;
$arrayAssociations = $associations;
$isEmpty = 1;

$bool1 = true;
$bool2 = false;
$bool3 = false;

//print_r($bool);
session_start();

?>
@section('content')
    <div class="container">
        <div>
            <form action="../back">
                <input type="submit"class="btn btn-info"value="Retour">
            </form>
        </div>
        <!-- Area where statistic will be draw -->
        <canvas id="my_chart" width="670" height="840" >
            Message pour les navigateurs ne supportant pas encore canvas.


        </canvas>
        <!-- Table with three column. Negative value for the top, to superpose the table on canevas -->
        <table style= "position: relative; width:800px; top: -840px ; z-index: -1;" cellspacing="1">



            <?php for($i=0;$i<$numberOfCategories;$i++){
            if ($bool[$i+1] !=  1)
                $isEmpty = 0;
            ?>

            <?php //Alternate 3 colors
            if($arrayAverage[$i]== ""){?>
            <tr style="height: 60px; " bgcolor= "#F2F2F2"  >
                <!-- Left -->
                <td style="width:200px; padding-left: 6px"><b><?php echo $arrayCategoriesName[$i];?></b></td>
                <!-- Center -->
                <td >
                    <img  src="{{URL::asset('/Images/bad.png')}}" height="18px" width="18px" align="right" >
                    <img  src="{{URL::asset('/Images/good.png')}}" height="18px" width="18px" align="left">
                </td>
                <!-- Right -->
                <td style="width:100px;">

                </td>
            </tr>
            <?php } //Alternate 3 colors
            if($arrayAverage[$i]>0 && $arrayAverage[$i]<3){?>
            <tr style="height: 60px; " bgcolor= "#ffb3b3"  >
                <!-- Left -->
                <td style="width:200px; padding-left: 6px"><b><?php echo $arrayCategoriesName[$i];?></b></td>
                <!-- Center -->
                <td >
                    <img  src="{{URL::asset('/Images/bad.png')}}" height="18px" width="18px" align="right" >
                    <img  src="{{URL::asset('/Images/good.png')}}" height="18px" width="18px" align="left">
                </td>
                <!-- Right -->
                <td style="width:100px;">

                </td>
            </tr>
            <?php }
            if($arrayAverage[$i]>=3 && $arrayAverage[$i]<5){?>
            <tr style="height: 60px; " bgcolor= "#ffe0b3"  >
                <!-- Left -->
                <td style="width:200px; padding-left: 6px"><b><?php echo $arrayCategoriesName[$i];?></b> </td>
                <!-- Center -->
                <td>
                    <img  src="{{URL::asset('/Images/bad.png')}}" height="18px" width="18px" align="right">
                    <img  src="{{URL::asset('/Images/good.png')}}" height="18px" width="18px" align="left">
                </td>
                <!-- Right -->
                <td style="width:100px;">

                </td>
            </tr>
            <?php }
            if($arrayAverage[$i]>=5){?>
            <tr style="height: 60px; " bgcolor= "#b3ffb3" >
                <!-- Left -->
                <td style="width:200px; padding-left: 6px"><b><?php echo $arrayCategoriesName[$i];?></b></td>
                <!-- Center -->
                <td>
                    <img  src="{{URL::asset('/Images/bad.png')}}" height="18px" width="18px" align="right">
                    <img  src="{{URL::asset('/Images/good.png')}}" height="18px" width="18px" align="left">
                </td>
                <!-- Right -->
                <td style="width:100px; ">

                </td>
            </tr>
            <?php }?>

            <?php } //End of "for"?>


        </table  >
        <!-- buttons -->
    <?php
    // Take the the canvas higher and add number of category multiply by 60 (pixels)
    $topPosition = (840+($numberOfCategories*60))*-1;?>
    <!-- Display button on the right -->
        <table style= "position: relative; width:100px; top: <?php echo $topPosition.'px';?> ; left: 710px">
            <?php for($i=0;$i<$numberOfCategories;$i++){
            ?>
            <tr style="height: 60px; ">
                <td>
                    <form action="QuestionsController@association" method="post">
                        <input  type="hidden" name="numAssoc"  value = <?php echo $i;?>>
                        <button type="button" class="btn btn-success btn-md"  data-toggle = "modal" data-target=<?php echo '#myModal'.$i?> data-num-cat= <?php echo $i;?>>Aide</button>
                        <!-- <input type="submit" class="btn btn-info btn-md" value="Aide"> -->
                    </form>
                </td>
            </tr>


            <?php }?>
        </table>

        <br>
        <!-- Modale -->

        <?php for($k=0;$k<$numberOfCategories;$k++){
        ?>
        <div id=<?php echo 'myModal'.$k?> class="modal fade" role="dialog" >
        <div class="modal-dialog" >
            <div class="modal-content" style ="background-color: #FFE699; ">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Associations pouvant vous aider</h4>
                </div>
                <div class="modal-body" id=<?php echo 'mod'.$k?>>
                    <?php
                    $numCategories = $k;
                    $numCategories+=1; //Because Category 0 don't exist
                    $arrayInfosAssociation = [];
                    $j = 0;
                    $b = 1;
                    $positionOfAssociation = findAssociation($arrayAssociations, $numCategories);
                    $sizeOfArray = (count($arrayAssociations) - 1);
                    while ($b == 1) {
                        /*Get all value for category chosen
                          "section header contain the number of category in data from REDCap. If "section_header" is empty, that mean
                          we don^t change category*/
                        if ($arrayAssociations[$positionOfAssociation]->section_header == $numCategories || $arrayAssociations[$positionOfAssociation]->section_header == '') {
                            $arrayInfosAssociation[$j] = $arrayAssociations[$positionOfAssociation]->field_label;
                            $positionOfAssociation += 1;
                            $j += 1;
                        } else {
                            $b = 0;
                        }
                        if ($positionOfAssociation > $sizeOfArray) {
                            $b = 0;
                        }
                    }
                    displayInfos($arrayInfosAssociation);
                    ?>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-info" data-dismiss="modal"  >Fermer</button>
                </div>
            </div>
        </div>
    </div>
    <?php }?>

    <!-- Table to display number result, to compare with statistic. It was put in comment,
        could be useful to develop for the future -->

    <table style="width:30% ; border:2px solid green; position: relative; top: -2200px; left: 900px">
        <tr>
            <th>Tableau de resultats</th>
        </tr>

        <?php
        for($i=0;$i<$numberOfCategories;$i++){?>
        <tr style="border:1px solid green;">
            <td style="padding: 5px">
                <?php  echo $arrayCategoriesName[$i];?>
            </td>
            <td style="padding: 5px">
                <b>
                    <?php
                    if ($arrayAverage[$i]!=null){
                        echo $arrayAverage[$i];
                    }
                    else{
                        echo 0;
                    }
                    ?>
                </b>
            </td>
        </tr>

        <?php  } ?>
        <tr>
            <td>
                <form action="../home" >
                    <input type="submit" class="btn btn-info"  value="Recommencer" >
                </form>

            </td>
        </tr>
    </table>

    <table style="width:30% ; border:2px solid green; position: relative; top: -2170px; left: 900px">

        <tr style="border:1px solid green;">

            <td style="padding: 5px">Legende</td>
        </tr>
        <tr>
            <td style="padding: 5px">
                <svg height="100" width="100">
                    <circle cx="50" cy="50" r="40" stroke="black" stroke-width="3" fill="#b3ffb3" />
                    Sorry, your browser does not support inline SVG.
                </svg>
            </td>
            <td>
                Sujet maitrisé
            </td>

        </tr>
        <tr>
            <td>
                <svg height="100" width="100">
                    <circle cx="50" cy="50" r="40" stroke="black" stroke-width="3" fill="#ffe0b3" />
                    Sorry, your browser does not support inline SVG.
                </svg>
            </td>
            <td>
                Sujet moyennement maitrisé
            </td>

        </tr>
        <tr>
            <td>
                <svg height="100" width="100">
                    <circle cx="50" cy="50" r="40" stroke="black" stroke-width="3" fill="#ffb3b3" />
                    Sorry, your browser does not support inline SVG.
                </svg>
            </td>
            <td>
                Sujet non-maitrisé
            </td>
        <tr>
            <td>
                <svg height="100" width="100">
                    <circle cx="50" cy="50" r="40" stroke="black" stroke-width="3" fill="#F2F2F2" />
                    Sorry, your browser does not support inline SVG.
                </svg>
            </td>
            <td>
                Non répondu
            </td>

        </tr>

    </table>

    <table style="width:30% ; border:2px solid green; position: relative; top: -2140px; left: 900px">

        <td style="padding: 5px">
        <td>
            <form action="../home">
                <input type="button"  class="btn btn-info"  value="Graphique 1" name="gr1" onclick = "displayGraphics(name)">

                <input type="button" class="btn btn-info"  value="Graphique 2" name="gr2" onclick = "displayGraphics(name)">

                <input type="button" class="btn btn-info"  value="Graphique 2" name="gr3" onclick = "displayGraphics(name)">
            </form>


        </td>

        </tr>

    </table>



    </div>

    <br>
    <br>
    <br>



@endsection

<!-- Create statistic with Canvas -->
<script type="text/javascript">
    window.onload = function()
    {
        var canvas = document.getElementById('my_chart');
        if(!canvas)
        {
            alert("Impossible de récupérer le canvas");
            return;
        }
        var context = canvas.getContext('2d');
        if(!context)
        {
            alert("Impossible de récupérer le context du canvas");
            return;
        }
        var x = 245;
        var y = 36
        //Get value from PHP
        var numCategory = <?php echo $numberOfCategories;?>;
        var littleLine =0; //To alternate little and big vertical bar
        //Positions
        var arrayX=[];
        var arrayY=[];
        var empty =<?php echo $isEmpty;?>;
        var arrEmpty = <?php echo json_encode($bool); ?>;
        var exit = false;
        //Get averages
        <?php echo "var arrayValues = ". json_encode($arrayAverage).";\n";?>
        //Draw horizontal black lines
        for(var i=0;i<numCategory;i++){
            context.beginPath();
            context.moveTo(245, y);
            context.lineTo(653, y);
            context.stroke();
            context.closePath();
            //Draw Vertical lines (bar)
            for(var j=0;j<7;j++){
                if(littleLine%2==1){ //Little line
                    context.beginPath();
                    context.moveTo(x, y - 5);
                    context.lineTo(x, y + 5);
                    context.stroke();
                    context.closePath();
                }
                else{ //big line
                    context.beginPath();
                    context.moveTo(x, y - 8);
                    context.lineTo(x, y + 8);
                    context.stroke();
                    context.closePath();
                }
                littleLine++;
                x+=68; //To go 68 pixels on right for next vertical line
            }
            /*Red Points
            compute coordinate X Y to draw red point, related to average*/
            if(arrayValues[i]>5&&arrayValues[i]<=6){
                x=653-(68*6);
            }
            if(arrayValues[i]>4&&arrayValues[i]<=5){
                x=653-(68*5);
            }
            if(arrayValues[i]>3&&arrayValues[i]<=4){
                x=653-(68*4);
            }
            if(arrayValues[i]>2&&arrayValues[i]<=3){
                x=653-(68*3);
            }
            if(arrayValues[i]>1&&arrayValues[i]<=2){
                x=653-(68*2);
            }
            if(arrayValues[i]>0&&arrayValues[i]<=1){
                x=653-68;
            }
            if(arrayValues[i]==0){
                x=653;
            }

            // Draw red point
            context.beginPath();
            context.fillStyle = "#b32400";
            context.arc(x, y, 5, 0, Math.PI*2);
            if(arrEmpty[i+1] == 1) {
                context.fill();
            }
            context.closePath();
            /*Keep coordonate of actual red point in an array
            used after to draw line between points*/
            arrayX.push(x);
            arrayY.push(y);
            littleLine =0;
            x=245;
            y+=60; //Prepare coordonate for the next horizontal line (60 pixels below)
        }
        //Draw line between points
        for (var i = 0; i < numCategory - 1; i++) {

            if ((arrEmpty[i+1] == 1)&&(arrEmpty[i+2] == 1) ){
                context.beginPath();
                context.strokeStyle = "#b32400";
                context.lineWidth = 2;
                context.moveTo(arrayX[i + 1], arrayY[i + 1]);
                context.lineTo(arrayX[i], arrayY[i]);
                context.stroke();
                context.closePath();
            }
     }

    }
</script>
<?php
//Find good association related to clicked button
function findAssociation($array, $num)
{
    $i = 0;
    $num = strval($num);
    //Fin category in array from REDCap
    foreach ($array as $element) {
        if ($element->section_header == $num) {
            return $i;
        }
        $i++;
    }
}
//Display infos in the modal
function displayInfos($array)
{
    $title;
    $infos;
    $nextHyphenPos;
    $end;
    foreach ($array as $element) {
        //Get title (Text before ":")
        $title = substr($element, 0, strpos($element, ':'));
        //Variables to display info for each category
        $sizeString = strlen($element);
        $b = 1;
        $arrayTemp = [];
        $i = 0;
        $end = true;
        //Seach first "*"
        $nextHyphenPos = strpos($element, '*');
        //Get String since "*"
        $element = substr($element, $nextHyphenPos + 1);
        while ($end !== false) {
            //Seach next "*"
            $end = strpos($element, '*', 0);
            if ($end !== false) {
                $arrayTemp[$i] = substr($element, 0, $end);
                $i++;
            } else {
                $arrayTemp[$i] = $element;
            }
            $nextHyphenPos = strpos($element, '*', 1);
            //Get String since "*"
            $element = substr($element, $nextHyphenPos + 1);
        }
        //Display in modal
        echo '<b>' . $title . ':</b> <br>';
        echo '<ul>';
        for ($j = 0; $j < count($arrayTemp); $j++) {
            echo '<li>' . $arrayTemp[$j] . '</li>';
        }
        echo '</ul> <br>';
    }
}
//Display graphics with buttons
function displayGraphics($strButton)
{

    switch($strButton){
        case 'gr1':
            if($bool1 == true){

            }

}

}
?>