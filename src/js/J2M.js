
(function() {

	/**
	 * Takes Jira markup and converts it to Markdown.
	 *
	 * https://jira.atlassian.com/secure/WikiRendererHelpAction.jspa?section=all
	 *
	 * @param {string} input - Jira markup text
	 * @returns {string} - Markdown formatted text
	 */
	function toM(input) {

		input = input.replace(/^bq\.(.*)$/gm, function (match, content) {
			return '> ' + content + "\n";
		});

		input = input.replace(/([*_])(.*)\1/g, function (match,wrapper,content) {
			var to = (wrapper === '*') ? '**' : '*';
			return to + content + to;
		});

		// multi-level numbered list
		input = input.replace(/^((?:#|-|\+|\*)+) (.*)$/gm, function (match, level, content) {
			var len = 2;
			var prefix = '1.';
			if (level.length > 1) {
				len = parseInt((level.length - 1) * 4) + 2;
			}

			// take the last character of the level to determine the replacement
			var prefix = level[level.length - 1];
			if (prefix == '#') prefix = '1.';

			return Array(len).join(" ") + prefix + ' ' + content;
		});

		// headers, must be after numbered lists
		input = input.replace(/^h([0-6])\.(.*)$/gm, function (match,level,content) {
			return Array(parseInt(level) + 1).join('#') + content;
		});

		input = input.replace(/\{\{([^}]+)\}\}/g, '`$1`');
		input = input.replace(/\?\?((?:.[^?]|[^?].)+)\?\?/g, '<cite>$1</cite>');
		input = input.replace(/\+([^+]*)\+/g, '<ins>$1</ins>');
		input = input.replace(/\^([^^]*)\^/g, '<sup>$1</sup>');
		input = input.replace(/~([^~]*)~/g, '<sub>$1</sub>');
		input = input.replace(/-([^-]*)-/g, '-$1-');

		input = input.replace(/\{code(:([a-z]+))?\}([^]*?)\{code\}/gm, '```$2$3```');
		input = input.replace(/\{quote\}([^]*)\{quote\}/gm, function(match, content) {
			lines = content.split(/\r?\n/gm);

			for (var i = 0; i < lines.length; i++) {
				lines[i] = '> ' + lines[i];
			}

			return lines.join("\n");
		});

		input = input.replace(/!([^\n\s]+)!/, '![]($1)');
		input = input.replace(/\[([^|]+)\|(.+?)\]/g, '[$1]($2)');
		input = input.replace(/\[(.+?)\]([^\(]+)/g, '<$1>$2');

		input = input.replace(/{noformat}/g, '```');
		input = input.replace(/{color:([^}]+)}([^]*?){color}/gm, '<span style="color:$1">$2</span>');

		// Convert header rows of tables by splitting input on lines
		lines = input.split(/\r?\n/gm);
		lines_to_remove = []
		for (var i = 0; i < lines.length; i++) {
			line_content = lines[i];

			seperators = line_content.match(/\|\|/g);
			if (seperators != null) {
				lines[i] = lines[i].replace(/\|\|/g, "|");
				console.log(seperators)

				// Add a new line to mark the header in Markdown,
				// we require that at least 3 -'s are between each |
				header_line = "";
				for (var j = 0; j < seperators.length-1; j++) {
					header_line += "|---";
				}

				header_line += "|";

				lines.splice(i+1, 0, header_line);

			}
		}

		// Join the split lines back
		input = ""
		for (var i = 0; i < lines.length; i++) {
			input += lines[i] + "\n"
		}



		return input;
	};

	/**
	 * Takes Markdown and converts it to Jira formatted text
	 *
	 * @param {string} input
	 * @returns {string}
	 */
	function toJ(input) {
		// remove sections that shouldn't be recursively processed
		var START = 'J2MBLOCKPLACEHOLDER';
		var replacementsList = [];
		var counter = 0;
		
		input = input.replace(/`{3,}(\w+)?((?:\n|.)+?)`{3,}/g, function(match, synt, content) {
			var code = '{code';
		
			if (synt) {
				code += ':' + synt;
			}
		
			code += '}' + content + '{code}';
			var key = START + counter++ + '%%';
			replacementsList.push({key: key, value: code});
			return key;
		});
		
		input = input.replace(/`([^`]+)`/g, function(match, content) {
			var code = '{{'+ content + '}}';
			var key = START + counter++ + '%%';
			replacementsList.push({key: key, value: code});
			return key;
		});

		input = input.replace(/`([^`]+)`/g, '{{$1}}');

		input = input.replace(/^(.*?)\n([=-])+$/gm, function (match,content,level) {
			return 'h' + (level[0] === '=' ? 1 : 2) + '. ' + content;
		});

		input = input.replace(/^([#]+)(.*?)$/gm, function (match,level,content) {
			return 'h' + level.length + '.' + content;
		});

		input = input.replace(/([*_]+)(.*?)\1/g, function (match,wrapper,content) {
			var to = (wrapper.length === 1) ? '_' : '*';
			return to + content + to;
		});

		// multi-level bulleted list
		input = input.replace(/^(\s*)- (.*)$/gm, function (match,level,content) {
			var len = 2;
			if(level.length > 0) {
				len = parseInt(level.length/4.0) + 2;
			}
			return Array(len).join("-") + ' ' + content;
		});

		// multi-level numbered list
		input = input.replace(/^(\s+)1. (.*)$/gm, function (match, level, content) {
			var len = 2;
			if (level.length > 1) {
				len = parseInt(level.length / 4) + 2;
			}
			return Array(len).join("#") + ' ' + content;
		});

		var map = {
			cite: '??',
			del: '-',
			ins: '+',
			sup: '^',
			sub: '~'
		};

		input = input.replace(new RegExp('<(' + Object.keys(map).join('|') + ')>(.*?)<\/\\1>', 'g'), function (match,from,content) {
			//console.log(from);
			var to = map[from];
			return to + content + to;
		});

		input = input.replace(/<span style="color:(#[^"]+)">([^]*?)<\/span>/gm, '{color:$1}$2{color}');

		input = input.replace(/~~(.*?)~~/g, '-$1-');

		input = input.replace(/!\[[^\]]+\]\(([^)]+)\)/g, '!$1!');
		input = input.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '[$1|$2]');
		input = input.replace(/<([^>]+)>/g, '[$1]');

		// restore extracted sections
		for(var i =0; i < replacementsList.length; i++){
			var sub = replacementsList[i];
			input = input.replace(sub["key"], sub["value"]);
		}

		// Convert header rows of tables by splitting input on lines
		lines = input.split(/\r?\n/gm);
		lines_to_remove = []
		for (var i = 0; i < lines.length; i++) {
			line_content = lines[i];

			if (line_content.match(/\|---/g) != null) {
				lines[i-1] = lines[i-1].replace(/\|/g, "||")
				lines.splice(i, 1)
			}
		}

		// Join the split lines back
		input = ""
		for (var i = 0; i < lines.length; i++) {
			input += lines[i] + "\n"
		}
		return input;
	};


	/**
	 * Exports object
	 * @type {{toM: toM, toJ: toJ}}
	 */
	var J2M = {
		toM: toM,
		toJ: toJ
	};

	// exporting that can be used in a browser and in node
	try {
		window.J2M = J2M;
	} catch (e) {
		// not a browser, we assume it is node
		module.exports = J2M;
	}
})();
