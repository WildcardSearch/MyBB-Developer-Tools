<?php
/*
 * Plugin Name: Developer Tools for MyBB 1.8.x
 * Copyright 2018 WildcardSearch
 * http://www.rantcentralforums.com
 *
 * this file contains data used by classes/installer.php
 */

$tables = array(
	'pgsql' => array(
		'phiddles' => array(
			'id' => 'SERIAL',
			'title' => 'VARCHAR(32) NOT NULL',
			'content' => 'TEXT',
			'dateline' => 'INT NOT NULL, PRIMARY KEY(id)',
		),
	),
	"phiddles" => array(
		"id" => 'INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY',
		"title" => 'VARCHAR(32) NOT NULL',
		"content" => 'TEXT',
		"dateline" => 'INT(10)',
	),
);

$settings = array(
	'developer_tools_settings' => array(
		'group' => array(
			'name' => 'developer_tools_settings',
			'title' => $lang->developer_tools,
			'description' => $lang->developer_tools_settingsgroup_description,
			'disporder' => '107',
			'isdefault' => 0,
		),
		'settings' => array(
			'developer_tools_minify_js' => array(
				'name' => 'developer_tools_minify_js',
				'title' => $lang->developer_tools_minify_js_title,
				'description' => $lang->developer_tools_minify_js_desc,
				'optionscode' => 'yesno',
				'value' => '0',
				'disporder' => '50'
			),
		),
	),
);

$styleSheets = array(
	"folder" => 'developer_tools',
	"acp" => array(
		"global" => array(
			"stylesheet" => <<<EOF
/*
 * Plugin Name: Developer Tools for MyBB 1.8.x
 * Copyright 2018 WildcardSearch
 * http://www.rantcentralforums.com
 *
 * this file contains global style information
 */

iframe.outputFrame {
	height: 700px;
	width: 100%;
	overflow-y: auto;
}
	
/* CodeMirror */

.CodeMirror {
	font-size: 1.8em;
	height: 400px;
	padding: 7px 0px 0px 2px;
}

div.CodeMirror span.CodeMirror-matchingbracket {
	outline: none;
	color: #ffff4c !important;
	font-weight: bold;
}

.cm-matchhighlight {
	background-color: yellow;
	color: black;
	font-weight: bold;
}

iframe {
	border: none;
}
EOF
		),
		"tabs" => array(
			"stylesheet" => <<<EOF
/*
 * Plugin Name: Developer Tools for MyBB 1.8.x
 * Copyright 2018 WildcardSearch
 * http://www.rantcentralforums.com
 *
 * this file contains style rules for tabs
 */

div.quick_tab {
	background: white;
	color: black;
	padding: 0px;
	border: 1px solid gray;
}

li.quick_tab {
	display: inline;
	color: black;
	font-size: 1.4em;
	font-weight: bold;
}

li.quick_tab a {
	color: black;
	background: #B8B8B8;
	padding: 5px 15px 0px 15px;
	text-decoration: none;
	border-radius: 3px 3px 0px 0px;
	border-bottom: 1px solid gray;
}

li.quick_tab span {
	background: white;
	padding: 5px 15px 2px 15px;
	border-left: 1px solid gray;
	border-right: 1px solid gray;
	border-top: 1px solid gray;
	border-radius: 3px 3px 0px 0px;
}
EOF
		),
	),
);

