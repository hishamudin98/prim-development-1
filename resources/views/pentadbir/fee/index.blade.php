@extends('layouts.master')

@section('css')
    <link href="{{ URL::asset('assets/libs/chartist/chartist.min.css') }}" rel="stylesheet" type="text/css" />
    @include('layouts.datatable')
    <style>
        .modal-dialog {
            top: 15%;
        }

        .scrollheight {
            height: 400px;
        }

        @media (max-width: 576px) {
            .modal-dialog {
                max-width: none !important;
                width: 90% !important;
            }
        }

    </style>
@endsection

@section('content')
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="page-title-box">
                <h4 class="font-size-18">Yuran</h4>
                <!-- <ol class="breadcrumb mb-0">
                                                                                                                                    <li class="breadcrumb-item active">Welcome to Veltrix Dashboard</li>
                                                                                                                                </ol> -->
            </div>
        </div>
    </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-2">
                <div class="card-body pb-1" style="background-color: #e6e6e6;border: 1px solid #dfdfdf;">
                    <p class="text-muted mb-1">
                        Langkah-langkah menambahkan yuran :
                    </p>
                    <ul class="text-muted">
                        <li>Tambah kategori yuran baru dengan klik butang senarai kategori.</li>
                        <li>Tambah yuran dengan klik butang tambah yuran.</li>
                        <li>Tambah perincian yuran bagi menetapkan setiap harga item bagi yuran yg dipilih.</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card card-primary mb-2">

                {{ csrf_field() }}
                <div class="card-body">

                    <div class="form-group">
                        <label>Nama Organisasi</label>
                        <select name="organization" id="organization" class="form-control">
                            <option value="" selected disabled>Pilih Organisasi</option>
                            @foreach ($organization as $row)
                                <option value="{{ $row->id }}">{{ $row->nama }}</option>
                            @endforeach
                        </select>
                    </div>


                </div>

                {{-- <div class="">
                <button onclick="filter()" style="float: right" type="submit" class="btn btn-primary"><i
                        class="fa fa-search"></i>
                    Tapis</button>
            </div> --}}

            </div>
        </div>

        <div class="col-md-12">
            <div class="card">
                {{-- <div class="card-header">List Of Applications</div> --}}
                <div>
                    {{-- route('sekolah.create') --}}
                    <a class="btn btn-secondary mt-4 ml-4" data-toggle="modal" data-target="#categoryModal">
                        Senarai
                        Kategori</a>

                    <a href="{{ route('fees.create') }}" class="btn btn-primary mt-4 ml-2" data-toggle="modal"
                        data-target="#feeModal"> <i class="fas fa-plus"></i>
                        Tambah
                        Yuran</a>
                </div>

                <div class="card-body">

                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (\Session::has('success'))
                        <div class="alert alert-success">
                            <p>{{ \Session::get('success') }}</p>
                        </div>
                    @endif

                    {{-- <div align="right">
                            <a href="{{route('admin.create')}}" class="btn btn-primary">Add</a>
                <br />
                <br />
            </div> --}}
                    <div class="scrollheight">
                        <div class="table-responsive">
                            <table id="feesTable" class="table table-bordered table-striped dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr style="text-align:center">
                                        <th> No. </th>
                                        <th>Nama Yuran</th>
                                        <th>Jumlah Amaun (RM)</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="modal fade" id="categoryModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Senarai Kategori</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="scrollheight">

                        <a href="{{ route('category.create') }}" class="btn btn-primary mb-3"> <i
                                class="fas fa-plus"></i>
                            Tambah Kategori</a>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <tr style="text-align:center">
                                    <th>Bil.</th>
                                    <th>Nama Kategori</th>
                                    <th>Action</th>
                                </tr>

                                @foreach ($listcategory as $row)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $row->nama }}</td>
                                        <td>
                                            <div class="d-flex justify-content-center">
                                                <a href="{{ route('category.edit', $row->id) }}"
                                                    class="btn btn-primary m-1">Edit</a>
                                            </div>
                                        </td>

                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button> --}}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="feeModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="feeModal">Tambah Yuran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="scrollheight">

                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form method="post" action="{{ route('fees.store') }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Nama Yuran</label>
                                    <input type="text" name="name" class="form-control" placeholder="Nama Yuran">
                                </div>

                                <div class="form-group">
                                    <label>Nama Organisasi</label>
                                    <select name="organization" id="organizationdd" class="form-control">
                                        <option value="" selected>Pilih Organisasi</option>
                                        @foreach ($organization as $row)
                                            <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="yearhide form-group">
                                    <label>Tahun</label>
                                    <select name="year" id="year" class="form-control">
                                        <option value="" selected>Pilih Tahun</option>
                                    </select>
                                </div>

                                <div class="cbhide form-check-inline">

                                </div>

                                <div class="form-group mb-0">
                                    <div>
                                        <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">
                                            Simpan
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->


                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button> --}}
                </div>
            </div>
        </div>

        {{-- confirmation delete modal --}}
        <div id="deleteConfirmationModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Padam Yuran</h4>
                    </div>
                    <div class="modal-body">
                        Adakah anda pasti?
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn btn-primary" id="delete"
                            name="delete">Padam</button>
                        <button type="button" data-dismiss="modal" class="btn">Batal</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </div>
@endsection


