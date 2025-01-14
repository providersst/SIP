@extends('base')

@section('content')

<div class="page-header">
    <div class="row align-items-end">
        <div class="col-lg-8">
            <div class="page-header-title">
                <div class="d-inline">
                    <h4>Chamados</h4>
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
                        <a href="{{ route('tickets.index') }}"> Chamados </a>
                    </li>
                    <li class="breadcrumb-item"><a href="#!">Informações</a>
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

                  @permission('create.chamados')
                    <a class="btn btn-sm btn-success btn-new-tickets waves-effect waves-light m-r-15 m-b-5 m-t-5" href="{{route('tickets.create')}}"><i class="icofont icofont-paper-plane"></i> Novo Chamado</a>
                  @endpermission

                  @if($ticket->status_id != 4 && $ticket->status_id != 5)

                  <div class="btn-group m-b-5 m-t-5">

                      @if($ticket->status_id == 1)
                        <a href="#" onclick="startTicket()" class="btn btn-primary btn-sm waves-effect waves-light"><i class="icofont icofont-play"></i> Iniciar</a>
                      @elseif($ticket->status_id == 2)
                        <a href="#" onclick="concludeTicket()" class="btn btn-primary btn-sm waves-effect waves-light"><i class="icofont icofont-play"></i> Concluir</a>
                      @elseif($ticket->status_id == 3 && $ticket->user_id == auth()->user()->id)
                        <a href="#" onclick="finishTicket()" class="btn btn-primary btn-sm waves-effect waves-light"><i class="icofont icofont-play"></i> Finalizar</a>
                      @endif

                      @if($ticket->status_id != 4)
                        <a href="{{ route('tickets.edit', $ticket->uuid) }}" class="btn btn-info btn-sm waves-effect waves-light"><i class="icofont icofont-edit-alt"></i> Editar</a>
                      @endif

                      @if($ticket->status_id == 1 || $ticket->status_id == 2 || $ticket->status_id == 3)
                        <a class="btn btn-danger btn-sm waves-effect waves-light btnCancelTicket" data-route="{{ route('ticket_cancel', $ticket->uuid) }}" href="#"><i class="icofont icofont-ban"></i> Cancelar</a>
                      @endif

                 </div>

                 @endif

              </div>
          </div>
      </div>
    </div>

  </div>

  <div class="row">

    @if($ticket->status_id == 3)
    <div class="col-sm-12">
        <!-- List type card start -->
        <div class="card bg-c-green update-card">
            <div class="card-header">
                <h4>Chamado Concluído!</h4>
            </div>

              <div class="card-block">

                @if($ticket->user_id == auth()->user()->id)
                <p class="lead">Seu chamado foi concluído, agora informe se o chamado resolveu a sua necessidade.</p>
                <form style="display:inline" id="ticket-finish" style="display:inline" action="{{ route('ticket_finish', $ticket->uuid) }}" method="POST">
                    @csrf
                    <button class="btn btn-primary btn-sm btn-round"><i class="fa fa-tag"></i>  Finalizar Chamado</button>
                </form>
                @else
                <p class="lead">Agora é preciso que o solicitante finalize o chamado.</p>
                @endif

              </div>
        </div>
    </div>
    @elseif($ticket->status_id == 4)
      <div class="col-sm-12">
          <div class="card bg-c-green update-card">
              <div class="card-header">
                  <h4>Chamado Finalizado!</h4>
              </div>
          </div>
      </div>
    @elseif($ticket->status_id == 5)
      <div class="col-sm-12">
          <div class="card bg-c-pink update-card">
              <div class="card-header">
                  <h4>Chamado Cancelado!</h4>
              </div>
          </div>
      </div>
    @endif

    <div class="col-lg-9">

      <div class="card">
          <div class="card-header">
              <h5>Solicitação</h5>
          </div>

          <form style="display:none" id="ticket-start" class="dropdown-item waves-light waves-effect" action="{{ route('ticket_start', $ticket->uuid) }}" method="POST">
              @csrf
              <input type="hidden" name="priority" id="priority"/>
              <button class="btn btn-success btn-sm btn-round"><i class="fa fa-tag"></i>  Executar Chamado</button>
          </form>
          <form style="display:none" id="ticket-finish" class="dropdown-item waves-light waves-effect" style="display:inline" action="{{ route('ticket_finish', $ticket->uuid) }}" method="POST">
              @csrf
              <button class="btn btn-success btn-sm btn-round"><i class="fa fa-tag"></i>  Finalizar Chamado</button>
          </form>
          <form style="display:none" id="ticket-conclude" class="dropdown-item waves-light waves-effect" style="display:inline" action="{{ route('ticket_conclude', $ticket->uuid) }}" method="POST">
              @csrf
              <button class="btn btn-success btn-sm btn-round"><i class="fa fa-tag"></i>  Concluir Chamado</button>
          </form>
          <form style="display:none" id="ticket-cancel" class="dropdown-item waves-light waves-effect" style="display:inline" action="{{ route('ticket_cancel', $ticket->uuid) }}" method="POST">
              @csrf
              <button class="btn btn-success btn-sm btn-round"><i class="fa fa-tag"></i>  Cancelar Chamado</button>
          </form>

          <div class="card-block">
              <div class="row">

                  <div class="col-sm-6 col-xl-8">
                    <h4 class="sub-title">Titulo</h4>
                    <p class="text-muted m-b-30">
                        <b>{{$ticket->type->category->name}}: </b>{{$ticket->type->name}}
                    </p>
                  </div>

                  <div class="col-sm-12 col-xl-4">
                    <h4 class="sub-title">Solicitante</h4>
                    <p class="lead m-b-30">
                        {{ $ticket->user->person->name }}
                    </p>
                  </div>

                  <div class="col-sm-12 col-xl-4">
                    <h4 class="sub-title">Código</h4>
                    <p class="text-muted m-b-30">
                        #{{ str_pad($ticket->id, 6, "0", STR_PAD_LEFT)  }}
                    </p>
                  </div>
                  <div class="col-sm-12 col-xl-4">
                    <h4 class="sub-title">Cadastro</h4>
                    <p class="text-muted m-b-30">
                        {{ $ticket->created_at->format('d/m/Y H:i:s') }}
                    </p>
                  </div>


                  <div class="col-sm-6 col-xl-4">
                    <h4 class="sub-title">Prioridade</h4>
                    <p class="text-muted m-b-30">
                        <label class="label label-{{ \App\Helpers\Helper::statusTaskPriorityCollor($ticket->priority) }}">{{ $ticket->priority }}</label>
                    </p>
                  </div>

                  <div class="col-sm-12 col-xl-4">
                    <h4 class="sub-title">Setor</h4>
                    <p class="text-muted m-b-30">
                        {{ $ticket->user->person->department->name }} >>
                        <br/>
                        {{ $ticket->user->person->occupation->name }}
                    </p>
                  </div>
                  <div class="col-sm-12 col-xl-4">
                    <h4 class="sub-title">Telefone</h4>
                    <p class="text-muted m-b-30">
                        {{ $ticket->user->person->phone }}
                    </p>
                  </div>
                  <div class="col-sm-12 col-xl-4">
                    <h4 class="sub-title">Ramal</h4>
                    <p class="text-muted m-b-30">
                        {{ $ticket->user->person->branch }}
                    </p>
                  </div>
                  <div class="col-sm-12 col-xl-4">
                    <h4 class="sub-title">Email</h4>
                    <p class="text-muted m-b-30">
                        {{ $ticket->user->email }}
                    </p>
                  </div>

                  @php

                    $status = $ticket->status_id;

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

                  <div class="col-sm-12 col-xl-4">
                    <h4 class="sub-title">Status</h4>
                    <p class="text-muted m-b-30">
                      <label class="label label-lg label-{{ $bgColor }}">{{ $ticket->status->name }}</label>
                    </p>
                  </div>

                  <div class="col-sm-12 col-xl-4">
                    <h4 class="sub-title">Responsável</h4>
                    <p class="text-muted m-b-30">
                        {{ $ticket->responsible->person->name ?? '-' }}
                    </p>
                  </div>

                  <div class="col-sm-12 col-xl-12">
                      <h4 class="sub-title">Descrição</h4>
                      <p class="m-b-30">
                          {!! $ticket->description !!}
                      </p>
                  </div>
              </div>
          </div>

      </div>

      <div class="card comment-block">
          <div class="card-header">
              <h5 class="card-header-text"><i class="icofont icofont-comment m-r-5"></i>Comentários</h5>
          </div>
          <div class="card-block">
              <ul class="media-list">

                  <div id="div-coments-list"></div>

                @foreach($ticket->messages->sortByDesc('id') as $message)

                  <li class="media mediaFile">
                      <div class="media-left">
                          <img class="media-object img-radius comment-img" src="{{ route('image', ['user' => $message->user->uuid, 'link' => $message->user->avatar, 'avatar' => true])}}" title="{{$message->user->name}}" alt="{{$message->user->name}}">
                      </div>
                      <div class="media-body">
                          <h6 class="media-heading txt-primary"><span class="f-12 text-muted m-l-5">{{ $message->user->person->name }}, {{$message->created_at->format('d/m/Y H:i:s')}}</span>

                            @if(auth()->user()->isAdmin() || $message->user->id == auth()->user()->id)
                                <a href="#" data-route="{{ route('ticket_message_destroy', $message->uuid) }}" class="btn btn-danger btn-sm btn-round f-right btnRemoveItem" style="cursor:pointer"><i class="fa fa-trash"></i> Apagar</a>
                            @endif

                          </h6>
                          <p>{{$message->message}}</p>

                          <hr/>
                      </div>
                  </li>

                @endforeach

              </ul>
              <div class="md-float-material d-flex">
                  <div class="col-md-12 btn-add-task">
                    <form id="formTicketComment" class="formValidation" data-parsley-validate method="post" action="{{route('ticket_message_store')}}">
                      {{csrf_field()}}
                      <input name="id" type="hidden" value="{{$ticket->uuid}}"/>
                      <textarea rows="5" name="message" id="message" class="form-control" required placeholder="Insira um Comentário"></textarea>
                      <br/>
                      <button class="btn btn-success">Enviar</button>
                    </form>

                  </div>
              </div>
          </div>
      </div>

    </div>

    <div class="col-lg-3 col-sm-12">

      <div class="card user-activity-card">
          <div class="card-header">
            <h5>Registro de Atividades</h5>
          </div>
          <div class="card-block">

            @if($ticket->logs->isNotEmpty())

              @foreach($ticket->logs->sortByDesc('id') as $activity)

                <div class="row m-b-25">
                    <div class="col">
                        <h6 class="m-b-5">{{ $activity->created_at->format('d/m/Y H:i') }}</h6>
                        <p class="text-muted m-b-0">{{ $activity->description }} {{ html_entity_decode(\App\Helpers\Helper::getTagHmtlForModel($activity->subject_type, $activity->subject_id)) }}</p>
                        <p class="text-muted m-b-0"><i class="feather icon-clock m-r-10"></i>{{ $activity->created_at->diffForHumans() }}</p>
                    </div>
                </div>

              @endforeach

            @else

              <div class="widget white-bg no-padding">
                  <div class="p-m text-center">
                      <h1 class="m-md"><i class="fas fa-history fa-2x"></i></h1>
                      <br/>
                      <h6 class="font-bold no-margins">
                          Nenhum log registrado até o momento.
                      </h6>
                  </div>
              </div>

            @endif

          </div>
      </div>

    </div>

  </div>

