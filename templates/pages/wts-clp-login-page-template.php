<?php
/*
 * Template Name: Wts Custom Login Page 
 * Description: A Page Template for our custom login page.This load attrubutes 
 * as per selected theme.
 */
?>

<?php
?>
<html>
	<head>
		<title>Login</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
	</head>
<body class="wts-clp-login-page_body">
<?php //get_header(); ?>
<?php
//this will checks for posts and show their content.
if ( have_posts() ) {
	while ( have_posts() )
	{			
		the_post();	
		the_content();
		
	}
}
?>
</body>
</html>