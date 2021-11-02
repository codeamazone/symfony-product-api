<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A product
 * 
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
#[ApiResource]
class Product
{
    /**
     * The ID is autogenerated
     * 
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * The name of the product
     * 
     * @ORM\Column(type="string", length=50)
     */
    #[Assert\NotBlank]
    private $name;

    /**
     * The product's GTIN (14 digits), or null if the product doesn't have one
     * 
     * @ORM\Column(type="string", length=14, nullable=true)
     */
    #[Assert\Length(
        min: 14,
        max: 14,
        exactMessage: 'The GTIN should consist of exactly {{ limit }} digits.',
        )]
    #[Assert\Regex(
        pattern: '/[0-9]+/',
        message: 'The GTIN should only consist of digits.'
    )]
    private $gtin;

    /**
     * Detailed description of the product
     * 
     * @ORM\Column(type="text")
     */
    #[Assert\NotBlank]
    private $description;

    /**
     * The color of the product
     * 
     * @ORM\Column(type="string", length=50)
     */
    #[Assert\NotBlank]
    private $color;

    /**
     * The price of the product
     * 
     * @ORM\Column(type="float")
     */
    #[Assert\NotBlank]
    private $price;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getGtin(): ?string
    {
        return $this->gtin;
    }

    public function setGtin(?string $gtin): self
    {
        $this->gtin = $gtin;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }
}
