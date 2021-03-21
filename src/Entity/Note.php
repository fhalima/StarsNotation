<?php

namespace App\Entity;

use App\Repository\NoteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NoteRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Note
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $value;

    /**
     * @ORM\Column(type="text")
     */
    private $comment;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateregister;


    /**
     * Méthode appelée avant le 1er enregistrement de l'entité
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        // Définir la date de création par défaut
        if ($this->dateregister === null) {
            $this->dateregister = new \DateTime();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getDateregister(): ?\DateTimeInterface
    {
        return $this->dateregister;
    }

    public function setDateregister(\DateTimeInterface $dateregister): self
    {
        $this->dateregister = $dateregister;

        return $this;
    }
}
