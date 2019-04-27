<div class="panel-body slimScrollLogs timeline-details">
    <ul class="timeline">
        @foreach($logs as $log)
            <li class="timeline-inverted">
                <div class="tl-circ">
                    <img class="avatar-small"
                         src="/img/no-photo.png"
                         alt=""></div>
                <div class="timeline-panel">
                    <div class="tl-heading">
                        <div><strong>
                                {{App\User::read($log->user_id,'name')}} {!! App\Models\Log::colorCat($log->action) !!}
                            </strong></div>
                        <div>
                            {{$log->event}}
                        </div>
                        <div>
                            <small class="text-muted"><i class="icon-time"></i>
                                {{date('m-d-Y',strtotime($log->created_at))}}
                            </small>
                        </div>
                    </div>
                    <div class="tl-body">
                        <div>
                            <a href="#"><i class="icon-external-link"></i> </a>

                        </div>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
</div>
<div class="panel-footer">
    {{$logs->links()}}
</div>