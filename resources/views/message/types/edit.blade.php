@extends('base')

@section('content')

<div class="page-header">
    <div class="row align-items-end">
        <div class="col-lg-8">
            <div class="page-header-title">
                <div class="d-inline">
                    <h4>Editar Tipo de Recado</h4>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="page-header-breadcrumb">
                <ul class="breadcrumb-title">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}"> <i class="feather icon-home"></i> </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('message-types.index') }}"> Tipo de Recado </a>
                    </li>
                    <li class="breadcrumb-item"><a href="#!">Editar</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
  <div class="card">
      <div class="card-header">
          <h5>Editar Tipo de Recado</h5>
      </div>
      <div class="card-block">

        <form class="formValidation" data-parsley-validate method="post" action="{{route('message-types.update', $type->uuid)}}">
            {{csrf_field()}}
            {{method_field('PUT')}}
            <div class="row m-b-30">

                <div class="col-md-4">

                  <div class="form-group"><label class="col-form-label">Nome</label>
                      <div class="input-group"><input type="text" value="{{ $type->name }}" name="name" class="form-control" autofocus required/></div>
                  </div>

                </div>

            </div>

            <button class="btn btn-success btn-sm">Salvar</button>
            <a class="btn btn-outline-danger btn-sm" href="{{ route('message-types.index') }}">Cancelar</a>

        </form>

      </div>
  </div>
</div>

@endsection
