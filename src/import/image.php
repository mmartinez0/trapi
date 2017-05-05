  <head>
  </head>

  <body><?php

    require( 'include.php' );

    $src = isset($_GET['src']) ? $_GET['src'] : null;

    if( ! empty($src) ) {?>
      <img src="<?php echo TRAPI_CACHE_URL . $src; ?>"/><?php
    }?>

  </body>
