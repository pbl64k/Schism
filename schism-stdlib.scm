
(comment "Schism stdlib")

(comment "assertions")

(define [assert true-expr]
	(if true-expr
		true
		(sys\raise-error "assertion failure: expression did not evaluate to true")))

(define [assert-equals expected received]
	(if [equal? expected received]
		true
		(sys\raise-error (string-append "assert-equals failure: expected " (repr expected) ", received " (repr received)))))

(comment "types and equality")

(define empty? nil?)

(define null? nil?)

(define pair? cons?)

(define [list? x] (or (null? x) (pair? x)))

(define lambda? procedure?)

(define eq? equal?)

(define eqv? equal?)

(comment "numeric")

(define PI 3.141592653589793)

(define E 2.718281828459045)

(define number? numeric?)

(define == =)

(define != /=)

(define [zero? x] (= x 0))

(define [positive? x] (< 0 x))

(define [negative? x] (< x 0))

(define [abs x] (if [negative? x] (- x) x))

(define [sign x]
	(if [zero? x]
		0
		(/ x (abs x))))

(define [max a b] (if [< a b] b a))

(define [min a b] (if [< a b] a b))

(define [truncate x]
	(if [negative? x]
		(ceiling x)
		(floor x)))

(define [quotient a b] (truncate (/ a b)))

(define [remainder a b] (- a (* b (quotient a b))))

(define [floored-quotient a b] (floor (/ a b)))

(define [modulo a b] (- a (* b (floored-quotient a b))))

(define [gcd a b] (if [zero? b] a (gcd b (remainder a b))))

(define [lcm a b] (/ (abs (* a b)) (gcd a b)))

(define [sqrt x] (expt x 0.5))

(define [even? x] (zero? (remainder x 2)))

(define [odd? x] (not (even? x)))

(define [exp x] (expt E x))

(define [succ x] (+ 1 x))

(define [pred x] (- x 1))

(comment "strings and characters")

(define [char? x] (and (string? x) (= 1 (string-length x))))

(define [char<? a b]
	(letrec
		([precondition-1 (assert (char? a))]
		[precondition-2 (assert (char? b))])
		(string<? a b)))

(define [char-ci<? a b]
	(letrec
		([precondition-1 (assert (char? a))]
		[precondition-2 (assert (char? b))])
		(string-ci<? a b)))

(define [char<=? a b]
	(letrec
		([precondition-1 (assert (char? a))]
		[precondition-2 (assert (char? b))])
		(string<=? a b)))

(define [char-ci<=? a b]
	(letrec
		([precondition-1 (assert (char? a))]
		[precondition-2 (assert (char? b))])
		(string-ci<=? a b)))

(define [char=? a b]
	(letrec
		([precondition-1 (assert (char? a))]
		[precondition-2 (assert (char? b))])
		(string=? a b)))

(define [char-ci=? a b]
	(letrec
		([precondition-1 (assert (char? a))]
		[precondition-2 (assert (char? b))])
		(string-ci=? a b)))

(define [char/=? a b]
	(letrec
		([precondition-1 (assert (char? a))]
		[precondition-2 (assert (char? b))])
		(string/=? a b)))

(define [char-ci/=? a b]
	(letrec
		([precondition-1 (assert (char? a))]
		[precondition-2 (assert (char? b))])
		(string-ci/=? a b)))

(define [char>? a b]
	(letrec
		([precondition-1 (assert (char? a))]
		[precondition-2 (assert (char? b))])
		(string>? a b)))

(define [char-ci>? a b]
	(letrec
		([precondition-1 (assert (char? a))]
		[precondition-2 (assert (char? b))])
		(string-ci>? a b)))

(define [char>=? a b]
	(letrec
		([precondition-1 (assert (char? a))]
		[precondition-2 (assert (char? b))])
		(string>=? a b)))

(define [char-ci>=? a b]
	(letrec
		([precondition-1 (assert (char? a))]
		[precondition-2 (assert (char? b))])
		(string-ci>=? a b)))

(define [char->integer x]
	(letrec
		([precondition (char? x)])
		(string->integer x)))

(define integer->char integer->string)

(comment "basic combinators")

(define [id x] x)

(define [const x] (lambda [y] x))

(define [flip f] (lambda [x y] (f y x)))

(define [dot f g] (lambda [x] (f (g x))))

(define [subst f g] (lambda [x] ((f x) (g x))))

(define [fix f] (lambda [x] (f (fix f) x)))

(comment "pairs and lists")

(define empty nil)

(define pair cons)

(define first car)

(define rest cdr)

(define [fold-left f init xs]
	(if [null? xs]
		init
		(fold-left f (f init (car xs)) (cdr xs))))

