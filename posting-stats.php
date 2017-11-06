function posting_stats( $blog_id, $since_date, $new_post_id = 0 ) {
	global $wpdb;

	switch_to_blog( $blog_id );

	// Useful for counting a latest unpublished post
	$where_or = '';
	if ( $new_post_id ) {
		$where_or = $wpdb->prepare( ' OR ID = %d', $new_post_id );
	}

	$posts = $wpdb->get_col( $wpdb->prepare(
		"SELECT post_content
		 FROM $wpdb->posts
		 WHERE ( post_type = 'post'
		 	AND post_status = 'publish'
		 	AND post_date >= %s )
		 	$where_or",
		$since_date
	) );

	if ( ! count( $posts ) ) {
		echo "\n\nNo posts found.\n\n";

		return;
	}

	$post_count = count( $posts );
	$word_count = $link_count = $img_count = $vid_count = 0;

	foreach ( $posts as $post ) {
		$word_count += str_word_count( strip_tags( $post ) );
		$link_count += substr_count( $post, '<a ' );
		$img_count += substr_count( $post, '<img ' );

		$post = apply_filters( 'the_content', $post );
		$vid_count += substr_count( $post, 'videopress.com/embed' );
		$vid_count += substr_count( $post, 'youtube-player' );
	}

	echo "\n" . number_format( $post_count ) . " Posts";
	echo "\n" . number_format( $word_count ) . " Words";
	echo "\n" . number_format( $link_count ) . " Links";
	echo "\n" . number_format( $img_count ) . " Images";
	echo "\n" . number_format( $vid_count ) . " Videos";

	echo "\n\n";

	restore_current_blog();
}
