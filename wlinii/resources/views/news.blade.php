@extends('layouts.base')
@section('content')
    <div class="page-content">
        <div class="content-title" style="display: flex;margin-bottom: 20px">
            <div style="margin-right: auto"><h2>Lista Noticias</h2></div>
            <div class="actions">
                <a class="btn btn-info btn-sm" href="{{route('newsDetail') }}">Cerrar News</a>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Imagen</th>
                        <th scope="col">Fecha</th>
                        <th scope="col">Titulo</th>
                        <th scope="col">Sub/Titulo</th>
                
                        <th scope="col">Estado</th>
                        <th scope="col" style="width: 100px;">Acción</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($newsList as $news)
                        <tr>
                            <td>{{$news->IdNoticias}}</td>
                            <td>
                                @if($news && $news->Image)
                                    <img id="preview-coverimg" src="{{env('SERVICE_URL').$news->Image}}" alt="" style="width: 50px">
                                @else
                                    <img id="preview-coverimg" src="img/dummy.png" alt="" style="width: 50px">
                                @endif
                            </td>
                            <td>{{$news->FechaCurso}}</td>
                            <td>{{$news->Titulo}}</td>
                            <td>{{$news->SubTitulo}}</td>
                          
                            <td>
                                @if ($news->Estado == 1) <i class="fa fa-check" style="color: #28a745"></i>
                                @else <i class="fa fa-times" style="color: #dc3545"></i>
                                @endif
                            </td>
                            <td>
                                <a class="btn btn-success btn-sm" href="{{route('newsDetail', ['id' => $news->IdNoticias])}}">
                                    <i class="fa fa-edit"></i>
                                </a> 
                                <a class="btn btn-danger btn-sm" href="{{route('deleteNews', ['id'=> $news->IdNoticias]) }}">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop