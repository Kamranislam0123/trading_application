@extends('layouts.app')

@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <style>
        .label:empty {
            display: block;
        }
        /*.table td, th{*/
        /*    font-size:20px;*/
        /*}*/
        /*.table btn{*/
        /*    font-size:20px;*/
        /*}*/
    </style>
@endsection

@section('title')
    Client Order Management
@endsection

@section('content')
    @if(Session::has('message'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{ Session::get('message') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    {{-- <a class="btn btn-primary" href="{{ route('marketing.add') }}">Add Client Order </a> --}}

                    <hr>

                  <div class="table-responsive">
                      <table id="table" class="table table-bordered table-striped">
                          <thead>
                          <tr>
                              <th>Contact Date</th>
                              <th>Last Remark</th>
                              <th>Request Type</th>
                              <th>Last Update</th>
                              <th>Next Contact Date</th>
                              <th>Next Contact Date</th>
                              <th>Last Days</th>
                              <th>Marketing</th>
                              <th>Client</th>
                              <th>Company</th>
                              <th>Mobile</th>
                              <th>Amount</th>
                              <th>Source</th>
                              <th>Status</th>
                              <th>Action</th>
                          </tr>
                          </thead>

                          <tbody>
                          @foreach($clients as $client)
                              <tr>
                                  <td>{{ $client->date->format('d-m-Y') }}</td>
                                  <td>{{ $client->last_remark }}</td>
                                  <td>
                                      {{ $client->client_service->name??'' }}
                                  </td>

                                  <td>{{ $client->last_contact_date ? date('d-m-Y',strtotime($client->last_contact_date)):'' }}</td>

                                  <td>{{ $client->next_contact_date }} </td>
                                  <td><span style="min-width:5px!important;min-height:10px!important" class="label {{ $client->next_contact_date <= date('Y-m-d') ? 'label-danger' : 'label-info' }}">{{ $client->next_contact_date ? date('d-m-Y',strtotime($client->next_contact_date)):'' }}</span></td>
                                  <td>
                                      <?php
                                      $fdate = $client->updated_at;
                                      $tdate = date('d-m-Y');
                                      $ldate = $client->last_contact_date?date('d-m-y',strtotime($client->last_contact_date)):'';
                                      $datetime1 = new DateTime($fdate);
                                      $datetime2 = new DateTime($tdate);
                                      $datetime3 = new DateTime($ldate);
                                      $interval = $datetime1->diff($datetime2);
                                      $interval2 = $datetime3->diff($datetime2);
                                      $days = $interval->format('%a');
                                      $days2 = $interval2->format('%a');
                                      ?>
                                      {{--                                    @if($ldate <= $tdate)--}}
                                      {{--                                            <span class="label label-danger">{{$days}}</span>--}}
                                      {{--                                        @else--}}
                                      {{--                                            <span class="label label-primary">{{$days }}</span>--}}
                                      {{--                                    @endif--}}
                                      {{$days}}
                                  </td>
                                  <td>
                                      {{ $client->marketing->name??'' }}
                                  </td>
                                  <td>{{ $client->client_name }}</td>
                                  <td>{{ $client->company_name }}</td>
                                  <td>{{ $client->mobile }}</td>
                                  <td class="text-right">{{ number_format($client->propose_amount, 2) }}</td>
                                  <td>
                                      {{ $client->client_source->name??'' }}
                                  </td>
                                  <td>
                                      @if ($client->status == 1)
                                          <span class="label label-warning">Low</span>
                                      @elseif($client->status == 2)
                                          <span class="label label-primary">Medium</span>
                                      @elseif($client->status == 3)
                                          <span class="label label-info">High</span>
                                      @elseif($client->status == 4)
                                          <span class="label label-success">Work Order</span>
                                      @elseif($client->status == 5)
                                          <span class="label label-danger">Negative</span>
                                      @endif
                                  </td>
                                  <td>
                                      {{-- <a class="btn btn-primary btn-sm btn-update" role="button" data-id="{{$client->id}}">Regular Update</a>
                                      <a class="btn btn-success btn-sm btn-pay" role="button" data-id="{{$client->id}}" >Update</a> --}}
                                      <a class="btn btn-info btn-sm" href="{{ route('client_operation_details', ['id' => $client->id]) }}">Details</a>
                                  </td>
                              </tr>
                          @endforeach
                          </tbody>
                      </table>
                  </div>
                </div>
            </div>
        </div>
    </div>
{{--    update Modal start--}}

    <!-- Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title text-center" id="exampleModalLabel">Client Update</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="modal-update"  name="modal-update">
                        <div class="form-group">
                            <label for="client_name">Client Name</label>
                            <input type="text" class="form-control" id="client_name" name="client_name">
                            <input type="hidden" name="client_id" id="client_id">
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="client_status" class="form-control">
                                <option value="">Select Status</option>
                                <option value="1">Low</option>
                                <option value="2">Medium </option>
                                <option value="3">High</option>
                                <option value="4">Work Order</option>
                                <option value="5">Negative</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Work Order Complete Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right date" id="order_complete_date" name="order_complete_date" value="{{ date('Y-m-d') }}" autocomplete="off">
                            </div>
                            <!-- /.input group -->
                        </div>
                        <div class="form-group">
                            <label for="amount">Propose Amount</label>
                            <input type="text" class="form-control" id="propose_amount" name="propose_amount">
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="submit-update">Update</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
{{--    update Modal end--}}
{{--    regular update start--}}
    <div class="modal fade" id="regularUpdateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title text-center" id="exampleModalLabel">Regular Update</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="modal-regular-update"  name="modal-regular-update">
                        <div class="form-group">
                            <label for="client_name">Client Name</label>
                            <input type="text" class="form-control" id="name" name="name">
                            <input type="hidden" name="id" id="id">
                        </div>
                        <div class="form-group">
                            <label for="client_name">Company Name</label>
                            <input type="text" class="form-control" id="company_name" name="company_name">
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="regular_status" name="status" class="form-control">
                                <option value="">Select Status</option>
                                <option value="1">Low</option>
                                <option value="2">Medium </option>
                                <option value="3">High</option>
                                <option value="4">Work Order</option>
                                <option value="5">Negative</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right date" id="date" name="date" value="{{ date('Y-m-d') }}" autocomplete="off">
                            </div>
                            <!-- /.input group -->
                        </div>
                        <div class="form-group">
                            <label>Next Contact Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right date" id="next_contact_date" name="next_contact_date" value="{{ date('Y-m-d') }}" autocomplete="off">
                            </div>
                            <!-- /.input group -->
                        </div>
                        <div class="form-group">
                            <label for="amount">Remark</label>
                            <input type="text" class="form-control" id="remark" name="remark">
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="submit-regular-update">Update</button>
                    </div>
                </div>

            </div>
        </div>
    </div>

{{--    regular update end--}}
@endsection


@section('script')
    <!-- DataTables -->
    <script src="{{ asset('themes/backend/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('themes/backend/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

    <!-- sweet alert 2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script>
        $(function () {
            $('#table').DataTable({

              "order":[[4, 'asc']],
              'columnDefs' : [
        //hide the second & fourth column
        { 'visible': false, 'targets': [4] }
    ]
            });
        })
        //Date picker
        $('.date').datepicker({
            autoclose: true,
            format: 'yyyy-mm-dd'
        });

        $('body').on('click', '.btn-pay', function () {

            var clientId = $(this).data('id');
            // $('#client_name').html('<option value="">Select Project</option>');
            $('#updateModal').modal('hide');
            $.ajax({
                method: "GET",
                url: "{{ route('get_client') }}",
                data: { clientId: clientId }
            }).done(function( response ) {

                $('#client_name').val(response.client_name);
                $('#propose_amount').val(response.propose_amount);
                $('#client_id').val(response.id);
                $('#client_status').val(response.status);
                $('#updateModal').modal('show');
            });

        });
        $('#submit-update').click(function () {
            var formDataOne = new FormData($('#modal-update')[0]);
            $.ajax({
                type: "POST",
                url: "{{ route('client.update') }}",
                data: formDataOne,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#updateModal').modal('hide');
                        // $('#modal-pay').modal('hide');
                        Swal.fire(
                            'Paid!',
                            response.message,
                            'success'
                        ).then((result) => {
                            location.reload();
                            //window.location.href = response.redirect_url;
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: response.message,
                        });
                    }
                }
            });
        });

        // Regular Update
        $('body').on('click', '.btn-update', function () {
            var clientId = $(this).data('id');
            $('#regularUpdateModal').modal('hide');
            $.ajax({
                method: "GET",
                url: "{{ route('get_client') }}",
                data: { clientId: clientId }
            }).done(function( response ) {

                $('#name').val(response.client_name);
                $('#company_name').val(response.company_name);
                $('#id').val(response.id);
                $('#regular_status').val(response.status);
                $('#regularUpdateModal').modal('show');
            });

        });

        $('#submit-regular-update').click(function () {
            var formData = new FormData($('#modal-regular-update')[0]);


            $.ajax({
                type: "POST",
                url: "{{ route('client.regular_update') }}",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#regularUpdateModal').modal('hide');
                        // $('#modal-pay').modal('hide');
                        Swal.fire(
                            'Paid!',
                            response.message,
                            'success'
                        ).then((result) => {
                            location.reload();
                            //window.location.href = response.redirect_url;
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: response.message,
                        });
                    }
                }
            });
        });

    </script>
@endsection
