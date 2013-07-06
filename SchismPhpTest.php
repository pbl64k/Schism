<?php

	require_once('PicoparsecTokenList.php');
	require_once('Picoparsec.php');
	require_once('Schism.php');

?><!DOCTYPE HTML PUBLIC
          "-//W3C//DTD HTML 4.01//EN"
          "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <title>Schism/PHP tests</title>
    <style type="text/css">

      body
      {
      }

    </style>
    <script type="text/javascript">
	</script>
  </head>
  <body>
    <div>
<?php

	try
	{
		$si = \Schism\Schism::make();

		$si->execute(file_get_contents('schism-stdlib.scm'));

		$res = $si->execute(file_get_contents('schism-tests.scm'));

		print(count($res).' tests.<br>');

		foreach ($res as $x)
		{
			print('Test passed.<br>');
		}
	}
	catch (Exception $ex)
	{
		print('ERROR.<br><pre>'.print_r($ex, TRUE).'</pre>');
	}

?>
    </div>
  </body>
</html>
