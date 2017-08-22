<?php

/**
 * Counts the days (optionally limited by type of day) between (inclusive) two dates.
 *
 * @param string $start_date First day (Y-m-d format).
 * @param string $end_date   Last day (Y-m-d format).
 * @param array  $types      Optional. Types of days to count. Default to all types
 *
 * @return int Number of days matching the $types.
 */
function get_days_between_dates( $start_date, $end_date, $types = array() ) {
	$count = 0;

	$included_day_type_indexes = array();
	if ( empty( $types ) ) {
		$included_day_type_indexes = range( 1, 7 );
	} else {
		foreach ( $types as $type ) {
			switch ( strtolower( $type ) ) {
				case 'weekday':
					$included_day_type_indexes = array_merge( $included_day_type_indexes, range( 1, 5 ) );
					break;
				case 'weekend':
					$included_day_type_indexes = array_merge( $included_day_type_indexes, range( 6, 7 ) );
					break;
				case 'monday':
				case 'mon':
					$included_day_type_indexes[] = 1;
					break;
				case 'tuesday':
				case 'tues':
				case 'tue':
				case 'tu':
					$included_day_type_indexes[] = 2;
					break;
				case 'wednesday':
				case 'wed':
					$included_day_type_indexes[] = 3;
					break;
				case 'thursday':
				case 'thurs':
				case 'thur':
				case 'thu':
				case 'th':
					$included_day_type_indexes[] = 4;
					break;
				case 'friday':
				case 'fri':
					$included_day_type_indexes[] = 5;
					break;
				case 'saturday':
				case 'caturday':
				case 'sat':
					$included_day_type_indexes[] = 6;
					break;
				case 'sunday':
				case 'sun':
					$included_day_type_indexes[] = 7;
					break;
			}
		}

		$included_day_type_indexes = array_unique( $included_day_type_indexes );
	}

	$date = strtotime( $start_date );
	$end = strtotime( $end_date );
	while ( $date <= $end ) {
		if ( in_array( date( 'N', $date ), $included_day_type_indexes ) ) {
			$count++;
		}

		$date = strtotime( '+1 day', $date );
	}

	return $count;
}
