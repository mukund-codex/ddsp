<?php echo form_open("$controller/search",array('id'=>'collectinfo')); ?>
<div class="search-bar">
    <div class="search-icon">
        <i class="material-icons">search</i>
    </div>
    <input type="text" name="keywords" placeholder="START TYPING..." autocomplete="off">
    <div class="close-search">
        <i class="material-icons">close</i>
    </div>
</div>
<?php echo form_close(); ?>