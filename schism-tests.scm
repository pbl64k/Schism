
(comment "Schism tests")

(comment "booleans")

(assert-equals false (not true))
(assert-equals true (not false))

(assert-equals false (and false false))
(assert-equals false (and false true))
(assert-equals false (and true false))
(assert-equals true (and true true))

(assert-equals false (or false false))
(assert-equals true (or false true))
(assert-equals true (or true false))
(assert-equals true (or true true))

(assert-equals false (and true false true))
(assert-equals true (and true true true))
(assert-equals false (or false false false))
(assert-equals true (or false true false))

(assert-equals true (and true))
(assert-equals false (and false))
(assert-equals true (or true))
(assert-equals false (or false))

(comment "don't mind the conspicuous space after the keywords.")

(assert-equals true (and ))
(assert-equals false (or ))

(assert-equals false (not (or (not true) (and true (not false) (or false true)) (and (not true) true))))

(comment "numbers")

(assert-equals 4 (+ 2 2))
(assert-equals 4 (- 6 2))
(assert-equals 4 (* 2 2))
(assert-equals 3 (/ 6 2))

(assert-equals 124.55 (+ +123.45 1.1))
(assert-equals 124.55 (+ 123.45 1.1))
(assert-equals 122.45 (+ 123.45 -1.0))

(assert-equals -1e12 (* +1.0e+6 -1.000e6))
(assert-equals 1.0 (* 1.0e+6 1e-6))

(assert-equals 15 (+ 5 4 3 2 1))
(assert-equals 120 (* 5 4 3 2 1))

(assert-equals true (< 1 2))
(assert-equals false (< 2 2))
(assert-equals false (< 3 2))
(assert-equals true (<= 1 2))
(assert-equals true (<= 2 2))
(assert-equals false (<= 3 2))
(assert-equals false (= 1 2))
(assert-equals true (= 2 2))
(assert-equals false (= 3 2))
(assert-equals true (/= 1 2))
(assert-equals false (/= 2 2))
(assert-equals true (/= 3 2))
(assert-equals false (> 1 2))
(assert-equals false (> 2 2))
(assert-equals true (> 3 2))
(assert-equals false (>= 1 2))
(assert-equals true (>= 2 2))
(assert-equals true (>= 3 2))

(assert-equals 0 (+))
(assert-equals 1 (*))
(assert-equals 10 (+ 10))
(assert-equals -10 (- 10))
(assert-equals 10 (* 10))
(assert-equals 0.5 (/ 2))

(assert-equals 120 (* (/ 10 10) (- 4 2) (+ 1 2) (* 2 2) (+ 1 2 2)))
(assert-equals true (and (= 4 (+ 2 2)) (< (* 2 2) (* 2 3)) (>= 12 (/ 12 2))))

(assert-equals false (zero? 1.0))
(assert-equals false (zero? -1e-15))
(assert-equals true (zero? 0))
(assert-equals true (zero? 0.0))

(assert-equals true (positive? 1.0))
(assert-equals false (positive? -1e-15))
(assert-equals false (positive? 0))
(assert-equals false (positive? 0.0))

(assert-equals false (negative? 1.0))
(assert-equals true (negative? -1e-15))
(assert-equals false (negative? 0))
(assert-equals false (negative? 0.0))

(assert-equals 0 (abs 0))
(assert-equals 25 (abs 25))
(assert-equals 0.1e-10 (abs -0.1e-10))
(assert-equals 2 (abs -2.0))

(assert-equals -0.1e-10 (min -0.1e-10 (abs -0.1e-10)))
(assert-equals 2 (max 1 2))
(assert-equals 1.0 (max 1 1.0))

(assert-equals 2 (floor 2.4))
(assert-equals 2 (floor 2.6))
(assert-equals -3 (floor -2.4))
(assert-equals -3 (floor -2.6))

(assert-equals 3 (ceiling 2.4))
(assert-equals 3 (ceiling 2.6))
(assert-equals -2 (ceiling -2.4))
(assert-equals -2 (ceiling -2.6))

(assert-equals 2 (truncate 2.4))
(assert-equals 2 (truncate 2.6))
(assert-equals -2 (truncate -2.4))
(assert-equals -2 (truncate -2.6))

(assert-equals 2 (round 2.4))
(assert-equals 3 (round 2.5))
(assert-equals 3 (round 2.6))
(assert-equals -2 (round -2.4))
(assert-equals -3 (round -2.6))

(assert-equals 1 (remainder 13 4))
(assert-equals -1 (remainder -13 4))
(assert-equals 1 (remainder 13 -4))
(assert-equals -1 (remainder -13 -4))

