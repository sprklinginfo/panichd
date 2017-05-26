@extends($master)
@section('page', trans('ticketit::lang.show-ticket-title') . trans('ticketit::lang.colon') . $ticket->subject)
@section('content')
        @include('ticketit::shared.header')
        @include('ticketit::tickets.partials.ticket_body')
		
        <div class="row" style="margin-top: 2em;">
        	<div class="col-xs-6">
				<h2 style="margin-top: 0em;">{{ trans('ticketit::lang.comments') }}</h2>
			</div>
        	<div class="col-xs-6 text-right">
        		<button type="button" class="btn btn-info" data-toggle="modal" data-target="#ticket-comment-modal">Afegir comentari</button>
        	</div>
        </div>
        @include('ticketit::tickets.partials.comments')
        {!! $comments->render() !!}
        @include('ticketit::tickets.partials.comment_form')		
@endsection

@section('footer')
    <script>
		var category_id=<?=$ticket->category_id;?>;
        $(document).ready(function() {
            $( ".deleteit" ).click(function( event ) {
                event.preventDefault();
                if (confirm("{!! trans('ticketit::lang.show-ticket-js-delete') !!}" + $(this).attr("node") + " ?"))
                {
                    var form = $(this).attr("form");
                    $("#" + form).submit();
                }

            });
            $('#category_id').change(function(){				
				// Update agent list
				var loadpage = "{!! route($setting->grab('main_route').'agentselectlist') !!}/" + $(this).val() + "/{{ $ticket->id }}";
                $('#agent_id').load(loadpage);
				
				// Update tag list				
				$('#jquery_select2_container .select2-container').hide();
				$('#jquery_tag_category_'+$(this).val()).next().show();
            });
            $('#confirmDelete').on('show.bs.modal', function (e) {
                $message = $(e.relatedTarget).attr('data-message');
                $(this).find('.modal-body p').text($message);
                $title = $(e.relatedTarget).attr('data-title');
                $(this).find('.modal-title').text($title);

                // Pass form reference to modal for submission on yes/ok
                var form = $(e.relatedTarget).closest('form');
                $(this).find('.modal-footer #confirm').data('form', form);
            });

            <!-- Form confirm (yes/ok) handler, submits form -->
            $('#confirmDelete').find('.modal-footer #confirm').on('click', function(){
                $(this).data('form').submit();
            });
			
			$('.response_type').click(function(){
				var type = $(this).attr('data-type');				
				$('#ticket-comment-modal #response_type').val(type);
				$(this).addClass($(this).attr('data-active-class'));
				
				var alt = type == 'note' ? 'reply' : 'note';
				$('#popup_comment_btn_'+alt).removeClass($('#popup_comment_btn_'+alt).attr('data-active-class'));
				
			});

			$('#ticket-edit-comment-modal').on('show.bs.modal', function (e) {
				/*var elem = $(e.relatedTarget);
				var htmlcode = $('#jquery_comment_edit_'+$(elem).attr('data-id')).clone();
				alert($(elem.currentTarget).find('.note-editable :first').toString()) //.append(htmlcode);*/
			});
        });
    </script>
    @include('ticketit::tickets.partials.summernote')
	@include('ticketit::tickets.partials.tags_footer_script')
@append
