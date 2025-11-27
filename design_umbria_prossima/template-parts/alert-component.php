<?php
if (!empty($args) && is_array($args)) {
    $active   = $args['active'] ?? null;
    $type   = $args['type'] ?? null;
    $message   = $args['message'] ?? null;

    if($active == 'on'){?>
      <div class="alert alert-<?php echo $type ?> h5 my-0" role="alert" style="width: 100%;">
         <?php echo $message;?>
      </div>
    <?php
    }
}