(assert-equals 1 (modulo 13 4))
(assert-equals 3 (modulo -13 4))
(assert-equals -3 (modulo 13 -4))
(assert-equals -1 (modulo -13 -4))

(assert-equals 6 (gcd 18 12))

(assert-equals 36 (lcm 18 12))

(assert-equals 8 (expt 2 3))
(assert-equals 1024 (expt 2 10))
(assert-equals 81 (expt 3 4))

(assert-equals 4 (sqrt 16))
(assert-equals 32 (sqrt 1024))
(assert-equals 9 (sqrt 81))

(assert-equals false (even? 3))
(assert-equals true (even? 2))
(assert-equals false (even? 1))
(assert-equals true (even? 0))
(assert-equals false (even? -1))
(assert-equals true (even? -2))
(assert-equals false (even? -3))
(assert-equals true (odd? 3))
(assert-equals false (odd? 2))
(assert-equals true (odd? 1))
(assert-equals false (odd? 0))
(assert-equals true (odd? -1))
(assert-equals false (odd? -2))
(assert-equals true (odd? -3))

(assert-equals 1 (exp 0))
(assert-equals E (exp 1))
(assert-equals (expt E 2) (exp 2))

(assert-equals 0 (sin 0))
(assert-equals 1 (sin (/ PI 2)))

(assert-equals 2 (log (exp 2)))

(define [factorial n]
	(if [= n 0]
		1
		(* n (factorial (- n 1)))))

(assert-equals 5040 (factorial 7))

(define [fibo n]
	(letrec
		([fibo-helper
			(lambda [a b n]
				(if [= n 0]
					a
					(fibo-helper b (+ a b) (- n 1))))])
		(fibo-helper 0 1 n)))

(assert-equals 0 (fibo 0))
(assert-equals 1 (fibo 1))
(assert-equals 1 (fibo 2))
(assert-equals 2 (fibo 3))
(assert-equals 3 (fibo 4))
(assert-equals 5 (fibo 5))
(assert-equals 8 (fibo 6))
(assert-equals 13 (fibo 7))

(comment "strings")

(assert-equals true (string? "abcde"))
(assert-equals false (string? 123))
(assert-equals false (string? true))

(assert-equals 0 (string-length ""))
(assert-equals 1 (string-length "a"))
(assert-equals 3 (string-length "abc"))

(assert-equals false (string=? "abc" "abcde"))
(assert-equals true (string=? "abcde" (string-append "ab" "c" "de")))
(assert-equals true (string=? "abcde" "abcde"))
(assert-equals false (string=? "abcde" "aBcDe"))
(assert-equals false (string=? "abcde" "ABCDE"))
(assert-equals true (string-ci=? "abcde" "abcde"))
(assert-equals true (string-ci=? "abcde" "aBcDe"))
(assert-equals true (string-ci=? "abcde" "ABCDE"))

(assert-equals true (string<? "aaa" "aab"))
(assert-equals false (string<? "aaa" "aaa"))
(assert-equals false (string<? "aba" "aaa"))
(assert-equals true (string-ci<? "aaa" "aab"))
(assert-equals false (string-ci<? "aaa" "aaa"))
(assert-equals false (string-ci<? "aba" "aaa"))
(assert-equals true (string<=? "aaa" "aab"))
(assert-equals true (string<=? "aaa" "aaa"))
(assert-equals false (string<=? "aba" "aaa"))
(assert-equals true (string-ci<=? "aaa" "aab"))
(assert-equals true (string-ci<=? "aaa" "aaa"))
(assert-equals false (string-ci<=? "aba" "aaa"))
(assert-equals false (string=? "aaa" "aab"))
(assert-equals true (string=? "aaa" "aaa"))
(assert-equals false (string=? "aba" "aaa"))
(assert-equals false (string-ci=? "aaa" "aab"))
(assert-equals true (string-ci=? "aaa" "aaa"))
(assert-equals false (string-ci=? "aba" "aaa"))
(assert-equals true (string/=? "aaa" "aab"))
(assert-equals false (string/=? "aaa" "aaa"))
(assert-equals true (string/=? "aba" "aaa"))
(assert-equals true (string-ci/=? "aaa" "aab"))
(assert-equals false (string-ci/=? "aaa" "aaa"))
(assert-equals true (string-ci/=? "aba" "aaa"))
(assert-equals false (string>? "aaa" "aab"))
(assert-equals false (string>? "aaa" "aaa"))
(assert-equals true (string>? "aba" "aaa"))
(assert-equals false (string-ci>? "aaa" "aab"))
(assert-equals false (string-ci>? "aaa" "aaa"))
(assert-equals true (string-ci>? "aba" "aaa"))
(assert-equals false (string>=? "aaa" "aab"))
(assert-equals true (string>=? "aaa" "aaa"))
(assert-equals true (string>=? "aba" "aaa"))
(assert-equals false (string-ci>=? "aaa" "aab"))
(assert-equals true (string-ci>=? "aaa" "aaa"))
(assert-equals true (string-ci>=? "aba" "aaa"))

