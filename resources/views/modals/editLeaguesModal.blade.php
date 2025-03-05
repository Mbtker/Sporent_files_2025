
<div class="modal" id="editLeaguesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('messages.Edit') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </button>
            </div>
            <form method="POST" action="{{ route('editLeagues') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <input id="Id" type="text" class="form-control" name="Id" hidden>
                        <label for="recipient-name" class="col-form-label">{{ __('messages.LeaguesTopic') }}: @error('Topic') <small id="ErrorName" class="form-text text-danger">{{ $message }}</small> @enderror</label>
                        <input id="Topic" type="text" class="form-control" name="Topic" value="{{ old('Topic') }}" required autofocus>
                    </div>
                    <div>
                        <label for="recipient-name" class="col-form-label">{{ __('messages.Location') }}: @error('Location') <small id="ErrorPhone" class="form-text text-danger">{{ $message }}</small> @enderror</label>
                        <input id="Location" type="text" class="form-control" name="Location" value="{{ old('Location') }}" >
                    </div>
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">{{ __('messages.Fee') }}: @error('Fee') <small id="ErrorEmail" class="form-text text-danger">{{ $message }}</small> @enderror</label>
                        <input id="Fee" type="text" class="form-control" name="Fee" value="{{ old('Fee') }}">
                    </div>
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">{{ __('messages.Status') }}: @error('Status') <small id="ErrorEmail" class="form-text text-danger">{{ $message }}</small> @enderror</label>
                        <select class="form-select form-control" id="Status" name="Status">
                            <option value="1" {{ old('Status') == '1' ? "selected" : "" }}>{{ __('messages.Active') }}</option>
                            <option value="0" {{ old('Status') == '0' ? "selected" : "" }}>{{ __('messages.Inactive') }}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary MyCancelButton" data-bs-dismiss="modal">{{ __('messages.Cancel') }}</button>
                    <button type="submit" class="btn btn-primary MyOkButton">{{ __('messages.Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
