<div class="modal" id="saveDataModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" id="storeForm" enctype="multipart/form-data">
                    @csrf
                <input type="hidden" name="update_id" id="update_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <p class="text-danger">All (*) Marke are required</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-8">
                            <x-textbox labelName="Name" name="name" required="required" col="col-md-12" placeholder="Enter name"/>
                            <x-textbox labelName="Email" type="email" name="email" required="required" col="col-md-12" placeholder="Enter Email"/>
                            <x-textbox labelName="Mobile Number" name="mobile_no" required="required" col="col-md-12" placeholder="Enter Mobile No"/>
                            <x-textbox type="password" labelName="Password" name="password" required="required" col="col-md-12" placeholder="Enter Password"/>
                            <x-textbox type="password" labelName="Confirm Password" name="password_confirmation" required="required" col="col-md-12" placeholder="Re Type Password"/>
                            <x-selectbox onchange="upazilaList(this.value, 'storeForm')" labelName="District" name="district_id" required="required" col="col-md-12">
                                @if (!$districts->isEmpty())
                                    @foreach ($districts as $district)
                                        <option value="{{ $district->id }}">{{ $district->location_name }}</option>
                                    @endforeach
                                @endif
                            </x-selectbox>
                            <x-selectbox  labelName="Upazila" name="upazila_id" required="required" col="col-md-12"/>
                            <x-textbox labelName="Postal Code" name="postal_code" required="required" col="col-md-12" placeholder="Enter Postal Code"/>
                            <x-textarea labelName="Address" name="address" required="required" col="col-md-12" placeholder="Enter Address"/>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group col-md-12">
                                <input type="file" class="dropify" name="avatar" id="avatar" data-show-errors="true" data-show="true" data-errors-position="outside" data-allowed-file-extensions="jpg jpeg png svg webp gif">
                                <input type="hidden" name="old_avatar" id="old_avatar">
                            </div>
                            <x-selectbox labelName="Role" name="role_id" required="required" col="col-md-12">
                                @if (!$roles->isEmpty())
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                                    @endforeach
                                @endif
                            </x-selectbox>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="saveBtn"></button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
           </form>
        </div>
    </div>
</div>
