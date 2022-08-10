<div class="no-scroll">

    <p class="sp att"><span class="red">ファイル選択はPCからのアップロードのみになります。</span></p>
    <table class="table">
        <thead>
        <tr>
            <th>設置エリア</th>
            <td><div class="select-c">
                <select name="location_id">
                    <option value="">設置エリアを選択してください</option>
                    @foreach ($locations as $key => $location)
                        @if (old('location_id', isset($drawing->location_id)? $drawing->location_id:'') == $key)
                        <option value="{{$key}}" selected>{{$location}}</option>
                        @else
                        <option value="{{$key}}">{{$location}}</option>
                        @endif
                    @endforeach
                </select></div>
                @error('location_id')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </td>
        </tr>
        <tr>
            <th>設置フロア</th>
            <td><input type="text" name="floor_number" placeholder="設置フロアを入力してください" value="{{ old('floor_number', isset($drawing->floor_number)?$drawing->floor_number:'')}}">
                @error('floor_number')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </td>
        </tr>

        <tr>
            <th>図面ファイル</th>
            <td>

                @php
                    $file_path = old('drawing_file_path', isset($drawing->drawing_file_path) ?  $drawing->drawing_file_path : '');
                    if (Storage::disk('temp')->has($file_path)) {
                        $file_url = asset('storage/temp/' . $file_path);
                    } else if (Storage::disk('drawings')->has($file_path)) {
                        $file_url = asset('storage/drawings/' . $file_path);
                    } else {
                        $file_url = '';
                    }
                @endphp
                <div class="file-wrap">
                    <img id="preview" src="{{ $file_url ? $file_url : '#' }}" class="{{ $file_url ? '' : 'hide' }}">
                    <label id="file_upload" class="image">ファイルを選択してください</label>
                    <span id="ufilename" style="margin-left: 10px;">{{ old('drawing_file_name', isset($drawing->drawing_file_name) ? $drawing->drawing_file_name:'') }}</span>
                    <input type="hidden" name="drawing_file_path" id="drawing_file_path" value="{{ old('drawing_file_path', isset($drawing->drawing_file_path) ? $drawing->drawing_file_path:'') }}"/>
                    <input type="hidden" name="drawing_file_name" id="drawing_file_name" value="{{ old('drawing_file_name', isset($drawing->drawing_file_name) ? $drawing->drawing_file_name:'') }}"/>
                </div>

                <div id="fileprogressbar"></div>
                @error('drawing_file_path')
                    <span class="error_text">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </td>
        </tr>

    </thead></table>
</div>




@section('page_css')
    <link href="{{ asset('assets/vendor/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">
    <style>
        label#file_upload {
            padding: 12px 24px;
            margin: 0 0;
            background: #3682fa;
            color: #fff;
            display: inline-block;
            cursor: pointer;
            border-radius: 7px;
            font-size: 13px;
            font-weight: bold;
        }

        .hide {
            display: none !important;
        }

    </style>
@endsection

@section('page_js')
    <script src="{{ asset('assets/vendor/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/ajaxupload/jquery.ajaxupload.js') }}"></script>

    <script>
        var g_filename = "";
        $(document).ready(function () {
            $('#file_upload').click(function(){
                $.ajaxUploadSettings.name = 'vfile';
            }).ajaxUploadPrompt({
                url : '{{ route("admin.camera.ajaxUploadFile") }}',
                data: {_token:'<?php echo csrf_token() ?>'},
                beforeSend : function () {
                    $("input[type=submit]").prop('disabled', true);
                    fullPath = $("input[name=vfile]").last().val();
                    if (fullPath) {
                        var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
                        var filename = fullPath.substring(startIndex);
                        if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
                            filename = filename.substring(1);
                        }
                        g_filename = filename;
                        $('#drawing_file_name').val(filename);
                    }
                },
                onprogress : function (e) {
                    if (e.lengthComputable) {
                        var percentComplete = e.loaded / e.total;
                        $( "#fileprogressbar").progressbar({
                            value: percentComplete*100,
                            change: function(e, ui) {
                                //var $this = $(this), val = $this.progressbar('option', 'value');
                                //$this.find('#percent').html(parseInt(val)+'%');
                            },
                            complete: function () {
                                $(this).progressbar( "destroy" );
                            }
                        });
                    }
                },
                error : function () {
                    showDialog("図面ファイル", "図面ファイルアップロードに失敗しました。図面ファイルには10M以下の画像ファイルを選択してください。");
                    $('#drawing_file_name').val('');
                    g_filename = '';
                    $("input[type=submit]").prop('disabled', false);
                },
                success : function (file_path) {
                    $("#drawing_file_path").val(file_path);
                    $("#drawing_file_name").val(g_filename);
                    $("#ufilename").text(g_filename);
                    $('#preview').removeClass('hide');
                    $("#preview").attr('src', '/storage/temp/' + file_path);
                    $("input[type=submit]").prop('disabled', false);
                },
                accept: "image/*"
            });
        });
    </script>
@endsection
