<!DOCTYPE html>
<html lang="en">
<head>
    <title>Enbolt MySQL Tool</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css" rel="stylesheet" />
    <!--  Plugin for DataTables.net  -->
    <script src="{{asset('/adminTheme/js/jquery.datatables.js') }}"></script>
    <style type="text/css">
        .text-enbolt{
            color: #2D44AC;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row" style="margin-top:10px;">
            <div class="col-md-8">
                <h2 class="text-enbolt" style="margin-top:0px;"><span class="text-enbolt">E</span>nbolt <span class="text-enbolt">M</span>ySQL <span class="text-enbolt">T</span>ool</h2>
            </div>
            <div class="col-md-4">
                <div class="graph pull-right">
                    <a href="{{route('emt.index')}}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <form method="post" action="{{route('emt.run')}}">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="comment">Result:</label>
                @if($type!='select'|| ($format == 'Array' || $format == 'Json'))
                    <?php
                        echo '<pre>';
                        print_r($data);
                    ?>
                @else
                    <div class="row">
                        <div class="col-md-12">
                            <hr>
                            <div class="">
                                @php
                                    $columnArray = (array) json_decode(json_encode($data[0]));
                                    $columnList = array_keys($columnArray);
                                    $columnList = array_combine($columnList, $columnList);
                                @endphp
                                <form class="search-filter-form" name="search_filter">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {!! Form::select('where[0][col]',$columnList,null,['class'=>'form-control','id'=>'column-name']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <select name="where[0][op]" class="form-control" id="op">
                                                    <option>=</option>
                                                    <option>&lt;</option>
                                                    <option>&gt;</option>
                                                    <option>&lt;=</option>
                                                    <option>&gt;=</option>
                                                    <option>!=</option>
                                                    <option>LIKE</option>
                                                    <option>LIKE %%</option>
                                                    <option>IN</option>
                                                    <option>IS NULL</option>
                                                    <option>NOT LIKE</option>
                                                    <option>NOT IN</option>
                                                    <option>IS NOT NULL</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {!! Form::text('where[0][op]',null,array('class'=>'form-control','id'=>'search-value')) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <button class="btn btn-info btn-sm custom-search-button">Search</button>
                                                <button class="btn btn-danger btn-sm search-reset-button">Reset</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <table id="datatables" class="table table-striped table-no-bordered table-hover">
                                    <thead>
                                        <tr>
                                            @foreach($data[0] as $key => $value)
                                                <th>{{$key}}</th>
                                            @endforeach
                                            <!-- <th>Actions</th> -->
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            @foreach($data[0] as $key => $value)
                                                <th>{{$key}}</th>
                                            @endforeach
                                            <!-- <th>Actions</th> -->
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                  @endif
            </div>
            <div class="form-group">
              <a href="{{route('emt.index')}}" class="btn btn-primary">Back</a>
            </div>
        </form>
    </div>
</body>
<script type="text/javascript">
    $(document).ready(function() {
        var columnList = <?php echo json_encode($data[0]); ?>;
        var qry = '<?php echo $qry; ?>';
        aoColumns = [];
        $.each(columnList,function(key,value){
            var column = { 
               data: key,
               name: key,
            };
            aoColumns.push(column);
        });
        $('#datatables').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            "pagingType": "full_numbers",
            "lengthMenu": [[10, 25, 50, 1000], [10, 25, 50, 1000]],
            "order": [[ 0, "desc" ]],
            orderable:true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search records",
            },
            ajax: {
                url: "{{ route('emt.getQueryData') }}",
                data: function(d) {
                    d.qry = qry;
                    d.column = $("#column-name").val();
                    d.op = $("#op").val();
                    d.value = $("#search-value").val();
                }
            },
            "columns": aoColumns
        });
        $('.search-reset-button').click(function(e){
            e.preventDefault();
            $("#search-value").val('');
            $('#datatables').DataTable().draw(true);
        });
        $('.custom-search-button').click(function(e){
            e.preventDefault();
            $('#datatables').DataTable().draw(true);
        });
    })
</script>
</html>
