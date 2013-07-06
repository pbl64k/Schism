<?php

	namespace Schism
	{
		class SchismRuntimeException extends \Exception
		{
		}
	
		function isBoolean()
		{
			$args = func_get_args();

			if (count($args) !== 1)
			{
				throw new SchismRuntimeException('boolean?: invalid number of arguments');
			}

			$x = $args[0];

			return toBoolean(is_array($x) && $x[0] === 'boolean');
		}
	
		function toBoolean($x)
		{
			return array('boolean', $x ? TRUE : FALSE);
		}
	
		function fromBoolean($x)
		{
			if (! is_array($x) || $x[0] !== 'boolean')
			{
				throw new SchismRuntimeException('Argument is not boolean:'."\n".var_export($x, TRUE));
			}
	
			return $x[1];
		}
	
		function isNumeric()
		{
			$args = func_get_args();

			if (count($args) !== 1)
			{
				throw new SchismRuntimeException('numeric?: invalid number of arguments');
			}

			$x = $args[0];

			return toBoolean(is_array($x) && $x[0] === 'numeric');
		}
	
		function toNumeric($x)
		{
			if (! is_numeric($x))
			{
				throw new SchismRuntimeException('Argument is not numeric:'."\n".var_export($x, TRUE));
			}
	
			return array('numeric', $x);
		}
	
		function fromNumeric($x)
		{
			if (! fromBoolean(isNumeric($x)))
			{
				throw new SchismRuntimeException('Argument is not numeric:'."\n".var_export($x, TRUE));
			}
	
			return $x[1];
		}
	
		function isString()
		{
			$args = func_get_args();

			if (count($args) !== 1)
			{
				throw new SchismRuntimeException('string?: invalid number of arguments');
			}

			$x = $args[0];

			return toBoolean(is_array($x) && $x[0] === 'string');
		}
	
		function toString($x)
		{
			if (! is_string($x))
			{
				throw new SchismRuntimeException('Argument is not a string:'."\n".var_export($x, TRUE));
			}
	
			return array('string', $x);
		}
	
		function fromString($x)
		{
			if (! fromBoolean(isString($x)))
			{
				throw new SchismRuntimeException('Argument is not a string:'."\n".var_export($x, TRUE));
			}
	
			return $x[1];
		}
	
		function isLambda()
		{
			$args = func_get_args();

			if (count($args) !== 1)
			{
				throw new SchismRuntimeException('procedure?: invalid number of arguments');
			}

			$x = $args[0];

			return toBoolean(is_array($x) && $x[0] === 'lambda');
		}
	
		function toLambda($x)
		{
			if (! is_callable($x))
			{
				throw new SchismRuntimeException('Argument is not callable:'."\n".var_export($x, TRUE));
			}
	
			return array('lambda', $x);
		}
	
		function fromLambda($x)
		{
			if (! fromBoolean(isLambda($x)))
			{
				throw new SchismRuntimeException('Argument is not callable:'."\n".var_export($x, TRUE));
			}
	
			return $x[1];
		}
	
		function isNil()
		{
			$args = func_get_args();

			if (count($args) !== 1)
			{
				throw new SchismRuntimeException('nil?: invalid number of arguments');
			}

			$x = $args[0];

			return toBoolean(is_array($x) && $x[0] === 'nil');
		}
	
		function nil()
		{
			return array('nil');
		}
	
		function isCons()
		{
			$args = func_get_args();

			if (count($args) !== 1)
			{
				throw new SchismRuntimeException('cons?: invalid number of arguments');
			}

			$x = $args[0];

			return toBoolean(is_array($x) && $x[0] === 'cons');
		}
	
		function cons()
		{
			$args = func_get_args();

			if (count($args) !== 2)
			{
				throw new SchismRuntimeException('cons: invalid number of arguments');
			}

			$a = $args[0];
			$b = $args[1];

			return array('cons', $a, $b);
		}
	
		function car()
		{
			$args = func_get_args();

			if (count($args) !== 1)
			{
				throw new SchismRuntimeException('car: invalid number of arguments');
			}

			$c = $args[0];

			if (! fromBoolean(isCons($c)))
			{
				throw new SchismRuntimeException('Argument is not a cons:'."\n".var_export($c, TRUE));
			}
	
			return $c[1];
		}
	
		function cdr()
		{
			$args = func_get_args();

			if (count($args) !== 1)
			{
				throw new SchismRuntimeException('cdr: invalid number of arguments');
			}

			$c = $args[0];

			if (! fromBoolean(isCons($c)))
			{
				throw new SchismRuntimeException('Argument is not a cons:'."\n".var_export($c, TRUE));
			}
	
			return $c[2];
		}
	
		function equal()
		{
			$args = func_get_args();

			if (count($args) !== 2)
			{
				throw new SchismRuntimeException('equal?: invalid number of arguments');
			}

			$a = $args[0];
			$b = $args[1];

			if (! is_array($a) || ! is_array($b))
			{
				throw new SchismRuntimeException('Invalid comparison:'."\n".var_export($a, TRUE)."\n".var_export($b, TRUE));
			}
	
			if ($a[0] !== $b[0])
			{
				return toBoolean(FALSE);
			}
	
			if ($a[0] === 'nil')
			{
				return toBoolean(TRUE);
			}
	
			if ($a[0] === 'cons')
			{
				return toBoolean(fromBoolean(equal($a[1], $b[1])) && fromBoolean(equal($a[2], $b[2])));
			}
	
			if ($a[0] === 'lambda')
			{
				throw new SchismRuntimeException('Invalid comparison of function values');
			}
	
			return toBoolean($a[1] == $b[1]);
		}
	
		function repr($x)
		{
			return toString(reprNative($x));
		}

		function reprNative()
		{
			$args = func_get_args();

			if (count($args) !== 1)
			{
				throw new SchismRuntimeException('repr: invalid number of arguments');
			}

			$x = $args[0];

			if (fromBoolean(isLambda($x)))
			{
				return '[lambda]';
			}

			if (fromBoolean(isNil($x)))
			{
				return 'empty';
			}

			if (fromBoolean(isNumeric($x)))
			{
				return strval(fromNumeric($x));
			}

			if (fromBoolean(isString($x)))
			{
				return '"'.strval(fromString($x)).'"';
			}

			if (fromBoolean(isBoolean($x)))
			{
				return fromBoolean($x) ? 'true' : 'false';
			}

			if (fromBoolean(isCons($x)))
			{
				return '(cons '.reprNative(car($x)).' '.reprNative(cdr($x)).')';
			}
	
			return var_export($x, TRUE);
		}

		function fromList($x, $acc)
		{
			if (fromBoolean(isNil($x)))
			{
				return $acc;
			}

			$acc[] = fromSchism(car($x));

			return fromList(cdr($x), $acc);
		}

		function fromMap($x, $acc)
		{
			if (fromBoolean(isNil($x)))
			{
				return $acc;
			}

			$acc[fromSchism(car(car($x)))] = fromSchism(cdr(car($x)));

			return fromMap(cdr($x), $acc);
		}

		function fromCons($x)
		{
			if (fromBoolean(isCons(car($x))))
			{
				return fromMap($x, array());
			}
			else
			{
				return fromList($x, array());
			}
		}

		function toSchism($x)
		{
			if (is_numeric($x))
			{
				return toNumeric($x);
			}

			if (is_string($x))
			{
				return toString($x);
			}

			if (is_array($x))
			{
				if (array_keys($x) !== range(0, count($x) - 1))
				{
					$res = nil();

					foreach ($x as $k => $v)
					{
						$res = cons(cons(toSchism($k), toSchism($v)), $res);
					}

					return $res;
				}
				else
				{
					$res = nil();

					for ($ix = count($x) - 1; $ix >= 0; ++$ix)
					{
						$res = cons(toSchism($x[$ix]), $res);
					}

					return $res;
				}
			}

			return nil();
		}

		function fromSchism($x)
		{
			if (fromBoolean(isBoolean($x)))
			{
				return fromBoolean($x);
			}

			if (fromBoolean(isNumeric($x)))
			{
				return fromNumeric($x);
			}

			if (fromBoolean(isString($x)))
			{
				return fromString($x);
			}

			if (fromBoolean(isLambda($x)))
			{
				return NULL;
			}

			if (fromBoolean(isNil($x)))
			{
				return NULL;
			}

			if (fromBoolean(isCons($x)))
			{
				return fromCons($x);
			}
		}
	
		global $defenv;

		$defenv = array(
				// system
				'sys\raise-error' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 1)
							{
								throw new SchismRuntimeException('sys\raise-error: invalid number of arguments');
							}

							$x = $args[0];

							throw new SchismRuntimeException(fromString($x));
						}),
	
				// types, equality and representation
				'boolean?' =>
						toLambda('\Schism\isBoolean'),
				'numeric?' =>
						toLambda('\Schism\isNumeric'),
				'string?' =>
						toLambda('\Schism\isString'),
				'procedure?' =>
						toLambda('\Schism\isLambda'),
				'nil?' =>
						toLambda('\Schism\isNil'),
				'cons?' =>
						toLambda('\Schism\isCons'),
				'equal?' =>
						toLambda('\Schism\equal'),
				'repr' =>
						toLambda('\Schism\repr'),
	
				// booleans
				'true' => toBoolean(TRUE),
				'false' => toBoolean(FALSE),
				'else' => toBoolean(TRUE),
				'not' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 1)
							{
								throw new SchismRuntimeException('not: invalid number of arguments');
							}

							$a = $args[0];

							return toBoolean(! fromBoolean($a));
						}),
	
				// numeric
				'+' =>
						toLambda(function()
						{
							return array_reduce(func_get_args(),
									function($a, $b)
									{
										return toNumeric(fromNumeric($a) + fromNumeric($b));
									}, toNumeric(0.0));
						}),
				'-' =>
						toLambda(function()
						{
							$args = func_get_args();

							if ((count($args) !== 1) && (count($args) !== 2))
							{
								throw new SchismRuntimeException('-: invalid number of arguments');
							}

							if (count($args) === 1)
							{
								return toNumeric(-fromNumeric($args[0]));
							}
							else
							{
								return toNumeric(fromNumeric($args[0]) - fromNumeric($args[1]));
							}
						}),
				'*' =>
						toLambda(function()
						{
							return array_reduce(func_get_args(),
									function($a, $b)
									{
										return toNumeric(fromNumeric($a) * fromNumeric($b));
									}, toNumeric(1.0));
						}),
				'/' =>
						toLambda(function()
						{
							$args = func_get_args();

							if ((count($args) !== 1) && (count($args) !== 2))
							{
								throw new SchismRuntimeException('/: invalid number of arguments');
							}

							if (count($args) === 1)
							{
								return toNumeric(1 / fromNumeric($args[0]));
							}
							else
							{
								return toNumeric(fromNumeric($args[0]) / fromNumeric($args[1]));
							}
						}),
				'<' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 2)
							{
								throw new SchismRuntimeException('<: invalid number of arguments');
							}

							$a = $args[0];
							$b = $args[1];

							return toBoolean(fromNumeric($a) < fromNumeric($b));
						}),
				'<=' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 2)
							{
								throw new SchismRuntimeException('<=: invalid number of arguments');
							}

							$a = $args[0];
							$b = $args[1];

							return toBoolean(fromNumeric($a) <= fromNumeric($b));
						}),
				'=' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 2)
							{
								throw new SchismRuntimeException('=: invalid number of arguments');
							}

							$a = $args[0];
							$b = $args[1];

							return toBoolean(fromNumeric($a) == fromNumeric($b));
						}),
				'/=' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 2)
							{
								throw new SchismRuntimeException('/=: invalid number of arguments');
							}

							$a = $args[0];
							$b = $args[1];

							return toBoolean(fromNumeric($a) != fromNumeric($b));
						}),
				'>' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 2)
							{
								throw new SchismRuntimeException('>: invalid number of arguments');
							}

							$a = $args[0];
							$b = $args[1];

							return toBoolean(fromNumeric($a) > fromNumeric($b));
						}),
				'>=' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 2)
							{
								throw new SchismRuntimeException('>=: invalid number of arguments');
							}

							$a = $args[0];
							$b = $args[1];

							return toBoolean(fromNumeric($a) >= fromNumeric($b));
						}),
				'floor' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 1)
							{
								throw new SchismRuntimeException('floor: invalid number of arguments');
							}

							$x = $args[0];

							return toNumeric(floor(fromNumeric($x)));
						}),
				'ceiling' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 1)
							{
								throw new SchismRuntimeException('ceiling: invalid number of arguments');
							}

							$x = $args[0];

							return toNumeric(ceil(fromNumeric($x)));
						}),
				'round' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 1)
							{
								throw new SchismRuntimeException('round: invalid number of arguments');
							}

							$x = $args[0];

							return toNumeric(round(fromNumeric($x)));
						}),
				'expt' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 2)
							{
								throw new SchismRuntimeException('expt: invalid number of arguments');
							}

							return toNumeric(pow(fromNumeric($args[0]), fromNumeric($args[1])));
						}),
				'sin' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 1)
							{
								throw new SchismRuntimeException('sin: invalid number of arguments');
							}

							$x = $args[0];

							return toNumeric(sin(fromNumeric($x)));
						}),
				'cos' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 1)
							{
								throw new SchismRuntimeException('cos: invalid number of arguments');
							}

							$x = $args[0];

							return toNumeric(cos(fromNumeric($x)));
						}),
				'tan' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 1)
							{
								throw new SchismRuntimeException('tan: invalid number of arguments');
							}

							$x = $args[0];

							return toNumeric(tan(fromNumeric($x)));
						}),
				'asin' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 1)
							{
								throw new SchismRuntimeException('asin: invalid number of arguments');
							}

							$x = $args[0];

							return toNumeric(asin(fromNumeric($x)));
						}),
				'acos' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 1)
							{
								throw new SchismRuntimeException('acos: invalid number of arguments');
							}

							$x = $args[0];

							return toNumeric(acos(fromNumeric($x)));
						}),
				'atan' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 1)
							{
								throw new SchismRuntimeException('atan: invalid number of arguments');
							}

							$x = $args[0];

							return toNumeric(atan(fromNumeric($x)));
						}),
				'log' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 1)
							{
								throw new SchismRuntimeException('log: invalid number of arguments');
							}

							$x = $args[0];

							return toNumeric(log(fromNumeric($x)));
						}),
				'number->string' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 1)
							{
								throw new SchismRuntimeException('number->string: invalid number of arguments');
							}

							$x = $args[0];

							return toString(strval(fromNumeric($x)));
						}),
				'integer->string' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 1)
							{
								throw new SchismRuntimeException('integer->string: invalid number of arguments');
							}

							$x = $args[0];

							return toString(chr(floor(fromNumeric($x))));
						}),
	
				// strings
				'string<?' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 2)
							{
								throw new SchismRuntimeException('string<?: invalid number of arguments');
							}

							$a = $args[0];
							$b = $args[1];

							return toBoolean(fromString($a) < fromString($b));
						}),
				'string-ci<?' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 2)
							{
								throw new SchismRuntimeException('string-ci<?: invalid number of arguments');
							}

							$a = $args[0];
							$b = $args[1];

							return toBoolean(strtolower(fromString($a)) < strtolower(fromString($b)));
						}),
				'string<=?' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 2)
							{
								throw new SchismRuntimeException('string<=?: invalid number of arguments');
							}

							$a = $args[0];
							$b = $args[1];

							return toBoolean(fromString($a) <= fromString($b));
						}),
				'string-ci<=?' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 2)
							{
								throw new SchismRuntimeException('string-ci<=?: invalid number of arguments');
							}

							$a = $args[0];
							$b = $args[1];

							return toBoolean(strtolower(fromString($a)) <= strtolower(fromString($b)));
						}),
				'string=?' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 2)
							{
								throw new SchismRuntimeException('string=?: invalid number of arguments');
							}

							$a = $args[0];
							$b = $args[1];

							return toBoolean(fromString($a) === fromString($b));
						}),
				'string-ci=?' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 2)
							{
								throw new SchismRuntimeException('string=?: invalid number of arguments');
							}

							$a = $args[0];
							$b = $args[1];

							return toBoolean(strtolower(fromString($a)) === strtolower(fromString($b)));
						}),
				'string/=?' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 2)
							{
								throw new SchismRuntimeException('string/=?: invalid number of arguments');
							}

							$a = $args[0];
							$b = $args[1];

							return toBoolean(fromString($a) !== fromString($b));
						}),
				'string-ci/=?' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 2)
							{
								throw new SchismRuntimeException('string/=?: invalid number of arguments');
							}

							$a = $args[0];
							$b = $args[1];

							return toBoolean(strtolower(fromString($a)) !== strtolower(fromString($b)));
						}),
				'string>?' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 2)
							{
								throw new SchismRuntimeException('string>?: invalid number of arguments');
							}

							$a = $args[0];
							$b = $args[1];

							return toBoolean(fromString($a) > fromString($b));
						}),
				'string-ci>?' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 2)
							{
								throw new SchismRuntimeException('string-ci>?: invalid number of arguments');
							}

							$a = $args[0];
							$b = $args[1];

							return toBoolean(strtolower(fromString($a)) > strtolower(fromString($b)));
						}),
				'string>=?' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 2)
							{
								throw new SchismRuntimeException('string>=?: invalid number of arguments');
							}

							$a = $args[0];
							$b = $args[1];

							return toBoolean(fromString($a) >= fromString($b));
						}),
				'string-ci>=?' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 2)
							{
								throw new SchismRuntimeException('string-ci>=?: invalid number of arguments');
							}

							$a = $args[0];
							$b = $args[1];

							return toBoolean(strtolower(fromString($a)) >= strtolower(fromString($b)));
						}),
				'string->number' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 1)
							{
								throw new SchismRuntimeException('string->number: invalid number of arguments');
							}

							$x = $args[0];

							return toNumeric(floatval(fromString($x)));
						}),
				'string->integer' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 1)
							{
								throw new SchismRuntimeException('string->integer: invalid number of arguments');
							}

							$x = $args[0];

							$xConv = fromString($x);

							return toNumeric(strlen($xConv) > 0 ? ord(substr($xConv, 0, 1)) : 0);
						}),
				'string-length' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 1)
							{
								throw new SchismRuntimeException('string-length: invalid number of arguments');
							}

							$a = $args[0];

							return toNumeric(strlen(fromString($a)));
						}),
				'string-append' =>
						toLambda(function()
						{
							return array_reduce(func_get_args(),
									function($a, $b)
									{
										return toString(fromString($a).fromString($b));
									}, toString(''));
						}),
				'substring' =>
						toLambda(function()
						{
							$args = func_get_args();

							if (count($args) !== 3)
							{
								throw new SchismRuntimeException('substring: invalid number of arguments');
							}

							$str = $args[0];
							$a = $args[1];
							$b = $args[2];

							return toString(strval(substr(fromString($str), fromNumeric($a), fromNumeric($b) - fromNumeric($a))));
						}),
	
				// lists
				'nil' => nil(),
				'cons' =>
						toLambda('\Schism\cons'),
				'car' =>
						toLambda('\Schism\car'),
				'cdr' =>
						toLambda('\Schism\cdr'),
				'list' =>
						toLambda(function()
						{
							$args = func_get_args();

							$res = nil();

							for ($i = count($args) - 1; $i >= 0; --$i)
							{
								$res = cons($args[$i], $res);
							}

							return $res;
						}),
				);
	
		function evaluate($ast, array &$env, $toplevel = FALSE)
		{
			if (! is_array($ast))
			{
				throw new SchismRuntimeException('Invalid AST:'."\n".var_export($ast, TRUE));
			}
	
			// special form: comment
			if ($ast[0] === 'comment')
			{
				return NULL;
			}
			// special form: lambda
			elseif ($ast[0] === 'lambda')
			{
				if (count($ast) !== 3)
				{
					throw new SchismRuntimeException('Invalid lambda syntax:'."\n".var_export($ast, TRUE));
				}
	
				$args = $ast[1];
	
				if (! is_array($args))
				{
					throw new SchismRuntimeException('Invalid lambda syntax:'."\n".var_export($ast, TRUE));
				}
	
				foreach ($args as $arg)
				{
					if ((! is_array($arg)) || ($arg[0] !== 'var'))
					{
						throw new SchismRuntimeException('Invalid lambda syntax:'."\n".var_export($ast, TRUE));
					}
				}
	
				$body = $ast[2];
	
				if (! is_array($body))
				{
					throw new SchismRuntimeException('Invalid lambda syntax:'."\n".var_export($ast, TRUE));
				}
	
				return
						toLambda(function() use(&$env, $args, $body)
						{
							$newenv = $env;
	
							if (count($args) !== count(func_get_args()))
							{
								throw new SchismRuntimeException('Invalid number of arguments');
							}
	
							foreach (array_map(function($a, $b) { return array($a, $b); }, $args, func_get_args()) as $arg)
							{
								$newenv[$arg[0][1]] = $arg[1];
							}
	
							return evaluate($body, $newenv);
						});
			}
			// special form: define
			elseif ($ast[0] === 'define')
			{
				if (! $toplevel)
				{
					throw new SchismRuntimeException('define must be a top-level expression:'."\n".var_export($ast, TRUE));
				}
	
				if (count($ast) !== 3)
				{
					throw new SchismRuntimeException('Invalid define syntax:'."\n".var_export($ast, TRUE));
				}
	
				$args = $ast[1];
	
				if (! is_array($args))
				{
					throw new SchismRuntimeException('Invalid define syntax:'."\n".var_export($ast, TRUE));
				}
	
				if (is_array($args[0]))
				{
					foreach ($args as $arg)
					{
						if ((! is_array($arg)) || ($arg[0] !== 'var'))
						{
							throw new SchismRuntimeException('Invalid define syntax:'."\n".var_export($ast, TRUE));
						}
					}
	
					$name = array_shift($args);
					$body = $ast[2];
		
					if (! is_array($body))
					{
						throw new SchismRuntimeException('Invalid define syntax:'."\n".var_export($ast, TRUE));
					}
	
					$env[$name[1]] =
							toLambda(function() use(&$env, $args, $body, $name)
							{
								$newenv = $env;
		
								if (count($args) !== count(func_get_args()))
								{
									throw new SchismRuntimeException('Invalid number of arguments');
								}
	
								foreach (array_map(function($a, $b) { return array($a, $b); }, $args, func_get_args()) as $arg)
								{
									$newenv[$arg[0][1]] = $arg[1];
								}
		
								return evaluate($body, $newenv);
							});
				}
				else
				{
					if (($args[0] !== 'var') || (! is_array($ast[2])))
					{
						throw new SchismRuntimeException('Invalid define syntax:'."\n".var_export($ast, TRUE));
					}
	
					$env[$args[1]] = evaluate($ast[2], $env);
				}
	
				return NULL;
			}
			// special form: letrec
			elseif ($ast[0] === 'letrec')
			{
				if ((count($ast) !== 3) || (! is_array($ast[1])))
				{
					throw new SchismRuntimeException('Invalid letrec syntax:'."\n".var_export($ast, TRUE));
				}
	
				$newenv = $env;
	
				foreach ($ast[1] as $clause)
				{
					if ((! is_array($clause)) || (count($clause) !== 2) || (! is_array($clause[0])) || ($clause[0][0] !== 'var') ||
							(! is_array($clause[1])))
					{
						throw new SchismRuntimeException('Invalid letrec syntax:'."\n".var_export($ast, TRUE));
					}
	
					$newenv[$clause[0][1]] = evaluate($clause[1], $newenv);
				}
	
				return evaluate($ast[2], $newenv);
			}
			// special form: if
			elseif ($ast[0] === 'if')
			{
				if (count($ast) !== 4)
				{
					throw new SchismRuntimeException('Invalid if syntax:'."\n".var_export($ast, TRUE));
				}
	
				if (fromBoolean(evaluate($ast[1], $env)))
				{
					return evaluate($ast[2], $env);
				}
				else
				{
					return evaluate($ast[3], $env);
				}
			}
			// special form: cond
			elseif ($ast[0] === 'cond')
			{
				if (count($ast) === 1)
				{
					throw new SchismRuntimeException('Invalid cond syntax:'."\n".var_export($ast, TRUE));
				}
	
				array_shift($ast);
	
				if ((! is_array($ast[0])) || (count($ast[0]) !== 2))
				{
					throw new SchismRuntimeException('Invalid cond syntax:'."\n".var_export($ast, TRUE));
				}

				if (fromBoolean(evaluate($ast[0][0], $env)))
				{
					return evaluate($ast[0][1], $env);
				}
				else
				{
					array_shift($ast);
					array_unshift($ast, 'cond');
	
					return evaluate($ast, $env);
				}
			}
			// special form: and
			elseif ($ast[0] === 'and')
			{
				if (count($ast) === 1)
				{
					return toBoolean(TRUE);
				}
	
				array_shift($ast);
	
				if (! fromBoolean(evaluate($ast[0], $env)))
				{
					return toBoolean(FALSE);
				}
				else
				{
					array_shift($ast);
					array_unshift($ast, 'and');
	
					return evaluate($ast, $env);
				}
			}
			// special form: or
			elseif ($ast[0] === 'or')
			{
				if (count($ast) === 1)
				{
					return toBoolean(FALSE);
				}
	
				array_shift($ast);
	
				if (fromBoolean(evaluate($ast[0], $env)))
				{
					return toBoolean(TRUE);
				}
				else
				{
					array_shift($ast);
					array_unshift($ast, 'or');
	
					return evaluate($ast, $env);
				}
			}
			elseif ($ast[0] === 'const')
			{
				return $ast[1];
			}
			elseif ($ast[0] === 'var')
			{
				if (! array_key_exists($ast[1], $env))
				{
					throw new SchismRuntimeException('Undefined symbol:'."\n".var_export($ast, TRUE));
				}
	
				return $env[$ast[1]];
			}
			else
			{
				$vals = array();
	
				foreach ($ast as $expr)
				{
					$vals[] = evaluate($expr, $env);
				}
	
				$f = fromLambda(array_shift($vals));
	
				return call_user_func_array($f, $vals);
			}
		}
	
		function idchar()
		{
			return \Picoparsec\expectf(function($x) { return ! in_array($x, array('(', ')', '[', ']', '{', '}', ' ', "\t", "\v", "\n", "\r", '"', "'")); }, 'idchar');
		}
	
		function strchar()
		{
			return \Picoparsec\expectf(function($x) { return ! in_array($x, array("\t", "\v", "\n", "\r", '"')); }, 'strchar');
		}
	
		function whitespace()
		{
			return \Picoparsec\expectf(function($x) { return in_array($x, array(' ', "\t", "\v", "\n", "\r")); }, 'whitespace');
		}
	
		function oparen()
		{
			return \Picoparsec\expectf(function($x) { return in_array($x, array('(', '[', '{')); }, 'oparen');
		}
	
		function cparen()
		{
			return \Picoparsec\expectf(function($x) { return in_array($x, array(')', ']', '}')); }, 'cparen');
		}
	
		function integer()
		{
			return \Picoparsec\transform(\Picoparsec\str(
					\Picoparsec\seq(array(
							\Picoparsec\attempt(\Picoparsec\choice(array(
									\Picoparsec\expect('+'),
									\Picoparsec\expect('-')))),
							\Picoparsec\str(\Picoparsec\repeat(\Picoparsec\num(), 1)),
							\Picoparsec\attempt(\Picoparsec\str(\Picoparsec\seq(array(
									\Picoparsec\expect('.'),
									\Picoparsec\str(\Picoparsec\repeat(\Picoparsec\num(), 1))
									)))),
							\Picoparsec\attempt(\Picoparsec\str(\Picoparsec\seq(array(
									\Picoparsec\choice(array(
										\Picoparsec\expect('e'),
										\Picoparsec\expect('E')
										)),
									\Picoparsec\attempt(\Picoparsec\choice(array(
											\Picoparsec\expect('+'),
											\Picoparsec\expect('-')))),
									\Picoparsec\str(\Picoparsec\repeat(\Picoparsec\num(), 1))
									))))
							))),
							function($str)
							{
								return array('const', toNumeric(floatval($str)));
							});
		}
	
		function string()
		{
			return \Picoparsec\transform(\Picoparsec\arrstseq(array(
					0 => \Picoparsec\expect('"'),
					'str' => \Picoparsec\repeat(strchar(), 0),
					1 => \Picoparsec\expect('"'),
					)),
					function($arr)
					{
						return array('const', toString(implode('', $arr['str'])));
					});
		}
	
		function identifier()
		{
			return \Picoparsec\transform(\Picoparsec\str(\Picoparsec\repeat(idchar(), 1)),
					function($str)
					{
						return array('var', $str);
					});
		}
	
		function comment()
		{
			return \Picoparsec\transform(\Picoparsec\seq(array(
					\Picoparsec\expectstr('comment'),
					whitespace(),
					)),
					function($x)
					{
						return 'comment';
					});
		}
	
		function lambda()
		{
			return \Picoparsec\transform(\Picoparsec\seq(array(
					\Picoparsec\expectstr('lambda'),
					whitespace(),
					)),
					function($x)
					{
						return 'lambda';
					});
		}
	
		function token_define()
		{
			return \Picoparsec\transform(\Picoparsec\seq(array(
					\Picoparsec\expectstr('define'),
					whitespace(),
					)),
					function($x)
					{
						return 'define';
					});
		}
	
		function letrec()
		{
			return \Picoparsec\transform(\Picoparsec\seq(array(
					\Picoparsec\expectstr('letrec'),
					whitespace(),
					)),
					function($x)
					{
						return 'letrec';
					});
		}
	
		function token_if()
		{
			return \Picoparsec\transform(\Picoparsec\seq(array(
					\Picoparsec\expectstr('if'),
					whitespace(),
					)),
					function($x)
					{
						return 'if';
					});
		}
	
		function cond()
		{
			return \Picoparsec\transform(\Picoparsec\seq(array(
					\Picoparsec\expectstr('cond'),
					whitespace(),
					)),
					function($x)
					{
						return 'cond';
					});
		}
	
		function token_and()
		{
			return \Picoparsec\transform(\Picoparsec\seq(array(
					\Picoparsec\expectstr('and'),
					whitespace(),
					)),
					function($x)
					{
						return 'and';
					});
		}
	
		function token_or()
		{
			return \Picoparsec\transform(\Picoparsec\seq(array(
					\Picoparsec\expectstr('or'),
					whitespace(),
					)),
					function($x)
					{
						return 'or';
					});
		}
	
		function emptyexpr()
		{
			return \Picoparsec\cnst(\Picoparsec\seq(array(
					\Picoparsec\repeat(whitespace()),
					oparen(),
					\Picoparsec\repeat(whitespace()),
					cparen(),
					\Picoparsec\repeat(whitespace())
					)), array());
		}

		function expr()
		{
			return \Picoparsec\choice(array(
					integer(),
					string(),
					comment(),
					lambda(),
					token_define(),
					letrec(),
					token_if(),
					cond(),
					token_and(),
					token_or(),
					identifier(),
					emptyexpr(),
					\Picoparsec\transform(
							\Picoparsec\arrstseq(array(
									0 => \Picoparsec\repeat(whitespace()),
									1 => oparen(),
									'exprs' =>
											function(\IParseState $state)
											{
												$parser = \Picoparsec\repeat(\Picoparsec\last(
														array(
																\Picoparsec\attempt(\Picoparsec\repeat(whitespace(), 1)),
																expr(),
																)), 1);
	
												return $parser($state);
											},
									2 => \Picoparsec\repeat(whitespace()),
									3 => cparen(),
									4 => \Picoparsec\repeat(whitespace()),
									)),
							function($x)
							{
								return $x['exprs'];
							}
							),
					));
		}
	
		function exprs()
		{
			return \Picoparsec\repeat(expr());
		}
	
		function parse($str)
		{
			$parser = exprs();
	
			return $parser(\ParseState::mk(\StringToCharTokenList::mk($str), \ArrayState::mk()));
		}
	
		function execute($str, $env)
		{
			$ast = parse($str);
	
			return evaluate($ast[0][0], $env);
		}
	
		final class Schism
		{
			private $env;
	
			final public static function make()
			{
				return new self;
			}
	
			final public function execute($code)
			{
				if (is_array($code))
				{
					$exprs = array($code);
				}
				else
				{
					$exprs = parse($code);
	
					try
					{
						$rest = $exprs[1]->nextToken();
	
						throw new SchismRuntimeException('Parse failure at:'."\n".var_export($rest, TRUE));
					}
					catch (\TokenExhaustionException $ex)
					{
					}
				}
	
				$res = array();
	
				foreach ($exprs[0] as $expr)
				{
					$curRes = evaluate($expr, $this->env, TRUE);
	
					if (! in_array($expr[0], array('define', 'comment')))
					{
						$res[] = $curRes;
					}
				}
	
				return $res;
			}
	
			final public function invoke($id, array $args)
			{
				$ast = array(array('var', $id));

				foreach ($args as $x)
				{
					$ast[] = array('const', toSchism($x));
				}

				return fromSchism(evaluate($ast, $this->env, FALSE));
			}

			final public function __call($id, array $args)
			{
				return $this->invoke($id, $args);
			}

			final private function __construct()
			{
				global $defenv;
	
				$this->env = $defenv;
			}
		}
	}

?>
