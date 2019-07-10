<?php

if ( ! function_exists( '_' ) ) {
	function _( $str ) {
		echo $str;
	}
}

function sanit( $str ) {
	return addcslashes( str_replace( array( ';', "\n" ), '', $str ), '\\' );
}

function parse_db_host( $host ) {
	$port    = null;
	$socket  = null;
	$is_ipv6 = false;

	// First peel off the socket parameter from the right, if it exists.
	$socket_pos = strpos( $host, ':/' );
	if ( $socket_pos !== false ) {
		$socket = substr( $host, $socket_pos + 1 );
		$host   = substr( $host, 0, $socket_pos );
	}

	// We need to check for an IPv6 address first.
	// An IPv6 address will always contain at least two colons.
	if ( substr_count( $host, ':' ) > 1 ) {
		$pattern = '#^(?:\[)?(?P<host>[0-9a-fA-F:]+)(?:\]:(?P<port>[\d]+))?#';
		$is_ipv6 = true;
	} else {
		// We seem to be dealing with an IPv4 address.
		$pattern = '#^(?P<host>[^:/]*)(?::(?P<port>[\d]+))?#';
	}

	$matches = array();
	$result  = preg_match( $pattern, $host, $matches );

	if ( 1 !== $result ) {
		// Couldn't parse the address, bail.
		return false;
	}

	$host = '';
	foreach ( array( 'host', 'port' ) as $component ) {
		if ( ! empty( $matches[ $component ] ) ) {
			$$component = $matches[ $component ];
		}
	}

	return array( $host, $port, $socket, $is_ipv6 );
}
