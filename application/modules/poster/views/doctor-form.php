<html>
<head>
    <title>Doctor Customization Form</title>
    <link type="text/css" rel="stylesheet" media="screen" href="<?= base_url('assets/components/image-area-select/css/style.css') ?>" />
    <link type="text/css" rel="stylesheet" media="screen" href="<?= base_url('assets/components/image-area-select/css/custom-file.css') ?>" />

    <style>
        .form-container {
            width: 98%;
            text-align:center;
        }
        form {
            width: 50%;
            margin: 100px auto;

        }
        div {
            margin-bottom: 30px;
        }
        div label {
            display:block;
            margin-bottom:10px
        }
        div input {
            width: 50%;
            padding: 5px;
        }
    </style>
    <script>var baseUrl = '<?= base_url(); ?>'</script>
</head>
<body>
    <div class="form-container">
        <?php echo form_open("poster/save",array('id'=>'updateProfilePhoto')); ?>
        <div class="form-group">
            <label for="doctor_name">Doctor Name <span class="required">*</span></label>
            <div><input type="text" id="doctor_name" name="doctor_name" autocomplete="off"></div>
        </div>

        <div class="form-group">
            <label for="doctor_photo">Doctor Photo <span class="required">*</span></label>
            <div style="position:relative">
                <div>
                    <div class="loadpicture"></div>
                    <input type="file" id="doctor_photo" name="doctor_photo" class="uploadphoto" style="width: 250px;height: 250px">
                    <input type="hidden" name="x1" value="" />
                    <input type="hidden" name="y1" value="" />
                    <input type="hidden" name="x2" value="" />
                    <input type="hidden" name="y2" value="" />
                </div>

                <div class="preview" style="text-align: center">
                    <div class="loadpicture"></div>
                    <img style="width:100%"  src="https://chrozon.com/uploads/users/2560/thumbs/899e6286f6e58ffd07046018944ec3ee.jpg" id="selectArea">
                    <input type="hidden" name="imageName" id="imageName" />
                </div>
            </div>
        </div>

        <div class="cropcontainer">
            <button type="button" class="actionbtn changebutton" title="Upload a new profile photo">Change</button>	
            <button type="button" class="actionbtn cropbutton" title="Crop & Save selection">Crop & Save</button>
        </div>

        <?php // $this->load->view('template/components/form-actions'); ?>
        <?php echo form_close(); ?>
    </div>
    <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="<?= base_url('assets/components/image-area-select/js/main.js') ?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/components/image-area-select/js/imgOnLoad.js') ?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/js/custom-script.js') ?>"></script>
</body>
</html>