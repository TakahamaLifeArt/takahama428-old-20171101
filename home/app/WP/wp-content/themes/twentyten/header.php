<?php
/**
 * Header template for our theme
 *
 * Displays all of the <head> section and everything up till <div id="main">.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() )
		echo esc_html( ' | ' . sprintf( __( 'Page %s', 'twentyten' ), max( $paged, $page ) ) );

	?></title>
<link rel="profile" href="//gmpg.org/xfn/11" />
<link rel="stylesheet" type="text/css" href="/common/css/base.css" media="screen" />
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />	
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php
	/*
	 * We add some JavaScript to pages with the comment form
	 * to support sites with threaded comments (when in use).
	 */
	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	/*
	 * Always have wp_head() just before the closing </head>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to add elements to <head> such
	 * as styles, scripts, and meta tags.
	 */
	wp_head();
?>

<style>

#container {
    position: relative;
    width: 750px;
    margin: 0px auto;
    padding: 0px 0px 20px;
    z-index: 0;
}

</style>
</head>

<body <?php body_class(); ?>>
<div id="wrapper" class="hfeed">
	<div id="header">
		<div id="masthead">
		<div id="top_bar">
			<div class="inner">

			</div>
		</div>
<div class="inner">
				<div class="h_wrap hw_1"><a href="//www.takahama428.com/"><img src="//www.takahama428.com/app/WP/wp-content/uploads/2015/12/logo1.png" alt="オリジナルTシャツ屋"></a></div>
					<div class="h_wrap hw_2"><img src="//www.takahama428.com/app/WP/wp-content/uploads/2015/12/no1_mark.png" alt="業界NO.1！スピード仕上げ 親切対応！"></div>
		
					<div class="h_wrap hw_3">
						<div class="h_tel">
							<a href="/contact/guide/">
								<p class="p1">お急ぎの方は<br>お電話下さい！</p>
								<p class="p2"></p>
								<p class="p3"><img src="//www.takahama428.com/app/WP/wp-content/uploads/2015/12/tel.png" alt="電話"></p>
								<p class="p4">TEL</p>
								<p class="p5">0120-130-428</p>
								<p class="p6">受付時間：平日 10:00-18:00</p>
								<p class="p7"><img src="//www.takahama428.com/app/WP/wp-content/uploads/2015/12/arrow_b.png"></p>
							</a>
						</div>
				
						<div class="h_mail">
							<a href="/contact/">
								<img src="//www.takahama428.com/app/WP/wp-content/uploads/2015/12/mail.png" alt="メール">
								<span>MAIL</span>
								<p>お問い合わせ（相談）</p>
								<img src="//www.takahama428.com/app/WP/wp-content/uploads/2015/12/arrow_w.png" class="h_arrow">
							</a>
						</div>
					</div>
					<div class="gro">
						<?php include $_SERVER['DOCUMENT_ROOT']."/common/inc/globalmenu_wp.php"; ?>
					</div>
<div id="access" role="navigation">
			  <?php /* Allow screen readers / text browsers to skip the navigation menu and get right to the good stuff */ ?>
				<div class="skip-link screen-reader-text"><a href="#content" title="<?php esc_attr_e( 'Skip to content', 'twentyten' ); ?>"><?php _e( 'Skip to content', 'twentyten' ); ?></a></div>
				<?php /* Our navigation menu. If one isn't filled out, wp_nav_menu falls back to wp_page_menu. The menu assiged to the primary position is the one used. If none is assigned, the menu with the lowest ID is used. */ ?>
				<?php wp_nav_menu( array( 'container_class' => 'menu-header', 'theme_location' => 'primary' ) ); ?>
			</div><!-- #access -->
		</div><!-- #masthead -->
	</div><!-- #header -->		
		
	</div>


	<div id="main">