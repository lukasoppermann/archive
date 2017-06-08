<?php echo `git pull origin master --force && git submodule update --init --recursive 2>&1`;
// fetch payload data
$payload = json_decode($_REQUEST['payload']);
// ----------------------
// find last commit 
foreach($payload->commits as $commits)
{
	if( $commits->id == $payload->after )
	{
		// get message
		$commit = $commits->message;
	}
}
// ----------------------
// check for cache handle
if( stristr($commit,'-cache') != FALSE )
{
	// remove chached files
	// ----------------------
	// get css all file names
	$files = glob('libs/css/cache/*');
	// get js all file names & merge
	$files = array_merge(glob('libs/js/cache/*'), $files);
	foreach($files as $file)
	{ // iterate files
		if(is_file($file))
		{
			unlink($file); // delete file
		}
	}
}