<?php
namespace GoetasWebservices\WsdlToPhp\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class Wsdl2PhpExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $xml = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $xml->load('services.xml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        foreach ($configs as $subConfig) {
            $config = array_merge($config, $subConfig);
        }
        $container->setParameter('wsdl2php.config', $config);

        $writer = $container->getDefinition('goetas.wsdl2php.metadata.generator');
        $writer->replaceArgument(3, $config);

        $container->setParameter('generate_metadata', (bool)$config['wsdl_metadata_destination']);

        $writer = $container->getDefinition('goetas.wsdl2php.metadata.writer');
        $writer->replaceArgument(0, $config['wsdl_metadata_destination']);
    }

    protected static function sanitizePhp($ns)
    {
        return strtr($ns, '/', '\\');
    }

    public function getAlias()
    {
        return 'wsdl2php';
    }
}
