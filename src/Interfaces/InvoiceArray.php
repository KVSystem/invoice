<?php

namespace Proengeno\Invoice\Interfaces;

use Proengeno\Invoice\Interfaces\Formatable;

interface InvoiceArray extends \Countable, \ArrayAccess, \IteratorAggregate, \JsonSerializable, Formatable
{
}
