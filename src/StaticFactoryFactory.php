<?php

/**
 * This file is part of coisa/factory.
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * @link      https://github.com/coisa/factory
 * @copyright Copyright (c) 2020 Felipe Sayão Lobato Abreu <github@felipeabreu.com.br>
 * @license   https://opensource.org/licenses/MIT MIT License
 */

namespace CoiSA\Factory;

/**
 * Class StaticFactoryFactory
 *
 * @package CoiSA\Factory
 */
final class StaticFactoryFactory implements FactoryInterface
{
    /**
     * @var string
     */
    private $staticFactory;

    /**
     * StaticFactoryFactory constructor.
     *
     * @param string $staticFactory
     *
     * @throws \UnexpectedValueException
     */
    public function __construct($staticFactory)
    {
        if (false === \class_exists($staticFactory)) {
            throw new \InvalidArgumentException(\sprintf('Class "%s" not found!', $staticFactory));
        }

        $implements = \class_implements($staticFactory);

        if (false === \in_array('CoiSA\\Factory\\StaticFactoryInterface', $implements)) {
            throw new \UnexpectedValueException(\sprintf(
                'Class "%s" should implement %s',
                $staticFactory,
                'CoiSA\\Factory\\StaticFactoryInterface'
            ));
        }

        $this->staticFactory = $staticFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function create()
    {
        $callable  = array($this->staticFactory, 'create');
        $arguments = \func_get_args();

        return \call_user_func_array($callable, $arguments);
    }
}
