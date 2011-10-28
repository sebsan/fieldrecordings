<?php

if(isset($_POST['ae_pwd']))
{
	session_start();
	if(isset($_SESSION['ae_pwd_tries']) && $_SESSION['ae_pwd_tries'] > 3)
	{
		echo '<html>
		<body>
		</body>
<pre>
You tried to access this page with a wrong password more than 3 times.
Please contact '.get_bloginfo('admin_email').' if you think you should be granted to access this page.
</pre>
		</html>
		';
		return;
	}
	if($_POST['ae_pwd'] === $post->post_password)
	{
		header('Content-Type:text/plain; charset=UTF-8');

		global $wpdb;

		$query = "
		SELECT * FROM ".$wpdb->posts." AS p 
		INNER JOIN ".$wpdb->postmeta." AS m 
		ON p.ID = m.post_id 
		WHERE (p.post_type = 'soe_artist' AND m.meta_key = 'artist_email') 
		ORDER BY p.post_date DESC;
		";

		$artists = $wpdb->get_results($query, OBJECT);

		foreach($artists as $artist)
		{
			echo $artist->meta_value . "\n";
		}
	}
	else
	{
		if(isset($_SESSION['ae_pwd_tries']))
			$_SESSION['ae_pwd_tries'] += 1;
		else
			$_SESSION['ae_pwd_tries'] = 1;
		echo '<html>
		<body>
		</body>
		<h3>Wrong password, please try again.</h3>
		<form name="ae" method="post" action="'.get_permalink($post->ID).'">
		<div><em>Password:</em></div>
		<input size="25" type="password" name="ae_pwd"/> <input type="submit" value="Ok">
		</html>
		';
	}
}
else
{
	echo '<html>
	<body>
	</body>
	<h3>This page is password protected</h3>
	<form name="ae" method="post" action="'.get_permalink($post->ID).'">
	<div><em>Password:</em></div>
	<input size="25" type="password" name="ae_pwd"/> <input type="submit" value="Ok">
	</html>
	';
}

?>