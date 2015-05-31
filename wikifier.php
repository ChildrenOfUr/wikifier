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
	die(">>> Your output will show up here.");
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
		$category = qp($doc, "h4.category-nav-back a")->text();
		$imagename = str_replace(" ", "_", qp($doc, "h1.first")->text());
		$description = qp($doc, "p.enc-description")->text();
		$tips = qp($doc, 'ul.item-notes');
		$animations = qp($doc, 'h4:contains(Animations)+table.asset_list');
		$staticimages = qp($doc, 'h4:contains(Static Images)+table.asset_list');
		$animatedgifs = qp($doc, '.gifs table.asset_list');
		$conversations = qp($doc, '.tab_contents.conversations');

		if (count($tips) == 0) {
			$showTips = false;
		} else {
			$showTips = true;
		}

		out('[[Category:' . $category . ']]');
		out('[[File:' . $imagename . '.png|right|frame|' . $description . ']]');
		// the file will not exist, but it will be put in a "pages with missing file links" category to upload one later
		out('');
		if ($showTips) {
			out('== Tips ==');
			out('');
			
			foreach ($tips->find('li') as $tip) {
				out('* ' . trim($tip->text()));
			}

			out('');
		}
		out('== Interactions ==');
		out('');
		out('=== Conversations ===');
		out('');
		foreach($conversations->find('hr+span') as $title) {
			out('==== ' . $title->text() . ' ====');
			out('');
			out('<markdown>');
			foreach ($conversations->find('span:not(hr+span)') as $label) {
				out('>**' . trim($label->text()) . ':** ' . $conversations->find('span+blockquote')->text());
				out('>');
			}
			out('</markdown>');
			out('');
		}
		out('');
		out('== Assets ==');
		out('=== Sprite Sheets ===');
		out('==== Animations ====');
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
		out("\t<tr>");
		out('</table>');
		out('</markdown>');
		out('');
		out('==== Static Images ====');
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
		out('==== Animated GIFs ====');
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
			echo "\t\t" . str_replace(array("\n", "  "), "", $row->find('td:first-child')->html()) . "\n";
			foreach($row->find('td:not(:first-child)') as $data) {
				out("\t\t<td>" . trim($data->find('td:not(:first-child)')->text()) . "</td>");
			}
			out("\t</tr>");	
		}
		out('</table>');
		out('</markdown>');
	break;

	default:
		echo('Invalid template specified.');
	break;
}

///////////////////////////////////
/// Make outputting text easier ///
///////////////////////////////////

function out($text) {
	echo($text . "\n");
}
?>