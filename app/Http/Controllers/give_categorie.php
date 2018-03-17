<?php

//This file to return the good association related to the clicked button "Aide" in chart.php
    $associationsInfo = $project->exportMetadataAss();


    $str     = str_replace('\u','u',$associationsInfo);
    $strJSON = preg_replace('/u([\da-fA-F]{4})/', '&#x\1;', $str);

    $associations = json_decode($strJSON);



        $arrayAssociations = $associations;

        $numCategories = $_POST['num'];
        unset($_POST);

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
?>