</div>

<input type="hidden" id="input-post-ticket-comment" value="{{ route('ticket_comment_post', $ticket->uuid) }}">

@endsection

@section('scripts')

<script>

    var formTicket = $("#formTicketComment");
    var comentsList = $("#div-coments-list");
    var inputPostComment = $("#input-post-ticket-comment").val();

    var $html = "";

    formTicket.submit(function(e) {

      var message = $("#message").val();

      e.preventDefault();
      swal.close();

      $html += '<li class="media mediaFile">' +
                  '<div class="media-left">' +
                      '<img class="media-object img-radius comment-img" src="{{ route('image', ['user' => auth()->user()->uuid, 'link' => auth()->user()->avatar, 'avatar' => true])}}" alt="">' +
                  '</div>' +
                  '<div class="media-body">' +
                      '<h6 class="media-heading txt-primary"><span class="f-12 text-muted m-l-5">{{ auth()->user()->person->name }}, {{ now()->format("d/m/Y H:i:s") }}</span> </h6>' +
                      '<p>' + message + '</p>' +
                  '</div>' +
              '</li>';

      comentsList.append($html);

      $.ajax({
        headers: {
         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: inputPostComment,
        data: {
            message: message,
        }

      });

      $("#message").val("");

      return false;

    });

    function startTicket() {

       Swal.fire({
          title: 'Selecione a Prioridade para iniciar o Chamado.',
          input: 'select',
          inputOptions: {
            'Normal': 'Normal',
            'Baixa': 'Baixa',
            'Alta': 'Alta',
            'Altissima': 'Altissíma'
          },
          showCancelButton: true,
          inputValidator: (value) => {
            return new Promise((resolve) => {
              if (value) {

                $("#priority").val(value);

                swal({
                  title: 'Aguarde um instante.',
                  text: 'Carregando os dados...',
                  type: 'info',
                  showConfirmButton: false,
                  allowOutsideClick: false
                });

                $("#ticket-start").submit();

                resolve();
              } else {
                resolve('You need to select oranges :)')
              }
            })
          }
        });

    }

    function concludeTicket() {

        swal({
          title: 'Concluir Chamado?',
          text: "Este chamado será concluído!",
          type: 'question',
          showCancelButton: true,
          confirmButtonColor: '#0ac282',
          cancelButtonColor: '#D46A6A',
          confirmButtonText: 'Sim',
          cancelButtonText: 'Cancelar'
          }).then((result) => {
          if (result.value) {

            swal({
              title: 'Aguarde um instante.',
              text: 'Carregando os dados...',
              type: 'info',
              showConfirmButton: false,
              allowOutsideClick: false
            });

            $("#ticket-conclude").submit();

          }
        });

    }

    function finishTicket() {

        swal({
          title: 'Finalizar Chamado?',
          text: "Este chamado será finalizado!",
          type: 'question',
          showCancelButton: true,
          confirmButtonColor: '#0ac282',
          cancelButtonColor: '#D46A6A',
          confirmButtonText: 'Sim',
          cancelButtonText: 'Cancelar'
          }).then((result) => {
          if (result.value) {

            swal({
              title: 'Aguarde um instante.',
              text: 'Carregando os dados...',
              type: 'info',
              showConfirmButton: false,
              allowOutsideClick: false
            });

            $("#ticket-conclude").submit();

          }
        });

    }

    var cancelTicket = $(".btnCancelTicket");

    cancelTicket.click(function() {

      var self = $(this);
      var url = self.data('route');

      swal({
        title: 'Cancelar Chamado?',
        text: "Então, informe o motivo do cancelamento.",
        type: 'question',
        customClass: 'bounceInLeft',
        input: 'textarea',
        confirmButtonText: 'Salvar',
        showLoaderOnConfirm: true,
        showCancelButton: true,
        confirmButtonColor: '#0ac282',
        cancelButtonColor: '#D46A6A',
        cancelButtonText: 'Cancelar',
        preConfirm: (text) => {
          return new Promise((resolve) => {
            setTimeout(() => {
              if (text === '') {
                swal.showValidationError(
                  'Por Favor Informe o motivo do cancelamento.'
                )
              }
              resolve()
            }, 1000)
          })
        },
        allowOutsideClick: () => false
        }).then((result) => {
        if (result.value) {

          swal({
            title: 'Aguarde um instante.',
            text: 'Carregando os dados...',
            type: 'info',
            showConfirmButton: false,
            allowOutsideClick: false
          });

          swal({
            type: 'success',
            title: 'O motivo de cancelamento foi enviado!',
            html: 'Motivo: ' + result.value,
            preConfirm: () => {
               return $.ajax({
                 headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                 url: url,
                 type: 'POST',
                 dataType: 'json',
                 data: {
                   message : result.value,
                 }
               }).done(function(data) {

                 window.location.reload();

               });

             }
          })


        }

      });
    });

</script>

@endsection