(define foldl fold-left)

(define [fold-right f init xs]
	(if [null? xs]
		init
		(f (car xs) (fold-right f init (cdr xs)))))

(define foldr fold-right)

(define [unfold-left f init]
	(letrec
		([next (f init)])
		(if [null? next]
			empty
			(cons (car next) (unfold-left f (cdr next))))))

(define unfoldl unfold-left)

(define unfold unfold-left)

(define [map f xs]
	(fold-right
		(lambda [elt res] (cons (f elt) res))
		empty
		xs))

(define [reverse xs]
	(fold-left
		(lambda [a b] (cons b a))
		empty
		xs))

(define [join xs] (fold-left append empty xs))

(define [bind f xs] ((dot join (lambda [xs] (map f xs))) xs))

(define [filter f xs]
	(bind
		(lambda [x] (if [f x] (cons x empty) empty))
		xs))

(define [all? f xs]
	(fold-left
		(lambda [acc elt] (and acc (f elt)))
		true
		xs))

(define for-all? (flip all?))

(define [any? f xs]
	(fold-left
		(lambda [acc elt] (or acc (f elt)))
		false
		xs))

(define there-exists? (flip any?))

(define [length xs]
	(fold-left
		(lambda [a b] (+ a 1))
		0
		xs))

(define [zip a b]
	(unfold-left
		(lambda [x]
			(if [or (null? (car x)) (null? (cdr x))]
				empty
				(cons
					(cons (car (car x)) (car (cdr x)))
					(cons (cdr (car x)) (cdr (cdr x))))))
		(cons a b)))

(define [member x xs]
	(cond
		[(null? xs) false]
		[(equal? (car xs) x) xs]
		[else (member x (cdr xs))]))

(define memq member)

(define memv member)

(define [element-of x xs] (pair? (member x xs)))

(define [append xs ys] (fold-right cons ys xs))

(define [ith xs i]
	(cond
		[(null? xs) (sys\raise-error "no such element")]
		[(zero? i) (car xs)]
		[else (ith (cdr xs) (- i 1))]))

(define list-ref ith)

(define [take n xs]
	(if [zero? n]
		empty
		(cons (car xs) (take (- n 1) (cdr xs)))))

(define list-head (flip take))

(define [drop n xs]
	(if [zero? n]
		xs
		(drop (- n 1) (cdr xs))))

(define list-tail (flip drop))

(define [sort-with less-than? xs]
	(if [null? xs]
		empty
		(letrec
			([pivot (car xs)])
			(append
				(sort-with
					less-than?
					(filter
						(lambda [x] (less-than? x pivot))
						(cdr xs)))
				(cons
					pivot
					(sort-with
						less-than?
						(filter
							(lambda [x] (not (less-than? x pivot)))
							(cdr xs))))))))

(define [merge-with f xs ys]
	(cond
		[(null? xs) ys]
		[(null? ys) xs]
		[(f (car xs) (car ys)) (cons (car xs) (merge-with f (cdr xs) ys))]
		[else (cons (car ys) (merge-with f xs (cdr ys)))]))

(define [merge-sort-with f xs]
	(letrec
		([num-xs (length xs)]
		[half-point (floor (/ num-xs 2))])
		(if [< num-xs 2]
			xs
			(merge-with f
				(merge-sort-with f (take half-point xs))
				(merge-sort-with f (drop half-point xs))))))

(define quick-sort (flip sort-with))

(define merge-sort (flip merge-sort-with))

(define sort merge-sort)

(define [assoc key xs]
	(cond
		[(null? xs) false]
		[(equal? (car (car xs)) key) (car xs)]
		[else (assoc key (cdr xs))]))

(define assq assoc)

(define assv assoc)

(define [range-step from to step]
	(if [<= to from]
		empty
		(cons from (range-step (+ step from) to step))))

(define [range from to] (range-step from to 1))

(define [make-list k elt]
	(unfold-left
		(lambda [x]
			(if [zero? x]
				empty
				(cons elt (- x 1))))
		k))

(comment "more string and characters")

(define [list->string cs]
	(fold-right string-append "" cs))

(define [string->list str]
	(unfold-left
		(lambda [x]
			(if [zero? (string-length x)]
				empty
				(cons (substring x 0 1) (substring x 1 (string-length x)))))
		str))

(define [string-null? str] (zero? (string-length str)))

(define [string-ref str ix] (substring str ix (+ ix 1)))

(define [string-head str pos] (substring str 0 pos))

(define [string-tail str pos] (substring str pos (string-length str)))

(define UPPERCASE-LETTER-CHARACTERS (string->list "ABCDEFGHIJKLMNOPQRSTUVWXYZ"))

