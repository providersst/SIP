@extends('base')

@section('content')

<div class="page-header">
    <div class="row align-items-end">
        <div class="col-lg-8">
            <div class="page-header-title">
                <div class="d-inline">
                    <h4>Fornecedores</h4>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="page-header-breadcrumb">
                <ul class="breadcrumb-title">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}"> <i class="feather icon-home"></i> </a>
                    </li>
                    <li class="breadcrumb-item"><a href="#!">Fornecedores</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="page-body">

  <div class="card">
      <div class="card-header">
          <h5>Listagem de Fornecedores</h5>
          <span class="text-success">Fornecedores<span>

          <div class="card-header-right">
              <ul class="list-unstyled card-option">

                  @permission('create.fornecedores')
                    <li><a class="btn btn-sm btn-success btn-round" href="{{route('vendors.create')}}">Novo Fornecedor</a></li>
                  @endpermission

              </ul>
          </div>

      </div>
      <div class="card-block">
          <div class="table-responsive">
              <table class="table table-striped table-bordered">
                  <thead>
                      <tr>
                          <th>#</th>
                          <th>Nome</th>
                          <th>Telefone</th>
                          <th>E-mail</th>
                          <th>Ativo</th>
                          <th>Opções</th>
                      </tr>
                  </thead>
                  <tbody>

                    @foreach($vendors as $vendor)
                      <tr>
                          <th scope="row">{{ $vendor->id }}</th>
                          <td>{{ $vendor->name }}</td>
                          <td>{{ $vendor->phone }}</td>
                          <td>{{ $vendor->email }}</td>

                          <td>{{ $vendor->active ? 'Ativo' : 'Inativo' }}</td>

                          <td class="dropdown">

                            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-cog" aria-hidden="true"></i></button>
                            <div class="dropdown-menu dropdown-menu-right b-none contact-menu">

                              @permission('edit.fornecedores')
                                <a href="{{route('vendors.edit', ['id' => $vendor->uuid])}}" class="dropdown-item">Editar </a>
                              @endpermission

                            </div>
                          </td>

                      </tr>
                    @endforeach

                  </tbody>
              </table>
          </div>
      </div>
  </div>

</div>

@endsection
