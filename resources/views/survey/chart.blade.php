<!doctype html>
<meta name="csrf-token" content="{{ csrf_token() }}">

@extends('layouts.app')
<?php //Get averages, nuber of categories and categoriesName
		$arrayAverage = array(2,3,4,5,6,7);
		$numberOfCategories  = count($arrayAverage, null);
		$arrayCategoriesName = array('test1','test2', 'test2', 'test2', 'test2', 'test2');



	?>
@section('content')
    <div class="container">
        <!-- Area where statistic will be draw -->
        <canvas id="my_chart" width="670" height="840" >
            Message pour les navigateurs ne supportant pas encore canvas.


        </canvas>
        <!-- Table with three column. Negative value for the top, to superpose the table on canevas -->
        <table style= "position: relative; width:800px; top: -840px ; z-index: -1;" cellspacing="1">
            <?php for($i=0;$i<$numberOfCategories;$i++){

            ?>
            <?php //Alternate 3 colors
            if($arrayAverage[$i]<3){?>
            <tr style="height: 60px; " bgcolor= "#b3ffb3"  >
                <!-- Left -->
                <td style="width:200px; padding-left: 6px"><b><?php echo $arrayCategoriesName[$i];?></b></td>
                <!-- Center -->
                <td >
                    <img  src="Images/bad.png" height="18px" width="18px" align="right" >
                    <img  src="Images/good.png" height="18px" width="18px" align="left">
                </td>
                <!-- Right -->
                <td style="width:100px;">

                </td>
            </tr>
            <?php }
            if($arrayAverage[$i]>=3 && $arrayAverage[$i]<6){?>
            <tr style="height: 60px; " bgcolor= "#ffe0b3"  >
                <!-- Left -->
                <td style="width:200px; padding-left: 6px"><b><?php echo $arrayCategoriesName[$i];?></b> </td>
                <!-- Center -->
                <td>
                    <img  src="Images/bad.png" height="18px" width="18px" align="right">
                    <img  src="Images/good.png" height="18px" width="18px" align="left">
                </td>
                <!-- Right -->
                <td style="width:100px;">

                </td>
            </tr>
            <?php }
            if($arrayAverage[$i]>=6){?>
            <tr style="height: 60px; " bgcolor= "#ffb3b3" >
                <!-- Left -->
                <td style="width:200px; padding-left: 6px"><b><?php echo $arrayCategoriesName[$i];?></b></td>
                <!-- Center -->
                <td>
                    <img  src="Images/bad.png" height="18px" width="18px" align="right">
                    <img  src="Images/good.png" height="18px" width="18px" align="left">
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
                    <form action="give_categorie.php" method="post">
                        <input type="hidden" name="numAssoc"  value = <?php echo $i;?>>
                        <button type="button" class="btn btn-success btn-md"  data-toggle = "modal" data-target="#myModal" data-num-cat= <?php echo $i;?>>Aide</button>
                        <!-- <input type="submit" class="btn btn-info btn-md" value="Aide"> -->
                    </form>
                </td>
            </tr>
            <?php }?>
        </table>

        <br>
        <!-- Modale -->
        <div id="myModal" class="modal fade" role="dialog" >
            <div class="modal-dialog" >
                <div class="modal-content" style ="background-color: #FFE699; ">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Associations pouvant vous aider</h4>
                    </div>
                    <div class="modal-body" id="mod2">

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-info" data-dismiss="modal"  >Fermer</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table to display number result, to compare with statistic. It was put in comment,
        could be useful to develop for the future -->

        <table style="width:30% ; border:2px solid green; position: relative; top: -2100px; left: 900px">
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
                    <b><?php echo $arrayAverage[$i];?></b>
                </td>
            </tr>

            <?php  } ?>
            <tr>
                <td>
                    <form action="start_survey.php">
                        <input type="submit" class="btn btn-info"  value="Recommencer">
                    </form>

                </td>
            </tr>
        </table>
    </div>

    <br>
    <br>
    <br>

    </body>
    <!-- When button is clicked -->
    <script type="text/javascript">
        $('#myModal').on('show.bs.modal', function(e){

            var catId = $(e.relatedTarget).data('num-cat');

            $('#mod2').load('give_categorie.php',{num: catId});

        });
    </script>

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

            //Get averages
            <?php echo "var arrayValues = ". json_encode($arrayAverage).";\n";?>


            //Draw horizontal lines
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
                        context.moveTo(x, y-5);
                        context.lineTo(x, y+5);
                        context.stroke();
                        context.closePath();
                    }
                    else{ //big line
                        context.beginPath();
                        context.moveTo(x, y-8);
                        context.lineTo(x, y+8);
                        context.stroke();
                        context.closePath();

                    }
                    littleLine++;
                    x+=68; //To go 68 pixels on right for next vertical line
                }

                /*Red Points
                compute coordinate X Y to draw red point, related to average*/
                if(arrayValues[i]<=1){
                    x=653-(68*6);
                }

                if(arrayValues[i]>1 && arrayValues[i]<=2){
                    x=653-(68*5);
                }

                if(arrayValues[i]>2 && arrayValues[i]<=3){
                    x=653-(68*4);
                }

                if(arrayValues[i]>3 && arrayValues[i]<=4){
                    x=653-(68*3);
                }

                if(arrayValues[i]>4 && arrayValues[i]<=5){
                    x=653-(68*2);
                }
                if(arrayValues[i]>5 && arrayValues[i]<=6){
                    x=653-68;
                }

                if(arrayValues[i]>6 && arrayValues[i]<=7){
                    x=653;
                }
                // Draw red point
                context.beginPath();
                context.fillStyle = "#b32400";
                context.arc(x, y, 5, 0, Math.PI*2);
                context.fill();
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

            for(var i=0;i<numCategory-1;i++){

                context.beginPath();
                context.strokeStyle = "#b32400";
                context.lineWidth = 2;
                context.moveTo(arrayX[i+1], arrayY[i+1]);
                context.lineTo(arrayX[i], arrayY[i]);
                context.stroke();
                context.closePath();
            }

        }
    </script>





@endsection