<?php
namespace Sopinet\ChatBundle\TypeMessage\Basic;

use Sopinet\ChatBundle\TypeMessage\BasicTypeMessageAbstract;

class Image extends BasicTypeMessageAbstract
{
    public function addMessage() {
        return "image";
    }
}