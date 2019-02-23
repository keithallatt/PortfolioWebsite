<!DOCTYPE html>
<html>
<head>
  <title> Keith Allatt's Programming Portfolio </title>
  <meta charset="utf-8">
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">

  <!-- jQuery library -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

  <!-- Latest compiled JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>

  <meta name="viewport" content="width=device-width, initial-scale=1">

  <style>
    body {
      background-color: #dddddd;
    }

    .container {
      background-color: #ffffff;
    }

    .dot {
      height: 15px;
      width: 15px;
      background-color: #b00;
      border-radius: 50%;
      display: inline-block;
    }
  </style>
</head>
<body>

<!-- For navbar -->
<br />
<br />
<br />

<div class="container">
  <h1> Keith Allatt's Programming Portfolio </h1>
  <p>
    Using GitHub API and Bootstrap to bring my work to life. This website loads
    information from my GitHub account (https://github.com/keithallatt) and
    displays some documentation for each project.
  </p>
</div>
<br />


<?php
// can't be refreshed a lot, will cause the api to not respond
// create a 'cache' to store the json so if the api isn't responding
// then it'll use the cache.
$cache_dir = "cache/";
if (!file_exists($cache_dir)) {
  mkdir($cache_dir, 0777, true);
}

// https://michelf.ca/projects/php-markdown/
require_once 'Michelf/Markdown.inc.php';
use Michelf\Markdown;


// fakes context to be able to scrape the web page
$context = stream_context_create(
    array(
        "http" => array(
            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
        )
    )
);

// for finding which repos exist
$api_url = "https://api.github.com/users/keithallatt/repos";
// for finding stats about a repo
$project_url = "https://api.github.com/repos/keithallatt/";
$html_url_1 = "https://raw.githubusercontent.com/keithallatt/";
$html_url_2 = "/master/README.md";

$payload = file_get_contents($api_url, false, $context);

/////////////////////////////////////
// fail returns false.
$got_request = $payload != false;
if ($got_request) {
  // got the request, can save to cache
  $location = $cache_dir . "keithallatt.json";

  $handle = fopen($location, 'w');

  if ($handle == false) {
    echo "Error writing";
  } else {
    fwrite($handle, $payload);
  }

} else {
  print_r("Reading from cache");
  // didn't get the request, load from cache
  $location = $cache_dir . "keithallatt.json";

  $handle = fopen($location, 'r');

  if ($handle == false) {
    echo "Error reading";
  } else {
    $payload = file_get_contents($location, false, $context);
  }
}
/////////////////////////////////////

// contains json
$ar = json_decode($payload);

print_r("<nav class=\"navbar navbar-inverse navbar-fixed-top\">");
print_r("<div class=\"container-fluid\"><div class=\"navbar-header\">");
print_r("<a class=\"navbar-brand\" href=\"#\">Portfolio</a>");
print_r("</div><ul class=\"nav navbar-nav\">");

foreach ($ar as &$value) {
  $name = $value->name;
  print_r("<li><a href=\"#" . $name . "_anchor\">" . $name . "</a></li>");
}
print_r("</ul></div></nav>");

// add more as it comes.
$language_colors = array(
    "Python" => "#1c2a96",
    "Java" => "#c18d34",
    "C#" => "#207721",
);

// prints out each name
foreach ($ar as &$value) {
    $name = $value->name;
    $full_url = $html_url_1 . $name . $html_url_2;

    $this_repo_stats = $project_url . $name;

    $this_stats_contents = file_get_contents($this_repo_stats, false, $context);

    /////////////////////////////////////
    // fail returns false.
    $got_request = $this_stats_contents != false;
    if ($got_request) {
      // got the request, can save to cache
      $location = $cache_dir . $name . ".json";

      $handle = fopen($location, 'w');

      if ($handle == false) {
        echo "Error writing";
      } else {
        fwrite($handle, $this_stats_contents);
      }

    } else {
      print_r("Reading from cache");
      // didn't get the request, load from cache
      $location = $cache_dir . $name . ".json";

      $handle = fopen($location, 'r');

      if ($handle == false) {
        echo "Error reading";
      } else {
        $this_stats_contents = file_get_contents($location, false, $context);
      }
    }
    /////////////////////////////////////

    $this_stats = json_decode($this_stats_contents);


    $project_language = $this_stats->language;
    $project_link = $this_stats->html_url;
    $project_created = $this_stats->created_at;

    print_r("<div class=\"container\"> <h2 id=\"" . $name . "_anchor\" style=\"padding-top: 80px; margin-top: -40px;\">" . $name . "</h2>\n");


    $background_color = $language_colors[$project_language];

    print_r("<span class=\"dot\" style=\"background-color: ". $background_color .";\"></span>&nbsp;&nbsp;&nbsp;&nbsp;");

    print_r($project_language . "&nbsp;&nbsp;&nbsp;&nbsp;");
    print_r($project_link . "&nbsp;&nbsp;&nbsp;&nbsp;");
    print_r("Created: " . $project_created . "&nbsp;&nbsp;&nbsp;&nbsp;");



    print_r("<br />");
    print_r("<button type=\"button\" class=\"btn btn-info\" data-toggle=\"collapse\" data-target=\"#" . $name . "\">See more..</button>\n");
    print_r("<div id=\"" . $name . "\" class=\"collapse\"> <br />");

    $file_contents = file_get_contents($full_url, false, $context);

    print_r(Markdown::defaultTransform($file_contents));

    print_r("<br /></div><br /> <br /> </div><br />");

    print_r("<br />");
    print_r("<br />");
}




?>
</body>
</html>
