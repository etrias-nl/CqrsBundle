<?php

declare(strict_types=1);

namespace Etrias\CqrsBundle\ExpressionLanguage;

use Symfony\Component\DependencyInjection\ExpressionLanguageProvider;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage as BaseExpressionLanguage;

class ExpressionLanguage extends BaseExpressionLanguage
{
    public function __construct()
    {
        parent::__construct(null, [
            new ExpressionLanguageProvider(),
            new class implements ExpressionFunctionProviderInterface {
                public function getFunctions(): array
                {
                    return [
                        new ExpressionFunction('is_granted', static function ($attribute, $object = null) {
                            return sprintf('call_user_func_array(array($this->get(\'security.authorization_checker\'), \'isGranted\'), array(%s, %s))', $attribute, $object);
                        }, static function (array $variables, $attribute, $object = null) {
                            return call_user_func_array(
                                [$variables['container']->get('security.authorization_checker'), 'isGranted'],
                                [$attribute, $object]
                            );
                        }),
                    ];
                }
            },
        ]);
    }
}
