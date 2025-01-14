@extends('base')

@section('content')

<div class="page-header">
    <div class="row align-items-end">
        <div class="col-lg-8">
            <div class="page-header-title">
                <div class="d-inline">
                    <h4>Ativos</h4>
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
                        <a href="{{ route('products.index') }}"> Ativos </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('purchasing.show', $purchasing->uuid) }}"> Detalhes </a>
                    </li>
                    <li class="breadcrumb-item"><a href="#!">Novo Item</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
  <div class="card">
      <div class="card-header">
          <h5>Novo Item </h5>
          <span class="text-success">Pedido de Compra #{{ $purchasing->id }}<span>
      </div>
      <div class="card-block">

        <form class="formValidation" data-parsley-validate method="post" action="{{route('purchasing-item.store')}}">
            @csrf

            <input type="hidden" name="purchasing_id" value="{{ request()->get('purchasing_id') }}">

            <input type="hidden" name="indexes" id="indexes" value=""/>

            <div id="rowForm"></div>

            <button class="btn btn-success btn-sm">Salvar</button>
            <a class="btn btn-danger btn-outline btn-sm" href="{{ route('purchasing.show', $purchasing->uuid) }}">Cancelar</a>

            <button type="button" id="btnAddRows" class="btn btn-primary btn-sm f-right">Adicionar Mais Registro</button>

        </form>

      </div>
  </div>
</div>

@endsection

@section('scripts')

  <script>

      var row = $("#rowForm");
      var btnAddRows = $("#btnAddRows");
      var indexes = $("#indexes");

      btnAddRows.click();

      var index = 0;

      function renderCols(index) {

        return '<div class="row m-b-30" id="row-'+index+'">'

             + '<div class="col-md-2">'
                 + '<div class="form-group">'
                     + '<label class="col-form-label">Unidade de Medida</label>'
                     + '<div class="input-group">'
                       + '<select class="form-control" title="Selecione" name="unit-'+index+'" required>'
                          + '<option value="">Selecione...</option>'
                           @foreach(\App\Helpers\Helper::metricUnits() as $item)
                               + '<option value="{{$item}}">{{$item}}</option>'
                           @endforeach
                      + '</select>'
                    + '</div>'
                + '</div>'
            + '</div>'
            + '<div class="col-md-6">'
              +   '<div class="form-group">'
                    + '<label class="col-form-label">Descrição do Item</label>'
                    + '<div class="input-group">'
                      + '<input type="text" required name="description-'+index+'" class="form-control" placeholder="Descrição do Item"/>'
                    + '</div>'
                + '</div>'
            + '</div>'
            + '<div class="col-md-2">'
              +   '<div class="form-group">'
                    + '<label class="col-form-label">Quantidade</label>'
                    + '<div class="input-group">'
                      + '<input type="number" min="1" required value="1" name="quantity-'+index+'" class="form-control" placeholder="Quantidade de itens"/>'
                    + '</div>'
                + '</div>'
            + '</div>'


            + '<div class="col-md-2">'
              +   '<div class="form-group">'
              + '<label class="col-form-label">Opç</label>'
                    + '<button type="button" class="btn btn-danger btn-sm btn-block btnRmItem" data-item="'+index+'"><i class="fa fa-close"></i> Remover </button>'
                + '</div>'
            + '</div>'
        + '</div>';

      }

      btnAddRows.click(function() {
        index++;
        row.append(renderCols(index));
        indexes.val(index);
      });

      var btnRmItem = $(".btnRmItem");

      $(document).on('click','.btnRmItem',function(){
        console.log('item');
        var self = $(this);
        $("#row-" + self.data('item')).remove();
      });

  </script>

@endsection
