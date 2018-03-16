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
            <div class="col-md-auto">

                <div class="panel panel-default">

                        <div class="panel-body">

                        {!! Form::open(['action' => 'QuestionsController@store', 'class' => 'form-horizontal']) !!}

                        <div id="form">

                            <legend style="color:#0058ff;"><?= $categories[$id-1]; ?></legend>

                            {!! Form::hidden('id',$id) !!}

                        @foreach($questions as $question)
                            @if($question->matrix_group_name == 'categorie'.$id)


                                <!-- Text Area -->
                                    <div class="form-group">
                                        <div class="col-lg-offset-9"  style="margin-top: 2%;">
                                            {!! Form::label('question', $question->field_label, ['class' => '']) !!}
                                        </div>
                                    </div>

                                    <fieldset id="group<?= $i?>" style="border-bottom-color: #e4e4e4;
    border-bottom-width: 0.5px;
    border-bottom-style: solid;">
                                        <!-- Radio Buttons -->
                                        <div class="form-group";>
                                            <div class="col-lg-auto" >
                                                <div class="radio" style="float:left; width: 12.5%; text-align: center; ">
                                                    <div class="radio" style="float: top; align-text:center;; " >
                                                        {!! Form::label('radio1', '0 - Pas du tout') !!}
                                                    </div>
                                                    <div class="radio" name="radio1" style="float: top; width: 50%; margin: 0 auto;text-align: center;">
                                                        {!! Form::radio('q'.$id.'_'.$i, '0', false, ['id' => 'radio1']) !!}
                                                    </div>
                                                </div>
                                                <div class="radio" style="float:left; clear: top; width: 12.5%;text-align: center;">
                                                    <div class="radio" style="float: top; align-text:center;" >
                                                        {!! Form::label('radio2', '1') !!}
                                                    </div>
                                                    <div class="radio" style="float: top; width: 50%; margin: 0 auto;text-align: center;">
                                                        {!! Form::radio('q'.$id.'_'.$i, '1', false, ['id' => 'radio2']) !!}
                                                    </div>
                                                </div>
                                                <div class="radio" style="float:left; clear: top; width: 12.5%;text-align: center;">
                                                    <div class="radio" style="float: top; align-text:center;" >
                                                        {!! Form::label('radio3', '2 - Un peu') !!}
                                                    </div>
                                                    <div class="radio" style="float: top; width: 50%; margin: 0 auto;text-align: center;">
                                                        {!! Form::radio('q'.$id.'_'.$i, '2', false, ['id' => 'radio3']) !!}
                                                    </div>
                                                </div>
                                                <div class="radio" style="float:left; clear: top; width: 12.5%;text-align: center;">
                                                    <div class="radio" style="float: top; align-text:center;" >
                                                        {!! Form::label('radio4', '3') !!}
                                                    </div>
                                                    <div class="radio" style="float: top; width: 50%; margin: 0 auto;text-align: center;">
                                                        {!! Form::radio('q'.$id.'_'.$i, '3', false, ['id' => 'radio4']) !!}
                                                    </div>
                                                </div>
                                                <div class="radio" style="float:left; clear: top; width: 12.5%;text-align: center;">
                                                    <div class="radio" style="float: top; align-text:center;" >
                                                        {!! Form::label('radio5', '4 - Assez') !!}
                                                    </div>
                                                    <div class="radio" style="float: top; width: 50%; margin: 0 auto;text-align: center;">
                                                        {!! Form::radio('q'.$id.'_'.$i, '4', false, ['id' => 'radio5']) !!}
                                                    </div>
                                                </div>
                                                <div class="radio" style="float:left; clear: top; width: 12.5%;text-align: center;">
                                                    <div class="radio" style="float: top; align-text:center;" >
                                                        {!! Form::label('radio6', '5') !!}
                                                    </div>
                                                    <div class="radio" style="float: top; width: 50%; margin: 0 auto;text-align: center;">
                                                        {!! Form::radio('q'.$id.'_'.$i, '5', false, ['id' => 'radio6']) !!}
                                                    </div>
                                                </div>
                                                <div class="radio" style="float:left; clear: top; width: 12.5%;text-align: center;">
                                                    <div class="radio" style="float: top; align-text:center;" >
                                                        {!! Form::label('radio7', '6 - Tout à fait') !!}
                                                    </div>
                                                    <div class="radio" style="float: top; width: 50%; margin: 0 auto;text-align: center;">
                                                        {!! Form::radio('q'.$id.'_'.$i, '6', false, ['id' => 'radio7']) !!}
                                                    </div>
                                                </div>
                                                <div class="radio" style="float:left; clear: top; width: 12.5%; min-width:100px;text-align: center;">
                                                    <div class="radio" style="float: top; align-text:center;" >
                                                        {!! Form::label('radio8', 'Pas concerné') !!}
                                                    </div>
                                                    <div class="radio" style="float: top; width: 50%; margin: 0 auto;">
                                                        {!! Form::radio('q'.$id.'_'.$i, '7', false, ['id' => 'radio8']) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div><div style="margin-bottom:45px"><hr style="background-color:white; color:white;    border-top: 1px solid #fff;"></div>
                                    </fieldset>
                                <?php $i++; ?>
                            @endif
                        @endforeach
                        </div>


                        <!-- Submit Button -->
                            <div class="form-group" style="clear: left">
                                <div class="col-lg-10 col-lg-offset-2">
                                    {!! Form::submit('Enregistrer le chapitre', ['class' => 'btn btn-lg btn-info pull-right'] ) !!}
                                </div>
                            </div>

                        </fieldset>

                        {!! Form::close()  !!}

                </div>
                </div>
            </div>
        </div>
    </div>



@endsection
