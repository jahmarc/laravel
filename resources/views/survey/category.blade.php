<!doctype html>
<meta name="csrf-token" content="{{ csrf_token() }}">

@extends('layouts.app')
<?php
$i=1;
$x=1;
$cptQuestions = 0;
?>
@section('content')

    <style>
        .control {
            font-family: arial;
            /*display: inline-block;
            */position: relative;
            padding-left: 30px;
            margin-bottom: 5px;
            padding-top: 3px;
            cursor: pointer;
            font-size: 16px;

        }
        .control input {
            position: absolute;
            z-index: -1;
            opacity: 0;
        }

        /** BOUTON 0 */
        .control_indicator0 {
            position: absolute;
            top: 2px;
            left: 0;
            height: 40px;
            width: 40px;
            background: #e6e6e6;
            border: 0px solid #000000;
        }
        .control-radio .control_indicator0 {
            border-radius: 50%;
        }

        .control:hover input ~ .control_indicator0,
        .control input:focus ~ .control_indicator0 {
            background: red;
        }

        .control input:checked ~ .control_indicator0 {
            background: red;
        }
        .control:hover input:not([disabled]):checked ~ .control_indicator0,
        .control input:checked:focus ~ .control_indicator0 {
            background: #0e6647d;
        }
        .control input:disabled ~ .control_indicator0 {
            background: #e6e6e6;
            opacity: 0.6;
            pointer-events: none;
        }
        .control_indicator0:after {
            box-sizing: unset;
            content: '';
            position: absolute;
            display: none;
        }
        .control input:checked ~ .control_indicator0:after {
            display: block;
        }
        .control-radio .control_indicator0:after {
            left: 7px;
            top: 7px;
            height: 6px;
            width: 6px;
            border-radius: 50%;
            background: #ffffff;
        }
        .control-radio input:disabled ~ .control_indicator0:after {
            background: #7b7b7b;
        }

        .control-radio input:disabled ~ .control_indicator0:after {
            background: #7b7b7b;
        }



        /** BOUTON 1 */
        .control_indicator1 {
            position: absolute;
            top: 2px;
            left: 0;
            height: 40px;
            width: 40px;
            background: #e6e6e6;
            border: 0px solid #000000;
        }
        .control-radio .control_indicator1 {
            border-radius: 50%;
        }

        .control:hover input ~ .control_indicator1,
        .control input:focus ~ .control_indicator1 {
            background: #ff7c00;
        }

        .control input:checked ~ .control_indicator1 {
            background: #ff7c00;
        }
        .control:hover input:not([disabled]):checked ~ .control_indicator1,
        .control input:checked:focus ~ .control_indicator1 {
            background: #0e6647d;
        }
        .control input:disabled ~ .control_indicator1 {
            background: #e6e6e6;
            opacity: 0.6;
            pointer-events: none;
        }
        .control_indicator1:after {
            box-sizing: unset;
            content: '';
            position: absolute;
            display: none;
        }
        .control input:checked ~ .control_indicator1:after {
            display: block;
        }
        .control-radio .control_indicator1:after {
            left: 7px;
            top: 7px;
            height: 6px;
            width: 6px;
            border-radius: 50%;
            background: #ffffff;
        }
        .control-radio input:disabled ~ .control_indicator1:after {
            background: #7b7b7b;
        }

        .control-radio input:disabled ~ .control_indicator1:after {
            background: #7b7b7b;
        }


        /** BOUTON 2 */
        .control_indicator2 {
            position: absolute;
            top: 2px;
            left: 0;
            height: 40px;
            width: 40px;
            background: #e6e6e6;
            border: 0px solid #000000;
        }
        .control-radio .control_indicator2 {
            border-radius: 50%;
        }

        .control:hover input ~ .control_indicator2,
        .control input:focus ~ .control_indicator2 {
            background: #ffd900;
        }

        .control input:checked ~ .control_indicator2 {
            background: #ffd900;
        }
        .control:hover input:not([disabled]):checked ~ .control_indicator2,
        .control input:checked:focus ~ .control_indicator2 {
            background: #0e6647d;
        }
        .control input:disabled ~ .control_indicator2 {
            background: #e6e6e6;
            opacity: 0.6;
            pointer-events: none;
        }
        .control_indicator2:after {
            box-sizing: unset;
            content: '';
            position: absolute;
            display: none;
        }
        .control input:checked ~ .control_indicator2:after {
            display: block;
        }
        .control-radio .control_indicator2:after {
            left: 7px;
            top: 7px;
            height: 6px;
            width: 6px;
            border-radius: 50%;
            background: #ffffff;
        }
        .control-radio input:disabled ~ .control_indicator2:after {
            background: #7b7b7b;
        }

        .control-radio input:disabled ~ .control_indicator2:after {
            background: #7b7b7b;
        }

        /** BOUTON 3 */
        .control_indicator3 {
            position: absolute;
            top: 2px;
            left: 0;
            height: 40px;
            width: 40px;
            background: #e6e6e6;
            border: 0px solid #000000;
        }
        .control-radio .control_indicator3 {
            border-radius: 50%;
        }

        .control:hover input ~ .control_indicator3,
        .control input:focus ~ .control_indicator3 {
            background: yellow;
        }

        .control input:checked ~ .control_indicator3 {
            background: yellow;
        }
        .control:hover input:not([disabled]):checked ~ .control_indicator3,
        .control input:checked:focus ~ .control_indicator3 {
            background: #0e6647d;
        }
        .control input:disabled ~ .control_indicator3 {
            background: #e6e6e6;
            opacity: 0.6;
            pointer-events: none;
        }
        .control_indicator3:after {
            box-sizing: unset;
            content: '';
            position: absolute;
            display: none;
        }
        .control input:checked ~ .control_indicator3:after {
            display: block;
        }
        .control-radio .control_indicator3:after {
            left: 7px;
            top: 7px;
            height: 6px;
            width: 6px;
            border-radius: 50%;
            background: #ffffff;
        }
        .control-radio input:disabled ~ .control_indicator3:after {
            background: #7b7b7b;
        }

        .control-radio input:disabled ~ .control_indicator3:after {
            background: #7b7b7b;
        }

        /** BOUTON 4 */
        .control_indicator4 {
            position: absolute;
            top: 2px;
            left: 0;
            height: 40px;
            width: 40px;
            background: #e6e6e6;
            border: 0px solid #000000;
        }
        .control-radio .control_indicator4 {
            border-radius: 50%;
        }

        .control:hover input ~ .control_indicator4,
        .control input:focus ~ .control_indicator4 {
            background: #d7ff00;
        }

        .control input:checked ~ .control_indicator4 {
            background: #d7ff00;
        }
        .control:hover input:not([disabled]):checked ~ .control_indicator4,
        .control input:checked:focus ~ .control_indicator4 {
            background: #0e6647d;
        }
        .control input:disabled ~ .control_indicator4 {
            background: #e6e6e6;
            opacity: 0.6;
            pointer-events: none;
        }
        .control_indicator4:after {
            box-sizing: unset;
            content: '';
            position: absolute;
            display: none;
        }
        .control input:checked ~ .control_indicator4:after {
            display: block;
        }
        .control-radio .control_indicator4:after {
            left: 7px;
            top: 7px;
            height: 6px;
            width: 6px;
            border-radius: 50%;
            background: #ffffff;
        }
        .control-radio input:disabled ~ .control_indicator4:after {
            background: #7b7b7b;
        }

        .control-radio input:disabled ~ .control_indicator4:after {
            background: #7b7b7b;
        }


        /** BOUTON 5 */
        .control_indicator5 {
            position: absolute;
            top: 2px;
            left: 0;
            height: 40px;
            width: 40px;
            background: #e6e6e6;
            border: 0px solid #000000;
        }
        .control-radio .control_indicator5 {
            border-radius: 50%;
        }

        .control:hover input ~ .control_indicator5,
        .control input:focus ~ .control_indicator5 {
            background: #97ff06;
        }

        .control input:checked ~ .control_indicator5 {
            background: #97ff06;
        }
        .control:hover input:not([disabled]):checked ~ .control_indicator5,
        .control input:checked:focus ~ .control_indicator5 {
            background: #0e6647d;
        }
        .control input:disabled ~ .control_indicator5 {
            background: #e6e6e6;
            opacity: 0.6;
            pointer-events: none;
        }
        .control_indicator5:after {
            box-sizing: unset;
            content: '';
            position: absolute;
            display: none;
        }
        .control input:checked ~ .control_indicator5:after {
            display: block;
        }
        .control-radio .control_indicator5:after {
            left: 7px;
            top: 7px;
            height: 6px;
            width: 6px;
            border-radius: 50%;
            background: #ffffff;
        }
        .control-radio input:disabled ~ .control_indicator5:after {
            background: #7b7b7b;
        }

        .control-radio input:disabled ~ .control_indicator5:after {
            background: #7b7b7b;
        }

        /** BOUTON 6 */
        .control_indicator6 {
            position: absolute;
            top: 2px;
            left: 0;
            height: 40px;
            width: 40px;
            background: #e6e6e6;
            border: 0px solid #000000;
        }
        .control-radio .control_indicator6 {
            border-radius: 50%;
        }

        .control:hover input ~ .control_indicator6,
        .control input:focus ~ .control_indicator6 {
            background: limegreen;
        }

        .control input:checked ~ .control_indicator6 {
            background: limegreen;
        }
        .control:hover input:not([disabled]):checked ~ .control_indicator6,
        .control input:checked:focus ~ .control_indicator6 {
            background: #0e6647d;
        }
        .control input:disabled ~ .control_indicator6 {
            background: #e6e6e6;
            opacity: 0.6;
            pointer-events: none;
        }
        .control_indicator6:after {
            box-sizing: unset;
            content: '';
            position: absolute;
            display: none;
        }
        .control input:checked ~ .control_indicator6:after {
            display: block;
        }
        .control-radio .control_indicator6:after {
            left: 7px;
            top: 7px;
            height: 6px;
            width: 6px;
            border-radius: 50%;
            background: #ffffff;
        }
        .control-radio input:disabled ~ .control_indicator6:after {
            background: #7b7b7b;
        }

        .control-radio input:disabled ~ .control_indicator6:after {
            background: #7b7b7b;
        }



        /** BOUTON 7 */
        .control_indicator7 {
            position: absolute;
            top: 2px;
            left: 0;
            height: 40px;
            width: 40px;
            background: #e6e6e6;
            border: 0px solid #000000;
        }
        .control-radio .control_indicator7 {
            border-radius: 50%;
        }

        .control:hover input ~ .control_indicator7,
        .control input:focus ~ .control_indicator7 {
            background: black;
        }

        .control input:checked ~ .control_indicator7 {
            background: black;
        }
        .control:hover input:not([disabled]):checked ~ .control_indicator7,
        .control input:checked:focus ~ .control_indicator7 {
            background: #0e6647d;
        }
        .control input:disabled ~ .control_indicator7 {
            background: #e6e6e6;
            opacity: 0.6;
            pointer-events: none;
        }
        .control_indicator7:after {
            box-sizing: unset;
            content: '';
            position: absolute;
            display: none;
        }
        .control input:checked ~ .control_indicator7:after {
            display: block;
        }
        .control-radio .control_indicator7:after {
            left: 7px;
            top: 7px;
            height: 6px;
            width: 6px;
            border-radius: 50%;
            background: #ffffff;
        }
        .control-radio input:disabled ~ .control_indicator7:after {
            background: #7b7b7b;
        }

        .control-radio input:disabled ~ .control_indicator7:after {
            background: #7b7b7b;
        }

