<?php
class A
{
    public function aa()
    {
        echo 'A::aa' . "\r\n";
    }
    
    public function call(A $x)
    {
        
    }
    
    public function Acall(A $x)
    {
        echo 'A::Acall' . "\r\n";
    }
}
class B extends A
{
    public function aa()
    {
        echo 'B::aa' . "\r\n";
    }
    
    public function call(B $x)
    {
        
    }
    
    public function Bcall(B $x)
    {
        echo 'B::Bcall' . "\r\n";
    }
}

function test(A $x)
{
    $x->aa();
}
function test2(B $x)
{
    $x->aa();
}

echo 'test(new B()); ';
test(new B());
//test2(new A()); error

$s = 'A';
echo '$s = \'A\'; test(new $s()); ';
test(new $s());

$s = 'B';
echo '$s = \'B\'; test(new $s()); ';
test(new $s());
echo '$s = \'B\'; test2(new $s()); ';
test2(new $s());

$s = 'B';
$o = new $s();
$o->Bcall(new $s());
