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
			background-color: #dedded;
		}

		.container {
			background-color: #fdfdfd;
		}

		.jumbotron {
			background-color: #abaaba;
		}

		.dot {
			height: 15px;
			width: 15px;
			background-color: #888;
			border-radius: 50%;
			display: inline-block;
		}

		.anchor {
		    display: block;
		    position: relative;
		    top: -70px;
		    visibility: hidden;
		}
}
	</style>
</head>
<body data-spy="scroll" data-target=".navbar" data-offset="50">

<!-- For navbar -->
<br />
<br />
<br />

<a class="anchor" id="title"></a>
<div class="jumbotron" style="padding: 30px;">
	<h1> Keith Allatt's Programming Portfolio </h1>
	<p>
		Using the GitHub API and Bootstrap to bring my portfolio to life. This website loads
		information from my GitHub account (<a href="https://github.com/keithallatt">https://github.com/keithallatt</a>) and
		displays some documentation for each project posted there.
	</p>
</div>

<br />
<a class="anchor" id="about_me"></a>
<div class="container">
	<h4> About me </h4>
	<p>
		Hi! My name is Keith and I am a student at the University of Toronto. I'm
		studying computer science and math, with interests in back-end development,
		small scale web development and math research.
	</p>
	<p>
		As of 2019, I am a second year student, working on building my portfolio
		as large as I can with well written, polished work. Here, I'm including
		some school projects that I've collaborated on, such as Warehouse Guardian.
	</p>
	<p>
		As mentioned before, I am on GitHub for my git repository hosting, so I
		can share my work, open source, to the public. A lot of my work revolves
		around problem solving. Solving a Sudoku Puzzle or how to automatically
		download updates to various software.
	</p>
</div>

<br />

<?php
$load_page = true;
if (!$load_page) {
	die("Not loading");
}


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
	array("http" => array(
		"header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) " .
			"AppleWebKit/537.36 (KHTML, like Gecko) " .
			"Chrome/50.0.2661.102 Safari/537.36"
	))
);

// for finding which repos exist
$api_url = "https://api.github.com/users/keithallatt/repos";
// for finding stats about a repo
$project_url = "https://api.github.com/repos/keithallatt/";
$html_url_1 = "https://raw.githubusercontent.com/keithallatt/";
$html_url_2 = "/master/README.md";

$payload = @file_get_contents($api_url, false, $context) or false;

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
	// different from portion in for each.
	print_r("<div class=\"container\" style=\"text-align: center;\"> <h5>You are viewing cached information for
		this website. <br /><br />Any recent changes to my GitHub will not appear here. </h5></div>");
	print_r("<br />");
	// didn't get the request, load from cache
	$location = $cache_dir . "keithallatt.json";

	$handle = fopen($location, 'r');

	if ($handle == false) {
		echo "Error reading";
	} else {
		$payload = fread($handle,filesize($location));
	}
}
/////////////////////////////////////

// contains json
$ar = json_decode($payload);

print_r("<nav class=\"navbar navbar-inverse navbar-fixed-top\">");
print_r("<div class=\"container-fluid\"><div class=\"navbar-header\">");
print_r("<a class=\"navbar-brand\" href=\"#\">Portfolio</a>");
print_r("</div><ul class=\"nav navbar-nav\">");
// to top
print_r("<li class=\"active\"><a href=\"#title\">Home</a></li>");
// to about
print_r("<li><a href=\"#about_me\">About Me</a></li>");
// dropdown for projects
print_r("<li class=\"dropdown\">");
print_r("<a class=\"dropdown-toggle\" data-toggle=\"dropdown\" href=\"#\">Projects");
print_r("<span class=\"caret\"></span></a>");
print_r("<ul class=\"dropdown-menu\">");

foreach ($ar as &$value) {
	$name = $value->name;
	print_r("<li><a href=\"#" . $name . "_anchor\">" . $name . "</a></li>");
}
print_r("</ul></li>");

// after projects

// contact / find my stuff (LinkedIn and github)
// to about
print_r("<li><a href=\"#contact_me\">Contact</a></li>");

print_r("</ul></div></nav>");

