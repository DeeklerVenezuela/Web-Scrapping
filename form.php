<?php
    $origin = array("Yellow Pages", "White Pages", "Hispanic Yellow Pages");
?>
<html>
    <head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
    </head>
    <body>
        <div class="row" style="top:200px; position:relative">
            <div class="col-md-6 col-lg-4 col-md-offset-3 col-lg-offset-3">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Importar Datos</h3>
                    </div>
                    <div class="panel-body">    
                        <form id="form" class="form-horizontal" action="webscrapping.php" method="POST">
                            <div class="form-group">
                                <label for="origin" class="col-md-4 col-lg-2 control-label">Origin</label>
                                <div class="col-md-8 col-lg-10">
                                    <select id="origin" class="form-control" name="origin">
                                        <?php foreach($origin as $id => $val){
                                                    echo "<option value='$id'>$val</option>";
                                        } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="firstname" class="col-md-4 col-lg-2 control-label">First Name</label>
                                <div class="col-md-8 col-lg-10">
                                    <input type="text" id="firstname" class="form-control" name="firstname" placeholder="John">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="lastname" class="col-md-4 col-lg-2 control-label">Last Name</label>
                                <div class="col-md-8 col-lg-10">
                                    <input type="text" id="lastname" class="form-control" name="lastname" placeholder="Doe">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="locality" class="col-md-4 col-lg-2 control-label">State</label>
                                <div class="col-md-8 col-lg-10">
                                    <input type="text" id="state" class="form-control" name="state" placeholder="CA">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="locality" class="col-md-4 col-lg-2 control-label">City</label>
                                <div class="col-md-8 col-lg-10">
                                    <input type="text" id="city" class="form-control" name="city" placeholder="Los Angeles">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="locality" class="col-md-4 col-lg-2 control-label">Zip Code</label>
                                <div class="col-md-8 col-lg-10">
                                    <input type="text" id="zipcode" class="form-control" name="zipcode" placeholder="92103">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="limit" class="col-md-4 col-lg-2 control-label">Pages</label>
                                <div class="col-md-8 col-lg-10">
                                    <select id="limit" class="form-control" name="limit">
                                        <?php for($i = 1; $i < 101; $i = ($i * 2)){
                                                echo "<option value='$i'>$i</option>";
                                        } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1 col-lg-1 col-md-offset-10 col-lg-offset-10">
                                <button class="btn btn-primary right" type="submit">Send</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
        <script>
            $("#downloadCSV").on("click", function(){
                //$('#wait-animation').show();
                //$('#wait-animation').hide();
                /*console.log('clicked');*/
                /*$.ajax({
                    method: "POST",
                    url: "webscrapping.php",
                    data: $('#form').serialize(),
                    dataType: "html"
                });*/
            });
        </script>
    </body>
</html>
