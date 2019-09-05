<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Feedback faces</title>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/page2.css'); ?>" />
</head>

<body>
    <style>
        p.top-text {
            font-size: 75px;
            /* font-family: 'redressed'; */
        }
        
        .emoji-list li {
            padding: 0 18px;
            min-height: 200px;
        }
        
        .emoji-text {
            font-size: 35px;
            font-weight: 700;
            font-family: sans-serif;
        }
        
        .btn.btn-success {
            padding: 10px 27px;
            font-size: 25px;
        }
        
        p.letter {
            font-size: 75px;
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
        
        p.text-center {
            font-size: 40px;
        }
    </style>
    <!-- partial:index.partial.html -->
    <div class="">
        <div class="row">
            <div class="col-md-12 text-center">
                <p class="text-center" style="font-size:65px;">Feedback Already Submitted.</p>
                <p class="text-center">Regards From Telma AM & Telma AMH</p>
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
                console.log(rate);
            });
        })(jQuery)
    </script>

</body>

</html>