@section('script')
    <!-- Peity chart-->
    <script src="{{ URL::asset('assets/libs/peity/peity.min.js') }}"></script>

    <!-- Plugin Js-->
    <script src="{{ URL::asset('assets/libs/chartist/chartist.min.js') }}"></script>

    <script src="{{ URL::asset('assets/js/pages/dashboard.init.js') }}"></script>


    <script>
        $(document).ready(function() {

            var feesTable;

            // fetch_data();

            function fetch_data(oid = '') {
                feesTable = $('#feesTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('fees.getFeesDatatable') }}",
                        data: {
                            oid: oid,
                        },
                        type: 'GET',

                    },
                    'columnDefs': [{
                        "targets": [0], // your case first column
                        "className": "text-center",
                        "width": "2%"
                    }, {
                        "targets": [1], // your case first column
                        "className": "text-center",
                    }, {
                        "targets": [2], // your case first column
                        "className": "text-center",
                        "width": "30%"
                    }],
                    order: [
                        [1, 'asc']
                    ],
                    columns: [{
                        "data": null,
                        searchable: false,
                        "sortable": false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    }, {
                        data: "feename",
                        name: 'feename'
                    }, {
                        data: "totalamount",
                        name: 'totalamount',
                        orderable: false,
                        searchable: false
                    }, {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }, ]
                });
            }

            $('#organization').change(function() {
                var organizationid = $("#organization option:selected").val();
                $('#feesTable').DataTable().destroy();
                console.log(organizationid);
                fetch_data(organizationid);
            });

            // csrf token for ajax
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var teacher_id;

            $(document).on('click', '.btn-danger', function() {
                teacher_id = $(this).attr('id');
                $('#deleteConfirmationModal').modal('show');
            });

            $('#delete').click(function() {
                $.ajax({
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        _method: 'DELETE'
                    },
                    url: "/teacher/" + teacher_id,
                    success: function(data) {
                        setTimeout(function() {
                            $('#confirmModal').modal('hide');
                        }, 2000);

                        $('div.flash-message').html(data);

                        feesTable.ajax.reload();
                    },
                    error: function(data) {
                        $('div.flash-message').html(data);
                    }
                })
            });

            $('.alert').delay(3000).fadeOut();

            $('.yearhide').hide();
            $('.cbhide').hide();

            $(document).on('change', '#checkedAll', function() {
                if (this.checked) {
                    $(".checkSingle").each(function() {
                        this.checked = true;
                    })
                } else {
                    $(".checkSingle").each(function() {
                        this.checked = false;
                    })
                }
            });

            $(document).on('change', '.checkSingle', function() {

                // console.log('asdf');
                // $('#cb_class').not(this).prop('checked', this.checked);
                if ($(this).is(":checked")) {
                    var isAllChecked = 0;
                    $(".checkSingle").each(function() {
                        if (!this.checked)
                            isAllChecked = 1;
                    })
                    if (isAllChecked == 0) {
                        $("#checkedAll").prop("checked", true);
                    }
                } else {
                    $("#checkedAll").prop("checked", false);
                }

            });

            $('#organizationdd').change(function() {

                if ($(this).val() != '') {
                    var organizationid = $("#organizationdd option:selected").val();
                    var _token = $('input[name="_token"]').val();
                    $.ajax({
                        url: "{{ route('fees.fetchYear') }}",
                        method: "POST",
                        data: {
                            oid: organizationid,
                            _token: _token
                        },
                        success: function(result) {
                            $('.yearhide').show();
                            $('#year').empty();

                            if (result.success.type_org == 1 || result.success.type_org == 2) {

                                $("#year").append(
                                    "<option value='' selected> Pilih Tahun</option>");
                                $("#year").append("<option value='1'>Tahun 1</option>");
                                $("#year").append("<option value='2'>Tahun 2</option>");
                                $("#year").append("<option value='3'>Tahun 3</option>");
                                $("#year").append("<option value='4'>Tahun 4</option>");
                                $("#year").append("<option value='5'>Tahun 5</option>");
                                $("#year").append("<option value='6'>Tahun 6</option>");

                            } else if (result.success.type_org == 3) {
                                $("#year").append(
                                    "<option value='' selected> Pilih Tingkatan</option>");
                                $("#year").append("<option value='1'>Tingkatan 1</option>");
                                $("#year").append("<option value='2'>Tingkatan 2</option>");
                                $("#year").append("<option value='3'>Tingkatan 3</option>");
                                $("#year").append("<option value='4'>Tingkatan 4</option>");
                                $("#year").append("<option value='5'>Tingkatan 5</option>");
                                $("#year").append("<option value='6'>Tingkatan 6</option>");
                            }

                            $('.cbhide').hide();
                        }

                    })
                }
            });

            $('#year').change(function() {

                if ($(this).val() != '') {
                    var organizationid = $("#organizationdd option:selected").val();
                    var year = $("#year option:selected").val();
                    var _token = $('input[name="_token"]').val();
                    $.ajax({
                        url: "{{ route('fees.fetchClass') }}",
                        method: "POST",
                        data: {
                            oid: organizationid,
                            year: year,
                            _token: _token
                        },
                        success: function(result) {
                            $('.cbhide').show();
                            $('#cb_class').remove();
                            $(".cbhide label").remove();
                            $(".cbhide").append(
                                "<label for='checkAll' style='margin-right: 22px;' class='form-check-label'> <input class='form-check-input' type='checkbox' id='checkedAll' name='all' value=''/> Semua Kelas </label>"
                            );

                            // console.log(result.success.oid);
                            jQuery.each(result.success, function(key, value) {

                                $(".cbhide").append(
                                    "<label for='cb_class' style='margin-right: 22px;' class='form-check-label'> <input class='checkSingle form-check-input' type='checkbox' id='cb_class' name='cb_class[]' value='" +
                                    value.cid + "'/> " + value.cname + " </label>");

                            });
                        }

                    })
                }
            });

        });

    </script>

@endsection
