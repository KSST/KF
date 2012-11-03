<?php
namespace KochTest\DI;

/** wrapWith */
interface Bare { }
class BareImplementation implements Bare { }
class WrapperForBare {
    function __construct(Bare $bare) { $this->bare = $bare; }
}
