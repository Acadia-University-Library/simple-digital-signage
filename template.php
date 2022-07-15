<?php
/*******************************************************************************
********************************************************************************
** CONFIGURATION (Refer to README.md)
********************************************************************************
*******************************************************************************/

define('MEDIA_DIRECTORY', './media');
define('MEDIA_CACHE', 'media_cache.txt');
define('MEDIA_CACHE_TIMEOUT_SECONDS', 900);
define('DISPLAY_REFRESH_SECONDS', 30);
define('DISPLAY_CSS', 'template.css');
define('DISPLAY_HEAD', 'template_head.html');
define('DISPLAY_TITLE', 'Simple Digital Signage');
define('DISPLAY_LANGUAGE', 'en');
define('DISPLAY_CHARACTER_SET', 'utf-8');



/*******************************************************************************
********************************************************************************
** DO NOT MODIFY CODE BELOW THIS POINT
********************************************************************************
*******************************************************************************/

$base_url = ((array_key_exists('HTTPS', $_SERVER) && strcasecmp($_SERVER['HTTPS'], 'on')) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];

if(array_key_exists('id', $_GET) && is_numeric($_GET['id'])) {
  $id = $_GET['id'];
}
else {
  $id = 999;
}

$media_glob = glob(MEDIA_DIRECTORY . '/*.*');
asort($media_glob, SORT_REGULAR);

if(file_exists(MEDIA_CACHE) && ((time() - filemtime(MEDIA_CACHE)) < MEDIA_CACHE_TIMEOUT_SECONDS)) {
  $media = unserialize(file_get_contents(MEDIA_CACHE));
}

if(empty($media) || !is_array($media) || $id < 0 || $id >= 999) {
  $media = array();
  for($k = 0; $k < count($media_glob); $k++) {
    $media_basename = explode('.', basename($media_glob[$k]));
    $media_refresh = DISPLAY_REFRESH_SECONDS;
    if(count($media_basename) > 1) {
      $media_type = $media_basename[count($media_basename)-1];
      if(count($media_basename) > 2 && is_numeric($media_basename[count($media_basename)-2])) {
        $media_refresh = $media_basename[count($media_basename)-2]; 
      }
      $media[] = array(
        'path' => $media_glob[$k], 
        'refresh' => $media_refresh, 
        'type' => $media_type,
        'a_href' => '<a href="?id=' . $k . '">' . $_SERVER['SCRIPT_NAME'] . '?id=' . $k . '</a>'
      );
    }
  
    file_put_contents(MEDIA_CACHE, serialize($media));
  }
}

if($id < -1 || $id >= count($media)) {
  $id = 0;
}
if($id < 0) {
  $display_content = $media;
  $display_content['type'] = 'index';
  $runtime = 0;
  for($k = 0; $k < count($media); $k++) {
    $runtime += $media[$k]['refresh'];
  }
  $display_content['runtime'] = floor($runtime / 3600) . 'h ' . floor($runtime / 60) . 'm ' . ($runtime % 60) . 's';
}
else {
  $display_content = $media[$id];
  if(file_exists($display_content['path'])) {
    header('refresh: ' . $display_content['refresh'] . '; url=' . $base_url . $_SERVER['SCRIPT_NAME'] . '?id=' . ++$id);
  }
  else {
    header('refresh: 0; url=' . $base_url . $_SERVER['SCRIPT_NAME'] . '?id=999');
  }
}

?>
<!DOCTYPE html>
<html lang="<?= DISPLAY_LANGUAGE ?>">
  <head>
    <title><?= DISPLAY_TITLE ?></title>
    <meta charset="<?= DISPLAY_CHARACTER_SET ?>">
    <link rel="stylesheet" type="text/css" href="<?= DISPLAY_CSS ?>">
    <?php @include(DISPLAY_HEAD); ?>
  </head>
  <body>
    <div id="wrapper" class="<?= $display_content['type']; ?>">
    <?php
      switch($display_content['type']) {
        case 'gif':
        case 'jpg':
        case 'png':
          echo '<img src="' . $display_content['path'] . '">';
          break;
        case 'html':
        case 'php':
          include($display_content['path']);
          break;
        case 'txt':
          echo '<div class="container"><pre>' . file_get_contents($display_content['path']) . '</pre></div>';
          break;
        case 'url':
          echo '<iframe src="' . file_get_contents($display_content['path']) . '" frameborder="0"></iframe>';
          break;
        case 'youtube':
          echo '<iframe 
            src="https://www.youtube-nocookie.com/embed/' . file_get_contents($display_content['path']) . '?rel=0&controls=0&showinfo=0&autoplay=1&cc_load_policy=1" 
            frameborder="0" allow="autoplay; encrypted-media"></iframe>';
          break;
        default:
          echo '<div class="container"><pre>' . print_r($display_content, true) . '</pre></div>';
          break;
      }
    ?>
    </div>
  </body>
</html>