@extends('layouts.appAdmin')
@section('style')
<style type="text/css">
    /*.tomato-bg {
    background-color: tomato !important;
    }
    .green-bg {
    background-color: #ccffcc !important;
    }*/
    .pending-cell{
        background-color: #fcdc03 !important;
    }
    .approved-cell{
        background-color: #ccffcc !important;
    }
    .reject-cell{
        background-color: tomato !important;
    }
    .btn-class{background: #fff; border: 0;}
</style>
 <style type="text/css">
        td.details-control::after {
            /*background: url('https://www.datatables.net/examples/resources/details_open.png') no-repeat center center;*/
            cursor: pointer;
            width: 85px;
            display: block;
            color: #518607;
            border-radius: 14px;
            box-shadow: 0 0 3px #444;
            text-align: center;
            content: 'view more +';
        }
        tr.shown td.details-control::after {
            /*background: url('https://www.datatables.net/examples/resources/details_close.png') no-repeat center center;*/
            content: 'view less -';
            color: #ED362C;
        }
        .device-info-class {
            white-space: -moz-pre-wrap !important;
            white-space: -webkit-pre-wrap;
            white-space: -pre-wrap;
            white-space: -o-pre-wrap;
            white-space: pre-wrap;
            word-wrap: break-word;
            word-break: break-all;
            white-space: normal;
        }
    </style>
@endsection
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 text-left">
                <div class=" col-md-6" style="padding-bottom: 10PX;">                            
                    <select name="status" class="downloadtrigger form-control" id="status-filter" onchange="$('#datatables').DataTable().draw()" style="display:inline; width:20%;">
                        <option value="">All</option>
                        <option selected value="0">Pending</option>
                        <option value="1">Approved</option>
                        <option value="2">Rejected</option>
                    </select>
                </div>
                <div class="col-md-6 text-left">
                    <a href="{{ route('approval-system.dashboard') }}" class="btn btn-default btn-fill btn-wd pull-right">Back</a>
                    <button type="button" class="btn btn-wd btn-success btn-fill btn-magnify pull-right advanceSearch" style="margin: 0px 5px;">
                        <span class="btn-label">
                            <i class="ti-search"></i>
                        </span>
                        Advance Search
                    </button>
                </div>
            </div>
            {!! searchFilter([
                 [
                    "db_name" => "approval_coi.proccess_id",
                    "label_name" => "Enbolt ID",
                    "type" => "text",
                    "values" => ""
                ], [
                    "db_name" => "basic_details.store_name",
                    "label_name" => "Store Name",
                    "type" => "text",
                    "values" => ""
                ], [
                    "db_name" => "basic_details.dc",
                    "label_name" => "DC",
                    "type" => "select",
                    "values" => $DCs
                ], [
                    "db_name" => "approval_coi.status",
                    "label_name" => "Status",
                    "type" => "select",
                    "values" => [
                        "" => "All",
                        "0" => "Pending",
                        "1" => "Approved",
                        "2" => "Rejected"
                    ]
                ],
            ]) !!}
            <div class="col-md-12">
                <div class="card">
                    <div class="card-content">
                        <div class="toolbar">
                            <!--Here you can write extra buttons/actions for the toolbar-->
                        </div>
                        <div class="fresh-datatables">
                            <table id="datatables" class="table table-striped table-no-bordered table-hover" cellspacing="0" width="100%" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>qry</th>
                                        <th>Type</th>
                                        <th>status</th>
                                        <th>Created_at</th>
                                        <th class="disabled-sorting">Action Taken</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>User</th>
                                        <th>qry</th>
                                        <th>Type</th>
                                        <th>status</th>                                        
                                        <th>Created_at</th>
                                        <th class="disabled-sorting">Action Taken</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@if(isset($agentList) && !empty($agentList) && $agentList->count() > 0)
    <div class="modal fade" id="selectSupervisorsModal" role="dialog">
        <div class="modal-dialog">
            <form id="selectSupervisorsForm" action="{{ route('approval-system.vendor-status', ['id' => '1', 'status' => '1']) }}" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Select Supervisors</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <select required="required" class="form-control selectpicker" name="supervisors_ids[]" data-style="btn-default btn-block" data-size="5" multiple="" data-live-search="true">
                                    @foreach($agentList as $agentKey => $agentDetail)
                                        <option value="{{ $agentKey }}" data-tokens="{{ $agentDetail }}">{{ $agentDetail }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
                {{ csrf_field() }}
            </form>
        </div>
    </div>
@endif
<div id="delivery-boy-approval-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="custom-width-modalLabel" aria-hidden="true" style="display: none;">
    <form action="" method="GET" class="approval-record-model">
        <div class="modal-dialog" style="width:55%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="custom-width-modalLabel">Approve record</h4>
                </div>
                <div class="modal-body">
                    <h4>Are You Sure, Want To Approve For This Record</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success waves-effect waves-light">Submit</button>
                </div>
            </div>
        </div>
    </form>
</div>
<div id="reject-bank-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="custom-width-modalLabel" aria-hidden="true" style="display: none;">
    <form action="" method="GET" class="reject-record-model" action="">
        <div class="modal-dialog" style="width:55%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="custom-width-modalLabel">Reject Record</h4>
                </div>
                <div class="modal-body">
                    <h4>Are You Sure, Want To Reject Changes For This Record</h4>
                    <div class="form-group">
                        <label>Remark</label>
                        <textarea required="required" class="form-control" name="remarks" placeholder="Comment" rows="3"></textarea>
                       
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger waves-effect waves-light">Reject</button>
                </div>
            </div>
        </div>
        {{ csrf_field() }}
    </form>
</div>
@endsection
@section('script')
<script type="text/javascript">
    $(document).ready(function() {
        function format(d) {
            return '<table><tr><td class="device-info-class">' + d.nature_of_deviation + '</td></tr></table>';
        }   
        // $("#selectSupervisorsModal").modal("show");
         var table = $('#datatables').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [],
            "ordering": false,
            "lengthMenu": [[10, 25, 50, 1000], [10, 25, 50, 1000]],
            ajax: {
                url: "{{ route('approval-system.get-emt-approval') }}",
                data: function(d) {
                    d.status_filter = $('#status-filter').val();
                    d.search_filter = $("form[name='search_filter']").serialize();
                }
            },
            createdRow: function( row, data) {
                if(data.status=='Approved'){
                    cell_class = 'approved-cell';
                }else if(data.status=='Rejected'){
                    cell_class = 'reject-cell';
                }else{
                    cell_class = 'pending-cell';
                }
                $( row ).find('td:first-child').addClass(cell_class+" "+data.status);
            },
            "columns": [
                {data:'user_name',name:'user_name'},
                {data:'qry',name:'qry'},
                {data:'type',name:'type'},
                {data:'status',name:'status'},                
                {data:'created_at',name:'created_at'},
                {data:'approvel_on',name:'approvel_on'}
                
            ],
            dom: "Blfrtip",
           buttons: [{
                           extend: "excel",
                           text: '<i title="Extract below data" class="fas fa-file-export"></i>',
                           className: "btn-class",
                           title: "{{ $pageTitle }}",
                           exportOptions: {
                               columns: ":not(.not-export)",
                           },
                           action: function ( e, dt, node, config ){
                             $("#lodder").removeClass("hide");
                             $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, node, config);  
                             setTimeout(function(){                      
                               $("#lodder").addClass("hide"); 
                             }, 2000);
                           }
                       }]
        });
        // modal pop-up with approve button or LMS ID textbox
        $("body").on("click", ".approval-record", function(event) {
            $this = $(this);
            var cm_id = $this.attr("cm_id");
            agents = JSON.parse('{!! json_encode($agents) !!}');
            dropdown = agents[cm_id];
            console.log(dropdown);
             $('#supervisor_id').html(''); 
            $.each(dropdown, function( index, value ) {
                          $('#supervisor_id').append('<option value="'+index+'">'+value+'</option>'); 
            });

          
                
                $("#supervisor_id").val("").selectpicker("refresh");

                
                
                      
        });
        /*$('#datatables tbody').on('click', 'td.details-control', function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);
            
                // This row is already open - close it
                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Open this row
                    row.child( format(row.data())).show();
                    tr.addClass('shown');
                }
            });*/
    });
</script>
@endsection
