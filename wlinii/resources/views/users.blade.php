@extends('layouts.base')
@section('content')
    <div class="page-content">
        <div class="content-title" style="display: flex;margin-bottom: 20px">
            <div style="margin-right: auto"><h2>Lista Usuarios</h2></div>
            <div class="actions">
            
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col">Codigo Inmobiliario</th>
                        <th scope="col">Nombre Completo</th>
                        <th scope="col">Tipo Usuario</th>
                        <th scope="col">Correo</th>
                        <th scope="col">Estado</th>
                        <th scope="col" style="width: 100px;">Acción</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($newsList as $users)
                        <tr>
                            <td>{{$users->usuario}}</td>
                            <td>{{$users->nombrecompleto}}</td>
                            <td>{{$users->flg_tipousuario}}</td>
                            <td>{{$users->des_correo1}}</td>
                   
                          
                            <td>
                                @if ($users->flg_estado == 1) <i class="fa fa-check" style="color: #28a745"></i>
                                @else <i class="fa fa-times" style="color: #dc3545"></i>
                                @endif
                            </td>
                            <td>
                                <a class="btn btn-success btn-sm" href="{{route('usersDetail', ['id' => trim($users->IdUsuario)])}}">
                                    <i class="fa fa-edit"></i>
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