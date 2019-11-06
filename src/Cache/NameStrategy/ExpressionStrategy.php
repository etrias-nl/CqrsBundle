<?php

declare(strict_types=1);


namespace Etrias\CqrsBundle\Cache\NameStrategy;

use Etrias\CqrsBundle\Command\QueryInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ExpressionStrategy implements NameStrategyInterface
{
    /**
     * @var string
     */
    protected $separator = '_';

    /**
     * @var string
     */
    protected $expression;

    /** @var ExpressionLanguage */
    protected $expressionLanguage;

    /**
     * ExpressionStrategy constructor.
     * @param string $expression
     */
    public function __construct(string $expression)
    {

        $this->expression = $expression;
        $this->expressionLanguage = new ExpressionLanguage();
    }

    /**
     * @param QueryInterface $command
     * @return string
     */
    public function getName(QueryInterface $command): string
    {
        $value = $this->expressionLanguage->evaluate($this->expression, ['command' => $command]);

        return $value;
    }
}
