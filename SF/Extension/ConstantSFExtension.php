<?php

namespace ITE\Js\Constant\SF\Extension;

use ITE\JsBundle\SF\SFExtension;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class ConstantSFExtension
 *
 * @author sam0delkin <t.samodelkin@gmail.com>
 */
class ConstantSFExtension extends SFExtension
{
    /**
     * @var array $constants
     */
    private $constants;

    /**
     * ConstantSFExtension constructor.
     *
     * @param array $constants
     */
    public function __construct($constants = null)
    {
        $this->constants = $constants ?: [];
    }

    /**
     * @inheritdoc
     */
    public function getConfiguration(ContainerBuilder $container)
    {
        $node = new TreeBuilder();
        $node = $node->root('constant');
        $node
            ->canBeEnabled()
            ->children()
            ->arrayNode('constants')
            ->canBeUnset()
            ->prototype('array')
            ->children()
            ->scalarNode('name')
            ->end()
            ->scalarNode('value')
            ->end()
            ->scalarNode('class')
            ->end()
            ->scalarNode('alias')
            ->end()
            ->end()
            ->end()
            ->end()
            ->end();

        return $node;
    }

    /**
     * @inheritDoc
     */
    public function dump()
    {
        if (empty($this->constants)) {
            return '';
        }

        $js = '(function($){$(function(){';
        foreach ($this->constants as $constant) {
            $js .= sprintf('SF.constants.addConstant(%s);', json_encode($constant));
        }
        $js .= '});})(jQuery);';

        return $js;
    }

    /**
     * @inheritdoc
     */
    public function loadConfiguration(array $config, ContainerBuilder $container)
    {
        if ($config['extensions']['constant']['enabled']) {
            $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../Resources/config'));
            $loader->load('sf.constant.yml');

            $constants = [];

            foreach ($config['extensions']['constant']['constants'] as $constant) {
                if (isset($constant['name'])) {
                    if (!isset($constant['value'])) {
                        throw new \Exception('Named constant should have a value.');
                    }
                    if (isset($constant['class']) || isset($constant['alias'])) {
                        throw new \Exception('You should either use named or class constants.');
                    }
                    $constants[] = [
                        'name' => $constant['name'],
                        'value' => $constant['value'],
                        'alias' => null,
                    ];
                } elseif (isset($constant['class'])) {
                    $refl = new \ReflectionClass($constant['class']);
                    foreach ($refl->getConstants() as $name => $value) {
                        $constants[] = [
                            'alias' => isset($constant['alias']) ? $constant['alias'] : null,
                            'name' => $name,
                            'value' => $value
                        ];
                    }

                }
            }

            $container->setParameter('ite_js.constant.constants', $constants);
            $this->constants = $constants;
        }
    }

    /**
     * @inheritdoc
     */
    public function getJavascripts()
    {
        return [__DIR__ . '/../../Resources/public/js/sf.constant.js'];
    }
}
