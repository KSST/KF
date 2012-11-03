<?php
namespace KochTest\DI;
class NotWithoutMe
{
}

class NeedsInitToCompleteConstruction
{
    public function init(NotWithoutMe $me)
    {
        $this->me = $me;
    }
}
