@section('content')
    @if($u->canCommentTicket($ticket->id) || ( !$comments->isEmpty() && $ticket->comments->forLevel(1)->count() ) )
        <div style="margin-top: 2em;">
            <h3 style="margin-top: 0em;">{{ trans('panichd::lang.comments') }}
                @if ($u->canCommentTicket($ticket->id))
                    <button type="button" class="btn btn-light btn-default" data-toggle="modal" data-target="#modal-comment-new" data-add-comment="{{ $ticket->hidden ? 'no' : 'yes' }}">{{ $ticket->hidden ? trans('panichd::lang.show-ticket-add-note') : trans('panichd::lang.show-ticket-add-comment') }}</button>
                @endif
            </h3>
        </div>
    @endif
    @if(!$comments->isEmpty())
        @include('panichd::tickets.partials.comments.list')
    @endif
    {!! $comments->render() !!}

    @include('panichd::tickets.partials.comments.modal_new')
@append

@section('footer')
    @include('panichd::tickets.partials.comments.scripts')
@append
