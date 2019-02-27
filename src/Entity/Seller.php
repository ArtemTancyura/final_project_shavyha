<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SellerRepository")
 */
class Seller extends User
{

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $station;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $products;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $orders;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $corporation;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getStation(): ?string
    {
        return $this->station;
    }

    public function setStation(string $station): self
    {
        $this->station = $station;

        return $this;
    }

    public function getProducts(): ?string
    {
        return $this->products;
    }

    public function setProducts(string $products): self
    {
        $this->products = $products;

        return $this;
    }

    public function getOrders(): ?string
    {
        return $this->orders;
    }

    public function setOrders(?string $orders): self
    {
        $this->orders = $orders;

        return $this;
    }

    public function getCorporation(): ?string
    {
        return $this->corporation;
    }

    public function setCorporation(?string $corporation): self
    {
        $this->corporation = $corporation;

        return $this;
    }
}
