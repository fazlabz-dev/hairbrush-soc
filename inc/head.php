<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta http-equiv="Content-Language" content="en-us" />
  <title>Hairbrush Social</title>
  <link href="/assets/main.css?<?=time()?>" media="screen, projection" rel="stylesheet" type="text/css" />
  <link rel="shortcut icon" href="/images/hairbrushfavicon.png" type="image/png" />
  <link rel="stylesheet" href="/assets/front.css" />
  <link href="https://pro.fontawesome.com/releases/v6.0.0-beta1/css/all.css" rel="stylesheet" type="text/css" />
  <script src="/assets/main.js?<?=time()?>"></script>
  <script src="https://twemoji.maxcdn.com/v/latest/twemoji.min.js" crossorigin="anonymous"></script>
  <?php if(isset($_SINGLE) && $_SINGLE) { ?>
  <meta property="og:title" content="<?=$_PROFILE["username"]?> (@<?=$_PROFILE["username"]?>)">
  <meta property="og:url" content="/<?=$_PROFILE["username"]?>/statuses/<?=$_STATUS["id"]?>">
  <meta property="og:description" content="<?=htmlentities($_STATUS["content"], ENT_QUOTES)?>">
  <meta content="#1DA1F2" data-react-helmet="true" name="theme-color" />
  <?php } ?>
</head>