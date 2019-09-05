<!DOCTYPE html>
<html lang="en">

<head>
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Feedback Face</title>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>" />

    <script>
        function preventBack(){window.history.forward();}
        setTimeout("preventBack()", 0);
        window.onunload=function(){null};
    </script>
</head>

<body>
    <style>
        p.top-text {
            font-size: 1.5rem;
            margin-top: 5em;
        }
        
        .emoji-list li {
            padding: 0 18px;
            min-height: 200px;
        }
        
        .emoji-text {
            font-size: 1.5rem;
            font-weight: 700;
            font-family: sans-serif;
        }
        
        .btn.btn-success {
            padding: 10px 27px;
            font-size: 25px;
            width:100%;
        }
        
        p.letter {
            font-size: 1.5rem;
            display: inline;
            padding: 40px 75px;
            font-weight: 900;
            transition: 0.1s;
            transition-timing-function: ease-in-out;
        }
        
        p.letter:hover {
            background: #e5e5e5;
        }
        
        p.letter.selected {
            background: #e5e5e5;
            border: 1px solid black;
        }
    </style>
    <!-- partial:index.partial.html -->
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center mt-5">
                <p class="top-text">Would you like to have full text of the daily alerts?</p>
            </div>
            <div class="col-6 text-center mt-5">
                <p class="letter">Y</p>
            </div>
            <div class="col-6 text-center mt-5">
                <p class="letter">N</p>
            </div>
            <div class="col-md-12 text-center mt-5">
                <?php $attrubiutes = array('id' => 'addForm');
                echo form_open('feedback/save', $attrubiutes); ?>
                    <input type="email" name="email_id" class="form-control" autocomplete="off" maxlength="100" id="email" placeholder = "Email ID" style="display:none;">
                    <input type="hidden" name="doctor_id" id="doctor_id" value="<?php echo $doctor_id; ?>" >
                    <input type="hidden" name="answer" id="answer" value="" />
                    <input type="hidden" name="question" id="question" value="question3" >
                <?php echo form_close(); ?>
            </div>
            <div class="col-md-12 text-center mt-3" id="next" style="display:none;">
                <button class="btn btn-success" name="next" id="next-button" onclick="next();">Submit</button>
                <!-- <a href="<?php echo base_url('feedback/thank_you/') ?>" class="btn btn-success"> Submit </a> -->
            </div>
        </div>
    </div>
    <!-- partial -->
    <script src="<?php echo base_url('assets/js/bootstrap.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/jquery-3.3.1.min.js'); ?>"></script>

    <script>
        (function($) {
            $('p.letter').on('click', function() {
                $('p.letter').removeClass('selected');
                $(this).addClass('selected');
                var rate = $(this).text();
                if(rate == 'Y'){
                    $("#email").show();
                }else{
                    $("#email").val('');
                    $("#email").hide();
                    $('label.error').hide();
                }
                $("#answer").val(rate);
                $("#next").show();
            });
        })(jQuery)

        function next(){
            var answer = $("#answer").val;
            if(answer == ''){
                location.reload();
            }

            var formObj = $('#addForm');
            $.each(formObj.find('input, select, textarea'), function (i, field) {
                var elem = $('[name="' + field.name + '"]');

                elem.removeClass('error')
                    .next('label.error').remove();
            });


            var url = $('#addForm').attr('action');
            $.ajax({
                type: "POST",
                url: url, 
                data: $('#addForm').serialize(),
                dataType: "json",  
                cache:false,
                success: function (response) {
                    if (response.status) {
					    window.location.href = (response.redirectTo) ? response.redirectTo : '';
                    }
                    if (response.errors) {
                            $.each(response.errors, function (key, val) {
                                var elem = $('[name="' + key + '"]', $('#addForm'));
                                
                                elem.removeClass('error')
                                    .next('label.error').remove()
                                    .end()
                                    .addClass('error').after(val);
                                

                            });

                        }
                }
            });
        }
        
    </script>

</body>

</html>