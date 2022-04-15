<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\Column(type: 'string', length: 255)]
    private $description;

    #[ORM\Column(type: 'date')]
    private $creationDate;

    #[ORM\Column(type: 'date')]
    private $updateDate;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $active;

    #[ORM\Column(type: 'string', nullable: true)]
    private $note;

    #[ORM\ManyToMany(targetEntity: Type::class, cascade: ['persist'])]
    private $type;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'user')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    private $user;

    #[ORM\ManyToMany(targetEntity: user::class, inversedBy: 'articles')]
    private $love;


    public function __construct()
    {
        $this->type = new ArrayCollection();
        $this->love = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getUpdateDate(): ?\DateTimeInterface
    {
        return $this->updateDate;
    }

    public function setUpdateDate(\DateTimeInterface $updateDate): self
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection<int, Type>
     */
    public function getType(): Collection
    {
        return $this->type;
    }

    public function addType(Type $type): self
    {
        if (!$this->type->contains($type)) {
            $this->type[] = $type;
        }

        return $this;
    }

    public function removeType(Type $type): self
    {
        $this->type->removeElement($type);

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, ArticleType>
     */
    public function getArticleTypes(): Collection
    {
        return $this->articleTypes;
    }

    public function addArticleType(ArticleType $articleType): self
    {
        if (!$this->articleTypes->contains($articleType)) {
            $this->articleTypes[] = $articleType;
            $articleType->addArticleId($this);
        }

        return $this;
    }

    public function removeArticleType(ArticleType $articleType): self
    {
        if ($this->articleTypes->removeElement($articleType)) {
            $articleType->removeArticleId($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, user>
     */
    public function getLove(): Collection
    {
        return $this->love;
    }

    public function addLove(user $love): self
    {
        if (!$this->love->contains($love)) {
            $this->love[] = $love;
        }

        return $this;
    }

    public function removeLove(user $love): self
    {
        $this->love->removeElement($love);

        return $this;
    }
}
