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

$url = 'http://www.glitchthegame.com/inhabitants/animals/chicken/';
$template = 'inhabitant'; // inhabitant, street

////////////////////////////////////////////////////
/// Program starts, no editing needed at runtime ///
////////////////////////////////////////////////////

$doc = file_get_contents($url);

switch ($template) {
	case 'inhabitant':
		$category = qp($doc, "h4.category-nav-back a")->text();
		$imagename = qp($doc, "h1.first")->text();
		$description = qp($doc, "p.enc-description")->text();
		$tips = qp($doc)->find('ul.item-notes');
		$animations = qp($doc)->find('h4:contains(Animations)+table.asset_list');
		$staticimages = qp($doc)->find('h4:contains(Static Images)+table.asset_list');
		$animatedgifs = qp($doc)->find('.gifs table.asset_list');

		if (count($tips) == 0) {
			$showTips = false;
		} else {
			$showTips = true;
		}

		out('[[Category:' . $category . ']]');
		out('[[File:' . $imagename . '.png|right|frame|' . $description . ']]');
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
		// TODO: conversation loop (may need to be manual due to the lack of unique markers on the page)
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
?>;