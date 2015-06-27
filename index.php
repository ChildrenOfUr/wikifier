<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>CoU Wikifier</title>
	<style>
		body {
			font-family: sans-serif;
		}

		input[type="url"] {
			width: 500px;
		}

		textarea {
			width: 100%;
			height: 400px;
			resize: vertical;
		}
	</style>
</head>
<body>
	<h1>CoU Wikifier</h1>
	<p>Converts <a href="http://www.glitchthegame.com/encyclopedia/">Glitch Encyclopedia</a> entries into Wikitext. <strong>This program makes calls to the Glitch encyclopedia every time it runs. Please do not abuse it!</strong> <a href="https://github.com/ChildrenOfUr/wikifier">View source on GitHub</a></p>
	<p>If you need another template to be added or have any problems, use the <a href="https://github.com/ChildrenOfUr/wikifier/issues" target="_blank">wikifier issues page</a> on GitHub.</p>
	<form id="input" action="" method="GET">
		<fieldset>
			<input type="url" name="url" placeholder="URL (http://glitchthegame.com/... or http://www.glitchthegame.com/...)" required>
			<select name="template">
				<option disabled selected>Page Type</option>
				<option value="achievement">Achievement</option>
				<option value="inhabitant">Inhabitant</option>
				<option value="item">Item</option>
			</select>
			<button type="submit">Convert</button>
		</fieldset>
		<br>
	</form>
	<textarea disabled><?php include_once("wikifier.php"); ?></textarea>
</body>
</html>