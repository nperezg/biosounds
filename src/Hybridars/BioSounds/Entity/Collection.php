<?php

namespace Hybridars\BioSounds\Entity;

class Collection
{
	
	const TABLE_NAME = "collection";
	const PRIMARY_KEY = "collection_id";
	const NAME = "name";
	const GALLERY_VIEW = 'gallery';
	const LIST_VIEW = 'list';

    /**
     * @var int
     */
	private $id;

    /**
     * @var string
     */
	private $name;

    /**
     * @var string
     */
	private $author;

    /**
     * @var string
     */
	private $source;

    /**
     * @var string
     */
	private $citation;

    /**
     * @var string
     */
	private $url;

    /**
     * @var string
     */
	private $note;

    /**
     * @var string
     */
	private $view = self::GALLERY_VIEW;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Collection
     */
    public function setId(int $id): Collection
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Collection
     */
    public function setName(string $name): Collection
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @param string $author
     * @return Collection
     */
    public function setAuthor(string $author): Collection
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @param string $source
     * @return Collection
     */
    public function setSource(string $source): Collection
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getCitation(): ?string
    {
        return $this->citation;
    }

    /**
     * @param null|string $citation
     * @return Collection
     */
    public function setCitation(?string $citation): Collection
    {
        $this->citation = $citation;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param null|string $url
     * @return Collection
     */
    public function setUrl(?string $url): Collection
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * @param null|string $note
     * @return Collection
     */
    public function setNote(?string $note): Collection
    {
        $this->note = $note;
        return $this;
    }

    /**
     * @return string
     */
    public function getView(): string
    {
        return $this->view;
    }

    /**
     * @param string $view
     * @return Collection
     */
    public function setView(string $view): Collection
    {
        $this->view = $view;
        return $this;
    }
}
