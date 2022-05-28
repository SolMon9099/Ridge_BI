
@if ($message = Session::get('success'))
<div class="alert alert-success alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{!! $message !!}</strong>
</div>
@endif

@if ($message = Session::get('error'))
<div class="alert alert-danger alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{!! $message !!}</strong>
</div>
@endif

@if ($message = Session::get('warning'))
<div class="alert alert-warning alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{!! $message !!}</strong>
</div>
@endif

@if ($message = Session::get('info'))
<div class="alert alert-info alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{!! $message !!}</strong>
</div>
@endif


@if ($errors->any())
<div class="alert alert-danger">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <ul>
        @foreach ($errors->all() as $error)
            <li class="error-text">{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<style>
div.alert {
    margin-top: 10px;
}

div.alert button {
    float: right;
}
li.error-text {
    margin-left: 10px !important;
    list-style: disc !important;
}


.alert {
    position: relative;
    padding: 12px 20px;
    margin: 16px 5px;
    border: 1px solid transparent;
    border-radius: 5px;
    text-align: left;
}

.alert-heading {
    color: inherit
}

.alert-link {
    font-weight: 700
}

.alert-dismissible {
    padding-right: 61.6px;
}

.alert-dismissible .close {
    position: absolute;
    top: 0;
    right: 0;
    padding: 12px 20px;
    color: inherit
}

.alert-primary {
    color: #1b4b72;
    background-color: #d6e9f8;
    border-color: #c6e0f5
}

.alert-primary hr {
    border-top-color: #b0d4f1
}

.alert-primary .alert-link {
    color: #113049
}

.alert-secondary {
    color: #383d41;
    background-color: #e2e3e5;
    border-color: #d6d8db
}

.alert-secondary hr {
    border-top-color: #c8cbcf
}

.alert-secondary .alert-link {
    color: #202326
}

.alert-success {
    color: #1d643b;
    background-color: #d7f3e3;
    border-color: #c7eed8
}

.alert-success hr {
    border-top-color: #b3e8ca
}

.alert-success .alert-link {
    color: #123c24
}

.alert-info {
    color: #385d7a;
    background-color: #e2f0fb;
    border-color: #d6e9f9
}

.alert-info hr {
    border-top-color: #c0ddf6
}

.alert-info .alert-link {
    color: #284257
}

.alert-warning {
    color: #857b26;
    background-color: #fffbdb;
    border-color: #fffacc
}

.alert-warning hr {
    border-top-color: #fff8b3
}

.alert-warning .alert-link {
    color: #5d561b
}

.alert-danger {
    color: #761b18;
    background-color: #f9d6d5;
    border-color: #f7c6c5
}

.alert-danger hr {
    border-top-color: #f4b0af
}

.alert-danger .alert-link {
    color: #4c110f
}

.alert-light {
    color: #818182;
    background-color: #fefefe;
    border-color: #fdfdfe
}

.alert-light hr {
    border-top-color: #ececf6
}

.alert-light .alert-link {
    color: #686868
}

.alert-dark {
    color: #1b1e21;
    background-color: #d6d8d9;
    border-color: #c6c8ca
}

.alert-dark hr {
    border-top-color: #b9bbbe
}

.alert-dark .alert-link {
    color: #040505
}

.close {
    padding: 16px;
    font-weight: bold;
    margin: -16px -16px -16px auto
}

button.close {
    background-color: transparent;
    border: 0;
    -webkit-appearance: none;
    float: right;
}

.close:not(:disabled):not(.disabled) {
    cursor: pointer
}

.close:not(:disabled):not(.disabled):focus,
.close:not(:disabled):not(.disabled):hover {
    color: #000;
    text-decoration: none;
    opacity: .75
}

.alert-dismissible {
    padding-right: 61.6px;
}

.alert-dismissible .close {
    position: absolute;
    top: 0;
    right: 0;
    padding: 12px 20px;
    color: inherit
}
</style>

<script>
$(document).ready(function() {
    $('.close').click(function(e){
        $(this).parent().remove();
    });
});
</script>
