<div class="pagination"><?php
  // build array containing links to all pages
  $tmp = [];
  for($p=1, $i=0; $i < $totalPages; $p++, $i++) {
    if($page == $p) {
      // current page shown as bold, no link
      $tmp[] = "<b>{$p}</b>";
    } else {
      if($p == 1) {
        $url = parse_url($_SERVER["REQUEST_URI"])["path"];
        $tmp[] = "<a href=\"$url\">{$p}</a>";
      } else {
        $tmp[] = "<a href=\"?page={$p}\">{$p}</a>";
      }
    }
  }

  $maxlinks = 10;
  $maxlinks = round($maxlinks/2);

  // thin out the links (optional)
  for($i = count($tmp) - 5; $i > 1; $i--) {
    if(abs($page - $i - 1) > 5) {
      unset($tmp[$i]);
    }
  }

  // display page navigation iff data covers more than one page
  if(count($tmp) > 1) {
    echo '<div class="c">';

    if($page > 1) {
      // display 'Prev' link
      echo "<a href=\"?page=" . ($page - 1) . "\">&laquo; Prev</a> | ";
    } else {
      echo "&laquo; Prev | ";
    }

    $lastlink = 0;
    foreach($tmp as $i => $link) {
      if($i > $lastlink + 1) {
        echo " ... "; // where one or more links have been omitted
      } elseif($i) {
        echo " | ";
      }
      echo $link;
      $lastlink = $i;
    }

    if($page <= $lastlink) {
      // display 'Next' link
      echo " | <a href=\"?page=" . ($page + 1) . "\">Next &raquo;</a>";
    } else {
      echo " | Next &raquo;";
    }

    echo "</div>\n\n";
  }
?></div>