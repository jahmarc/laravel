<!doctype html>
<meta name="csrf-token" content="{{ csrf_token() }}">

@extends('layouts.app')
<?php
$i=1;
$x=1;
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


                                <tr><td><a href="./category/<?=$i?>"><?=
                                            $i.')   '.$category;
                                            ?></a></td></tr>
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
