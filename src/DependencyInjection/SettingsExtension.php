<?php


/*
 * Copyright (c) 2024 Jan Böhmer
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Jbtronics\SettingsBundle\DependencyInjection;

use Jbtronics\SettingsBundle\Migrations\SettingsMigrationInterface;
use Jbtronics\SettingsBundle\ParameterTypes\ParameterTypeInterface;
use Jbtronics\SettingsBundle\Storage\StorageAdapterInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class SettingsExtension extends Extension
{

    public const TAG_PARAMETER_TYPE = 'jbtronics.settings.parameter_type';
    public const TAG_STORAGE_ADAPTER = 'jbtronics.settings.storage_adapter';
    public const TAG_MIGRATION = 'jbtronics.settings.migration';


    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.php');

        $container->registerForAutoconfiguration(ParameterTypeInterface::class)
            ->addTag(self::TAG_PARAMETER_TYPE);

        $container->registerForAutoconfiguration(StorageAdapterInterface::class)
            ->addTag(self::TAG_STORAGE_ADAPTER);

        $container->registerForAutoconfiguration(SettingsMigrationInterface::class)
            ->addTag(self::TAG_MIGRATION);

        $container->setParameter('jbtronics.settings.proxyDir', $container->getParameter('kernel.cache_dir').'/jbtronics_settings/proxies');
        $container->setParameter('jbtronics.settings.proxyNamespace', 'Jbtronics\SettingsBundle\Proxy');
    }
}