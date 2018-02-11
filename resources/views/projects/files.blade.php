@extends('layout.template')
@section('title')
    @lang("Projects")
@endsection
@section('container')
    <div class="row">
        @include('projects.project-nav',['project'=>$project])

        <div class="col-md-9">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title"><i class="fa fa-file-pdf-o"></i> @lang("Project files")
                        <button
                                data-toggle="modal"
                                data-target="#newFileModal"
                                class="btn btn-default btn-sm pull-right"><i class="fa fa-plus"></i> @lang("New file")</button>
                    </div>

                </div>
            </div>

            @foreach($files as $file)
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-2">
                                {!! \App\Models\Projects\ProjectFiles::getFileIcon($file->path,'fa-4x') !!}
                            </div>
                            <div class="col-sm-6">
                                <a class="h4 btn-link" href="/projects/file?dl={{$file->path}}">{{$file->filename}}</a>

                                <p class="text-muted">{{$file->desc}}</p>
                            </div>
                            <div class="col-sm-4">
                                <a href="/projects/delete-file?file={{$file->path}}" class="delete text-danger pull-right">
                                    <i class="fa fa-trash-o"></i>
                                </a>
                                @lang("Size"): {{\App\Tools::formatBytes($file->size)}}<br/>
                                @lang("Uploaded"): {{date('d M, Y',strtotime($file->created_at))}}<br/>
                                By: {{\App\User::read($file->user_id,['first_name','last_name'])}}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            {!! $files->links() !!}
        </div>

    </div>
@endsection
@push('modals')

    <div class="modal fade" id="newFileModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">@lang("Upload file")</h4>
                </div>
                {!! Form::open(['url'=>'projects/upload-file','files'=>true]) !!}
                {!! Form::hidden('project_id',$project->id) !!}
                <div class="modal-body">
                    <label>@lang("Name")</label>
                    {!! Form::text('filename',null,['required'=>"required"]) !!}
                    <label>@lang("Notes")</label>
                    {!! Form::textarea('desc',null,['rows'=>3]) !!}
                    <label>@lang("File")</label>
                    {!! Form::file('file') !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang("Close")</button>
                    <button class="btn btn-primary">@lang("Save")</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    @endpush