@extends('layout.template')
@section('panel-title')
    <div class="tabbable-panel">
        <div class="tabbable-line">
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">
                @lang("Payments")
            </a>
        </li>
    </ul>
            </div></div>
@endsection
@section('content')
<!-- Tab panes -->

<div class="tab-content">
    <div role="tabpanel" class="tab-pane fade in active" id="home">
        <iframe width="100%" height="100%" frameborder="0" style="width:100%;height: 600px;;border:none" src="https://docs.google.com/spreadsheets/d/1epz1OB-fZjK351hTFOQ97I4MBnxoRtDAYulI0tgXOcU/edit?usp=sharing"></iframe>
    </div>
</div>
@endsection