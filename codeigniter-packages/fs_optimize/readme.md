# fs_optimize

## Configuration

The drivers can be configured in a config file with the name `fs_optimize` using the following options.

	/*
	|--------------------------------------------------------------------------
	| FS Optimize
	|--------------------------------------------------------------------------
	|
	| HTML Compression
	*/
	$config['compression']['html'] = array(
		'compression' => TRUE,
		'gzip' => TRUE,
		'expire' => 3600
	);

	// CSS Compression
	$config['compression']['css'] = array(
		'compression' => TRUE,
		'gzip' => TRUE,
		'expire' => 3600
	);

	// JS Compression
	$config['compression']['css'] = array(
		'compression' => TRUE,
		'gzip' => TRUE,
		'expire' => 3600,
		'minify' => TRUE
	);