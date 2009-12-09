<?php

class C
{
    public function f($a, $b, $c)
    {
        $b = (int) $b;
        $c = htmlspecialchars($c);
        return $a . $b . $c;
    }

    public function f($a, $b, $c)
    {
        return $a . $b . $c;
    }
}

$x = 'const';
$y = $_GET['y'];
$z = $_POST['z'];

$o = new C;
$r = $o->f($x, $y, $z);
$s = $o->g($x, $y, $z);

echo $x; // safe! const string
echo $y; // unsafe! unescaped user input!

echo $r; // safe, result of f() is escaped since $x is constant.
echo $s; // unsafe, g() does not escape!
