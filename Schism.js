
var SchismParser =
{
	idchar: function()
	{
		return Picoparsec.expectf(function(x) { return ! /^[\(\)\[\]\{\} \t\v\n\r\"\']$/.test(x); }, 'idchar');
	},

	strchar: function()
	{
		return Picoparsec.expectf(function(x) { return ! /^[\t\v\n\r\"]$/.test(x); }, 'strchar');
	},

	whitespace: function()
	{
		return Picoparsec.expectf(function(x) { return /^[ \t\v\n\r]$/.test(x); }, 'whitespace');
	},

	oparen: function()
	{
		return Picoparsec.expectf(function(x) { return /^[\(\[\{]$/.test(x); }, 'oparen');
	},

	cparen: function()
	{
		return Picoparsec.expectf(function(x) { return /^[\)\]\}]$/.test(x); }, 'cparen');
	},

	integer: function()
	{
		return Picoparsec.transform(Picoparsec.str(
				Picoparsec.seq([
						Picoparsec.attempt(Picoparsec.choice([
								Picoparsec.expect('+'),
								Picoparsec.expect('-')])),
						Picoparsec.str(Picoparsec.repeat(Picoparsec.num(), 1)),
						Picoparsec.attempt(Picoparsec.str(Picoparsec.seq([
								Picoparsec.expect('.'),
								Picoparsec.str(Picoparsec.repeat(Picoparsec.num(), 1))
								], true))),
						Picoparsec.attempt(Picoparsec.str(Picoparsec.seq([
								Picoparsec.choice([
									Picoparsec.expect('e'),
									Picoparsec.expect('E')
									]),
								Picoparsec.attempt(Picoparsec.choice([
										Picoparsec.expect('+'),
										Picoparsec.expect('-')])),
								Picoparsec.str(Picoparsec.repeat(Picoparsec.num(), 1))
								], true)))
						], true)),
						function(str)
						{
							return ['const', Schism.toNumeric(parseFloat(str))];
						});
	},

	string: function()
	{
		return Picoparsec.transform(Picoparsec.arrstseq([
				Picoparsec.expect('"'),
				Picoparsec.repeat(SchismParser.strchar(), 0),
				Picoparsec.expect('"')
				], true, true, true),
				function(arr)
				{
					return ['const', Schism.toString(arr[1].reduce(function(a, b) { return a + b; }, ''))];
				});
	},

	identifier: function()
	{
		return Picoparsec.transform(Picoparsec.str(Picoparsec.repeat(SchismParser.idchar(), 1)),
				function(str)
				{
					return ['var', str];
				});
	},

	comment: function()
	{
		return Picoparsec.transform(Picoparsec.seq([
				Picoparsec.expectstr('comment'),
				SchismParser.whitespace()
				]),
				function(x)
				{
					return 'comment';
				});
	},

	lambda: function()
	{
		return Picoparsec.transform(Picoparsec.seq([
				Picoparsec.expectstr('lambda'),
				SchismParser.whitespace()
				]),
				function(x)
				{
					return 'lambda';
				});
	},

	define: function()
	{
		return Picoparsec.transform(Picoparsec.seq([
				Picoparsec.expectstr('define'),
				SchismParser.whitespace()
				]),
				function(x)
				{
					return 'define';
				});
	},

	letrec: function()
	{
		return Picoparsec.transform(Picoparsec.seq([
				Picoparsec.expectstr('letrec'),
				SchismParser.whitespace()
				]),
				function(x)
				{
					return 'letrec';
				});
	},

	token_if: function()
	{
		return Picoparsec.transform(Picoparsec.seq([
				Picoparsec.expectstr('if'),
				SchismParser.whitespace()
				]),
				function(x)
				{
					return 'if';
				});
	},

	cond: function()
	{
		return Picoparsec.transform(Picoparsec.seq([
				Picoparsec.expectstr('cond'),
				SchismParser.whitespace()
				]),
				function(x)
				{
					return 'cond';
				});
	},

	and: function()
	{
		return Picoparsec.transform(Picoparsec.seq([
				Picoparsec.expectstr('and'),
				SchismParser.whitespace()
				]),
				function(x)
				{
					return 'and';
				});
	},

	or: function()
	{
		return Picoparsec.transform(Picoparsec.seq([
				Picoparsec.expectstr('or'),
				SchismParser.whitespace()
				]),
				function(x)
				{
					return 'or';
				});
	},

	emptyexpr: function()
	{
		return Picoparsec.cnst(Picoparsec.seq([
				Picoparsec.repeat(SchismParser.whitespace(), 0),
				SchismParser.oparen(),
				Picoparsec.repeat(SchismParser.whitespace(), 0),
				SchismParser.cparen(),
				Picoparsec.repeat(SchismParser.whitespace(), 0)
				], true), []);
	},

	expr: function()
	{
		return Picoparsec.choice([
				SchismParser.integer(),
				SchismParser.string(),
				SchismParser.comment(),
				SchismParser.lambda(),
				SchismParser.define(),
				SchismParser.letrec(),
				SchismParser.token_if(),
				SchismParser.cond(),
				SchismParser.and(),
				SchismParser.or(),
				SchismParser.identifier(),
				SchismParser.emptyexpr(),
				Picoparsec.transform(
						Picoparsec.arrstseq([
								Picoparsec.repeat(SchismParser.whitespace(), 0),
								SchismParser.oparen(),
								function(state)
								{
									var parser = Picoparsec.repeat(Picoparsec.last([
											Picoparsec.attempt(Picoparsec.repeat(SchismParser.whitespace(), 1)),
											SchismParser.expr()
											], false), 1);

									return parser(state);
								},
								Picoparsec.repeat(SchismParser.whitespace(), 0),
								SchismParser.cparen(),
								Picoparsec.repeat(SchismParser.whitespace(), 0)
								], true, true, true),
						function(x)
						{
							return x[2];
						})
				]);
	},

	exprs: function()
	{
		return Picoparsec.repeat(SchismParser.expr(), 0);
	},

	parse: function(str)
	{
		var parser = SchismParser.exprs();

		return parser(new Picoparsec.ParseState(new Picoparsec.StringToCharTokenList(str), new Picoparsec.ArrayState([])));
	}
};

var SchismInterpreter =
{
	SchismRuntimeException: function(str)
	{
		this.str = str;
	},

	init: function()
	{
		this.env = this.copyEnv(SchismEnv);
	},

	copyEnv: function(env)
	{
		if (! (env instanceof Object))
		{
			return env;
		}

		res = {};

		for (var ix in env)
		{
			res[ix] = env[ix];
		}

		return res;
	},

	evaluate: function(ast, env, toplevel)
	{
		if (! (ast instanceof Array))
		{
			throw new SchismInterpreter.SchismRuntimeException('Invalid AST:' + "\n" + ast.toString());
		}

		if (ast[0] == 'comment')
		{
			return null;
		}
		else if (ast[0] == 'lambda')
		{
			if (ast.length != 3)
			{
				throw new SchismInterpreter.SchismRuntimeException('Invalid lambda syntax:' + "\n" + ast.toString());
			}

			var args = ast[1];

			if (! (args instanceof Array))
			{
				throw new SchismInterpreter.SchismRuntimeException('Invalid lambda syntax:' + "\n" + ast.toString());
			}

			for (var ix = 0; ix != args.length; ++ix)
			{
				if ((! (args[ix] instanceof Array)) || (args[ix][0] != 'var'))
				{
					throw new SchismInterpreter.SchismRuntimeException('Invalid lambda syntax:' + "\n" + ast.toString());
				}
			}

			var body = ast[2];

			if (! (body instanceof Array))
			{
				throw new SchismInterpreter.SchismRuntimeException('Invalid lambda syntax:' + "\n" + ast.toString());
			}

			return Schism.toLambda(function()
					{
						var newenv = SchismInterpreter.copyEnv(env);
	
						var as = [];

						if (args.length != arguments.length)
						{
							throw new SchismInterpreter.SchismRuntimeException('Invalid number of arguments');
						}

						for (var ix = 0; ix != args.length; ++ix)
						{
							as.push([args[ix], arguments[ix]]);
						}

						for (var ix = 0; ix != as.length; ++ix)
						{
							newenv[as[ix][0][1]] = as[ix][1];
						}
	
						return SchismInterpreter.evaluate(body, newenv);
					});
		}
		else if (ast[0] == 'define')
		{
			if (! toplevel)
			{
				throw new SchismInterpreter.SchismRuntimeException('define must be a top-level expression:' + "\n" + ast.toString());
			}

			if (ast.length != 3)
			{
				throw new SchismInterpreter.SchismRuntimeException('Invalid define syntax:' + "\n" + ast.toString());
			}

			var args = ast[1];

			if (! (args instanceof Array))
			{
				throw new SchismInterpreter.SchismRuntimeException('Invalid define syntax:' + "\n" + ast.toString());
			}

			if (args[0] instanceof Array)
			{
				for (var ix = 0; ix != args.length; ++ix)
				{
					if ((! (args[ix] instanceof Array)) || (args[ix][0] != 'var'))
					{
						throw new SchismInterpreter.SchismRuntimeException('Invalid define syntax:' + "\n" + ast.toString());
					}
				}

				var name = args[0];
				var body = ast[2];
	
				if (! (body instanceof Array))
				{
					throw new SchismInterpreter.SchismRuntimeException('Invalid define syntax:' + "\n" + ast.toString());
				}

				env[name[1]] = Schism.toLambda(function()
						{
							var newenv = SchismInterpreter.copyEnv(env);
		
							var as = [];

							if (args.length - 1 != arguments.length)
							{
								throw new SchismInterpreter.SchismRuntimeException('Invalid number of arguments');
							}

							for (var ix = 0; ix != args.length; ++ix)
							{
								if (ix == 0)
								{
									continue;
								}

								as.push([args[ix], arguments[ix - 1]]);
							}

							for (var ix = 0; ix != as.length; ++ix)
							{
								newenv[as[ix][0][1]] = as[ix][1];
							}
		
							return SchismInterpreter.evaluate(body, newenv);
						});
			}
			else
			{
				if ((args[0] != 'var') || (! (ast[2] instanceof Array)))
				{
					throw new SchismInterpreter.SchismRuntimeException('Invalid define syntax:' + "\n" + ast.toString());
				}

				env[args[1]] = SchismInterpreter.evaluate(ast[2], env);
			}

			return null;
		}
		else if (ast[0] == 'letrec')
		{
			if ((ast.length != 3) || (! (ast[1] instanceof Array)) || (! (ast[2] instanceof Array)))
			{
				throw new SchismInterpreter.SchismRuntimeException('Invalid letrec syntax:' + "\n" + ast.toString());
			}

			var newenv = SchismInterpreter.copyEnv(env);

			for (var ix = 0; ix != ast[1].length; ++ix)
			{
				if ((! (ast[1][ix] instanceof Array)) || (ast[1][ix].length != 2) || (! (ast[1][ix][0] instanceof Array)) || (ast[1][ix][0][0] != 'var') ||
						(! (ast[1][ix][1] instanceof Array)))
				{
					throw new SchismInterpreter.SchismRuntimeException('Invalid letrec syntax:' + "\n" + ast.toString());
				}

				newenv[ast[1][ix][0][1]] = SchismInterpreter.evaluate(ast[1][ix][1], newenv);
			}

			return SchismInterpreter.evaluate(ast[2], newenv);
		}
		else if (ast[0] == 'if')
		{
			if (ast.length != 4)
			{
				throw new SchismInterpreter.SchismRuntimeException('Invalid if syntax:' + "\n" + ast.toString());
			}

			if (Schism.fromBoolean(SchismInterpreter.evaluate(ast[1], env)))
			{
				return SchismInterpreter.evaluate(ast[2], env);
			}
			else
			{
				return SchismInterpreter.evaluate(ast[3], env);
			}
		}
		else if (ast[0] == 'cond')
		{
			if (ast.length == 1)
			{
				throw new SchismInterpreter.SchismRuntimeException('Invalid cond syntax:' + "\n" + ast.toString());
			}

			for (var ix = 0; ix != ast.length; ++ix)
			{
				if (ix == 0)
				{
					continue;
				}

				if ((! (ast[ix] instanceof Array)) || (ast[ix].length != 2))
				{
					throw new SchismInterpreter.SchismRuntimeException('Invalid cond syntax:' + "\n" + ast.toString());
				}

				if (Schism.fromBoolean(SchismInterpreter.evaluate(ast[ix][0], env)))
				{
					return SchismInterpreter.evaluate(ast[ix][1], env);
				}
			}

			throw new SchismInterpreter.SchismRuntimeException('Invalid cond syntax:' + "\n" + ast.toString());
		}
		else if (ast[0] == 'and')
		{
			for (var ix = 0; ix != ast.length; ++ix)
			{
				if (ix == 0)
				{
					continue;
				}

				if (! Schism.fromBoolean(SchismInterpreter.evaluate(ast[ix], env)))
				{
					return Schism.toBoolean(false);
				}
			}

			return Schism.toBoolean(true);
		}
		else if (ast[0] == 'or')
		{
			for (var ix = 0; ix != ast.length; ++ix)
			{
				if (ix == 0)
				{
					continue;
				}

				if (Schism.fromBoolean(SchismInterpreter.evaluate(ast[ix], env)))
				{
					return Schism.toBoolean(true);
				}
			}

			return Schism.toBoolean(false);
		}
		else if (ast[0] == 'const')
		{
			return ast[1];
		}
		else if (ast[0] == 'var')
		{
			if (! env.hasOwnProperty(ast[1]))
			{
				throw new SchismInterpreter.SchismRuntimeException('Undefined symbol:' + "\n" + ast.toString());
			}

			return env[ast[1]];
		}
		else
		{
			var vals = [];

			for (var ix = 0; ix != ast.length; ++ix)
			{
				vals.push(SchismInterpreter.evaluate(ast[ix], env));
			}

			f = Schism.fromLambda(vals.shift());

			return f.apply(null, vals);
		}
	},

	execute: function(str)
	{
		var exprs = SchismParser.parse(str);

		try
		{
			var rest = exprs[1].nextToken();

			throw new SchismInterpreter.SchismRuntimeException('Parse failure at:' + "\n" + rest.toString());
		}
		catch (e)
		{
			if (! (e instanceof Picoparsec.TokenExhaustionException))
			{
				throw e;
			}
		}

		var res = [];

		for (var ix = 0; ix != exprs[0].length; ++ix)
		{
			var curRes = SchismInterpreter.evaluate(exprs[0][ix], this.env, true);

			if ((exprs[0][ix][0] != 'define') && (exprs[0][ix][0] != 'comment'))
			{
				res.push(curRes);
			}
		}

		return res;
	},

	__noSuchMethod__: function(id, args)
	{
		var ast = [['var', id]];

		for (var ix = 0; ix != args.length; ++ix)
		{
			ast.push(['const', Schism.toSchism(args[ix])]);
		}

		return Schism.fromSchism(SchismInterpreter.evaluate(ast, this.env, false));
	}
};

var Schism =
{
	isBoolean: function(x)
	{
		if (arguments.length != 1)
		{
			throw new SchismInterpreter.SchismRuntimeException('boolean?: invalid number of arguments');
		}

		return Schism.toBoolean((x instanceof Array) && (x[0] == 'boolean'));
	},

	toBoolean: function(x)
	{
		return ['boolean', x ? true : false];
	},

	fromBoolean: function(x)
	{
		if ((! (x instanceof Array)) || (x[0] != 'boolean'))
		{
			throw new SchismInterpreter.SchismRuntimeException('Argument is not boolean:' + "\n" + x.toString());
		}

		return x[1];
	},

	isNumeric: function(x)
	{
		if (arguments.length != 1)
		{
			throw new SchismInterpreter.SchismRuntimeException('numeric?: invalid number of arguments');
		}

		return Schism.toBoolean((x instanceof Array) && (x[0] == 'numeric'));
	},

	toNumeric: function(x)
	{
		return ['numeric', parseFloat(x)];
	},

	fromNumeric: function(x)
	{
		if (! Schism.fromBoolean(Schism.isNumeric(x)))
		{
			throw new SchismInterpreter.SchismRuntimeException('Argument is not numeric:' + "\n" + x.toString());
		}

		return x[1];
	},

	isString: function(x)
	{
		if (arguments.length != 1)
		{
			throw new SchismInterpreter.SchismRuntimeException('string?: invalid number of arguments');
		}

		return Schism.toBoolean((x instanceof Array) && (x[0] == 'string'));
	},

	toString: function(x)
	{
		return ['string', '' + x];
	},

	fromString: function(x)
	{
		if (! Schism.fromBoolean(Schism.isString(x)))
		{
			throw new SchismInterpreter.SchismRuntimeException('Argument is not a string:' + "\n" + x.toString());
		}

		return x[1];
	},

	isLambda: function(x)
	{
		if (arguments.length != 1)
		{
			throw new SchismInterpreter.SchismRuntimeException('procedure?: invalid number of arguments');
		}

		return Schism.toBoolean((x instanceof Array) && (x[0] == 'lambda'));
	},

	toLambda: function(x)
	{
		if (! (x instanceof Function))
		{
			throw new SchismInterpreter.SchismRuntimeException('Argument is not a lambda:' + "\n" + x.toString());
		}

		return ['lambda', x];
	},

	fromLambda: function(x)
	{
		if (! Schism.fromBoolean(Schism.isLambda(x)))
		{
			throw new SchismInterpreter.SchismRuntimeException('Argument is not a lambda:' + "\n" + x.toString());
		}

		return x[1];
	},

	isNil: function(x)
	{
		if (arguments.length != 1)
		{
			throw new SchismInterpreter.SchismRuntimeException('nil?: invalid number of arguments');
		}

		return Schism.toBoolean((x instanceof Array) && (x[0] == 'nil'));
	},

	nil: function()
	{
		return ['nil'];
	},

	isCons: function(x)
	{
		if (arguments.length != 1)
		{
			throw new SchismInterpreter.SchismRuntimeException('cons?: invalid number of arguments');
		}

		return Schism.toBoolean((x instanceof Array) && (x[0] == 'cons'));
	},

	cons: function(a, b)
	{
		if (arguments.length != 2)
		{
			throw new SchismInterpreter.SchismRuntimeException('cons: invalid number of arguments');
		}

		return ['cons', a, b];
	},

	car: function(c)
	{
		if (arguments.length != 1)
		{
			throw new SchismInterpreter.SchismRuntimeException('car: invalid number of arguments');
		}

		if (! Schism.fromBoolean(Schism.isCons(c)))
		{
			throw new SchismInterpreter.SchismRuntimeException('Argument is not a cons:' + "\n" + c.toString());
		}

		return c[1];
	},

	cdr: function(c)
	{
		if (arguments.length != 1)
		{
			throw new SchismInterpreter.SchismRuntimeException('cdr: invalid number of arguments');
		}

		if (! Schism.fromBoolean(Schism.isCons(c)))
		{
			throw new SchismInterpreter.SchismRuntimeException('Argument is not a cons:' + "\n" + c.toString());
		}

		return c[2];
	},

	equal: function(a, b)
	{
		if (arguments.length != 2)
		{
			throw new SchismInterpreter.SchismRuntimeException('equal?: invalid number of arguments');
		}

		if ((! (a instanceof Array)) || (! (b instanceof Array)))
		{
			throw new SchismInterpreter.SchismRuntimeException('Invalid comparison:' + "\n" + a.toString() + "\n" + b.toString());
		}

		if (a[0] != b[0])
		{
			return Schism.toBoolean(false);
		}

		if (a[0] == 'nil')
		{
			return Schism.toBoolean(true);
		}

		if (a[0] == 'cons')
		{
			return Schism.toBoolean(Schism.fromBoolean(Schism.equal(a[1], b[1])) && Schism.fromBoolean(Schism.equal(a[2], b[2])));
		}

		if (a[0] == 'lambda')
		{
			throw new SchismInterpreter.SchismRuntimeException('Invalid comparison of function values');
		}

		return Schism.toBoolean(a[1] == b[1]);
	},

	repr: function(x)
	{
		return Schism.toString(Schism.reprNative(x));
	},

	reprNative: function(x)
	{
		if (arguments.length != 1)
		{
			throw new SchismInterpreter.SchismRuntimeException('repr: invalid number of arguments');
		}

		if (Schism.fromBoolean(Schism.isLambda(x)))
		{
			return '[lambda]';
		}

		if (Schism.fromBoolean(Schism.isNil(x)))
		{
			return 'empty';
		}

		if (Schism.fromBoolean(Schism.isNumeric(x)))
		{
			return '' + Schism.fromNumeric(x);
		}

		if (Schism.fromBoolean(Schism.isString(x)))
		{
			return '"' + Schism.fromString(x) + '"';
		}

		if (Schism.fromBoolean(Schism.isBoolean(x)))
		{
			return Schism.fromBoolean(x) ? 'true' : 'false';
		}

		if (Schism.fromBoolean(Schism.isCons(x)))
		{
			return '(cons ' + Schism.reprNative(Schism.car(x)) + ' ' + Schism.reprNative(Schism.cdr(x)) + ')';
		}

		return x;
	},

	fromList: function(x, acc)
	{
		if (Schism.fromBoolean(Schism.isNil(x)))
		{
			return acc;
		}

		acc.push(Schism.fromSchism(Schism.car(x)));

		return Schism.fromList(Schism.cdr(x), acc);
	},

	fromMap: function(x, acc)
	{
		if (Schism.fromBoolean(Schism.isNil(x)))
		{
			return acc;
		}

		acc[Schism.fromSchism(Schism.car(Schism.car(x)))] = Schism.fromSchism(Schism.cdr(Schism.car(x)));

		return Schism.fromMap(Schism.cdr(x), acc);
	},

	fromCons: function(x)
	{
		if (Schism.fromBoolean(Schism.isCons(Schism.car(x))))
		{
			return Schism.fromMap(x, {});
		}
		else
		{
			return Schism.fromList(x, []);
		}
	},

	toSchism: function(x)
	{
		var type = Object.prototype.toString.call(x).match(/^\[object (.*)\]$/)[1];

		if (type == 'Boolean')
		{
			return Schism.toBoolean(x);
		}

		if (type == 'Number')
		{
			return Schism.toNumeric(x);
		}

		if (type == 'String')
		{
			return Schism.toString(x);
		}

		if (type == 'Array')
		{
			var res = Schism.nil();

			for (var ix = x.length - 1; ix >= 0; --ix)
			{
				res = Schism.cons(Schism.toSchism(x[ix]), res);
			}

			return res;
		}

		var res = Schism.nil();

		for (var ix in x)
		{
			res = Schism.cons(Schism.cons(Schism.toSchism(ix), Schism.toSchism(x[ix])), res);
		}

		return res;
	},

	fromSchism: function(x)
	{
		if (Schism.fromBoolean(Schism.isBoolean(x)))
		{
			return Schism.fromBoolean(x);
		}

		if (Schism.fromBoolean(Schism.isNumeric(x)))
		{
			return Schism.fromNumeric(x);
		}

		if (Schism.fromBoolean(Schism.isString(x)))
		{
			return Schism.fromString(x);
		}

		if (Schism.fromBoolean(Schism.isLambda(x)))
		{
			return null;
		}

		if (Schism.fromBoolean(Schism.isNil(x)))
		{
			return null;
		}

		if (Schism.fromBoolean(Schism.isCons(x)))
		{
			return Schism.fromCons(x);
		}

		return null;
	}
};

var SchismEnv =
{
	'sys\\raise-error': Schism.toLambda(function(x)
			{
				if (arguments.length != 1)
				{
					throw new SchismInterpreter.SchismRuntimeException('sys\\raise-error: invalid number of arguments');
				}

				throw new SchismInterpreter.SchismRuntimeException(Schism.fromString(x));
			}),
	'boolean?': Schism.toLambda(Schism.isBoolean),
	'numeric?': Schism.toLambda(Schism.isNumeric),
	'string?': Schism.toLambda(Schism.isString),
	'procedure?': Schism.toLambda(Schism.isLambda),
	'nil?': Schism.toLambda(Schism.isNil),
	'cons?': Schism.toLambda(Schism.isCons),
	'equal?': Schism.toLambda(Schism.equal),
	repr: Schism.toLambda(Schism.repr),
	'true': Schism.toBoolean(true),
	'false': Schism.toBoolean(false),
	'else': Schism.toBoolean(true),
	not: Schism.toLambda(function(a)
			{
				if (arguments.length != 1)
				{
					throw new SchismInterpreter.SchismRuntimeException('not: invalid number of arguments');
				}

				return Schism.toBoolean(! Schism.fromBoolean(a));
			}),
	'+': Schism.toLambda(function()
			{
				return [].reduce.call(arguments, function(a, b) { return Schism.toNumeric(Schism.fromNumeric(a) + Schism.fromNumeric(b)); }, Schism.toNumeric(0));
			}),
	'-': Schism.toLambda(function()
			{
				if ((arguments.length != 1) && (arguments.length != 2))
				{
					throw new SchismInterpreter.SchismRuntimeException('-: invalid number of arguments');
				}

				if (arguments.length == 1)
				{
					return Schism.toNumeric(-Schism.fromNumeric(arguments[0]));
				}
				else
				{
					return Schism.toNumeric(Schism.fromNumeric(arguments[0]) - Schism.fromNumeric(arguments[1]));
				}
			}),
	'*': Schism.toLambda(function()
			{
				return [].reduce.call(arguments, function(a, b) { return Schism.toNumeric(Schism.fromNumeric(a) * Schism.fromNumeric(b)); }, Schism.toNumeric(1));
			}),
	'/': Schism.toLambda(function(a, b)
			{
				if ((arguments.length != 1) && (arguments.length != 2))
				{
					throw new SchismInterpreter.SchismRuntimeException('/: invalid number of arguments');
				}

				if (arguments.length == 1)
				{
					return Schism.toNumeric(1 / Schism.fromNumeric(arguments[0]));
				}
				else
				{
					return Schism.toNumeric(Schism.fromNumeric(arguments[0]) / Schism.fromNumeric(arguments[1]));
				}
			}),
	'<': Schism.toLambda(function(a, b)
			{
				if (arguments.length != 2)
				{
					throw new SchismInterpreter.SchismRuntimeException('<: invalid number of arguments');
				}

				return Schism.toBoolean(Schism.fromNumeric(a) < Schism.fromNumeric(b));
			}),
	'<=': Schism.toLambda(function(a, b)
			{
				if (arguments.length != 2)
				{
					throw new SchismInterpreter.SchismRuntimeException('<=: invalid number of arguments');
				}

				return Schism.toBoolean(Schism.fromNumeric(a) <= Schism.fromNumeric(b));
			}),
	'=': Schism.toLambda(function(a, b)
			{
				if (arguments.length != 2)
				{
					throw new SchismInterpreter.SchismRuntimeException('=: invalid number of arguments');
				}

				return Schism.toBoolean(Schism.fromNumeric(a) == Schism.fromNumeric(b));
			}),
	'/=': Schism.toLambda(function(a, b)
			{
				if (arguments.length != 2)
				{
					throw new SchismInterpreter.SchismRuntimeException('/=: invalid number of arguments');
				}

				return Schism.toBoolean(Schism.fromNumeric(a) != Schism.fromNumeric(b));
			}),
	'>': Schism.toLambda(function(a, b)
			{
				if (arguments.length != 2)
				{
					throw new SchismInterpreter.SchismRuntimeException('>: invalid number of arguments');
				}

				return Schism.toBoolean(Schism.fromNumeric(a) > Schism.fromNumeric(b));
			}),
	'>=': Schism.toLambda(function(a, b)
			{
				if (arguments.length != 2)
				{
					throw new SchismInterpreter.SchismRuntimeException('>=: invalid number of arguments');
				}

				return Schism.toBoolean(Schism.fromNumeric(a) >= Schism.fromNumeric(b));
			}),
	floor: Schism.toLambda(function(x)
			{
				if (arguments.length != 1)
				{
					throw new SchismInterpreter.SchismRuntimeException('floor: invalid number of arguments');
				}

				return Schism.toNumeric(Math.floor(Schism.fromNumeric(x)));
			}),
	ceiling: Schism.toLambda(function(x)
			{
				if (arguments.length != 1)
				{
					throw new SchismInterpreter.SchismRuntimeException('ceiling: invalid number of arguments');
				}

				return Schism.toNumeric(Math.ceil(Schism.fromNumeric(x)));
			}),
	round: Schism.toLambda(function(x)
			{
				if (arguments.length != 1)
				{
					throw new SchismInterpreter.SchismRuntimeException('round: invalid number of arguments');
				}

				return Schism.toNumeric(Math.round(Schism.fromNumeric(x)));
			}),
	expt: Schism.toLambda(function(a, b)
			{
				if (arguments.length != 2)
				{
					throw new SchismInterpreter.SchismRuntimeException('expt: invalid number of arguments');
				}

				return Schism.toNumeric(Math.pow(Schism.fromNumeric(arguments[0]), Schism.fromNumeric(arguments[1])));
			}),
	sin: Schism.toLambda(function(x)
			{
				if (arguments.length != 1)
				{
					throw new SchismInterpreter.SchismRuntimeException('sin: invalid number of arguments');
				}

				return Schism.toNumeric(Math.sin(Schism.fromNumeric(x)));
			}),
	cos: Schism.toLambda(function(x)
			{
				if (arguments.length != 1)
				{
					throw new SchismInterpreter.SchismRuntimeException('cos: invalid number of arguments');
				}

				return Schism.toNumeric(Math.cos(Schism.fromNumeric(x)));
			}),
	tan: Schism.toLambda(function(x)
			{
				if (arguments.length != 1)
				{
					throw new SchismInterpreter.SchismRuntimeException('tan: invalid number of arguments');
				}

				return Schism.toNumeric(Math.tan(Schism.fromNumeric(x)));
			}),
	asin: Schism.toLambda(function(x)
			{
				if (arguments.length != 1)
				{
					throw new SchismInterpreter.SchismRuntimeException('asin: invalid number of arguments');
				}

				return Schism.toNumeric(Math.asin(Schism.fromNumeric(x)));
			}),
	acos: Schism.toLambda(function(x)
			{
				if (arguments.length != 1)
				{
					throw new SchismInterpreter.SchismRuntimeException('acos: invalid number of arguments');
				}

				return Schism.toNumeric(Math.acos(Schism.fromNumeric(x)));
			}),
	atan: Schism.toLambda(function(x)
			{
				if (arguments.length != 1)
				{
					throw new SchismInterpreter.SchismRuntimeException('atan: invalid number of arguments');
				}

				return Schism.toNumeric(Math.atan(Schism.fromNumeric(x)));
			}),
	log: Schism.toLambda(function(x)
			{
				if (arguments.length != 1)
				{
					throw new SchismInterpreter.SchismRuntimeException('log: invalid number of arguments');
				}

				return Schism.toNumeric(Math.log(Schism.fromNumeric(x)));
			}),
	'number->string': Schism.toLambda(function(x)
			{
				if (arguments.length != 1)
				{
					throw new SchismInterpreter.SchismRuntimeException('number->string: invalid number of arguments');
				}

				return Schism.toString('' + Schism.fromNumeric(x));
			}),
	'integer->string': Schism.toLambda(function(x)
			{
				if (arguments.length != 1)
				{
					throw new SchismInterpreter.SchismRuntimeException('integer->string: invalid number of arguments');
				}

				return Schism.toString(String.fromCharCode(Schism.fromNumeric(x)));
			}),

	'string<?': Schism.toLambda(function(a, b)
			{
				if (arguments.length != 2)
				{
					throw new SchismInterpreter.SchismRuntimeException('string<?: invalid number of arguments');
				}

				return Schism.toBoolean(Schism.fromString(a) < Schism.fromString(b));
			}),
	'string-ci<?': Schism.toLambda(function(a, b)
			{
				if (arguments.length != 2)
				{
					throw new SchismInterpreter.SchismRuntimeException('string-ci<?: invalid number of arguments');
				}

				return Schism.toBoolean(Schism.fromString(a).toLowerCase() < Schism.fromString(b).toLowerCase());
			}),
	'string<=?': Schism.toLambda(function(a, b)
			{
				if (arguments.length != 2)
				{
					throw new SchismInterpreter.SchismRuntimeException('string<=?: invalid number of arguments');
				}

				return Schism.toBoolean(Schism.fromString(a) <= Schism.fromString(b));
			}),
	'string-ci<=?': Schism.toLambda(function(a, b)
			{
				if (arguments.length != 2)
				{
					throw new SchismInterpreter.SchismRuntimeException('string-ci<=?: invalid number of arguments');
				}

				return Schism.toBoolean(Schism.fromString(a).toLowerCase() <= Schism.fromString(b).toLowerCase());
			}),
	'string=?': Schism.toLambda(function(a, b)
			{
				if (arguments.length != 2)
				{
					throw new SchismInterpreter.SchismRuntimeException('string=?: invalid number of arguments');
				}

				return Schism.toBoolean(Schism.fromString(a) == Schism.fromString(b));
			}),
	'string-ci=?': Schism.toLambda(function(a, b)
			{
				if (arguments.length != 2)
				{
					throw new SchismInterpreter.SchismRuntimeException('string-ci=?: invalid number of arguments');
				}

				return Schism.toBoolean(Schism.fromString(a).toLowerCase() == Schism.fromString(b).toLowerCase());
			}),
	'string/=?': Schism.toLambda(function(a, b)
			{
				if (arguments.length != 2)
				{
					throw new SchismInterpreter.SchismRuntimeException('string/=?: invalid number of arguments');
				}

				return Schism.toBoolean(Schism.fromString(a) != Schism.fromString(b));
			}),
	'string-ci/=?': Schism.toLambda(function(a, b)
			{
				if (arguments.length != 2)
				{
					throw new SchismInterpreter.SchismRuntimeException('string-ci/=?: invalid number of arguments');
				}

				return Schism.toBoolean(Schism.fromString(a).toLowerCase() != Schism.fromString(b).toLowerCase());
			}),
	'string>?': Schism.toLambda(function(a, b)
			{
				if (arguments.length != 2)
				{
					throw new SchismInterpreter.SchismRuntimeException('string>?: invalid number of arguments');
				}

				return Schism.toBoolean(Schism.fromString(a) > Schism.fromString(b));
			}),
	'string-ci>?': Schism.toLambda(function(a, b)
			{
				if (arguments.length != 2)
				{
					throw new SchismInterpreter.SchismRuntimeException('string-ci>?: invalid number of arguments');
				}

				return Schism.toBoolean(Schism.fromString(a).toLowerCase() > Schism.fromString(b).toLowerCase());
			}),
	'string>=?': Schism.toLambda(function(a, b)
			{
				if (arguments.length != 2)
				{
					throw new SchismInterpreter.SchismRuntimeException('string>=?: invalid number of arguments');
				}

				return Schism.toBoolean(Schism.fromString(a) >= Schism.fromString(b));
			}),
	'string-ci>=?': Schism.toLambda(function(a, b)
			{
				if (arguments.length != 2)
				{
					throw new SchismInterpreter.SchismRuntimeException('string-ci>=?: invalid number of arguments');
				}

				return Schism.toBoolean(Schism.fromString(a).toLowerCase() >= Schism.fromString(b).toLowerCase());
			}),
	'string->number': Schism.toLambda(function(x)
			{
				if (arguments.length != 1)
				{
					throw new SchismInterpreter.SchismRuntimeException('string-number: invalid number of arguments');
				}

				return Schism.toNumeric(parseFloat(Schism.fromString(x)));
			}),
	'string->integer': Schism.toLambda(function(x)
			{
				if (arguments.length != 1)
				{
					throw new SchismInterpreter.SchismRuntimeException('string-integer: invalid number of arguments');
				}

				xConv = Schism.fromString(x);

				return Schism.toNumeric(xConv.length > 0 ? xConv.charCodeAt(0) : 0);
			}),
	'string-length': Schism.toLambda(function(a)
			{
				if (arguments.length != 1)
				{
					throw new SchismInterpreter.SchismRuntimeException('string-length: invalid number of arguments');
				}

				return Schism.toNumeric(Schism.fromString(a).length);
			}),
	'string-append': Schism.toLambda(function()
			{
				return [].reduce.call(arguments, function(a, b) { return Schism.toString(Schism.fromString(a) + Schism.fromString(b)); }, Schism.toString(''));
			}),
	substring: Schism.toLambda(function(str, a, b)
			{
				if (arguments.length != 3)
				{
					throw new SchismInterpreter.SchismRuntimeException('substring: invalid number of arguments');
				}

				return Schism.toString(Schism.fromString(str).substring(Schism.fromNumeric(a), Schism.fromNumeric(b)));
			}),

	nil: Schism.nil(),
	cons: Schism.toLambda(Schism.cons),
	car: Schism.toLambda(Schism.car),
	cdr: Schism.toLambda(Schism.cdr),
	list: Schism.toLambda(function()
			{
				var res = Schism.nil();

				for (var i = arguments.length - 1; i >= 0; --i)
				{
					res = Schism.cons(arguments[i], res);
				}

				return res;
			}),
};

