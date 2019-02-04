<?php

namespace CequensBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CequensBundle extends Bundle
{
    const applicationTypes = [
        1 => 'Webhook',
        2 => 'Incoming Call',
        3 => 'Incoming SMS',
    ];
}
