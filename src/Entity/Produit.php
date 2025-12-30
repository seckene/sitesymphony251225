<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $descrptionlong = null;

    #[ORM\Column]
    private ?float $prix = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $matiere = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $coupe = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $longueur = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $entretien = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: ProduitImage::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $produitImages;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: ProduitVariant::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $variants;

    public function __construct()
    {
        $this->produitImages = new ArrayCollection();
        $this->variants = new ArrayCollection();
    }

    // ================= GETTERS / SETTERS =================

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getDescrptionlong(): ?string
    {
        return $this->descrptionlong;
    }

    public function setDescrptionlong(?string $descrptionlong): self
    {
        $this->descrptionlong = $descrptionlong;
        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;
        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getMatiere(): ?string
    {
        return $this->matiere;
    }

    public function setMatiere(?string $matiere): self
    {
        $this->matiere = $matiere;
        return $this;
    }

    public function getCoupe(): ?string
    {
        return $this->coupe;
    }

    public function setCoupe(?string $coupe): self
    {
        $this->coupe = $coupe;
        return $this;
    }

    public function getLongueur(): ?string
    {
        return $this->longueur;
    }

    public function setLongueur(?string $longueur): self
    {
        $this->longueur = $longueur;
        return $this;
    }

    public function getEntretien(): ?string
    {
        return $this->entretien;
    }

    public function setEntretien(?string $entretien): self
    {
        $this->entretien = $entretien;
        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;
        return $this;
    }

    /** @return Collection<int, ProduitImage> */
    public function getProduitImages(): Collection
    {
        return $this->produitImages;
    }

    public function addProduitImage(ProduitImage $produitImage): self
    {
        if (!$this->produitImages->contains($produitImage)) {
            $this->produitImages->add($produitImage);
            $produitImage->setProduit($this);
        }
        return $this;
    }

    public function removeProduitImage(ProduitImage $produitImage): self
    {
        if ($this->produitImages->removeElement($produitImage)) {
            if ($produitImage->getProduit() === $this) {
                $produitImage->setProduit(null);
            }
        }
        return $this;
    }

    /** @return Collection<int, ProduitVariant> */
    public function getVariants(): Collection
    {
        return $this->variants;
    }

    public function addVariant(ProduitVariant $variant): self
    {
        if (!$this->variants->contains($variant)) {
            $this->variants->add($variant);
            $variant->setProduit($this);
        }
        return $this;
    }

    public function removeVariant(ProduitVariant $variant): self
    {
        if ($this->variants->removeElement($variant)) {
            if ($variant->getProduit() === $this) {
                $variant->setProduit(null);
            }
        }
        return $this;
    }
}
