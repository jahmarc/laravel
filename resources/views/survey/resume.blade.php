<!doctype html>
<meta name="csrf-token" content="{{ csrf_token() }}">

@extends('layouts.app')
<?php
$i=1;
$x=1;
//print_r($data);
//print_r($idChapter); //id catégorie
//print_r($idQuestion); //total questions of a category
/*for ($y=1;$y<=$idQuestion;$y++){
    print_r($y);
}*/
?>
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Table des matières</div>
                    <div class="panel-body">
                        <table class="table">
                            @foreach($categories as $category)
                                <tr>
                                    <td>
                                        <a href="./category/<?=$i?>"><?=
                                            $i.')   '.$category;
                                            ?></a>
                                    </td>
                                    <?php
                                     if (($incomplete == true)&&($complete == true) && ($idChapter == $i)): ?>
                                        <td>
                                            <img  src="{{URL::asset('/Images/warning.png')}}" height="25px" width="25px" align="right" title = "La catégorie est partiellement remplie !">
                                        </td>
                                    <?php
                                    elseif (($incomplete == false)&&($complete == true) && ($idChapter == $i)): ?>
                                        <td>
                                            <img  src="{{URL::asset('/Images/ok.png')}}" height="25px" width="25px" align="right" title = "La catégorie est complètement remplie !">
                                        </td>
                                    <?php
                                    elseif ($idChapter == $i): ?>
                                        <td>
                                            <img  src="{{URL::asset('/Images/ko.png')}}" height="25px" width="25px" align="right" title = "La catégorie est vide !">
                                        </td>
                                    <?php endif; ?>

                                </tr>
                                <?php $i++;?>
                            @endforeach
                        </table>

                    </div>
                    <div>
                        <input class="btn btn-lg btn-success pull-right"  style="margin:20px;" onclick="window.location='./chart/<?=$id?>'" value="Mes résultats">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
