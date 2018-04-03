<?php

if (!($loader = @include __DIR__ . '/../vendor/autoload.php')) {
    echo 'You need to install the project dependencies using Composer';
    exit(1);
}
