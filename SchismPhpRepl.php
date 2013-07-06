<?php

	require_once('PicoparsecTokenList.php');
	require_once('Picoparsec.php');
	require_once('Schism.php');

	print('Starting up Schism/PHP REPL...'."\n"); 

	$schism = \Schism\Schism::make();

	$schism->execute(file_get_contents('schism-stdlib.scm'));

	print('> ');

	while (($line = readline()) !== FALSE)
	{
		try
		{
			$res = $schism->execute($line);

			foreach ($res as $r)
			{
				print(\Schism\reprNative($r)."\n");
			}
		}
		catch (\Schism\SchismRuntimeException $ex)
		{
			print('Error'."\n");
		}

		print('> ');
	}

	print("\n".'Bye.'."\n");

?>
