<?php

function pwqcheck($newpass, $oldpass = '', $user = '', $aux = '', $args = '')
{
// pwqcheck(1) itself returns the same message on internal error
	$retval = 'Bad passphrase (check failed)';

	$descriptorspec = array(
		0 => array('pipe', 'r'),
		1 => array('pipe', 'w'));
// Leave stderr (fd 2) pointing to where it is, likely to error_log

// Replace characters that would violate the protocol
	$newpass = strtr($newpass, "\n", '.');
	$oldpass = strtr($oldpass, "\n", '.');
	$user = strtr($user, "\n:", '..');

// Trigger a "too short" rather than "is the same" message in this special case
	if (!$newpass && !$oldpass)
		$oldpass = '.';

	if ($args)
		$args = ' ' . $args;
	if (!$user)
		$args = ' -2' . $args; // passwdqc 1.2.0+

	$command = 'exec '; // No need to keep the shell process around on Unix
	$command .= 'pwqcheck' . $args;
	if (!($process = @proc_open($command, $descriptorspec, $pipes)))
		return $retval;

	$err = 0;
	fwrite($pipes[0], "$newpass\n$oldpass\n") || $err = 1;
	if ($user)
		fwrite($pipes[0], "$user::::$aux:/:\n") || $err = 1;
	fclose($pipes[0]) || $err = 1;
	($output = stream_get_contents($pipes[1])) || $err = 1;
	fclose($pipes[1]);

	$status = proc_close($process);

// There must be a linefeed character at the end.  Remove it.
	if (substr($output, -1) === "\n")
		$output = substr($output, 0, -1);
	else
		$err = 1;

	if ($err === 0 && ($status === 0 || $output !== 'OK'))
		$retval = $output;

	return $retval;
}

?>