(define LOWERCASE-LETTER-CHARACTERS (string->list "abcdefghijklmnopqrstuvwxyz"))

(define LOWER-TO-UPPER-LETTER-MAPPING (zip LOWERCASE-LETTER-CHARACTERS UPPERCASE-LETTER-CHARACTERS))

(define UPPER-TO-LOWER-LETTER-MAPPING (zip UPPERCASE-LETTER-CHARACTERS LOWERCASE-LETTER-CHARACTERS))

(define LETTER-CHARACTERS (append UPPERCASE-LETTER-CHARACTERS LOWERCASE-LETTER-CHARACTERS))

(define NUMERIC-CHARACTERS (string->list "01234567890"))

(define WHITESPACE-CHARACTERS (string->list " "))

(define [char-alphabetic? c]
	(letrec
		([precondition (char? c)])
		(element-of c LETTER-CHARACTERS)))

(define [char-numeric? c]
	(letrec
		([precondition (char? c)])
		(element-of c NUMERIC-CHARACTERS)))

(define [char-whitespace? c]
	(letrec
		([precondition (char? c)])
		(element-of c WHITESPACE-CHARACTERS)))

(define [char-upper-case? c]
	(letrec
		([precondition (char? c)])
		(element-of c UPPERCASE-LETTER-CHARACTERS)))

(define [char-lower-case? c]
	(letrec
		([precondition (char? c)])
		(element-of c LOWERCASE-LETTER-CHARACTERS)))

(define [char-upcase c]
	(letrec
		([precondition (char? c)])
		(if [map\has? LOWER-TO-UPPER-LETTER-MAPPING c]
			(map\get LOWER-TO-UPPER-LETTER-MAPPING c)
			c)))

(define [char-downcase c]
	(letrec
		([precondition (char? c)])
		(if [map\has? UPPER-TO-LOWER-LETTER-MAPPING c]
			(map\get UPPER-TO-LOWER-LETTER-MAPPING c)
			c)))

(comment "misc")

(define [integrate f xs init]
	(if [<= (length xs) 1]
		(cons init empty)
		(letrec
			([diff (- (car (cdr xs)) (car xs))]
			[y (+ init (* diff (f (car xs) init)))])
			(cons init (integrate f (cdr xs) y)))))

(define [integrate-rk f xs init]
	(if [<= (length xs) 1]
		(list init)
		(letrec
			([h (- (car (cdr xs)) (car xs))]
			[k1 (f (car xs) init)]
			[k2 (f (+ (car xs) (/ h 2)) (+ init (* 0.5 h k1)))]
			[k3 (f (+ (car xs) (/ h 2)) (+ init (* 0.5 h k2)))]
			[k4 (f (+ (car xs) h) (+ init (* h k3)))]
			[y (+ init (* (/ 6) h (+ k1 (* 2 k2) (* 2 k3) k4)))])
			(cons init (integrate-rk f (cdr xs) y)))))

(comment "maps (lists of pairs, representing key-value associations)")

(define [map\has? xs key]
	(cond
		[(null? xs) false]
		[(equal? (car (car xs)) key) true]
		[else (map\has? (cdr xs) key)]))

(define [map\elt-already-exists key]
	(sys\raise-error (string-append "element already exists: " (repr key))))

(define [map\elt-not-found key]
	(sys\raise-error (string-append "element not found: " (repr key))))

(define [map\get xs key]
	(cond
		[(null? xs) (map\elt-not-found key)]
		[(equal? (car (car xs)) key) (cdr (car xs))]
		[else (map\get (cdr xs) key)]))

(define [map\update-without-checking xs key value]
	(map
		(lambda [elt]
			(if [equal? (car elt) key]
				(cons key value)
				elt))
		xs))

(define [map\update xs key value]
	(if [map\has? xs key]
		(map\update-without-checking xs key value)
		(map\elt-not-found key)))

(define [map\delete-without-checking xs key]
	(filter
		(lambda [elt] (not (equal? (car elt) key)))
		xs))

(define [map\delete xs key]
	(if [map\has? xs key]
		(map\delete-without-checking xs key)
		(map\elt-not-found key)))

(define [map\insert-without-checking xs key value]
	(cons (cons key value) xs))

(define [map\insert xs key value]
	(if [map\has? xs key]
		(map\elt-already-exists key)
		(map\insert-without-checking xs key value)))

(define [map\insert-or-update xs key value]
	(if [map\has? xs key]
		(map\update-without-checking xs key value)
		(map\insert-without-checking xs key value)))

(define [map\keys xs] (map car xs))

(define [map\values xs] (map cdr xs))

