<?php
namespace KochTest\DI;
class NotWithoutMe
{
}

class NeedsInitToCompleteConstruction
{
    function init(NotWithoutMe $me)
    {
        $this->me = $me;
    }
}
