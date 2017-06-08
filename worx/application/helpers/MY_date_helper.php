<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter MY_date Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/helpers/date
 */
// ------------------------------------------------------------------------
/**
 * Converts time in milliseconds to given output, rounds to int on default
 *
 * @access	public
 * @param	integer
 * @param	string
 * @param	string
 * @param	boolean
 * @return	int / float
 */
function time_convert( $time = null, $output = 'hours', $input = 'seconds', $int = TRUE)
{
	// formats for converting
	$formats['seconds'] = 1;
	$formats['minutes'] = 60;
	$formats['hours'] 	= 3600;
	$formats['days'] 	= 86400;
	$formats['month'] 	= 2592000;	
	$formats['years'] 	= 31536000;
	// convert time to seconds
	$time = $time * $formats[$input];
	// return as int or float
	if( $int === TRUE )
	{
		// convert time to output
		return floor($time / $formats[$output]);
	}
	else
	{
		// convert time to output
		return $time / $formats[$output];		
	}
}
/**
 * Converts unix timestamp to time ago
 *
 * @access	public
 * @param	integer
 * @param	string
 * @return	int / float
 */
function time_ago( $time )
{
	//load assets
	$CI = &get_instance();
	$CI->lang->load('date');
	$CI->load->helper('language');
	// get time difference
	$ago = time() - $time;
	// if one minute ago
	if( $ago < 100 )
	{
		return sprintf(lang('minute_ago'), time_convert($ago, 'minutes'));
	}
	// minutes ago
	elseif( $ago > 100 && $ago < 3600)
	{
		return sprintf(lang('minutes_ago'), time_convert($ago, 'minutes'));
	}
	// one hour ago
	elseif( $ago > 3600 && $ago < 7200)
	{
		return sprintf(lang('hour_ago'), time_convert($ago, 'hours'));
	}
	// hours ago
	elseif( $ago > 7200 && $ago < 86000)
	{
		return sprintf(lang('hours_ago'), time_convert($ago, 'hours'));
	}
	// one day ago
	elseif( $ago > 86000 && $ago < 172000)
	{
		return sprintf(lang('day_ago'), time_convert($ago, 'days'));
	}
	// days ago
	elseif( $ago > 170000 && $ago < 2592000)
	{
		return sprintf(lang('days_ago'), time_convert($ago, 'days'));
	}
	// one month ago
	elseif( $ago > 2592000 && $ago < 5184000 )
	{
		return sprintf(lang('one_month_ago'), time_convert($ago, 'month'));
	}
	// month ago
	elseif( $ago > 5184000 && $ago < 31530000 )
	{
		return sprintf(lang('month_ago'), time_convert($ago, 'month'));
	}
	// one year ago
	elseif( $ago > 31530000 && $ago < 63060000 )
	{
		return sprintf(lang('year_ago'), time_convert($ago, 'years'));
	}
	// years ago
	elseif( $ago > 63060000 )
	{
		return sprintf(lang('years_ago'), time_convert($ago, 'years'));
	}
}
/**
 * Server_time
 *
 * @access	public
 * @return	server time 
 */
function server_time()
{
	$server_offset = substr(date('O'),0,3);
	$offset   = $server_offset * 3600; // timezone offset
	// return server date
	return gmdate( 'Y-m-d H:i:s', time() + $offset );
}
/**
 * server_to_user
 *
 * @access	public
 * @return	server time for user gtml 
 */
function server_to_user( $timestamp, $offset = 'UTC', $unix = FALSE )
{
	is_array(timezones($offset)) ? $offset = 'UTC' : '';
	// get server time in gmt 0
	$time_gmt = gmt_time($timestamp, server_gmt_offset(), TRUE);
	// calc user time
	$user_time = $time_gmt + (timezones($offset)*3600);
	// if unix = TRUE return timestamp
	if( $unix == TRUE )
	{
		return $user_time;
	}
	// return gmt time
	return date( 'Y-m-d H:i:s', $user_time );
}
/**
 * gmt_time - converts to gmt +0
 *
 * @access	public
 * @return	gmt +0 
 */