(assert-equals true (char? "a"))
(assert-equals true (char? "b"))
(assert-equals true (char? "c"))
(assert-equals false (char? ""))
(assert-equals false (char? "bb"))
(assert-equals false (char? "abcde"))

(assert-equals true (char=? "a" "a"))
(assert-equals false (char=? "a" "A"))
(assert-equals true (char-ci=? "a" "a"))
(assert-equals true (char-ci=? "a" "A"))
(assert-equals false (char-ci=? "a" "B"))

(assert-equals 102.7 (string->number "102.7"))
(assert-equals "102.7" (number->string 102.7))

(assert-equals 65 (string->integer "Alice and Bob"))
(assert-equals "A" (integer->string 65))

(assert-equals 0 (string-length ""))
(assert-equals 1 (string-length "e"))
(assert-equals 2 (string-length "de"))
(assert-equals 3 (string-length "cde"))
(assert-equals 4 (string-length "bcde"))
(assert-equals 5 (string-length "abcde"))

(assert-equals "" (substring "" 0 1))
(assert-equals "e" (substring "e" 0 1))
(assert-equals "d" (substring "de" 0 1))
(assert-equals "c" (substring "cde" 0 1))
(assert-equals "b" (substring "bcde" 0 1))
(assert-equals "a" (substring "abcde" 0 1))

(assert-equals "" (letrec ([str ""]) (substring str 1 (string-length str))))
(assert-equals "" (letrec ([str "e"]) (substring str 1 (string-length str))))
(assert-equals "e" (letrec ([str "de"]) (substring str 1 (string-length str))))
(assert-equals "de" (letrec ([str "cde"]) (substring str 1 (string-length str))))
(assert-equals "cde" (letrec ([str "bcde"]) (substring str 1 (string-length str))))
(assert-equals "bcde" (letrec ([str "abcde"]) (substring str 1 (string-length str))))

(assert-equals 0 (letrec ([str ""]) (string-length (substring str 1 (string-length str)))))
(assert-equals 0 (letrec ([str "e"]) (string-length (substring str 1 (string-length str)))))
(assert-equals 1 (letrec ([str "de"]) (string-length (substring str 1 (string-length str)))))
(assert-equals 2 (letrec ([str "cde"]) (string-length (substring str 1 (string-length str)))))
(assert-equals 3 (letrec ([str "bcde"]) (string-length (substring str 1 (string-length str)))))
(assert-equals 4 (letrec ([str "abcde"]) (string-length (substring str 1 (string-length str)))))

(assert-equals "bcd" (substring "abcde" 1 4))

(assert-equals "abcde" (list->string (list "a" "b" "c" "d" "e")))

(assert-equals (list "a" "b" "c" "d" "e") (string->list "abcde"))

(assert-equals "c" (string-ref "abcde" 2))

(assert-equals false (char-alphabetic? " "))
(assert-equals false (char-alphabetic? "2"))
(assert-equals true (char-alphabetic? "b"))
(assert-equals true (char-alphabetic? "Z"))
(assert-equals false (char-numeric? " "))
(assert-equals true (char-numeric? "2"))
(assert-equals false (char-numeric? "b"))
(assert-equals false (char-numeric? "Z"))
(assert-equals true (char-whitespace? " "))
(assert-equals false (char-whitespace? "2"))
(assert-equals false (char-whitespace? "b"))
(assert-equals false (char-whitespace? "Z"))
(assert-equals false (char-upper-case? " "))
(assert-equals false (char-upper-case? "2"))
(assert-equals false (char-upper-case? "b"))
(assert-equals true (char-upper-case? "Z"))
(assert-equals false (char-lower-case? " "))
(assert-equals false (char-lower-case? "2"))
(assert-equals true (char-lower-case? "b"))
(assert-equals false (char-lower-case? "Z"))

(assert-equals " " (char-upcase " "))
(assert-equals "2" (char-upcase "2"))
(assert-equals "B" (char-upcase "b"))
(assert-equals "Z" (char-upcase "Z"))
(assert-equals " " (char-downcase " "))
(assert-equals "2" (char-downcase "2"))
(assert-equals "b" (char-downcase "b"))
(assert-equals "z" (char-downcase "Z"))

