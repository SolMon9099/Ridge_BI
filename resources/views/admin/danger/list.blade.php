@extends('admin.layouts.app')

@section('content')

<div id="wrapper">
    <div class="breadcrumb">
        <ul>
            <li><a href="{{route('admin.danger')}}">危険エリア侵入判定</a></li>
            <li>危険エリア検知リスト(アーカイブ)</li>
        </ul>
    </div>
    <div id="r-content">
        <div class="title-wrap">
            <h2 class="title">危険エリア検知リスト(アーカイブ)</h2>
        </div>
        <form action="danger_list.php" method="post" name="form1" id="form1">
            <div class="title-wrap ver2 stick">
                <div class="sp-ma">
                        <div class="sort">
                            <ul class="date-list">
                                <li>
                                <h4>検出期間</h4>
                                </li>
                                <li>
                                <input type="date" value="<?php echo date('Y-m-d');?>">
                                </li>
                                <li>～</li>
                                <li>
                                <input type="date" value="<?php echo date('Y-m-d');?>">
                                </li>
                            </ul>
                        <ul class="date-list">
                            <li>
                            <h4>ルール</h4>
                            </li>
                            <li><a data-target="rule" class="modal-open setting">選択する</a></li>
                        </ul>
                    <button type="button" class="apply">絞り込む</button>
                    </div>
                </div>
            </div>
            <ul class="kenchi-list">
                <li>
                    <div class="movie"><a data-target="movie0000" class="modal-open  setting2 play"><img src="{{ asset('assets/admin/img/samplepic.svg') }}"></a></div>
                    <div class="text">
                        <time>2022/2/10 11:00</time>
                        <table>
                        <tr>
                            <td>（仮称）ＧＳプロジェクト新築工事</td>
                            <td>3階</td>
                            <td>トイレ横の資材置き場</td>
                            <td>侵入する</td>
                        </tr>
                        </table>
                    </div>
                </li>
                <li>
                    <div class="movie"><a data-target="movie0000" class="modal-open  setting2 play"><img src="{{ asset('assets/admin/img/samplepic.svg') }}"></a></div>
                    <div class="text">
                        <time>2022/2/10 11:00</time>
                        <table>
                        <tr>
                            <td>（仮称）ＧＳプロジェクト新築工事</td>
                            <td>3階</td>
                            <td>トイレ横の資材置き場</td>
                            <td>侵入する</td>
                        </tr>
                        </table>
                    </div>
                </li>
                <li>
                    <div class="movie"><a data-target="movie0000" class="modal-open  setting2 play"><img src="{{ asset('assets/admin/img/samplepic.svg') }}"></a></div>
                    <div class="text">
                        <time>2022/2/10 11:00</time>
                        <table>
                        <tr>
                            <td>（仮称）ＧＳプロジェクト新築工事</td>
                            <td>3階</td>
                            <td>トイレ横の資材置き場</td>
                            <td>侵入する</td>
                        </tr>
                        </table>
                    </div>
                </li>
            </ul>
        </form>
    </div>
</div>
<!--MODAL -->
<div id="rule" class="modal-content">
    <div class="textarea">
        <div class="listing">
            <h3>検索対象となる、ルールを選択してください</h3>
            <div class="scroll active sp-pl0">
            <table class="table2 text-centre">
                <thead>
                <tr>
                    <th class="w10"></th>
                    <th>カメラNo</th>
                    <th>現場名</th>
                    <th>設置フロア</th>
                    <th>設置場所</th>
                    <th>アクション</th>
                    <th>カラー</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="stick-t"><div class="checkbtn-wrap">
                        <input name="checkbox" type="checkbox" id="1">
                        <label for="1"></label>
                    </div></td>
                    <td> 12345</td>
                    <td>（仮称）ＧＳプロジェクト新築工事</td>
                    <td>3階</td>
                    <td>トイレ横の資材置き場</td>
                    <td>かがむ</td>
                    <td><input type="color" id="color1" name="color1" value="#C00000"></td>
                </tr>
                <tr>
                    <td class="stick-t"><div class="checkbtn-wrap">
                        <input name="checkbox" type="checkbox" id="2">
                        <label for="2"></label>
                    </div></td>
                    <td> 12345</td>
                    <td>（仮称）ＧＳプロジェクト新築工事</td>
                    <td>3階</td>
                    <td>トイレ横の資材置き場</td>
                    <td>手を挙げる</td>
                    <td><input type="color" id="color2" name="color2" value="#2CC30E"></td>
                </tr>
                <tr>
                    <td class="stick-t"><div class="checkbtn-wrap">
                        <input name="checkbox" type="checkbox" id="3">
                        <label for="3"></label>
                    </div></td>
                    <td> 12345</td>
                    <td>（仮称）ＧＳプロジェクト新築工事</td>
                    <td>3階</td>
                    <td>トイレ横の資材置き場</td>
                    <td>かがむ</td>
                    <td><input type="color" id="color3" name="color3" value="#FFE100"></td>
                </tr>
                </tbody>
            </table>
            <div class="modal-set">
                <button type="submit" class="modal-close">設 定</button>
            </div>
            </div>
        </div>
    </div>
    <p class="closemodal"><a class="modal-close">×</a></p>
</div>
<!-- -->
<!--MODAL -->
<div id="movie0000" class="modal-content">
<div class="textarea">
    <div class="v">
    <video controls>
        <source src="{{ asset('assets/admin/video/video1.mp4') }}">
    </video>
    </div>
</div>
<p class="closemodal"><a class="modal-close">×</a></p>
</div>
<!-- -->
<!--MODAL -->
<div id="movie0001" class="modal-content">
<div class="textarea">
    <div class="v">
    <video controls>
        <source src="{{ asset('assets/admin/video/video1.mp4') }}">
    </video>
    </div>
</div>
<p class="closemodal"><a class="modal-close">×</a></p>
</div>
<!-- -->

@endsection
