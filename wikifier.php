<?php

/////////////////////////////////////////////////
/// Load dependencies and set error reporting ///
/////////////////////////////////////////////////

ini_set('display_errors', 1);
error_reporting(E_ALL);
libxml_use_internal_errors(TRUE);

require('vendor/autoload.php');

//////////////////////////////////
/// Change these two variables ///
//////////////////////////////////

if (isset($_GET["url"]) && isset($_GET["template"])) {
	$url = $_GET["url"];
	$template = $_GET["template"];
} else {
	die("
>>> Your output will show up here.

>>> Enter a valid URL and choose the template to use.
>>> You will need to take a screenshot of the image for everything but achievements, which you can right click and save.
>>> Make sure to use the filename in the wikitext when saving images!
>>> Upload images to the wiki by clicking the 'Upload file' link in the left wiki sidebar.

>>> Thank you for contributing!
");
}

////////////////////////////////////////////////////
/// Program starts, no editing needed at runtime ///
////////////////////////////////////////////////////

if (strpos($url, "http://glitchthegame.com/") === false && strpos($url, "http://www.glitchthegame.com/") === false) {
	die("ERROR >>> The URL must be from the Glitch encyclopedia and have http:// in front of it.\n");
}

$doc = file_get_contents($url);

if ($doc == "") {
	die("ERROR >>> The URL was invalid.\n");
}

switch ($template) {
	case 'inhabitant':
		////////////////////
		/// Collect data ///
		////////////////////
		$category = qp($doc, "h4.category-nav-back a")->text();
		$imagename = str_replace(" ", "_", qp($doc, "h1.first")->text());
		$description = qp($doc, "p.enc-description")->text();
		$tips = qp($doc, 'ul.item-notes');
		$animations = qp($doc, 'h4:contains(Animations)+table.asset_list, .sprites > table.asset_list');
		$staticimages = qp($doc, 'h4:contains(Static Images)+table.asset_list');
		$animatedgifs = qp($doc, '.gifs table.asset_list');
		$conversations = qp($doc, '.tab_contents.conversations *')->html();
		$merchandise = qp($doc, 'ul.items-list');

		//////////////////////////////////////
		/// Decide which sections to write ///
		//////////////////////////////////////

		if (count($tips) == 0) {
			$showTips = false;
		} else {
			$showTips = true;
		}

		if (count($merchandise) == 0) {
			$showMerchandise = false;
		} else {
			$showMerchandise = true;
		}

		/////////////
		/// Write ///
		/////////////

		if ($category == "Other") {
			$categoryArray = explode("/", $url);
			$category = ucfirst($categoryArray[3]);
		}
		out('[[Category:' . $category . ']]');
		out('[[File:' . $imagename . '.png|right]]'); // the file will not exist, but it will be put in a "pages with missing file links" category to upload one later
		out('');
		out('<big>' . $description . '</big>');
		out('');

		if ($showTips) {
			out('== Tips ==');
			out('');
			
			foreach ($tips->find('li') as $tip) {
				out('* ' . trim($tip->text()));
			}

			out('');
		}

		out('== Assets ==');

		out('=== Animations ===');

		out('');
		out('<markdown>');
		out('<table class="table">');
		out("\t<tr>");
		out("\t\t<th>State</th>");
		out("\t\t<th>Filesize</th>");
		out("\t\t<th>Dimensions</th>");
		out("\t\t<th>Frame Dimensions</th>");
		out("\t\t<th># of Frames</th>");
		out("\t\t<th>Loops</th>");
		out("\t</tr>");
		foreach ($animations->find('tr:not(:first-child') as $row) {
			out("\t<tr>");
			echo "\t\t" . str_replace(array("\n", "  "), "", $row->find('td:first-child')->html()) . "\n";
			foreach($row->find('td:not(:first-child)') as $data) {
				out("\t\t<td>" . trim($data->find('td:not(:first-child)')->text()) . "</td>");
			}
			out("\t</tr>");
		}
		out('</table>');
		out('</markdown>');
		out('');

		out('=== Static Images ===');

		out('');
		out('<markdown>');
		out('<table class="table">');
		out("\t<tr>");
		out("\t\t<th>State</th>");
		out("\t\t<th>Filesize</th>");
		out("\t\t<th>Dimensions</th>");
		out("\t</tr>");
		foreach($staticimages->find('tr:not(:first-child') as $row) {
			out("\t<tr>");
			echo "\t\t" . str_replace(array("\n", "  "), "", $row->find('td:first-child')->html()) . "\n";
			foreach($row->find('td:not(:first-child)') as $data) {
				out("\t\t<td>" . trim($data->find('td:not(:first-child)')->text()) . "</td>");
			}
			out("\t</tr>");	
		}
		out('</table>');
		out('</markdown>');
		out('');

		out('=== Animated GIFs ===');

		out('');
		out('<markdown>');
		out('<table class="table">');
		out("\t<tr>");
		out("\t\t<th>Image</th>");
		out("\t\t<th>Dimensions</th>");
		out("\t\t<th>Filesize</th>");
		out("\t</tr>");
		foreach($animatedgifs->find('tr:not(:first-child') as $row) {
			out("\t<tr>");
			echo "\t\t" . str_replace(array("\n", "  ", "data-src"), array("", "", "src"), $row->find('td:first-child')->html()) . "\n";
			foreach($row->find('td:not(:first-child)') as $data) {
				out("\t\t<td>" . trim($data->find('td:not(:first-child)')->text()) . "</td>");
			}
			out("\t</tr>");	
		}
		out('</table>');
		out('</markdown>');
		out('');
		if ($conversations != NULL) {

			out('=== Conversations ===');

			out('');
			# Conversations is a string containing the HTML of the conversations tab.
			# Fix the conversations by replacing each element from this array
			# with its corresponding element (by index) from the next array.
			$conversationsSearch = array(
				"<table>",
				"</table>",
				"<blockquote>",
				"</blockquote>",
				"<hr />",
				"<p>",
				"</p>",
				"  ",
				'<span style="font-size: 12px; font-weight: bold;">',
				'<span style="font-size: 14px; font-weight: bold;">'
			);
			$conversationsReplace = array(
				"",
				"",
				"",
				"",
				"",
				"",
				"",
				"",
				"\n" . '<span style="font-size: 12px; font-weight: bold;">',
				"\n\n" . '<span style="font-size: 14px; font-weight: bold;">'
			);
			$conversations = str_replace($conversationsSearch, $conversationsReplace, $conversations);
			# close ** and ====
			//$conversations = '====' . join(array_map("appendEnd4Head", explode('====', $conversations)));
			out($conversations);
			out('');
		}
		if ($showMerchandise) {
			out('== Merchandise ==');
			out('');
			
			foreach ($merchandise->find('li') as $item) {
				out('* [[' . trim($item->text()) . ']]');
			}

			out('');
		}
	break;

	case 'item';
	////////////////////
	/// Collect data ///
	////////////////////
	$category = qp($doc, "h4.category-nav-back a")->text();
	$imagename = str_replace(" ", "_", qp($doc, "h1.first")->text());
	$description = qp($doc, "p.enc-description")->text();
	$animations = qp($doc, 'h4:contains(Animations)+table.asset_list');
	$staticimages = qp($doc, 'h4:contains(Static Images)+table.asset_list');
	$animatedgifs = qp($doc, '.gifs table.asset_list');
	$acq = qp($doc, "li.item-note")->text();
	$currants = qp($doc, "li.item-price strong")->text();
	$slotnum = qp($doc, "li.item-stack strong")->text();
	$wear = qp($doc, "li.item-wear strong")->text();

	/////////////
	/// Write ///
	/////////////

	if ($category == "Other") {
		$categoryArray = explode("/", $url);
		$category = ucfirst($categoryArray[3]);
	}
	out('[[Category:' . $category . ']]');
	out('[[File:' . $imagename . '.png|right]]'); // the file will not exist, but it will be put in a "pages with missing file links" category to upload one later
	out('');
	out('<big>' . $description . '</big>');
	out('');
	out('== Acquisition ==');
	out('');
	out(trim($acq));
	out('');
	out('== Facts ==');
	out('');
	out('* Worth about <big>' . $currants . '</big>');
	out('* Fits <big>' . $slotnum . '</big> in a backpack slot');
	out('* Durable for about <big>' . $wear . '</big>');
	out('');
	out('== Assets ==');

	out('=== Animations ===');

	out('');
	out('<markdown>');
	out('<table class="table">');
	out("\t<tr>");
	out("\t\t<th>State</th>");
	out("\t\t<th>Filesize</th>");
	out("\t\t<th>Dimensions</th>");
	out("\t\t<th>Frame Dimensions</th>");
	out("\t\t<th># of Frames</th>");
	out("\t\t<th>Loops</th>");
	out("\t</tr>");
	foreach ($animations->find('tr:not(:first-child') as $row) {
		out("\t<tr>");
		echo "\t\t" . str_replace(array("\n", "  "), "", $row->find('td:first-child')->html()) . "\n";
		foreach($row->find('td:not(:first-child)') as $data) {
			out("\t\t<td>" . trim($data->find('td:not(:first-child)')->text()) . "</td>");
		}
		out("\t</tr>");
	}
	out('</table>');
	out('</markdown>');
	out('');

	out('=== Static Images ===');

	out('');
	out('<markdown>');
	out('<table class="table">');
	out("\t<tr>");
	out("\t\t<th>State</th>");
	out("\t\t<th>Filesize</th>");
	out("\t\t<th>Dimensions</th>");
	out("\t</tr>");
	foreach($staticimages->find('tr:not(:first-child') as $row) {
		out("\t<tr>");
		echo "\t\t" . str_replace(array("\n", "  "), "", $row->find('td:first-child')->html()) . "\n";
		foreach($row->find('td:not(:first-child)') as $data) {
			out("\t\t<td>" . trim($data->find('td:not(:first-child)')->text()) . "</td>");
		}
		out("\t</tr>");	
	}
	out('</table>');
	out('</markdown>');
	out('');

	out('=== Animated GIFs ===');

	out('');
	out('<markdown>');
	out('<table class="table">');
	out("\t<tr>");
	out("\t\t<th>Image</th>");
	out("\t\t<th>Dimensions</th>");
	out("\t\t<th>Filesize</th>");
	out("\t</tr>");
	foreach($animatedgifs->find('tr:not(:first-child') as $row) {
		out("\t<tr>");
		echo "\t\t" . str_replace(array("\n", "  ", "data-src"), array("", "", "src"), $row->find('td:first-child')->html()) . "\n";
		foreach($row->find('td:not(:first-child)') as $data) {
			out("\t\t<td>" . trim($data->find('td:not(:first-child)')->text()) . "</td>");
		}
		out("\t</tr>");	
	}
	out('</table>');
	out('</markdown>');
	out('');
	break;

	case 'achievement':
		////////////////////
		/// Collect data ///
		////////////////////
		$category = qp($doc, "h4.category-nav-back a")->text();
		$imagename = str_replace(" ", "_", qp($doc, "h1.first")->text());
		$description = qp($doc, "p.enc-description")->text();
		$related = qp($doc, 'div.section.label-section > ul.items-list.achievement-badges');

		/////////////
		/// Write ///
		/////////////
	
		if (count($related) != 0) {
			$showRelated = true;
		} else {
			$showRelated = false;
		}
		
		out('[[Category:' . $category . ']]');
		out('[[File:' . $imagename . '.png|center]]');
		// the file will not exist, but it will be put in a "pages with missing file links" category to upload one later
		out('');
		out('== Criteria ==');
		out('');
		out('<big>' . $description . '</big>');
		out('');
		if ($showRelated) {
			out('== Related Achievements ==');
			out('');
			out('<gallery>');
			foreach ($related->find('li') as $achievement) {
				out(trim(str_replace(" ", "_", $achievement->text())) . '.png|[[' . trim($achievement->text()) . ']]');
			}
			out('</gallery>');
		}

	break;

	default:
		echo('ERROR >>> Invalid template specified.');
	break;
}

///////////////////////////////////
/// Make outputting text easier ///
///////////////////////////////////

function out($text) {
	echo($text . "\n");
}

function appendEnd4Head($s) {
	return $s . " ====";
}

function appendEndBold($s) {
	return $s . ": **";
}
?>
