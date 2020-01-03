<?php

declare(strict_types=1);


namespace Etrias\CqrsBundle\Cache;


use Etrias\CqrsBundle\Command\QueryInterface;
use Exception;
use League\Tactician\Middleware;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Serializer\Exception\UnsupportedException;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;
use Symfony\Component\Serializer\SerializerInterface;

class CacheMiddleware implements Middleware, SerializerAwareInterface
{
    use SerializerAwareTrait;

    /** string */
    const SEPARATOR = "::";

    /**
     * @var TagAwareAdapterInterface
     */
    protected $cache;

    /**
     * @var CacheConfigRegistry
     */
    protected $cacheConfigRegistry;

    /**
     * @var ExpressionLanguage
     */
    protected $expressionLanguage;
    /**
     * @var ContainerInterface
     */
    protected $container;

    /** @var  string */
    protected $encoding;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * CacheMiddleware constructor.
     * @param TagAwareAdapterInterface $cache
     * @param CacheConfigRegistry $cacheConfigRegistry
     * @param string $encoding
     * @param ExpressionLanguage $expressionLanguage
     * @param LoggerInterface $logger
     * @param ContainerInterface $container
     */
    public function __construct(
        TagAwareAdapterInterface $cache,
        CacheConfigRegistry $cacheConfigRegistry,
        string $encoding,
        ExpressionLanguage $expressionLanguage,
        LoggerInterface $logger,
        ContainerInterface $container
    )
    {
        $this->cache = $cache;
        $this->cacheConfigRegistry = $cacheConfigRegistry;
        $this->expressionLanguage = $expressionLanguage;
        $this->container = $container;
        $this->logger = $logger;
        $this->encoding = $encoding;
    }

    public function setSerializer(SerializerInterface $serializer)
    {
        if (!$serializer->supportsEncoding($this->encoding)) {
            throw new UnsupportedException('Not supported encoding: '. $encoding);
        }

        $this->serializer = $serializer;
    }

    /**
     * @param object $command
     * @param callable $next
     *
     * @return mixed
     */
    public function execute($command, callable $next)
    {
        if (!($command instanceof QueryInterface)) {
            return $next($command);
        }

        $cacheConfig = $this->cacheConfigRegistry->getConfig(get_class($command));

        $cacheName = $cacheConfig->getNameStrategy()->getName($command);
        $item = $this->cache->getItem($cacheName);

        if ($item->isHit()) {
            try {
                return $this->serializer->decode($item->get(), $this->encoding);
            } catch (Exception $exception){
                $this->logger->critical('Cannot unserialize cache: '. $cacheName);
            }
        }

        $returnValue = $next($command);

        $item->set($this->serializer->encode($returnValue, $this->encoding));

        if ($cacheConfig->getExpiresAfter()) {
            $item->expiresAfter($cacheConfig->getExpiresAfter());
        } else if ($cacheConfig->getExpiresAt()) {
            $item->expiresAt($cacheConfig->getExpiresAt());
        } else {
            $item->expiresAt(null);
        }

        foreach ($cacheConfig->getTagExpressions() as $tagExpression) {
            $tag = $this->expressionLanguage->evaluate(
                $tagExpression,
                [
                    'command' => $command,
                    'container' => $this->container,
                    'result' => $returnValue
                ]
            );
            if ($tag !== null) {
                $item->tag($tag);
            }
        }

        $this->cache->save($item);


        return $returnValue;
    }
}
