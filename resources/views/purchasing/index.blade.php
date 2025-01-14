@extends('base')

@section('content')

<div class="page-header">
    <div class="row align-items-end">
        <div class="col-lg-8">
            <div class="page-header-title">
                <div class="d-inline">
                    <h4>Pedidos de Compra</h4>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="page-header-breadcrumb">
                <ul class="breadcrumb-title">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}"> <i class="feather icon-home"></i> </a>
                    </li>
                    <li class="breadcrumb-item"><a href="#!">Pedidos de Compra</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="page-body">

  <div class="row">

    <div class="col-xl-12 col-lg-12 filter-bar">

      <div class="card">
          <div class="card-block">
              <div class=" waves-effect waves-light m-r-10 v-middle issue-btn-group">

                  @permission('create.pedidos.de.compra')
                    <a class="btn btn-sm btn-success waves-effect waves-light m-r-15 m-b-5 m-t-5" href="{{route('purchasing.create')}}"><i class="icofont icofont-paper-plane"></i> Nova Solicitação</a>
                  @endpermission

              </div>
          </div>
      </div>
    </div>

  </div>

  <div class="row">

    <div class="col-lg-3">

        <div class="card">
            <div class="card-header">
                <h5><i class="icofont icofont-filter m-r-5"></i>Filtro</h5>
            </div>
            <div class="card-block">
                <form method="get" action="?">
                    <input type="hidden" name="find" value="1"/>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="code" placeholder="Código da Solicitação">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <input type="text" id="daterange" class="form-control" placeholder="Periodo">

                            <input type="hidden" name="start" id="start" value="{{ now()->format('d/m/Y') }}"/>
                            <input type="hidden" name="end" id="end" value="{{ now()->format('d/m/Y') }}"/>

                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <select class="form-control select2" name="status">
                              <option value="">Situação</option>
                              @foreach(\App\Helpers\Helper::purchasingStatus() as $item)
                                <option value="{{$item}}">{{$item}}</option>
                              @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <select class="form-control select2" name="status">
                              <option value="">Situação</option>
                              @foreach(\App\Helpers\Helper::users() as $user)
                                <option value="{{$user->id}}">{{$user->person->name}}</option>
                              @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-success btn-sm btn-block">
                            <i class="icofont icofont-job-search m-r-5"></i> Pesquisar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-9">
        <!-- Recent Orders card start -->
        <div class="card">
            <div class="card-header">
                <h5>Listagem de Pedidos De Compra</h5>
                <span>Registros retornados: {{ $quantity }}</span>
            </div>
            <div class="card-block table-border-style">
                <div class="table-responsive">
                    <table class="table table-lg table-styling">
                        <thead>
                            <tr class="table-primary">
                              <th>#</th>
                              <th>Solicitante</th>
                              <th style="width: 35%;">Motivo</th>
                              <th>Situação</th>
                              <th>Cadastro</th>
                              <th>Opções</th>
                            </tr>
                        </thead>
                        <tbody>
                          @forelse ($purchasings as $purchasing)

                            @php

                              $status = $purchasing->status;

                              $bgColor = 'success';

                              switch($status) {
                                case '2':
                                  $bgColor = 'warning';
                                  break;
                                case '3':
                                  $bgColor = 'primary';
                                  break;
                                case '4':
                                  $bgColor = 'primary';
                                  break;
                                case '5':
                                  $bgColor = 'danger';
                                  break;
                              }

                            @endphp

                              <tr>
                                  <th scope="row"><a href="{{route('purchasing.show', ['id' => $purchasing->uuid])}}">#{{ str_pad($purchasing->id, 6, "0", STR_PAD_LEFT) }}</a></th>
                                  <td>{{ $purchasing->user->person->name }}</td>
                                  <td style="white-space: normal;">{{ $purchasing->motive }}</th>
                                  <td><span class="label label-{{$bgColor}}"> {{$purchasing->status}} </span></td>
                                  <td>{{ $purchasing->created_at->format('d/m/Y H:i') }}
                                      <label class="label label-inverse-{{ $bgColor }}">{{ $purchasing->created_at->diffForHumans() }}</label>
                                  </td>

                                  <td class="dropdown">

                                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-cog" aria-hidden="true"></i></button>
                                    <div class="dropdown-menu dropdown-menu-right b-none contact-menu">

                                      @permission('view.ativos')
                                        <a href="{{route('purchasing.show', ['id' => $purchasing->uuid])}}" class="dropdown-item">Visualizar </a>
                                      @endpermission

                                      @permission('edit.ativos')
                                        <a href="{{route('purchasing.edit', ['id' => $purchasing->uuid])}}" class="dropdown-item">Editar </a>
                                      @endpermission

                                      @permission('edit.ativos')
                                        <a data-route="{{ route('purchasing.destroy', ['id' => $purchasing->uuid]) }}" style="cursor:pointer" class="dropdown-item text-danger btnRemoveItem">Remover </a>
                                      @endpermission

                                    </div>
                                  </td>

                              </tr>
                            @endforeach

                          </tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{ $purchasings->links() }}

  </div>

</div>

@endsection