// add more as it comes.
$language_colors = json_decode(
	fread(fopen("colors.json", "r"), filesize("colors.json"))
);

$colummns = 2; // max
$span = 12 / $colummns;
$column_count = 0;

print_r("<div style=\"background-color: #dedded;\" class=\"container\">");
// prints out each name
foreach ($ar as &$value) {
		if ($column_count == 0) {
			// add a little space between rows
			//print_r("<div class=\"row\">\n");
			//print_r("<br />");
			//print_r("</div>\n");

			print_r("<div class=\"row\">\n");
		}

		print_r("<div style=\"border-style: solid; border-color: #dedded;
			background-color: #ffffff\" class=\"col-sm-" . $span . "\">\n");

		$name = $value->name;
		$full_url = $html_url_1 . $name . $html_url_2;

		$this_repo_stats = $project_url . $name;

		$this_stats_contents = @file_get_contents($this_repo_stats, false, $context);

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
			// didn't get the request, load from cache
			$location = $cache_dir . $name . ".json";

			$handle = fopen($location, 'r');

			if ($handle == false) {
				echo "Error reading";
			} else {
				$this_stats_contents = fread($handle,filesize($location));
			}
		}
		/////////////////////////////////////

		$this_stats = json_decode($this_stats_contents);

		$project_language = $this_stats->language;
		$project_link = $this_stats->html_url;
		$description = $this_stats->description;


		print_r("<div> <h2 id=\"" . $name . "_anchor\"
			style=\"padding-top: 80px; margin-top: -40px;
			text-align: center;\">" . $name . "</h2>\n<hr>\n");

		print_r("<div>"); // for lang, desc

		print_r("<div class=\"row\" style=\"min-height:100px;\">"); // nested row
		print_r("<div class=\"col-md-4\">");


		$background_color = $language_colors->$project_language->color;


		print_r("<span class=\"dot\" style=\"background-color: " . $background_color .
			";\"></span>&nbsp;&nbsp;&nbsp;&nbsp;");

		print_r($project_language . "<br />");
		print_r("<a href=\"" . $project_link . "\">Project Link</a><br />");

		print("</div>"); // for col div

		print_r("<div class=\"col-md-8\">");
		// description of repo
		print_r($description);
		print("</div>");

		print("</div>"); // lang desc close


		print("<div style=\"padding: 15px;\">");

		print_r("<button type=\"button\" class=\"btn btn-info\"
			data-toggle=\"collapse\" data-target=\"#" . $name . "\"
			style=\"width: 100%;\">
			Read more</button>\n");
		print_r("<div id=\"" . $name . "\" class=\"collapse\"> <br />");

		print_r("<div style=\"overflow-y: scroll; height: 250px;\">");

		$file_contents = file_get_contents($full_url, false, $context);

		// show markdown readme for the repo
		print_r(Markdown::defaultTransform($file_contents));

		print("</div>"); // for scroll div


		print("</div>"); // for collapse div
		print("</div>"); // for row


		// add spacing after
		print_r("<br /></div></div>");

		$column_count += 1;
		if ($column_count >= $colummns) {
			$column_count = 0;
		}
		// post change column

		print_r("</div>\n");
		if ($column_count == 0) {
			print_r("</div>\n");
		}
}

print_r("</div>")

?>

<br />
<a class="anchor" id="contact_me"></a>
<div class="container" style="padding: 15px;">
	<h4> See more on: </h4>
	<p>
		LinkedIn: <a href="https://www.linkedin.com/in/keith-allatt-a83907178/">https://www.linkedin.com/in/keith-allatt-a83907178/</a>
	</p>
	<p>
		GitHub: <a href="https://github.com/keithallatt">https://github.com/keithallatt</a>
	</p>
</div>


<br />
<div class="container" style="text-align: center; padding: 15px;">
	<a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/4.0/">
		<img alt="Creative Commons Licence" style="border-width:0"
			src="https://i.creativecommons.org/l/by-nc-nd/4.0/80x15.png" />
		</a>
		<br />
		This work is licensed under a
		<a rel="license"
			href="http://creativecommons.org/licenses/by-nc-nd/4.0/">
			Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International License
		</a>.
</div>
<br />
</body>
</html>