(comment "lexical scoping")

(assert-equals 9
	(letrec
		([f
			(letrec
				([x 4])
				(lambda [z] (+ x z)))])
		(letrec
			([x 10])
			(f 5))))

(assert-equals 2
	(letrec
		([true false])
		(if true 1 2)))
	
(assert-equals 2
	(if true
		(letrec
			([x	(letrec
				([true false])
				(if true 1 2))])
			(if true x true))
		3))
	
(define [f1] 5)

(assert-equals 5 (f1))

(define f2 (lambda [] 5))

(assert-equals 5 (f2))

(define CONSTANT 5)

(define [mult-by-const x] (* CONSTANT x))

(define mult-by-const-alt
	(letrec
		([constant CONSTANT])
		(lambda [x] (* constant x))))

(define CONSTANT 6)

(assert-equals 24 (mult-by-const 4))

(assert-equals 20 (mult-by-const-alt 4))

(define defined-value 2)

(assert-equals 2 defined-value)

(comment "HOFs")

(assert-equals true (id true))
(assert-equals false (id false))
(assert-equals 0 (id 0))
(assert-equals 1 (id 1))
(assert-equals "abcde" (id "abcde"))
(assert-equals (cons 1 (cons 2 empty)) (id (cons 1 (cons 2 empty))))

(assert-equals true ((const true) false))
(assert-equals 100 ((const 100) true))

(assert-equals 3 ((flip /) 2 6))

(assert-equals 9 ((dot (lambda [x] (+ 1 x)) (lambda [x] (* 2 x))) 4))

(define fix-fact (fix (lambda [f x] (if (< x 1) 1 (* x (f (- x 1)))))))

(assert-equals 5040 (fix-fact 7))

(assert-equals (factorial 0) (fix-fact 0))
(assert-equals (factorial 1) (fix-fact 1))
(assert-equals (factorial 2) (fix-fact 2))
(assert-equals (factorial 3) (fix-fact 3))
(assert-equals (factorial 4) (fix-fact 4))
(assert-equals (factorial 5) (fix-fact 5))

(comment "lists")

(define test-list (cons 1 (cons 2 (cons 3 empty))))

(assert-equals test-list (list 1 2 3))
(assert-equals empty (list))

(assert-equals true (empty? empty))
(assert-equals false (empty? test-list))

(assert-equals 1 (first test-list))
(assert-equals 2 (first (rest test-list)))

(assert-equals 6 (foldl + 0 test-list))
(assert-equals 6 (foldr + 0 test-list))
(assert-equals 6 (foldl * 1 test-list))
(assert-equals 6 (foldr * 1 test-list))

(define [hylo-fact n]
	(foldl * 1
		(unfoldl
			(lambda [x]
				(if [= x 0]
					empty
					(cons x (- x 1))))
			n)))

(assert-equals (factorial 0) (hylo-fact 0))
(assert-equals (factorial 1) (hylo-fact 1))
(assert-equals (factorial 2) (hylo-fact 2))
(assert-equals (factorial 3) (hylo-fact 3))
(assert-equals (factorial 4) (hylo-fact 4))
(assert-equals (factorial 5) (hylo-fact 5))

(assert-equals 2 (first (map (lambda [x] (* x 2)) test-list)))
(assert-equals 4 (first (rest (map (lambda [x] (* x 2)) test-list))))
(assert-equals 6 (first (rest (rest (map (lambda [x] (* x 2)) test-list)))))
(assert-equals 2 (first (filter (lambda [x] (> x 1)) test-list)))
(assert-equals 3 (first (rest (filter (lambda [x] (> x 1)) test-list))))

(assert-equals 3 (first (reverse test-list)))
(assert-equals 2 (first (rest (reverse test-list))))
(assert-equals test-list (reverse (reverse test-list)))
(assert-equals (list 3 2 1) (reverse test-list))

(define neg-test-list (map (lambda [x] (- 0 x)) test-list))
(define mix-test-list (append test-list neg-test-list))

(assert-equals false (all? (lambda [x] (< x 0)) test-list))
(assert-equals true (all? (lambda [x] (< x 0)) neg-test-list))
(assert-equals false (all? (lambda [x] (< x 0)) mix-test-list))
(assert-equals true (all? (lambda [x] (< 0 x)) test-list))
(assert-equals false (all? (lambda [x] (< 0 x)) neg-test-list))
(assert-equals false (all? (lambda [x] (< 0 x)) mix-test-list))
(assert-equals false (any? (lambda [x] (< x 0)) test-list))
(assert-equals true (any? (lambda [x] (< x 0)) neg-test-list))
(assert-equals true (any? (lambda [x] (< x 0)) mix-test-list))
(assert-equals true (any? (lambda [x] (< 0 x)) test-list))
(assert-equals false (any? (lambda [x] (< 0 x)) neg-test-list))
(assert-equals true (any? (lambda [x] (< 0 x)) mix-test-list))

