@extends('layouts.app')

@push('stylesheet')
    <style>
        .required label:first-child::after{
            content: " *";
            color: red;
            font-weight: bold;
        }

        /* Toggle buttonn */
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked+.slider {
            background-color: #2FA360;
        }
        input:not(:checked)+.slider {
            background-color: #D0211C;
        }

        input:focus+.slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked+.slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
        #dataTable .table{
            width: 100%;
        }
    </style>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            User List
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-primary float-right" onclick="showModal('Add New User','Save')">Add New</button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-12">
                            <form mathod="POST" id="formFilter">
                                <div class="row">
                                    <x-textbox  name="name" col="col-md-3" placeholder="Enter name" />
                                    <x-textbox type="email" name="email" col="col-md-3"
                                        placeholder="Enter email" />
                                    <x-textbox name="mobile_no" col="col-md-3"
                                        placeholder="Enter mobile no" />
                                    <x-selectbox onchange="upazilaList(this.value,'formFilter')"
                                        name="district_id" col="col-md-3">
                                        @if (!$districts->isEmpty())
                                            @foreach ($districts as $district)
                                                <option value="{{ $district->id }}">{{ $district->location_name }}</option>
                                            @endforeach
                                        @endif
                                    </x-selectbox>
                                    <x-selectbox  name="upazila_id" col="col-md-3" />
                                    <x-selectbox name="role_id" col="col-md-3">
                                        @if (!$roles->isEmpty())
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                                            @endforeach
                                        @endif
                                    </x-selectbox>
                                    <x-selectbox  name="status" col="col-md-3">
                                        <option value="">Select Please</option>
                                        <option value="1">Active</option>
                                        <option value="2">Inactive</option>
                                    </x-selectbox>
                                    <div class="form-group col-md-3" style="padding-top:25px;">
                                        <button type="button" class="btn btn-success" id="btnFilter">Search</button>
                                        <button type="reset" class="btn btn-danger" id="btnReset">Reset</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-12 mt-5">
                            <table class="table table-bordered" id="dataTable">
                                <thead>
                                   <th>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="select_all" onchange="selectAll()">
                                        <label class="form-check-label" for="select_all">
                                        </label>
                                    </div>
                                   </th>
                                    <th>SL</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>District</th>
                                    <th>Upazila</th>
                                    <th>Postal Code</th>
                                    <th>Verified Email</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('modal.modal-xl')
