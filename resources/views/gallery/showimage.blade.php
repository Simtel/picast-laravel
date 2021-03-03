@extends('layouts.app')
@section('title','Просмотр иозбражения')
@section('content')
    <div style="text-align: center;">
        <img src='{{$image->getFullPath()}}'>
    </div>
    <br>
    <div style="text-align: center;">
        <div class="yashare-auto-init" data-yashareL10n="ru"
             data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir"
             data-yashareTheme="counter">

        </div>
        <br>
    </div>
    <div class="well">
        <h3>Внимание</h3>
        <table>
            <tr>
                <td>
                    <ul>
                        <li><input type='text' style='width:800px;'
                                   value='{{$image->getFullPath()}}'></li>
                        <li><input type='text' style='width:800px;'
                                   value='[img]{{$image->getFullPath()}}[/img]'></li>
                        <li><input type='text' style='width:800px;'
                                   value='[url={{$image->getFullPath()}}][img]{{$image->getThumbFullPath()}}[/img][/url]'>
                        </li>
                        <li><input type='text' style='width:800px;'
                                   value='[url=<?=URL::route('show_image',$image->id)?>][img]{{$image->getThumbFullPath()}}[/img][/url]'>
                        </li>
                    </ul>
                </td>
                <td style='padding-left:100px;'><img src='{{$image->getThumbFullPath()}}'></td>
    </div>
@endsection