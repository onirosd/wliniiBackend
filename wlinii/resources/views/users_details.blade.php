@extends('layouts.base')
@section('content')
    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <form id="news-form" action="{{route('saveUsers2')}}"  method="post">
                 @csrf
                    <div class="row">
                       
                        <div class="col-md-4">
                            <input name="id" type="text" value="{{$users ? $users->IdUsuario : ''}}" hidden>
                         

                            <div class="form-group">
                                <label for="title">Usuario : {{$users->NUsuario}}</label>
                            </div>
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <label>State: </label>
                                </div>
                                @if ($users)  
                                    @if($users->Flg_Estado)
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
 
@stop