function posting_streak( $blog_id, $after = '' ) {
	global $wpdb;

	switch_to_blog( $blog_id );

	$longest_streak = $longest_slump = array(
	    'days' => 0,
	);

	$after_sql = '';
	if ( $after ) {
		$after_sql = $wpdb->prepare( 'AND post_date_gmt > %s', $after );
	}

	$posts = $wpdb->get_col(
		"SELECT post_date
		FROM $wpdb->posts
		WHERE post_type = 'post'
		AND post_status = 'publish'
		$after_sql"
	);

	if ( ! count( $posts ) ) {
		echo "\n\nNo posts found. Looks like your life is a slump!\n\n";

		return;
	}

	// First post always starts a streak
	$first_day = $streak_start_day = $last_post_day = date( 'Y-m-d', strtotime( $posts[0] ) );

	$day_seconds = 24 * 60 * 60;

	foreach ( $posts as $date ) {
		// Strip times
		$day = date( 'Y-m-d', strtotime( $date ) );

		// Multiple posts in one day
		if ( $day == $last_post_day ) {
			continue;
		}

		$previous_day_ts = strtotime( $day . ' -1 day' );
		if ( $last_post_day != date( 'Y-m-d', $previous_day_ts ) ) {
			$slump_days = ( $previous_day_ts - strtotime( $last_post_day ) ) / $day_seconds + 1;
			if ( $slump_days > $longest_slump['days'] ) {
				$longest_slump['start'] = date( 'Y-m-d', strtotime( $last_post_day . ' +1 day' ) );
				$longest_slump['end']   = date( 'Y-m-d', $previous_day_ts );
				$longest_slump['days']  = $slump_days;
			}

			$streak_days = ( strtotime( $last_post_day ) - strtotime( $streak_start_day ) ) / $day_seconds + 1;
			if ( $streak_days > $longest_streak['days'] ) {
				$longest_streak['start'] = $streak_start_day;
				$longest_streak['end']   = $last_post_day;
				$longest_streak['days']  = $streak_days;
			}

			$streak_start_day = $day;
		}

		$last_post_day = $day;
	}

	echo "\nPosts published: " . number_format( count( $posts ) ) . " since $first_day";
	echo "\n\nLongest streak: " . number_format( $longest_streak['days'] ) . " ({$longest_streak['start']} - {$longest_streak['end']})";
	echo "\nLongest slump: " . number_format( $longest_slump['days'] ) . " ({$longest_slump['start']} - {$longest_slump['end']})";
	echo "\n\nCurrently on a ";
	$yesterday = date( 'Y-m-d', strtotime( 'yesterday' ) );
	if ( $last_post_day >= $yesterday ) {
		$streak_days = ( strtotime( $last_post_day ) - strtotime( $streak_start_day ) ) / $day_seconds + 1;
		echo "$streak_days day streak, since $streak_start_day. Post ";
		if ( $last_post_day == $yesterday ) {
			echo 'today';
		} else {
			echo 'tomorrow';
		}
		echo ' to keep it going!';
	} else {
		echo "$slump_days day slump. Last post was on $last_post_day.";
	}
	echo "\n\n";

	restore_current_blog();
}
