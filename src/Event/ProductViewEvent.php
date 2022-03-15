<?php

namespace App\Event;

use App\Entity\Product;
use Symfony\Contracts\EventDispatcher\Event;

class ProductViewEvent extends Event
{

    private $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    //pour rendre le produit accessible
    public function getProduct(): Product
    {
        return $this->product;
    }
}
