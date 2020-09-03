@extends('layouts.base')
@section('content')
    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <form id="news-form" action="{{route('saveNews')}}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div style="display: flex;flex-direction: column;justify-content: center;align-items: center;">
                                @if($news && $news->Image)
                                    <img id="preview-coverimg" src="{{env('SERVICE_URL').$news->Image}}" alt="" style="width: 100%; max-width: 300px">
                                @else
                                    <img id="preview-coverimg" src="img/dummy.png" alt="" style="width: 100%; max-width: 300px">
                                @endif
                                <button type="button" id="open-file-button" class="btn btn-info" style="margin-top: 20px">Cover Image</button>
                                <input type="file" id="cover-image" hidden>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <input name="id" type="text" value="{{$news ? $news->IdNoticias : ''}}" hidden>
                            <input id="img-url" name="img-url" type="text" value="{{$news && $news->Image ? $news->Image : '' }}" hidden>

                            <div class="form-group">
                                <label for="title">Title</label>
                                @if ($news) <input type="text" class="form-control" id="title" name="title" placeholder="Enter Title" value="{{ $news->Titulo }}" required>
                                @else <input type="text" class="form-control" id="title" name="title" placeholder="Enter Title" required>
                                @endif
                                <div class="invalid-feedback">this field is required</div>
                            </div>
                            <div class="form-group">
                                <label for="subtitle">SubTitle</label>
                                @if ($news) <input type="text" class="form-control" id="subtitle" name="subtitle" placeholder="Enter Subtitle" value="{{ $news->SubTitulo }}" required>
                                @else <input type="text" class="form-control" id="subtitle" name="subtitle" placeholder="Enter Subtitle" required>
                                @endif
                                <div class="invalid-feedback">this field is required</div>
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                @if ($news) <textarea type="text" class="form-control" id="description" name="description" placeholder="Enter Description" required rows="5">{{$news->Descripcion}}</textarea>
                                @else <textarea type="text" class="form-control" id="description" name="description" placeholder="Enter Description" required rows="5"></textarea>
                                @endif
                                <div class="invalid-feedback">this field is required</div>
                            </div>

                            <div class="form-group row">
  <label for="example-datetime-local-input" class="col-2 col-form-label">Fecha Evento</label>
  <div class="col-10">
    <input class="form-control" type="date" name = "fechacurso" value="{{ $news->FechaCurso }}" id="example-datetime-local-input">
  </div>
</div>
                            
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <label>State: </label>
                                </div>
                                @if ($news)  
                                    @if($news->Estado)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="state" id="state1" value="1" checked>
                                        <label class="form-check-label" for="state1">active</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="state" id="state2" value="0">
                                        <label class="form-check-label" for="state2">disable</label>
                                    </div>
                                    @else
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="state" id="state1" value="1">
                                        <label class="form-check-label" for="state1">active</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="state" id="state2" value="0" checked>
                                        <label class="form-check-label" for="state2">disable</label>
                                    </div>
                                    @endif
                                @else
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="state" id="state1" value="1" checked>
                                        <label class="form-check-label" for="state1">active</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="state" id="state2" value="0">
                                        <label class="form-check-label" for="state2">disable</label>
                                    </div>
                                @endif
                            </div>

                            <button id="submit-btn" type="submit" class="btn btn-primary" style="width: 100%">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        let service_url = "<?php echo env('SERVICE_URL') ?>";

        $(document).ready(function(){
            $('#open-file-button').on('click', function(){
                $('#cover-image').click();
            });

            $('#cover-image').on('change', function(e){
                const { files } = e.target;

                let file = files[0];
                if(!file) return;

                let url = URL.createObjectURL(file);
                $('#preview-coverimg').attr('src', url);

                let formData = new FormData();
                formData.append('file', file);
                $.ajax({
                    type: "POST",
                    enctype: 'multipart/form-data',
                    url: service_url + '/api/upload',
                    data: formData,
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function(data){
                        if(data.status == 'success' && data.url){
                            $('#img-url').val(data.url);
                        }
                    },
                    error: function(e){
                        console.log('image uploading failed');
                    }
                });
            });
        });
        
    </script>
@stop