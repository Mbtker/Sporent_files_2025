
<div class="modal" id="sendMessageModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="MessageModalTitle">..</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </button>
            </div>
            <form method="POST" action="" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input id="IsSMS" type="text" class="form-control" name="IsSMS" hidden>
                    <input id="TokenId" type="text" class="form-control" name="TokenId" hidden>
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">{{ __('messages.To') }}:</label>
                        <label for="recipient-name" class="col-form-label" id="ToName">...</label>
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="col-form-label">{{ __('messages.Message') }}:</label>
                        <textarea id="Message" type="text" class="form-control" name="Message"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background: #7a7a7a">Cancel</button>
                    <button type="submit" class="btn btn-primary MyOkButton">{{ __('messages.Send') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
