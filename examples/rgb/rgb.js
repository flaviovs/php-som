$(function () {
    var $res = $("#result");
    var timer = null;

    function rgb2hex(r, g, b)
    {
	return '#' + ('000000'
		      + Number(r << 16 | g << 8 | b).toString(16)).slice(-6);
    }

    function build_list(colors)
    {
	var $ul = $('<ul>'), $li;
	for (name in colors)
	{
	    $li = $('<li>');

	    $li
		.append($("<span>")
			.css('padding', '0 2em')
			.css('background-color', rgb2hex(colors[name][0],
							 colors[name][1],
							 colors[name][2])))
		.append(name);
	    $ul.append($li);
	}

	return $ul;
    }

    function ready(data)
    {
	if ( data['error'] )
	{
	    $res.text(data['error']);
	    return;
	}

	$res
	    .empty()
	    .append('<h2>Best matches</h2>');

	if (Object.keys(data['best']).length)
	    $res.append(build_list(data['best']));
	else
	    $res.append('No named color match the picked RGB value.');

	if (Object.keys(data['other']).length)
	{
	    $res
		.append('<h2>Other matches</h2>')
		.append(build_list(data['other']));
	}

    }

    function color_changed()
    {
	var color = $(this).val();

	$('#demo')
	    .css('background-color', color)
	    .text(color);

	$res.empty();

	if (timer)
	{
	    clearTimeout(timer);
	    timer = null;
	}

	timer = setTimeout(function() {
	    $res.text('Please wait...');
	    $.get('getresult.php',
		  {'rgb': color},
		  ready,
		  'json')
		.fail(function(data, text) {
		    $res.html('AJAX request failed: <em>' + text + '</em>');
		})}, 200);
    }

    $("input[type='text'].color")
	.minicolors({inline: true, control: 'wheel'})
	.change(color_changed);

    color_changed();

});