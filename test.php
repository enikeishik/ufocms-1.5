<?php
abstract class A
{
    public function aa()
    {
        echo 'A::aa';
    }
}
class B extends A
{
    public function aa()
    {
        echo 'B::aa';
    }
}

function test(A $x)
{
    $x->aa();
}
test(new B());
