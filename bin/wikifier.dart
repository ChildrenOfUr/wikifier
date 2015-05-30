import 'dart:io';
import 'dart:html' as html;
import 'package:html5lib/parser.dart' show parse;

void main() {

	String json;
	List<String> urls = new List();

	urls = new File("../run/urls.txt").readAsLinesSync();

	for (int i = 0; i < urls.length; i++) {
		json = htmltojson(urls[i]);
		new File("../run/json/" + gettitle(urls[i]) + ".json").writeAsStringSync(json);
	}
}

String htmltojson(String url) {
	String out = "{\n";
	String rawDOM;

	// json parts
	String name, category, description;

	html.HttpRequest.getString(url)
	.then((String contents) {
		rawDOM = contents;
	});

	var page = parse(rawDOM);

	name = page.querySelector("h1#first").innerHtml;
	category = page.querySelector("h4.category-nav-back a").innerHtml;
	description = page.querySelector("p.enc-description").innerHtml;

	out += '\t"name": ' + name + '",\n';
	out += '\t"category": ' + category + '",\n';
	out += '\t"description": ' + description + '", \n';

	out += "}";

	return out;
}

String jsontowikitext(String json) {
	String out;

	// 1. parse json
	// 2. assemble wikitext

	return out;
}

String gettitle(String url) {
	RegExp regex = new RegExp(r"[^/]+(?=/$|$)");
	return regex.stringMatch(url);
}