@include('modal.modal-view')
@endsection
@push('scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/sweetalert2@11.js') }}"></script>
    <script src="{{ asset('js/dropify.min.js') }}"></script>
    <script src="{{ asset('js/datatables.bundle7.0.8.js') }}"></script>

    <script>
        // ===============Datatable===============
        var table;
        $(document).ready(function ($) {
            table = $('#dataTable').DataTable({
                "processing": true, //Feature control the processing indicator
                "serverSide": true, //Feature control DataTable server side processing mode
                "order": [], //Initial no order
                "responsive": true, //Make table responsive in mobile device
                "bInfo": true, //TO show the total number of data
                "bFilter": false, //For datatable default search box show/hide
                "lengthMenu": [
                    [5, 10, 15, 25, 50, 100, 1000, 10000, -1],
                    [5, 10, 15, 25, 50, 100, 1000, 10000, "All"]
                ],
                "pageLength": 10, //number of data show per page
                "language": {
                    processing: `<img src="{{asset('svg/table-loading.svg')}}" alt="Loading...."/>`,
                    emptyTable: '<strong class="text-danger">No Data Found</strong>',
                    infoEmpty: '',
                    zeroRecords: '<strong class="text-danger">No Data Found</strong>'
                },
                "ajax": {
                    "url": "{{route('user.list')}}",
                    "type": "POST",
                    "data": function (data) {
                            data.name = $('#formFilter #name').val();
                            data.email = $('#formFilter #email').val();
                            data.mobile_no = $('#formFilter #mobile_no').val();
                            data.district_id = $('#formFilter #district_id').val();
                            data.upazila_id = $('#formFilter #upazila_id').val();
                            data.role_id = $('#formFilter #role_id').val();
                            data.status = $('#formFilter #status').val();
                            data._token      = _token;
                        }
                },
                "columnDefs":[
                    {
                        "targets":[0,2,12],
                        "orderable":false
                    },
                    {
                        "targets":[3,6,7,8,11],
                        "className":"text-center"
                    },
                ],
                "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",

                "buttons": [
                    'colvis',
                    {
                        "extend": 'print',
                        "title": "User List",
                        "orientation": "landscape", //portrait
                        "pageSize": "A4", //A3,A5,A6,legal,letter
                        "exportOptions": {
                            columns: function (index, data, node) {
                                return table.column(index).visible();
                            }
                        },
                        customize: function (win) {
                            $(win.document.body).addClass('bg-white');
                        },
                    },
                    {
                        "extend": 'csv',
                        "title": "User List",
                        "filename": "user-list",
                        "exportOptions": {
                            columns: function (index, data, node) {
                                return table.column(index).visible();
                            }
                        }
                    },
                    {
                        "extend": 'excel',
                        "title": "User List",
                        "filename": "user-list",
                        "exportOptions": {
                            columns: function (index, data, node) {
                                return table.column(index).visible();
                            }
                        }
                    },
                    {
                        "extend": 'pdf',
                        "title": "User List",
                        "filename": "user-list",
                        "orientation": "landscape", //portrait
                        "pageSize": "A4", //A3,A5,A6,legal,letter
                        "exportOptions": {
                            columns: [0, 2, 3, 4, 5, 6, 7, 8, 9]
                        },
                        customize: function (doc) {
                        doc.content[1].table.widths = ['5%', '10%', '10%', '20%', '10%', '10%',
                            '15%', '10%', '10%'
                        ];
                        doc.styles.tableHeader.alignment = "left";
                        //Remove the title created by datatTables
                        //Create a date string that we use in the footer. Format is dd-mm-yyyy
                        var now = new Date();
                        var jsDate = now.getDate() + '-' + (now.getMonth() + 1) + '-' + now
                            .getFullYear();
                        var logo =
                            'data:image/jpeg;base64,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ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA=';
                        doc.pageMargins = [20, 20, 20, 20];
                        // Set the font size fot the entire document
                        doc.defaultStyle.fontSize = 10;
                        // Set the fontsize for the table header
                        doc.styles.tableHeader.fontSize = 10;
                        doc.content.splice(0, 1, {
                            margin: [0, 0, 0, 5],
                            alignment: 'center',
                            fontSize: 10,
                            image: logo,
                            width: 35,
                        }, {
                            alignment: 'center',
                            text: ['User List'],
                            fontSize: 10,
                            margin: [0, 0, 0, 5],
                            bold: true
                        });
                        // Create a footer object with 2 columns
                        // Left side: report creation date
                        // Right side: current page and total pages
                        doc['footer'] = (function (page, pages) {
                            return {
                                columns: [{
                                        alignment: 'left',
                                        text: ['Created on: ', {
                                            text: jsDate.toString()
                                        }]
                                    },
                                    {
                                        alignment: 'right',
                                        text: ['page ', {
                                            text: page.toString()
                                        }, ' of ', {
                                            text: pages.toString()
                                        }]
                                    }
                                ],
                                margin: [20, 5, 20, 5]
                            }
                        });
                        // Change dataTable layout (Table styling)
                        // To use predefined layouts uncomment the line below and comment the custom lines below
                        // doc.content[0].layout = 'lightHorizontalLines'; // noBorders , headerLineOnly
                        var objLayout = {};
                        objLayout['hLineWidth'] = function (i) {
                            return .5;
                        };
                        objLayout['vLineWidth'] = function (i) {
                            return .5;
                        };
                        objLayout['hLineColor'] = function (i) {
                            return '#aaa';
                        };
                        objLayout['vLineColor'] = function (i) {
                            return '#aaa';
                        };
                        objLayout['paddingLeft'] = function (i) {
                            return 4;
                        };
                        objLayout['paddingRight'] = function (i) {
                            return 4;
                        };
                        doc.content[0].layout = objLayout;
                    }
                    },
                    ]
            });

            //Append bulk delete button
            $('.dataTables_wrapper .dt-buttons').append('<button class="btn btn-danger" type="button" id="bulkActionDelete"><i class="fas fa-trash"></i> Delete All</button>');
        });

        // ===============Data table filter===============
        $(document).on('click', '#btnFilter', function () {
            table.ajax.reload();
        });
        $(document).on('click', '#btnReset', function () {
            $('#formFilter')[0].reset();
            table.ajax.reload();
        });


        // ===============Dropify===============
        $('.dropify').dropify();
        // ===============Show User Modal===============
        function showModal(title,btn_text){
            $('#storeForm')[0].reset();
            $('#storeForm').find('.is-invalid').removeClass('is-invalid');
            $('#storeForm').find('.error').remove();
            $('#password, #password_confirmation').parent().removeClass('d-none');
            $('#storeForm .dropify-render img').attr('src', '');
            $('.dropify-clear').trigger('click');
            $('#saveDataModal').modal('show');

            $('#saveDataModal .modal-title').text(title);
            $('#saveDataModal #saveBtn').text(btn_text);
        }
        // ==============User form submit================
        $(document).on('click', '#saveBtn', function(){
            var storeForm = document.getElementById('storeForm');
            var formData = new FormData(storeForm);
            var url = "{{ route('user.store') }}";
            var id = $('update_id').val();
            var method;
            if (id){
                method = 'update';
            }else{
                method = 'add';
            }
            storeFormData(table,url,method,formData);
        });
        function storeFormData(table,url,method,formData){
            $.ajax({
                type: "POST",
                url: url,
                data: formData,
                dataType: "json",
                contentType: false,
                processData: false,
                cache: false,
                success: function (data) {
                    // validation form
                    $('#storeForm').find('.is-invalid').removeClass('is-invalid');
                    $('#storeForm').find('.error').remove();
                    if(data.status == false){
                        $.each(data.errors, function (key, value) {
                            $('#storeForm #'+key).addClass('is-invalid');
                            $('#storeForm #'+key).parent().append('<div class="error invalid-tooltip d-block">'+value+'</div>');
                         });
                    }else{
                        flashMessage(data.status, data.message);
                        if(data.status == 'success'){
                            if(method == 'update'){
                                table.ajax.reload(null,false);
                            }else{
                                table.ajax.reload();
                            }
                            $('#saveDataModal').modal('hide');
                        }
                    }
                },
                error: function(xhr, ajaxOption, thrownError){
                    console.log(thrownError+'\r\n'+xhr.statusText+'\r\n'+xhr.responseText);
                    console.log('errors');
                },
            });
        }

        // ================Edit User====================

        $(document).on('click', '.edit_data', function () {
            let id = $(this).data('id');
            if(id){
                $.ajax({
                    type: "POST",
                    url: "{{ route('user.edit') }}",
                    data: {
                        id: id,
                        _token: _token
                    },
                    dataType: "json",
                    success: function (data) {
                       $('#password, #password_confirmation').parent().addClass('d-none');
                       $('#storeForm #update_id').val(data.id);
                       $('#storeForm #name').val(data.name);
                       $('#storeForm #email').val(data.email);
                       $('#storeForm #mobile_no').val(data.mobile_no);
                       $('#storeForm #district_id').val(data.district_id, 'storeForm');
                       upazilaList(data.district_id, 'storeFrom');
                       setTimeout(() => {
                        $('#storeForm #upazila_id').val(data.upazila_id);
                       }, 1000);
                       $('#storeForm #postal_code').val(data.postal_code);
                       $('#storeForm #address').val(data.address);
                       $('#storeForm #role_id').val(data.role_id);
                       if (data.avatar) {
                            let avatar = "{{asset('storage/'.USER_AVATAR)}}/" + data.avatar;
                            $('#storeForm .dropify-preview').css('display', 'block');
                            $('#storeForm .dropify-render').html('<image src="' + avatar + '"/>');
                            $('#storeForm #old_avatar').val(data.avatar);
                        }

                       $('#saveDataModal .modal-title').html('<i class="fas fa-edit"></i> <span>Edit '+data.name+'</span>');
                       $('#saveDataModal #saveBtn').text('Update');
                       $('#saveDataModal').modal('show');
                    },
                    error: function(xhr, ajaxOption, thrownError){
                        console.log(thrownError+'\r\n'+xhr.statusText+'\r\n'+xhr.responseText);
                    },
                });
            }
        });

        // ==================View User Data==================

        $(document).on('click', '.view_data', function () {
            let id = $(this).data('id');
            if(id){
                $.ajax({
                    type: "POST",
                    url: "{{ route('user.show') }}",
                    data: {
                        id: id,
                        _token: _token
                    },
                    dataType: "json",
                    success: function (data) {
                       $('#ViewData').html('');
                       $('#ViewData').html(data.view_data);

                       $('#viewDataModal').modal('show');
                       $('#viewDataModal .modal-title').html('<i class="fas fa-eye"></i> <span>View '+data.name+' Details</span>');
                    },
                    error: function(xhr, ajaxOption, thrownError){
                        console.log(thrownError+'\r\n'+xhr.statusText+'\r\n'+xhr.responseText);
                    },
                });
            }
        });

        // ================Chnage User Status================

        $(document).on('change', '.changeStatus', function () {
            let id = $(this).data('id');
            let status;
            if($(this).is(":checked")){
                status = 1;
            }else{
                status = 2;
            }
            if(id && status){
                $.ajax({
                    type: "POST",
                    url: "{{ route('user.change.status') }}",
                    data: {
                        id: id,
                        status: status,
                        _token: _token
                    },
                    dataType: "json",
                    success: function (data) {
                        flashMessage(data.status, data.message);
                      if(data.status == 'success'){
                          data.table.reload(null,false);
                      }
                    },
                    error: function(xhr, ajaxOption, thrownError){
                        console.log(thrownError+'\r\n'+xhr.statusText+'\r\n'+xhr.responseText);
                    },
                });
            }
        });

        //===================Delete User Data================

        $(document).on('click', '.delete_data', function () {
            let id = $(this).data('id');
            let name = $(this).data('name');
            let row = table.row($(this).parent('tr'));
            let url = "{{ route('user.delete') }}";
            deleteData(id,url,table,row,name)
        });

        function deleteData(id,url,table,row,name){
            Swal.fire({
            title: 'Are you sure to delete '+name+' data?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: {
                            id: id,
                            _token: _token,
                        },
                        dataType: "json",
                    }).done(function(response){
                        if(response.status == 'success'){
                            swal.fire('Deleted',response.message,"success").then(function(){
                                table.row(row).remove().draw(false);
                            });
                        }
                    }).fail(function(){
                        swal.fire('Ooops...',"Something went wrong!", "error");
                    });
                }
            });
        }

        //===================Delete User Data================
        $(document).on('click', '#bulkActionDelete', function () {
            let id = [];
            let rows;

            $('.select_data:checked').each(function(){
                id.push($(this).val());
                rows = table.rows($('.select_data:checked').parents('tr'));
            });
            if(id.length == 0){
                flashMessage('error', 'Please checked at list one row');
            }else{
                let url = "{{ route('user.bulk.action.delete') }}";
                bulkActionDelete(id,url,table,rows);
            }
        });

        function bulkActionDelete(id,url,table,rows){
            Swal.fire({
            title: 'Are you sure to delete all checked data?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete all!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: {
                            id: id,
                            _token: _token,
                        },
                        dataType: "json",
                    }).done(function(response){
                        if(response.status == 'success'){
                            swal.fire('Deleted',response.message,"success").then(function(){
                                $('#select_all').prop('checked', false);
                                table.rows(rows).remove().draw(false);
                            });
                        }
                    }).fail(function(){
                        swal.fire('Ooops...',"Something went wrong!", "error");
                    });
                }
            });
        }


        // =================Upazila list==================
        function upazilaList(district_id, form){
            if(district_id){
                $.ajax({
                    type: "POST",
                    url: "{{ route('upazila.list') }}",
                    data: {
                        district_id: district_id,
                        _token: _token
                    },
                    dataType: "json",
                    success: function (response) {
                        $('#'+form+' #upazila_id').html('');
                        $('#'+form+' #upazila_id').html(response);
                    },
                    error: function(xhr, ajaxOption, thrownError){
                        console.log(thrownError+'\r\n'+xhr.statusText+'\r\n'+xhr.responseText);
                    },
                });
            }
        }

        // =================flashMessage====================
        function flashMessage(status, message) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: status,
                title: message,
                showConfirmButton: false,
                timer: 3000,
            });
        }
    </script>
    <script src="{{ asset('js/main.js') }}"></script>
@endpush
