  <head>
    <link href="http://vjs.zencdn.net/5.4.4/video-js.css" rel="stylesheet">

    <!-- If you'd like to support IE8 -->
    <script src="http://vjs.zencdn.net/ie8/1.1.1/videojs-ie8.min.js"></script>
  </head>

  <body><?php

    require( 'include.php' );

    function show_player( $video, $poster ) {
      $ext = pathinfo($video, PATHINFO_EXTENSION);?>
      <video id="my-video" class="video-js" controls preload="auto" width="640" height="264" poster="<?php echo TRAPI_CACHE_URL . $poster; ?>" data-setup="{}">
        <source src="<?php echo TRAPI_CACHE_URL . $video; ?>"  type="video/<?php echo $ext; ?>"/>
      </video><?php
    }

    $video = isset($_GET['video']) ? $_GET['video'] : null;
    $poster = isset($_GET['poster']) ? $_GET['poster'] : null;

    if( ! empty($video) && ! empty($poster) )
      show_player( $video, $poster); 
    ?>

    <script src="http://vjs.zencdn.net/5.4.4/video.js"></script>
  </body>
