<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReportRepository")
 */
class Report implements \JsonSerializable

{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $deviceID;

    /**
     * @ORM\Column(type="string", length=250)
     */

    private $err_desc;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id", onDelete="Cascade")
     */
    private $client;


    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getDeviceID()
    {
        return $this->deviceID;
    }

    public function setDeviceID($deviceID)
    {
        $this->deviceID = $deviceID;
    }

    public function getDescription()
    {
        return $this->err_desc;
    }

    public function setDescription($err_desc)
    {
        $this->err_desc = $err_desc;
    }

    public function jsonSerialize()
    {
        $props = get_object_vars($this);
        return $props;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }
}
