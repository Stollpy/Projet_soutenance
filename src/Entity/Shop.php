<?php

namespace App\Entity;

use App\Repository\ShopRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ShopRepository::class)
 */
class Shop
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $picture;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $logo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $SIRET;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="shop", orphanRemoval=true)
     */
    private $products;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="shop", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="Bookmarks")
     */
    private $users;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $shop_address;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $shop_city;

    /**
     * @ORM\Column(type="string", length=6)
     */
    private $shop_zipcode;

    /**
     * @ORM\OneToMany(targetEntity=OrderLine::class, mappedBy="shop")
     */
    private $orderLines;


    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->orderLines = new ArrayCollection();
    }

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

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getSIRET(): ?string
    {
        return $this->SIRET;
    }

    public function setSIRET(string $SIRET): self
    {
        $this->SIRET = $SIRET;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setShop($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getShop() === $this) {
                $product->setShop(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addBookmark($this);
        }
        return $this;
    } 
      
    public function getShopAddress(): ?string
    {
        return $this->shop_address;
    }

    public function setShopAddress(string $shop_address): self
    {
        $this->shop_address = $shop_address;
        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeBookmark($this);
        }
        return $this;
    }

    public function getShopCity(): ?string
    {
        return $this->shop_city;
    }

    public function setShopCity(string $shop_city): self
    {
        $this->shop_city = $shop_city;
        return $this;
    }

    public function getShopZipcode(): ?string
    {
        return $this->shop_zipcode;
    }

    public function setShopZipcode(string $shop_zipcode): self
    {
        $this->shop_zipcode = $shop_zipcode;
        return $this;
    }

    /**
     * @return Collection|OrderLine[]
     */
    public function getOrderLines(): Collection
    {
        return $this->orderLines;
    }

    public function addOrderLine(OrderLine $orderLine): self
    {
        if (!$this->orderLines->contains($orderLine)) {
            $this->orderLines[] = $orderLine;
            $orderLine->setShop($this);
        }

        return $this;
    }

    public function removeOrderLine(OrderLine $orderLine): self
    {
        if ($this->orderLines->removeElement($orderLine)) {
            // set the owning side to null (unless already changed)
            if ($orderLine->getShop() === $this) {
                $orderLine->setShop(null);
            }
        }

        return $this;
    }
}
