<?php

namespace Hr\ApiBundle\Entity;

use App\Entity\Memo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Hr\ApiBundle\Repository\UserOrganizerRepository")
 * @ORM\Table(name="authUserOrganizer")
 */
class UserOrganizer
{
    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Memo", mappedBy="owner")
     */
    private $memos;

    public function __construct()
    {
        $this->memos = new ArrayCollection();
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function unsetUser(): self
    {
        $this->user = $this->user->getId();
        return $this;
    }

    /**
     * @return Collection|Memo[]
     */
    public function getMemos(): Collection
    {
        return $this->memos;
    }

    public function addMemo(Memo $memo): self
    {
        if (!$this->memos->contains($memo)) {
            $this->memos[] = $memo;
            $memo->addOwner($this);
        }

        return $this;
    }

    public function removeMemo(Memo $memo): self
    {
        if ($this->memos->contains($memo)) {
            $this->memos->removeElement($memo);
            $memo->removeOwner($this);
        }

        return $this;
    }
}