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
</head>
<body>

<div class="container">
  <h2>Enbolt MySQL Tool</h2>
  <p>The form below contains a textarea for query:</p>
  <form method="post" action="{{route('emt.run')}}">
    {{ csrf_field() }}

    
    
    <div class="form-group">
      <a href="{{route('emt.index')}}"><button type="button" class="btn btn-default">Back</button></a>
    </div>

    <div class="form-group">
      <label for="comment">Result:</label>
      @if($type!='select')
      <?php 
          if($format == 'Json'){
              echo '<pre>';
              echo $data;
              echo '</pre>';
          }
          if($format == 'Array'){
              echo '<pre>';
              print_r($data);
          }
      ?>
      @else
        <div class="row">
            <div class="col-md-12">
                <hr>
                <div class="">
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
      <a href="{{route('emt.index')}}"><button type="button" class="btn btn-default">Back</button></a>
    </div>
  </form>
</div>

</body>
<script type="text/javascript">
    $(document).ready(function() {
        var columnList = <?php echo json_encode($data[0]); ?>;
        console.log(columnList);
        var qry = '<?php echo $qry; ?>';
        aoColumns = [];
        $.each(columnList,function(key,value){
            var column = { 
               data: key,
               name: key,
            };
            aoColumns.push(column);
        });
        console.log(aoColumns);
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
                }
            },
            "columns": aoColumns
        });
    })
</script>
</html>
