@extends('admin.layouts.app')

@section('content')
<div id="wrapper">
    <div id="r-content">
	    <div class="sp-ma">
            <h2 class="title">最近の検知</h2>
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
        </div>
	</div>
</div>
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
@endsection