td{
    width:10%;
}

        #pbutton{
            margin-top: -40px;
        }

        label{font-size: xx-large;}
    </style>




    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-auto">

                <div class="panel panel-default">

                    <div class="panel-body">

                        {!! Form::open(['action' => 'QuestionsController@store', 'class' => 'form-horizontal']) !!}

                        <div id="form">

                            <legend style="color:#0058ff; "><?= $id.')   '.$categories[$id-1]; ?></legend>

                            {!! Form::hidden('id',$id) !!}

                            <div class="form-group">
                                <title>$category</title>
                            </div>

                            @foreach($questions as $question)
                                @if($question->matrix_group_name == 'categorie'.$id)


                                <!-- Text Area -->
                                    <div class="form-group">
                                        <div class="col-lg-offset-9"  style="margin-top: 2%;">
                                            {!! Form::label('question', $question->field_label, ['class' => '']) !!}
                                        </div>
                                    </div>

                                    <fieldset id="group<?= $i?>" style="padding-bottom: 30px;border-bottom-color: #e4e4e4;border-bottom-width: 0.5px;border-bottom-style: solid;">
                                        <!-- Radio Buttons -->
                                        <div class="form-group";>
                                            <div class="col-lg-auto">
                                                <div class="radio" style="float:left; width: 100%; text-align: center; ">


                                                <table>

                                                    <tr>
                                                        <td>
                                                            <label class="control control-radio">
                                                                <p id="pbutton">0 - Pas du tout</p>
                                                                {!! Form::radio('q'.$id.'_'.$i, '0', false, ['id' => 'radio1']) !!}
                                                                <div class="control_indicator0" style="margin-left: 47px;"></div>
                                                            </label>
                                                        </td>

                                                        <td><!-- BOUTON 1 -->
                                                            <label class="control control-radio">
                                                                <p id="pbutton">1</p>
                                                                {!! Form::radio('q'.$id.'_'.$i, '1', false, ['id' => 'radio2']) !!}
                                                                <div class="control_indicator1"></div>
                                                            </label>
                                                        </td>

                                                        <td><!-- BOUTON 2 -->
                                                            <label class="control control-radio">
                                                                <p id="pbutton">2 - Un peu</p>
                                                                {!! Form::radio('q'.$id.'_'.$i, '2', false, ['id' => 'radio3']) !!}
                                                                <div class="control_indicator2" style="margin-left: 47px;"></div>
                                                            </label>
                                                        </td>

                                                        <td><!-- BOUTON 3 -->
                                                            <label class="control control-radio">
                                                                <p id="pbutton">3</p>
                                                                {!! Form::radio('q'.$id.'_'.$i, '3', false, ['id' => 'radio4']) !!}
                                                                <div class="control_indicator3"></div>
                                                            </label>
                                                        </td>




                                                        <td><!-- BOUTON 4 -->
                                                            <label class="control control-radio">
                                                                <p id="pbutton">4 - Assez</p>
                                                                {!! Form::radio('q'.$id.'_'.$i, '4', false, ['id' => 'radio5']) !!}
                                                                <div class="control_indicator4" style="margin-left: 47px;"></div>
                                                            </label>
                                                        </td>



                                                        <td>
                                                            <!-- BOUTON 5 -->
                                                            <label class="control control-radio">
                                                                <p id="pbutton">5</p>
                                                                {!! Form::radio('q'.$id.'_'.$i, '5', false, ['id' => 'radio6']) !!}
                                                                <div class="control_indicator5"></div>
                                                            </label>
                                                        </td>


                                                        <td>

                                                            <!-- BOUTON 6 -->
                                                            <label class="control control-radio">
                                                                <p id="pbutton">6 - Tout à fait</p>
                                                                {!! Form::radio('q'.$id.'_'.$i, '6', false, ['id' => 'radio7']) !!}
                                                                <div class="control_indicator6" style="margin-left: 47px;"></div>
                                                            </label>
                                                        </td>

                                                        <td>

                                                            <!-- BOUTON 7 -->
                                                            <label class="control control-radio">
                                                                <p id="pbutton">Pas concerné</p>
                                                                {!! Form::radio('q'.$id.'_'.$i, '7', false, ['id' => 'radio8']) !!}
                                                                <div class="control_indicator7" style="margin-left: 47px;"  ></div>
                                                            </label>

                                                        </td>

                                                    </tr>
                                                </table>






                                            </div>
                                        </div><div style="margin-bottom:45px"><hr style="background-color:white; color:white;    border-top: 1px solid #fff;"></div>
                                    </fieldset>
                                    <?php $i++; $cptQuestions++?>
                                @endif
                            @endforeach
                        </div>


                        <!-- Submit Button -->
                        <div class="form-group" style="clear: left; margin-top:40px;">
                            <div class="col-lg-10 col-lg-offset-2">
                                {!! Form::submit('Enregistrer le chapitre', ['class' => 'btn btn-lg btn-info pull-right'] ) !!}
                                {!! Form::hidden('cptQuestions',$cptQuestions) !!}
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
