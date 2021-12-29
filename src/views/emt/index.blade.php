<!DOCTYPE html>
<html lang="en">
<head>
  <title>Opaper</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css" >
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script> 
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script> 
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script> 
    <style type="text/css">
        .dataTables_filter{
            /*text-align: right;*/
        }
        #wrapper {
    padding-left: 0;
    -webkit-transition: all 0.5s ease;
    -moz-transition: all 0.5s ease;
    -o-transition: all 0.5s ease;
    transition: all 0.5s ease;
}

#wrapper.toggled {
    padding-left: 250px;
}

#sidebar-wrapper {
    z-index: 1000;
    position: fixed;
    left: 250px;
    width: 0;
    height: 100%;
    margin-left: -250px;
    overflow-y: auto;
    background: #fff;
    -webkit-transition: all 0.5s ease;
    -moz-transition: all 0.5s ease;
    -o-transition: all 0.5s ease;
    transition: all 0.5s ease;
}

#wrapper.toggled #sidebar-wrapper {
    width: 250px;
}

#page-content-wrapper {
    width: 100%;
    position: absolute;
    padding: 15px;
}

#wrapper.toggled #page-content-wrapper {
    position: absolute;
    margin-right: -250px;
}

/* Sidebar Styles */

.sidebar-nav {
    position: absolute;
    top: 0;
    width: 305px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.sidebar-nav li {
    text-indent: 5px;
}

.sidebar-nav li a {
    display: block;
    text-decoration: none;
    color: blue;
}

.sidebar-nav li a span:hover {
    text-decoration: none;
    color: red;
    background: rgba(255,255,255,0.2);
}

.sidebar-nav li a:active,
.sidebar-nav li a:focus {
    text-decoration: none;
}

.sidebar-nav > .sidebar-brand {
    font-size: 25px;
    line-height: 40px;
    text-align: center;
}

.sidebar-nav > .sidebar-brand a {
    color: red;
    position: relative;
    top: 10px;
}

.sidebar-nav > .sidebar-brand a:hover {
    color: red;
    background: none;
}
.column-list{
    padding-left: 30px;
}
.column-list li{
    list-style-type: none;
    font-size: 12px;
}
@media(min-width:768px) {
    #wrapper {
        padding-left: 330px;
    }

    #wrapper.toggled {
        padding-left: 0;
    }

    #sidebar-wrapper {
        width: 325px;
    }

    #wrapper.toggled #sidebar-wrapper {
        width: 0;
    }

    #page-content-wrapper {
        padding: 20px;
        position: relative;
    }

    #wrapper.toggled #page-content-wrapper {
        position: relative;
        margin-right: 0;
    }
    .main-content{
        padding: 10px;
    }
    .display-none{
        display: none;
    }
}
    </style>
    <script type="text/javascript">
        
    </script>
</head>
<body>

<div class="container-fluid" id="wrapper">
    <div id="sidebar-wrapper">
        <ul class="sidebar-nav">
            <li class="sidebar-brand">
                <a href="#">
                    Enbolt
                </a>
            </li>
            <hr>
            @php $tableNameInDBName = 'Tables_in_'.env('DB_DATABASE'); @endphp
            @foreach ($tablesList as $key => $value)
                <li>
                    <a href="#"><span class="column-list-open"><i class="fa fa-plus"></i></span> <span class="table-name">{{$value->$tableNameInDBName}}</span> <span class="select-query-span" table-name="{{$value->$tableNameInDBName}}">select</span></a>
                    <ul class="column-list display-none">
                        @php
                            $columnList = \Schema::getColumnListing($value->$tableNameInDBName);
                        @endphp
                        @foreach($columnList as $column)
                            <li>{{$column}}</li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>
    </div>
    <div class="row main-content">
        <div class="col-md-12">
          <h2>Enbolt MySQL Tool</h2>
          <p>The form below contains a textarea for query:</p>
          <form method="post" action="{{route('emt.run')}}">
            {{ csrf_field() }}

            <div class="form-group">
              <label for="comment">query:</label>
              <textarea class="form-control" name="qry" required="required" rows="5" id="comment">{{ old('qry') }}</textarea>
              <span class="text-danger">
                  @if(\Session::has('error'))
                    {{\Session::get('error')}}
                    @php \Session::forget('error') @endphp
                  @endif
              </span>
            </div>
            {{--<div class="form-group">
              <label for="comment">format:</label>
              <select class="form-control" name="format" id="format">
                  <option value="Array">Array</option>
                  <option value="Json">Json</option>
              </select>      
            </div>--}}
            <div class="form-group">
              <button type="submit" name="submit" value="SUBMIT" class="btn btn-default">Execute</button>
              <button type="submit" name="submit" value="DOWNLOAD" class="btn btn-default">Download</button>
            </div>
          </form>
            <div class="row">
                <div class="col-md-12">
                    <hr>
                    <div class="fresh-datatables">
                        <table id="datatables" class="table table-striped table-no-bordered table-hover" cellspacing="0" width="100%" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Query</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Approved By</th>
                                    <th>Approved At</th>
                                    <th>Remark</th>
                                    <th>Query Count</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>#</th>
                                    <th>Query</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Approved By</th>
                                    <th>Approved At</th>
                                    <th>Remark</th>
                                    <th>Query Count</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
<script type="text/javascript">
    $(document).ready(function() {

        $('#datatables').DataTable({
            processing: true,
            serverSide: true,
            "pagingType": "full_numbers",
            "lengthMenu": [[10, 25, 50, 1000], [10, 25, 50, 1000]],
            "order": [[ 8, "desc" ]],
            responsive: true,
            orderable:true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search records",
            },
            ajax: {
                url: "{{ route('emt.getApprovalList') }}"
            },
            "columns": [
                {data:'id',name:'id'},
                {data:'qry',name:'qry'},
                {data:'type',name:'type'},
                {data:'status',name:'status'},
                {data:'approved_by',name:'approved_by'},
                {data:'approved_at',name:'approved_at'},
                {data:'remarks',name:'remarks'},
                {data:'qry_count',name:'qry_count'},
                {data:'created_at',name:'created_at'},
                {data:'action',name:'action',orderable:false,searchable:false},
            ]
        });
    });
    $("body").on('click','.table-name',function(e) {
        e.preventDefault();
        query = $('#comment').val();
        tableName = $(this).text();
        query = query+tableName;
        $('#comment').val(query);

    });
    $("body").on('click','.column-list-open',function(e) {
        var columnSpan = $(this).parent().parent();
        columnSpan.find('.column-list').toggle();
    });
    $("body").on('click','.select-query-span',function(e) {
        var tableName = $(this).attr('table-name');
        query = 'select * from '+tableName;
        $('#comment').val(query);
    });
</script>
</html>
