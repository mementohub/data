<?php

namespace Mementohub\Data;

use Illuminate\Contracts\Support\Arrayable;
use Mementohub\Data\Traits\Cloneable;
use Mementohub\Data\Traits\Normalizable;
use Mementohub\Data\Traits\Parsable;
use Mementohub\Data\Traits\Transformable;

abstract class Data implements Arrayable
{
    use Cloneable;
    use Normalizable;
    use Parsable;
    use Transformable;
}
