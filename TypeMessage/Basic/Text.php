<?php
namespace Sopinet\ChatBundle\TypeMessage\Basic;

use Sopinet\ChatBundle\TypeMessage\BasicTypeMessageAbstract;

class Text extends BasicTypeMessageAbstract
{
    public function addMessage() {
        return "text";
    }
}