function gmt_time( $timestamp, $offset = 'UTC', $unix = FALSE )
{
	// calc gtml 0 stamp
	$gmt_zero = $timestamp - (timezones($offset)*3600);
	// if unix = TRUE return timestamp
	if( $unix == TRUE )
	{
		return $gmt_zero;
	}
	// else return date
	return date( 'Y-m-d H:i:s', $gmt_zero );
}
/**
 * Server_gmt_offset
 *
 * @access	public
 * @param boolean
 * @return	server gmt 
 */
function server_gmt_offset( $ref = TRUE )
{
	$server_offset = substr(date('O'),0,3);
	// prepare date if ref == true
	if( $ref == TRUE )
	{
		return get_timezone($server_offset);
	}
	// return server offset
	return $server_offset;
}
/**
 * get_timezone
 *
 * @access	public
 * @param boolean
 * @return	server gmt 
 */
function get_timezone( $offset )
{
	// prep offset
	if( substr($offset, 1,1) != 1 && substr($offset, 2,1) == 0 )
	{
		$offset = substr($offset,0,2);
	}
	elseif( substr($offset, 3,1) == 0 )
	{
		$offset = substr($offset,0,3);	
	}
	elseif( substr($offset, 4,1) == 0 )
	{
		$offset = substr($offset,0,4);
	}
	if( strlen($offset) > 3 )
	{
		$offset = substr($offset, 0, 3).'.'.substr($offset,3,strlen($offset)-1);
	}
	if( substr($offset,1,1) == 0 )
	{
		if( strlen($offset) == 2 )
		{
				$offset = substr($offset, 1, 1);
		}
		else
		{
			$offset = substr($offset, 1, strlen($offset)-1);
		}
	}
	// zones
	$zones = array(
					'UM12'		=> -12,
					'UM11'		=> -11,
					'UM10'		=> -10,
					'UM95'		=> -9.5,
					'UM9'		=> -9,
					'UM8'		=> -8,
					'UM7'		=> -7,
					'UM6'		=> -6,
					'UM5'		=> -5,
					'UM45'		=> -4.5,
					'UM4'		=> -4,
					'UM35'		=> -3.5,
					'UM3'		=> -3,
					'UM2'		=> -2,
					'UM1'		=> -1,
					'UTC'		=> 0,
					'UP1'		=> +1,
					'UP2'		=> +2,
					'UP3'		=> +3,
					'UP35'		=> +3.5,
					'UP4'		=> +4,
					'UP45'		=> +4.5,
					'UP5'		=> +5,
					'UP55'		=> +5.5,
					'UP575'		=> +5.75,
					'UP6'		=> +6,
					'UP65'		=> +6.5,
					'UP7'		=> +7,
					'UP8'		=> +8,
					'UP875'		=> +8.75,
					'UP9'		=> +9,
					'UP95'		=> +9.5,
					'UP10'		=> +10,
					'UP105'		=> +10.5,
					'UP11'		=> +11,
					'UP115'		=> +11.5,
					'UP12'		=> +12,
					'UP1275'	=> +12.75,
					'UP13'		=> +13,
					'UP14'		=> +14
				);
	// find zone
	foreach($zones as $key => $time_offset)
	{
		if( $time_offset == $offset ) 
		{
			return $key;
		}
	}
}
/**
 * leading_zero
 *
 * @access	public
 * @param  int
 * @return	in with leading zero
 */
function leading_zero( $date )
{
	// if 2 numbers
	if( strlen($date) > 1)
	{
		return $date;	
	}
	// if not, add leading zero
	return '0'.$date;
}
/* End of file MY_date_helper.php */
/* Location: ./application/helpers/MY_date_helper.php */