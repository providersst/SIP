@extends('base')

@section('content')

<div class="page-header">
    <div class="row align-items-end">
        <div class="col-lg-8">
            <div class="page-header-title">
                <div class="d-inline">
                    <h4>Permissões do Modulo</h4>
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
                        <a href="{{ route('modules.index') }}"> Módulos </a>
                    </li>
                    <li class="breadcrumb-item"><a href="#!">Permissões</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="page-body">

  <div class="card">
      <div class="card-header">
          <h5>Permissões do Módulo {{ $module->name }}</h5>
          <span>Lista de usuários que tem a permissão acima. </span>
      </div>
      <div class="card-block table-border-style">
          <div class="table-responsive">
              <table class="table table-lg table-styling">
                  <thead>
                      <tr class="table-primary">
                          <th>Conceder?</th>
                          <th>Nome</th>
                          <th>E-mail</th>
                      </tr>
                  </thead>
                  <tbody>
                    @foreach(\App\Helpers\Helper::users() as $user)

                      @php
                          $hasPermission = $user->hasPermission($permission->slug);
                      @endphp

                      <tr>

                          <td class="project-actions">
                              <input {{ auth()->user()->id == $user->id ? 'disabled' : '' }} type="checkbox" class="js-switch checkboxPermissions" {{ $hasPermission ? 'checked' : '' }}
                                data-route-grant="{{route('user_permissions_grant', [$user->uuid, $permission->id])}}"
                                data-route-revoke="{{route('user_permissions_revoke', [$user->uuid, $permission->id])}}" value="1"/>
                          </td>

                          <td>{{ $user->person->name }}</td>
                          <td>{{ $user->email }}</td>

                      </tr>
                    @endforeach
                  </tbody>
              </table>
          </div>
      </div>
  </div>

</div>

@endsection
