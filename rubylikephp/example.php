<?php
/**
* Just few examples of using EDO and RubyLikePHP class
*
* author zbynek.petr@gmail.com
*/ 

require_once './rubylikephp.php';

// creates a new EDO object with string data type and value
edo(" "); 

// read file rubylikephp.php like text and get all lines containing "function" into an array
$my_array = edo(" ")->from_f("rubylikephp.php")->lines()->find('function ')->v();


edo(array(5, 12, 3, 9, 74, 2, 15))->map('&$x', 'return $x + 100;')->push(array(999 => 99999))->p();

edo("test string")->i(2, null)->p();
edo( array('a', 'b', 'c', 'd', 'e'))->i(2, 3)->p();

// this is the same as next line - just prints the debug content of the variable
$my_string = edo("MY_STRING")->grep('xING');

// now we store EDO object
$my_edo_object = edo("STRING");
$d = $my_edo_object->downcase()->replace('RIN', '-')->p();


// working with date and time
$d = edo("2008-12-31 00:00:22");
echo $d->add("+3 DAY")->to_s("d.m.Y H:i");

$d = edo("2008-12-31")->to_s();


// working with the string
$conf_string = "sdf sdgfdfg sdfg

fdg sdgf sdfg sdg


sdfgsdfgsdfgsdfgsdgf

sdfgsdg sd fgsdf gsd";

edo($conf_string)->lines()->filter()->join("\n- ")->p();


// read some config
// split it to lines then filter empty items, then filter not commented lines
$x = edo( "#config FILE

key = value
key2 = value2

# this is line comment
")->lines()->filter()->filter('$x', 'if ($x[0] != \'#\') return $x;')->v();

// now split to array key => value
foreach ($x as $k => $val) {
	$x[$k] = edo($val)->split("=")->trim()->v();
}

edo($x)->p();

// another tests
edo( array('a' => 1, 'b' => 5, 'c' => 4, 6, 2) )->keys()->grep('c');

$my_str = edo( array('a' => 1, 'b' => 5, 'c' => 4, 6, 2) )->keys()->join(';')->to_s();

echo $my_str;
edo($my_str)->split(';')->p();

$my_string = edo("this is the [text] which I want")->re(".*(\[[a-z]+\]).*")->i(1)->v();
echo '<pre>';
var_export($my_string);
echo '</pre>';

// sum of the array
echo edo(array(2, 3, 1))->sum();

// add metohd
edo( "abc" )->add("cde")->p();
edo( array('a', 1, 'g') )->add('xy')->p();
echo edo("2011-11-04")->add("-1 WEEK")->to_s("d");

// and more and more
// HAVE A FUN :-) ZP

?>
