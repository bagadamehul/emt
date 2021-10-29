<!DOCTYPE html>
<html lang="en">
<head>
  <title>Enbolt MySQL Tool</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
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
    </div>

    <div class="form-group">
      <a href="{{route('emt.index')}}"><button type="button" class="btn btn-default">Back</button></a>
    </div>
  </form>
</div>

</body>
</html>
