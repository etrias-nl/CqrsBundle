<?php

declare(strict_types=1);


namespace Etrias\CqrsBundle\Cache;


use DateTimeInterface;
use Etrias\CqrsBundle\Cache\NameStrategy\NameStrategyInterface;

class CacheConfig
{
    /**
     * @var integer|null
     */
    protected $expiresAfter;

    /**
     * @var DateTimeInterface|null
     */
    protected $expiresAt;

    /**
     * @var NameStrategyInterface
     */
    protected $nameStrategy;

    /**
     * @var string[]
     */
    protected $tagExpressions = [];

    public function __construct(NameStrategyInterface $nameStrategy)
    {

        $this->nameStrategy = $nameStrategy;
    }

    /**
     * @return int|null
     */
    public function getExpiresAfter()
    {
        return $this->expiresAfter;
    }

    /**
     * @param int|null $expiresAfter
     * @return CacheConfig
     */
    public function setExpiresAfter(?int $expiresAfter): CacheConfig
    {
        $this->expiresAfter = $expiresAfter;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * @param DateTimeInterface|null $expiresAt
     * @return CacheConfig
     */
    public function setExpiresAt(?DateTimeInterface $expiresAt): CacheConfig
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    /**
     * @return NameStrategyInterface
     */
    public function getNameStrategy(): NameStrategyInterface
    {
        return $this->nameStrategy;
    }

    /**
     * @param NameStrategyInterface $nameStrategy
     * @return CacheConfig
     */
    public function setNameStrategy(NameStrategyInterface $nameStrategy): CacheConfig
    {
        $this->nameStrategy = $nameStrategy;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getTagExpressions(): array
    {
        return $this->tagExpressions;
    }

    /**
     * @param string[] $tagExpressions
     * @return CacheConfig
     */
    public function setTagExpressions(array $tagExpressions): CacheConfig
    {
        $this->tagExpressions = $tagExpressions;

        return $this;
    }




}
