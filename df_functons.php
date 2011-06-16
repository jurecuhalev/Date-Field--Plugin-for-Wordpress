<?php
/**
*
*	General Functions
*
*/

/**
*	Date Inputs
*
*	Generates full set of date input fields - Day, Month, Year, Hour, Minute
*
*/
function date_input( $date, $name ) {
	
	global $wp_locale;
	
	/* If a date is not passed, choose 0 */
	$time_adj = current_time('timestamp');
	$jj = ($date) ? date( 'd', $date ) : 0;
	$mm = ($date) ? date( 'm', $date ) : 0;
	
	echo '<style>
		.df_input_year{widtgh:40px;}
		.df_input_time{width:30px;}
	</style>';
	
	/* Month Input */
	$month = '<select name="' . $name . '_month">';
	$month .= '<option value="0">Month</option>';
	for ( $i = 1; $i < 13; $i = $i +1 ) {
		$month .= "\t\t\t" . '<option value="' . zeroise($i, 2) . '"';
		if ( $i == $mm )
			$month .= ' selected="selected"';
		$month .= '>' . $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) ) . "</option>\n";
	}
	$month .= '</select>';
	
	/* Day Input */
	$day = '<select name="' . $name . '_day">';
	$day .= '<option value="0">Day</option>';
	for ( $i = 1; $i < 32; $i = $i +1 ) {
		$day .= '<option value="' . zeroise($i, 2) . '"';
		if ( $i == $jj )
			$day .= ' selected="selected"';
		$day .= '>' . $i . "</option>\n";
	}
	$day .= '</select>';
	
	/* Year Input */
	if( $date ) { $value = date('Y', $date) ;} else { $value = 'Year'; }
	$year = '<input type="text" value="' . $value . '" maxwidth="4" size="4" name="' . $name . '_year" class="df_input_year" />';
	
	/* Time input */
	if( $date ) { $value = date('H', $date) ;} else { unset($value); }
	$hour = '<input type="text" maxlength="2" size="2" name="' . $name . '_hour" value="'.$value.'" class="df_input_time">';
	if( $date ) { $value = date('i', $date);} else { unset($value); }; 
	$minute = '<input type="text" maxlength="2" size="2" name="' . $name . '_minute" value="'.$value.'" class="df_input_time">';
	
	echo $month . ' ' . $day . ', ' . $year . '@' . $hour . ':' . $minute;
	
	
}

function isEmpty($array){
return (count(array_filter($array)) == 0) ? 1 : 0;
}
?>