<!doctype html>
<meta name="csrf-token" content="{{ csrf_token() }}">

@extends('layouts.app')
<?php
$i=1;
$x=1;
for ($y=0;$y<sizeof($categories);$y++){
    $key = 'pourcent'.($y+1);
    $arPourcent[]=$data[0]->$key;
}
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
                                    if ($arPourcent[$i-1] <> ""): ?>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar"  aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $arPourcent[$i-1] ?>%">
                                              <?php echo round($arPourcent[$i-1]).'%' ; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <?php endif; ?>
                                    <?php
                                     if (($arPourcent[$i-1] == 0 ) &&( $arPourcent[$i-1] <> "") ): ?>
                                        <td>
                                            <img  src="{{URL::asset('/Images/ko.png')}}" height="25px" width="25px" align="right" title = "La catégorie est vide !">
                                        </td>
                                    <?php
                                    elseif ($arPourcent[$i-1] == 100): ?>
                                        <td>
                                            <img  src="{{URL::asset('/Images/ok.png')}}" height="25px" width="25px" align="right" title = "La catégorie est complètement remplie !">
                                        </td>
                                    <?php
                                    elseif ( $arPourcent[$i-1] <> ""): ?>
                                        <td>
                                            <img  src="{{URL::asset('/Images/warning.png')}}" height="25px" width="25px" align="right" title = "La catégorie est partiellement remplie !">
                                        </td>
                                    <?php endif; ?>
                                <?php $i++;?>
                            @endforeach
                                </tr>
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