(assert-equals true (for-all? (list 1 2 3) (lambda [x] (< 0 x))))

(assert-equals 0 (length empty))
(assert-equals 3 (length test-list))
(assert-equals 1 (length (cons 1 empty)))
(assert-equals 3 (length (reverse test-list)))
(assert-equals 3 (length (zip test-list (reverse test-list))))

(assert-equals (cons 3 empty) (member 3 test-list))
(assert-equals test-list (member 1 test-list))
(assert-equals false (member 4 test-list))

(assert-equals true (element-of 3 test-list))
(assert-equals true (element-of 1 test-list))
(assert-equals false (element-of 4 test-list))

(assert-equals (rest (rest test-list)) (cons (ith test-list (- (length test-list) 1)) empty))

(assert-equals 1 (first (first (zip test-list (reverse test-list)))))
(assert-equals 3 (rest (first (zip test-list (reverse test-list)))))

(assert-equals test-list (append empty test-list))
(assert-equals test-list (append test-list empty))
(assert-equals 1 (first (append test-list (reverse test-list))))
(assert-equals (reverse test-list) (rest (rest (rest (append test-list (reverse test-list))))))

(define test-list-2 (join (cons test-list (cons test-list (cons test-list empty)))))

(assert-equals 9 (length test-list-2))
(assert-equals 1 (ith test-list-2 0))
(assert-equals 1 (ith test-list-2 3))
(assert-equals 3 (ith test-list-2 8))

(assert-equals 1 (ith test-list 0))
(assert-equals 2 (ith test-list 1))
(assert-equals 3 (ith test-list 2))
(assert-equals 3 (ith (reverse test-list) 0))
(assert-equals 2 (ith (reverse test-list) 1))
(assert-equals 1 (ith (reverse test-list) 2))

(assert-equals (cons 1 (cons 2 empty)) (take 2 test-list))

(assert-equals (cons 3 empty) (drop 2 test-list))

(assert-equals (cons 1 (cons 2 (cons 3 (cons 4 (cons 5 (cons 6 (cons 7 empty)))))))
	(sort (cons 5 (cons 2 (cons 1 (cons 4 (cons 6 (cons 7 (cons 3 empty))))))) <))

(assert-equals (cons 1 (cons 2 (cons 3 (cons 4 (cons 5 (cons 6 (cons 7 empty)))))))
	(merge-sort (cons 5 (cons 2 (cons 1 (cons 4 (cons 6 (cons 7 (cons 3 empty))))))) <))

(assert-equals (cons 1 1) (assoc 1 (list (cons 3 3) (cons 2 2) (cons 1 1))))

(assert-equals empty (range 10 10))
(assert-equals empty (range 11 10))
(assert-equals (list 1 2 3 4 5 6 7 8 9) (range 1 10))

(comment "maps")

(define my-map
	(cons (cons "a" 1)
	(cons (cons "b" 2)
	(cons (cons "c" 3) empty))))

(assert-equals true (map\has? my-map "a"))
(assert-equals true (map\has? my-map "b"))
(assert-equals true (map\has? my-map "c"))
(assert-equals false (map\has? my-map "d"))
(assert-equals 1 (map\get my-map "a"))
(assert-equals 2 (map\get my-map "b"))
(assert-equals 3 (map\get my-map "c"))
(assert-equals 5 (map\get (map\insert my-map "d" 5) "d"))
(assert-equals 4 (map\get (map\update my-map "b" 4) "b"))
(assert-equals false (map\has? (map\delete my-map "c") "c"))

(assert-equals (cons "a" (cons "b" (cons "c" empty))) (map\keys my-map))
(assert-equals (cons 1 (cons 2 (cons 3 empty))) (map\values my-map))

(comment "misc")

(define [sum xs] (foldl + 0 xs))

(define [dot-product v w]
	(sum
		(map (lambda [p] (* (first p) (rest p)))
			(zip v w))))

(assert-equals 15 (dot-product
	(cons 1 (cons 2 (cons 3 (cons 4 (cons 5 empty)))))
	(cons 1 (cons (- 0 2) (cons 3 (cons (- 0 4) (cons 5 empty)))))))

