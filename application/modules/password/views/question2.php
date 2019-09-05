<!DOCTYPE html>
<html>

<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Feedback Face</title>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>" />

</head>

<body>
    <style>
        .emoji-list {
            list-style-type: none;
            display: flex;
            margin-left: -45px;
            margin-top: 4em;
        }
        
        p.top-text {
            font-size: 1.5rem;
            margin-top: 3em;
        }
        
        .emoji-list li {
            padding: 0 18px;
            min-height: 200px;
        }
        .emoji-list li img{
            max-width:100%;
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
    </style>
    <!-- partial:index.partial.html -->
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center mt-5">
                <p class="top-text">Kindly rate us on quality of science information?</p>
            </div>
            <div class="">
                <ul class="emoji-list">
                    <li class="emoji" data-rate="1" data-emoji="Terrible"><img src="<?php echo base_url('assets/images/terrible.png'); ?>"></li>
                    <li class="emoji" data-rate="2" data-emoji="Bad"><img src="<?php echo base_url('assets/images/bad.png'); ?>"></li>
                    <li class="emoji" data-rate="3" data-emoji="Okay"><img src="<?php echo base_url('assets/images/okay.png'); ?>"></li>
                    <li class="emoji" data-rate="4" data-emoji="Good"><img src="<?php echo base_url('assets/images/good.png'); ?>"></li>
                    <li class="emoji" data-rate="5" data-emoji="Great"><img src="<?php echo base_url('assets/images/great.png'); ?>"></li>
                </ul>
                <h3 class="emoji-text text-center mt-2"></h3>
            </div>
            <?php $attrubiutes = array('id' => 'addForm');
                echo form_open('feedback/save', $attrubiutes); ?>
                    <input type="hidden" name="doctor_id" id="doctor_id" value="<?php echo $doctor_id; ?>" >
                    <input type="hidden" name="answer" id="answer" value="" />
                    <input type="hidden" name="question" id="question" value="question2" >
                <?php echo form_close(); ?>
            <div class="col-md-12" id="next" style="display:none;">
                <button class="btn btn-success" name="next" id="next-button" onclick="next();">Next</button>
               <!--  <a href="<?php echo base_url('feedback/question3/') ?>" class="btn btn-success"> Next </a> -->
            </div>
        </div>
    </div>
    <!-- partial -->
    <script src="<?php echo base_url('assets/js/bootstrap.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/jquery-3.3.1.min.js'); ?>"></script>

    <script>
        (function($) {
            $('li.emoji').on('click', function() {
                var rate = $(this).attr('data-rate');
                var emoji = $(this).attr('data-emoji');
                $('li.emoji').css('transform', 'scale(1)');
                $(this).css('transform', 'scale(1.3)');
                $('.emoji-text').html('');
                $('.emoji-text').html(emoji);
                $("#answer").val(rate);
                $("#next").show();
            });
        })(jQuery)

        function next(){
            var answer = $("#answer").val;
            if(answer == ''){
                location.reload();
            }
            var url = $('#addForm').attr('action');
            console.log(url);
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
                }
            });
        }
    </script>

</body>

</html>