$images = array(
	'folder' => 'developer_tools',
	'acp' => array(
		'donate.gif' => array(
			'image' => <<<EOF
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
EOF
		),
		'pixel.gif' => array(
			'image' => <<<EOF
R0lGODlhAQABAID/AMDAwAAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==
EOF
		),
		'settings.gif' => array(
			'image' => <<<EOF
R0lGODlhEAAQAOMLAAAAAAMDAwYGBgoKCg0NDRoaGh0dHUlJSVhYWIeHh5aWlv///////////////////yH5BAEKAA8ALAAAAAAQABAAAARe8Mn5lKJ4nqRMOtmDPBvQAZ+IIQZgtoAxUodsEKcNSqXd2ahdwlWQWVgDV6JiaDYVi4VlSq1Gf87L0GVUsARK3tBm6LAAu4ktUC6yMueYgjubjHrzVJ2WKKdCFBYhEQA7
EOF
		),
		'delete.gif' => array(
			'image' => <<<EOF
R0lGODlhIAAgAOekAFw1Na4wMDVpuzVquzZqvDZrvDdrvDdsvTtuvUBxwDx0wUB3wkF6w0J8w016w018xUWAx0aAxkiAw0iEx0iEyEuGx0uGyEyGxkyHyE2IyE+MyFCMyFGNyFKNyFWPyWOLylWQylWQy1WRymaNzFqSyluUzGmW3GuY3Gmdz3Si0k+y91Gy91Oy+FKz93yq14So1VO0+FW0+Fa0932w4X2y4pCx3WG8+2K8/GK9/GO9+2C+/IS24me9/GS+/GW++2i//W7C/arA4q7C45PU/5TV/5XW/5bW/7nO6JvW/5jX/7PS8Z7X/6DY/7jW9LrW9bTY9rvY9rzZ9r3a9sHb+MLc+MPd+cTd+cXf+cbf+sff+sfg+sjg+cjg+sjg+8jh+8nh+8rh+svh+8zh+8ni+8vi+87i/Mzj+83j+83j/M7j+8/j/Mnl+tDk+9Hk/M3m/NPl/NPl/dLm/NXl/tPm/Nfm8tTm/NTm/dXm/dXn/dbn/dbn/tfn/tbo/tfo/tjo/tzq9t7q9t7r9t/r9d/s9+Pt9+nv9ebw9+jx+Ory+uvy+Ozy9+zy+Ovz+uzz+e3z+O30+O30+e30+u70+O/0+e/0+vD0+PD0+fH1+fL2+vL3+/P3+/P3/PT3+/T4/PX5/Pf6/fj7/fj7//r7/f3+/v///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////yH5BAEKAP8ALAAAAAAgACAAAAj+AP8JHEiwoMGDCBMqXCjwiAsXJUqAmDhRhIgQITx4QFGjhihRCx1ClEgRhEWMGjl6BKkwhSNHkiQ9evSy5qJFihTRoUHjxImPCUlgwqRFy5cvY7p0uXJlypQmTUI9eTJjhgkTLA0KJWoUqVKmTqFKpWoVK8IOly6hQZMmzRkyZLZsoULFiRNQbtysWbNjx4uzademCfA27ty6dwPo5esXIYe0atQEmLwWDBgrVqJEmRxAk6ZChSQ4hqyGDWc0ljFvngwAwOfQji1ZmjOnTh3OZ85w4cK5tRIllSpdQLhh0qQ9e/LkwcM5jpjeAKRAgUKJEgbixpErx2OHM3Qp0qn+W0eoARIkP+j99NGjx3trLVWq0I0UyQJ58+nVs3cvBr58KvTZd1AGjTTCBx999KGcd5OxYUYWWWCBBSKIUIAQgQYiqCB0nD0Y4YQVIlRBIomwtyBrAMABB2dhhHFUJplAICKJJkJ3h4ptsPhijAhNcMghcsgB3RtvtNFGGWVw5oUXn3zCAEIRGGJIDDHIEEBrMLDAQgtctrDClQCo4IknCkApJZUyyAAADFlu2eUKKoQpJpkINUAIIUAAwQQTSyCBRBJJGGFEEUUQMcQQPvjACScH1Hlnnnv2+WeggxZ6aKKLNnrQAoII8sMPPPDgQw+k9oADDjnkYIMOOtxwAyhfoBiAEKeegipqqaaiqiqrrsIq60EPkDjIIIEEAsgfyP7ByLKMILLJJp10MsooDiAUxAgjJJBAAdxyS8C3BAwgrgACIIDABx8IYS222nbrLbjiDkCuueiqy9C9+OZ7b0AAOw==
EOF
		),
		'load.gif' => array(
			'image' => <<<EOF
R0lGODlhIAAgAOeaAFw1Na4wMDVpuzVquzZqvDZrvDdrvDdsvTtuvUBxwDx0wUB3wkF6w0J8w016w018xUWAx0aAxkiAw0iEx0iEyEuGx0uGyEyGxkyHyE2IyE+MyFCMyFGNyFKNyFWPyWOLylWQymaNzFqSyluUzGmW3GuY3Gmdz3Si0k+y91Gy91Oy+FKz93yq14So1VO0+FW0+Fa091S1932w4X2y4pCx3WG8+2K8/GK9/GO9+2C+/IS24me9/GS+/GW++2i//W7C/arA4q7C45PU/5TV/5XW/5bW/7nO6JvW/5jX/7PS8Z7X/6DY/7XU87vW9bTY9rvY9rzZ9r3a9sLc+MPd+cbf+sff+sfg+sng+8jh+8nh+8vh+8zh+8zi/M3i/M7i/Mzj+83j/M/j/Mnl+s/k/NDk+9Hk/NPl/NPl/dXl/tPm/Nfm8tTm/NTm/dXm/dPn/dXn/dbn/dbn/tfn/tbo/tfo/tjo/t/p9Nzq9t7q9t7r9t/r9d/s9+Pt9+nv9ebw9+jx+Ory+uvy+Ozy9+vz+uzz+e3z+O30+O30+e30+u70+O/0+e/0+vD0+PD0+fH1+fL2+vL3+/P3+/P3/PT3+/T4/PX5/Pf6/fj7/fr7/f3+/v///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////yH5BAEKAP8ALAAAAAAgACAAAAj+AP8JHEiwYICCCBMqJBig4cKHRliwGDEChEUQDTN68GCCBg1MmB7+izixosWMKDl6BCnyRKFCiRIZMoSyppoZM0qUYKlQxKNHVqxkqUnUiRMZMkiQCJnQJ1ArRKMeTbpUYQdHjsBE3SpGjA4dLaxi1bqVKACvYBVywBomDIC3AFDCnRupTx8JatmGIUNmDNmGAKBAsSNIUKS6d9U2apQmzZo1bsrIlSKlCRMmSZIwYnRB4QZFiuTIgQPnDRu5VapIiRLlyZNFizB4Bi2atGnUqlm7hi07oYZDh+oIr0MnjtwtW6xMmUIZESILCn8HH178ePLlzZ8rzECI0Jw5dOj+kJbL90tqKlQAAaKwvfv38KThzC1/Pv16hRUCBYoTJz7pNm2ccUYZZXShhRZZZAEJJBDgpx9//sEBoIAEGoigggwqNMEff6CBhoBnmCGiGQR64QUXV1yBBRaWWMKAQhH44ccLL8AAQwwuuKCCCiv0uEIKKASJQiWVKACjjDTaiKOOPPoIpJBEGplQA3zw8cMPSyyhxBFHIIFEEUUQQcQQQgjRQw+TTHKAQlRaiaWWXHoJpphkmommmgotoIcePviwww498CAoDzfcgAMONeSQgw02XHKJAXnu2eefgQ5a6KGJLtroowo9oN8ee+SRBx53lHrHIKgOAogkklBCSSY1mTigEBAhhJBAAgXkmisBvBIwwK8CCIAAAh98EMSstd6q6669/jpAsMMWe6xI1FZr7bUCBQQAOw==
EOF
		),
		'new.gif' => array(
			'image' => <<<EOF
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
EOF
		),
		'preview.gif' => array(
			'image' => <<<EOF
R0lGODlhIAAgAMZ6AAAAAAMHCggJCgkJCgkKCiQpLiw0PC41PTE3PjI4PjU6P1plcTVpuzVqu1xmcVxncTZrvDdrvF9ncjdsvTRzoDtuvTZ1oEBxwDx0wUB3wkF6w0J8w016w018xUWAx0aAxkiAw0iEx0iEyEuGx0uGyEyGxkyHyE2IyE+MyFCMyFGNyFKNyFWPyWOLylWQylWQy1WRymaNzFqSyluUzIKSo4KTo2mW3GuY3IuWpWmdz4iZq3Si0p6go0+y96Gjpnyq14So1VW0+Fa0932w4X2y4pCx3WK8/GC+/IS24me9/GW++2i//W7C/arA4q7C47nO6LrR6rzT7LTY9sfg+sjh+8nh+8nl+tXl/tfm8t7q9t7r9t/r9d/s9+Pt9+nv9ebw9+jx+Ory+uvy+Ozy9+zy+Ozz+e3z+O30+O30+e30+u70+O/0+e/0+vD0+PD0+fH1+fL2+vL3+/P3/PT3+/T4/PX5/Pf6/fj7/fr7/f3+/v///////////////////////yH5BAEKAH8ALAAAAAAgACAAAAf+gH+Cg4SFhoeIiYqLgk8/PzMzLpOTMDAvLywsOUVFeHiLjpCSlC6WmJqcnqCKO2ZmampnZ6+1ZGRjY1hERDc3n4kycHAAAFVVNQkJxQICBwc+UlJDQzY2rIbCxMbIyszO0NLU1tiFK29vxQ4OBTo6U1NRUe4FDw9WVkhIQIjn6QDr2r2LN09HvXv59iFSga5YgQJQ4EmUCOUhDx5evIBY2BDAw4gTKVrEqHGhGzfFUhYLCc+dAQNt2pRAlGLNmgEDLOjcaSGkvGZs2JigaRMnz50+owAViggFGjQECMhjOfGngDRpSDR9GnUqVXhWsWo9dKJMGQUK3E1RqXKKOwTgCMKEEYGo7Nm079imdKsDrly6h0aIEYMDx0e9xSoWoEEjThwPiAQTNgwRMQDFjB1DPhQCDJgrVyRIKGC5wIIFVKjYsaMB0YcvX4IEESLEgsoAAYpR6MG7R506GFzDlk3bdkrcunv7Bo5oQ5cuTJhY1qtEyZw5E5o/jz6dbfXr2Q9l2LJlyZIkSbofOWLEyJ07ERCNL38+/fT17d/HP9RhMBcuWmiRhWVhyCEHHXTkkQcHiDQRQwwXXADBhBOq1MCFDDBQQQUttOBEgw9GSGGFKV3YQIYbdvghIyy26CKLgQAAOw==
EOF
		),
		'save.gif' => array(
			'image' => <<<EOF
R0lGODlhIAAgAOebAFw1Na4wMDVpuzVquzZrvDdrvDdsvTtuvUBxwDx0wUB3wkF6w0J8w016w018xUWAx0aAxkiAw0iEx0iEyEuGx0uGyEyGxkyHyE2IyE+MyFCMyFGNyFKNyFWPyWOLylWQylWQy1WRymaNzFqSyluUzGmW3GuY3Gmdz3Si0k+y91Gy91Kz93yq14So1VO0+FW0+Fa091S1932w4X2y4pCx3WK8/GC+/IS24me9/GW++2i//W7C/arA4q7C47nO6LPS8bXU87jW9LrW9bvW9bTY9rvY9rzZ9r3a9sHb+MLc+MPd+cTd+cXf+cbf+sff+sfg+sjg+cjg+sjg+8ng+8jh+8nh+8rh+svh+8zh+8ni+8vi+8zi/M3i/M7i/Mzj+83j+83j/M7j+8/j/Mnl+s/k/NDk+9Hk/M3m/NPl/NPl/dLm/NXl/tPm/Nfm8tTm/NTm/dPn/dXn/dbn/dbn/tfn/tbo/tfo/tjo/t/p9N7r9t/r9d/s9+Pt9+nv9ebw9+jx+Ory+uvy+Ozy9+zy+Ozz+e3z+O30+O30+e30+u70+O/0+e/0+vD0+PD0+fH1+fL2+vL3+/P3+/P3/PT3+/T4/PX5/Pf6/fj7/fj7//r7/f3+/v///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////yH5BAEKAP8ALAAAAAAgACAAAAj+AP8JHEiwoMGDCBMqXCjQBwsWJEh8mDgxRAgQIDp0OEGDRqZMCx1ClEjxg0WMGjl6BKkQRaFCiRIZMvSy5qBBggS1mTHDhImPCUc8evQEAIAqWaRIYcIECZIgQTARISJDRokSLA0KfQTgSRUASZc2fRp1atWrWQtycOQIAJgwYb4YhQIlSRIhQi6dOTNmzI0bLRCudQTGaBgAWrQYTQJACAC9fP0CRriBrRgxZQCQcWt0iVEjAHJGitSnTwTKbAFgNlq4s1EAeESTNk25USM2Rt3AAWAGgNwojAEAAfLjByNGFhBqUKSIDh05cuK8eaNGDRYsTpwkOXKkSJFFiy7+KGfuHLp06taxa+fuHbz4gxkOHbpD/46dOXNeA7j+RIkSu4ggUgFC8c1X330BJBjAflj0918SAQ54EAaEEFJHHXbYAZ2CC5ZRhhfZNdEEIIBMgBCFFmKooRwcAuAhiE6ISKKJB1EQSCD4QbehgryZwcUVV1RRBSSQPICQjTjOoSOLPJrhI5BCEmnkQRL88ccaa6ShJRotdtHFFlNMQQUVlliyAEIQ+OHHCy/AAEMMLrS4wgoqpGBnCpVUkgCaarKpX4v6GYWnnggxwAcfO3Co6KIA5DDJJAYUemiii1YaQA6OQoqQAnrooYMOlipqQw01XHJJAZt2+ikOoSZow6hIpZ6KkAM37rFHHnmECogkklBCiSaaNIAQDyKIgAACBCRb6QADCCDAAQd44EEPwxZ7bLLKcshss89GOy1DBikI7rgDBUDuPwEBADs=
EOF
		),
		'save_disabled.gif' => array(
			'image' => <<<EOF
R0lGODlhIAAgAOehAAAAAAEBAQICAgMDAwQEBAUFBQYGBgcHBwgICAkJCQoKCgsLCwwMDA0NDQ4ODg8PDxAQEBERERISEhMTExQUFBUVFRYWFhcXFxgYGBkZGRoaGhsbGxwcHB0dHR4eHh8fHyAgICEhISIiIiMjIyQkJCUlJSYmJicnJygoKCkpKSoqKisrKywsLC0tLS4uLi8vLzAwMDExMTIyMjMzMzQ0NDU1NTY2Njc3Nzg4ODk5OTo6Ojs7Ozw8PD09PT4+Pj8/P0BAQEFBQUJCQkNDQ0REREVFRUZGRkdHR0hISElJSUpKSktLS0xMTE1NTU5OTk9PT1BQUFFRUVJSUlNTU1RUVFVVVVZWVldXV1hYWFlZWVpaWltbW1xcXF1dXV5eXl9fX2BgYGFhYWJiYmNjY2RkZGVlZWZmZmdnZ2hoaGlpaWpqamtra2xsbG1tbW5ubm9vb3BwcHFxcXJycnNzc3R0dHV1dXZ2dnd3d3h4eHl5eXp6ent7e3x8fH19fX5+fn9/f4CAgIGBgYKCgoODg4SEhIWFhYaGhoeHh4iIiImJiYqKiouLi4yMjI2NjY6Ojo+Pj5CQkJGRkZKSkpOTk5SUlJWVlZaWlpeXl5iYmJmZmZqampubm5ycnJ2dnZ6enp+fn6CgoKGhoaKioqOjo6SkpKWlpaampqenp6ioqKmpqaqqqqurq6ysrK2tra6urq+vr7CwsLGxsbKysrOzs7S0tLW1tba2tre3t7i4uLm5ubq6uru7u7y8vL29vb6+vr+/v8DAwMHBwcLCwsPDw8TExMXFxcbGxsfHx8jIyMnJycrKysvLy8zMzM3Nzc7Ozs/Pz9DQ0NHR0dLS0tPT09TU1NXV1dbW1tfX19jY2NnZ2dra2tvb29zc3N3d3d7e3t/f3+Dg4OHh4eLi4uPj4+Tk5OXl5ebm5ufn5+jo6Onp6erq6uvr6+zs7O3t7e7u7u/v7/Dw8PHx8fLy8vPz8/T09PX19fb29vf39/j4+Pn5+fr6+vv7+/z8/P39/f7+/v///yH5BAEKAP8ALAAAAAAgACAAAAj+AP8JHEiwoMGDCBMqXChw2ahRjBghmkiR4qFDllq12rdvoUOIEitWvJhxY0eFnOTJm8dynsqX8WLGI6dK1aRJHBMqqlevW48e3rx167ZtGzZs0qTtmzYtVapIkU4a3FmvRzdvQIcWPZp0adOnUREWokevB7iz4H4O5apUnLhw4V69IiWWbFqzPb59+4mth7Qe+9zClUv34Fh6cMP1UHxX289qPWLas+fOXZ66ZeH+vNvD8U90kilbxkzuJ7nS4vB269sDGjRnzlj6QUiIZbnbt0+PGxeUGzds1qxRo8byD23buMvp5u3NN3DhxOcZPziIpbnr12///Bm0W7ZsR2X2I6w+D3v2JeiXABX6Pfy82QcBqTR/O716uOB8+47JB6F8efSVY99i4eSnH38I+RFTcvWl14Nb3wQV1GSXHaRgPAwK6CCEEnpDIUJ8wAPPbiSOM+BZ3wwVlD76zIFQHpWVIqOMpAz4kCg44ogPPm68GGMp2/UwYJA/ibJjjwfVwQ47tNjn5JM9vHLPPWcgpCSTT2aJnlxTVnlQHOmkE0ssWjrpikYcmYEQmGLG8kqZ6LlypklqHnRHTOqoE2aZ8Uw5pT/+2IGQL4kkwgYbZiSaZRllkEFGGmlcBMyghR6aqKL2Mdroo5EeMilDBaUH6qgDLUHqPwEBADs=
EOF
		),
		'saveas.gif' => array(
			'image' => <<<EOF
R0lGODlhIAAgAOebAFw1Na4wMDVpuzVquzZrvDdrvDdsvTtuvUBxwDx0wUB3wkF6w0J8w016w018xUWAx0aAxkiAw0iEx0iEyEuGx0uGyEyGxkyHyE2IyE+MyFCMyFGNyFKNyFWPyWOLylWQylWQy1WRymaNzFqSyluUzGmW3GuY3Gmdz3Si0k+y91Gy91Kz93yq14So1VO0+FW0+Fa091S1932w4X2y4pCx3WK8/GC+/IS24me9/GW++2i//W7C/arA4q7C47nO6LPS8bXU87jW9LrW9bvW9bTY9rvY9rzZ9r3a9sHb+MLc+MPd+cTd+cXf+cbf+sff+sfg+sjg+cjg+sjg+8ng+8jh+8nh+8rh+svh+8zh+8ni+8vi+8zi/M3i/M7i/Mzj+83j+83j/M7j+8/j/Mnl+s/k/NDk+9Hk/M3m/NPl/NPl/dLm/NXl/tPm/Nfm8tTm/NTm/dPn/dXn/dbn/dbn/tfn/tbo/tfo/tjo/t/p9N7r9t/r9d/s9+Pt9+nv9ebw9+jx+Ory+uvy+Ozy9+zy+Ozz+e3z+O30+O30+e30+u70+O/0+e/0+vD0+PD0+fH1+fL2+vL3+/P3+/P3/PT3+/T4/PX5/Pf6/fj7/fj7//r7/f3+/v///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////yH5BAEKAP8ALAAAAAAgACAAAAj+AP8JHEiwoMGDCBMqXCjQBwsWJEh8mDgxRAgQIDp0OEGDRqZMCx1ClEjxg0WMGjl6BKkQRaFCiRIZMvSy5qBBggS1mTHDhImPCUc8evTkSZUqWaRIYcIECZIgQTARISJDRokSLA0KJWoUqVKmTqFKpWoVK0IOjhyBAROmxxctWnpASZKkh5BLZ86MGXPjRouzadf28NAjLuG6hPHq5esX4Ya0YgYDIOxhsmTCOSNF6tMngmPIkQmDGdxjCWk8mTd3dtyoERs2bnrAMWPGbZQoPZIMAQLkxw9GjCwg1KBIER06cuTEefNGjRosWJw4SXLkSJEiixZdGF78ePLlzZ/+R59e/Xr27QczHDp0p/0dO3PmAJgPAPoTJUroIkJUAaF69u7BF8CAAdSHxX35JbFffwdhQAghddRhhx3JEVhgGWV4IV0TTQACyAQIOQihhBTKYSEAGGroBIcegngQBYEEEl9yFRIIAG1cXHHFUZBA8gBCMMo4B40m2oijjjz6iJAEf/yxxhppRInGiV10scUUU1BBhSWWLIAQBH748cILMMAQgwsnrrCCCim0mUIllSTwZZhj0gfAiXbO92acCDHABx87WCjooADkMMkkBvT5Z6CDNhpADoYiipACeuihgw6OCmpDDTVcckkBk1Z6KQ6ZDmjDpp1+ipADMe6xRx48eWQKiCSSUEKJJpo0gBAPIoiAAAIEBNvoAAMIIMABB3hA2K69/hqssBYSW+yxyS7LUEEEXqvtQAFs+09AADs=
EOF
		),
		'import.png' => array(
			'image' => <<<EOF
iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAC3UAAAt1AEYYcVpAAAAB3RJTUUH4ggJEhUox4ItkgAABKZJREFUWMPFl02MFEUYhp+vqrp7ZneWVYKGJYLJsoAJRjAmisgmmKCJChI5mJh4UcNFLiSeiN5MPPgX4oFE9GK8KWrUxAsQ8Qc0/oEBDRiikODuuqzKz872bndXl4fqnXF2BnrQw1Yymama6u97v7feeqtakmyG+WyKeW7zDsDMHfj0xz/dodN/A3BubBKAui3HGTm/lLUFFVbcsBCAHQ/eDECgMukagHOupR8riAqdxC5ojFclBWCasO05k/v5eZ6X8ixzRbjr7VNu17bBloCzv21u2wJkBShNAsDIpGPf95c9cyOeweefGkKJUAmQUgbGLtRRSuGc4+Roa8JphMj5GDPiQeUzvsp67vvDgxXWLffzv7ExAM+88SsAr2wfdD2hyFUBzFI3W7VzjhlxPrHAnUOVK6mAj45dJM9zsswDn851EcMvl80shObqDFxMXAEgBxQiQuSaiSdje8X1fHhtPxMX6iyq+f6WNf2ICAdPeFG/9v5xnn38dkoZUEoBiixNEBHWreppSVyr6pb5s/9NxpYsy8jSlGrYBwJxcpnMZmxeuxQYKN8FLUvhcu65pY/J2LYkHf59GNPb3BHx2ZgDK7/0Ihz7i4GeAJj24rQpW1dpiEd47q2jfPLStu6NSAoRzSafjG2j2vhs3PgArN7plbdy+TIGBgZYMXgTv40nnJ2w2OoSbHUJmekpd8LMZsQpxF43HDkxXrr2c1utqvng0GkAeivNFFrpcgAN8yja+ltvvKbks2Af2Tj0/8+C/5J8dHS0AeL+dYPUp/NrOws6tS0z9zWXqJ6STmRtcwaeHmDTLxt85zINUTasOznX0Uk7MjBVn2KqPsVXP52nVtV8HO1vCK5T8jYW9oyyeudy9h081aKBrpbAWouoplseOTFOraoJFplrWordjx1Aa02cgI5HSKxqO+g6AhARlFKIEpTSiAiHj//BC+ff6U4De0YbZmaMKUwNgjDsUoR5MMcRC1Ba8WL+XlfJX3/yMFEUEQQBQRDQ1xNQMa4l9hVF2CMpWmnyPMdov2+zLPUgQgUlEnhz+9dtY1o7tNZopcoZmHIBzjmMNmQSkksEuulgr+oPSX5Orli9Ns2abBhgw4AoigjDkDRLyxmoZBalFVmW8fJxfyo657C2OXWZ3s8YD7UlX3Pvd+w++e+wXnR7l/i+s6a7bSgiXkAiiCiU0mitMMZ/KxEuHbjU8swdm45itMZojVLKi7DoG2MIgqA7I5rMNc45XO647vpenHOsX+gNxOUOW1j15qEf2MvGRvXDQwvagh8bT4ozwPfTVLpnQJQUDAhaGxKtSQNDFmiyQJMaxRPqc3/l2nHGi6yovrkNNcZolNKNLV3KQGqzpmFYg5OEL0Y9K1a1i+/uR89w5Dz0FpfSOiE1yYEcsUHhAaBEOhpRG4B+aR3SLsRiPRMdtpFxqrgVF5U7zxyAFPN14axdMaCVolqp+iv6bUnLtbxDAfRVZwebIosC7x+quDlHxn8v7O8tfy949/CE++xbL6yTZy54I+pwiuWd0Mx5j4iMBzW0tA+ArXct5oENi8uv5Z2oatsttHp7jaY+6hIVF/W8NJ7M9+v5P+1U8AXf3vebAAAAAElFTkSuQmCC
EOF
		),
		'export.png' => array(
			'image' => <<<EOF
iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAC3UAAAt1AEYYcVpAAAAB3RJTUUH4ggJEg8TxqQ+bQAABI9JREFUWMPFl01sFVUUx3/n3pn30b7SSlB5RDApRRYSwWi0EkjEUIMflYgLXehKXbE00RATXRhdaUxYED8WLlxCJGpcElADJCoRAhq+gpKQvtpWKaGv086be6+LO28e76N9JZr0JJM3d96Zc/73f/733DsSJ/MspymW2ZYdQND64OiZv92xy9cBuDY+A0DVdMeZd76UpRUFNty5EoC9T98LQKgSWTIA51zTOFKQT3USuTB7XpQaAHPk2t4LrPe31nblWVpFuO/LC27fnsGmgPV7Y01bgCQFpYkBGJtxHDp10zM35hl879UhlAiFEOnKwPh0FaUUzjnOV5oTziHknY8xLx6UnfezrFo/3j5YYHi99//JRAC88fkVAD56fdD15EQWBVCnrj5r5xzz4nxigUeGCgupgG9O38BaS5J44HNWpzF8uUxiIBcszsCN2KUALKAQEfKukXgmMgvW87kt/UxNV1lV8uPRzf2ICEfOeVHv/+osb7/8IF0ZUEoBiqQWIyIMb+xpSlwq6ib/+n8zkSFJEpJajWKuDwSi+CaJSXh2y1qgfHt9wDqbJS8VdXbNRKbputXGxv+hnJ9jQCYZkElK5ia7N2p0NMa7n3x3ewAkFVF9xq0JR+dH2t65b/06yuUyGwbv4Y+JmKtTBlNcgymuIQl6ugNITEJUg8jrhhPnJjrWfnR+hOhqxM6L29qCloqaw8cuA9BbaKTQSncHkDWP1LZuuquj8JJqLbt/eP+mNk08//jQf98LFko+Oj9CbSrJxgO7BrL7SqWSgXhyeJDqnP1/N6M69a228+I2KpUK5XK543vF+FrHTtoRwGx1ltnqLCd/m2xbcrdS3wbu4EiTDg4dudCkgSUxYIxBVKNbnjg3kS2/VupbbWDXQOZ7+NhltNZEMehojNioto2uIwARQSmFKEEpjYhw/Oxf7Jh+oiP1rTZ85jG+/uEKSimCIEibGoS53BI1YMOWjuhBLUZ9q7118hny+TxhGBKGIX09IYXANcVesBX3SA2tNNZaAu3r/+bde6AK4aqGe7dS6FpDO1o7tNZopboDmHUhzjkCHZCIxorlg/Fveenj+zOfde+sbQtUOVBpGr/GMJ/uPeX3ybwvay2pdQdQSAxKK5Ik4cOzfld0zvHQzl8zn+u9L3RkYPOOX5rGB373ovtsjU/jTNAdQL3mQRCgxGBR1I8QItJRyVmwtGQ29VHpi0EQICJLOxPOWI1zDmcdA3f04pxj60rfQJx1GGv5YgFBbh9a0TQ+PRGne0Cqm5osnQGUn4EFtA6IcKDBWsMr14/y/oH2Xq+1B2+tTZehTlfTLXG7AaiZpEGzCXAS82PFBzYqzvxGXrzkGUsPpb3EnJmMqZKjJBawiAnTHuAn06l8bQD6pfmRdjkMBhHpvIycSk/F/lc5yWovqb9OO+uSGNBKUSwU/RH9gbjpWN5Jf33F+sNGk8mHKfXpyTkf+N+V/b3dvwsOHp9y3//s1/T5P6f9BtRhF7OLrIY64HzgQQ2t7QNg96OreWrbalmaCLvYDM29vURDH1XJpwd12zWeLPfn+b/m0wRy2fVUBQAAAABJRU5ErkJggg==
EOF
		),
	),
);

?>
