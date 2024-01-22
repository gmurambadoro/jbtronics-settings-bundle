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

use Jbtronics\SettingsBundle\DependencyInjection\SettingsExtension;
use Jbtronics\SettingsBundle\Manager\SettingsHydrator;
use Jbtronics\SettingsBundle\Manager\SettingsHydratorInterface;
use Jbtronics\SettingsBundle\Manager\SettingsManager;
use Jbtronics\SettingsBundle\Manager\SettingsManagerInterface;
use Jbtronics\SettingsBundle\Manager\SettingsRegistry;
use Jbtronics\SettingsBundle\Manager\SettingsRegistryInterface;
use Jbtronics\SettingsBundle\Manager\SettingsResetter;
use Jbtronics\SettingsBundle\Manager\SettingsResetterInterface;
use Jbtronics\SettingsBundle\ParameterTypes\ParameterTypeInterface;
use Jbtronics\SettingsBundle\ParameterTypes\ParameterTypeRegistry;
use Jbtronics\SettingsBundle\ParameterTypes\ParameterTypeRegistryInterface;
use Jbtronics\SettingsBundle\Profiler\SettingsCollector;
use Jbtronics\SettingsBundle\Metadata\MetadataManager;
use Jbtronics\SettingsBundle\Metadata\MetadataManagerInterface;
use Jbtronics\SettingsBundle\Storage\StorageAdapterInterface;
use Jbtronics\SettingsBundle\Storage\StorageAdapterRegistry;
use Jbtronics\SettingsBundle\Storage\StorageAdapterRegistryInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_locator;

return static function (ContainerConfigurator $container) {
    $services = $container->services()
        ->defaults()->private()
        ->instanceof(ParameterTypeInterface::class)->tag(SettingsExtension::TAG_PARAMETER_TYPE)
        ->instanceof(StorageAdapterInterface::class)->tag(SettingsExtension::TAG_STORAGE_ADAPTER)
        ;


    $services->set('jbtronics.settings.settings_registry', SettingsRegistry::class)
        ->args([
            '$directories' => ['%kernel.project_dir%/src/Settings/'],
            '$cache' => service('cache.app'),
            '$debug_mode' => '%kernel.debug%',
        ])
        ->tag('kernel.cache_warmer')
    ;
    $services->alias(SettingsRegistryInterface::class, 'jbtronics.settings.settings_registry');

    $services->set('jbtronics.settings.metadata_manager', MetadataManager::class)
        ->args([
            '$cache' => service('cache.app'),
            '$debug_mode' => '%kernel.debug%',
            '$settingsRegistry' => service('jbtronics.settings.settings_registry'),
            '$parameterTypeGuesser' => service('jbtronics.settings.parameter_type_guesser'),
        ])
    ;
    $services->alias(MetadataManagerInterface::class, 'jbtronics.settings.metadata_manager');

    $services->set('jbtronics.settings.settings_manager', SettingsManager::class)
        ->args([
            '$metadataManager' => service('jbtronics.settings.metadata_manager'),
            '$settingsHydrator' => service('jbtronics.settings.settings_hydrator'),
            '$settingsResetter' => service('jbtronics.settings.settings_resetter'),
            '$settingsValidator' => service('jbtronics.settings.settings_validator'),
            '$settingsRegistry' => service('jbtronics.settings.settings_registry'),
        ])
        ;
    $services->alias(SettingsManagerInterface::class, 'jbtronics.settings.settings_manager');

    $services->set('jbtronics.settings.parameter_type_registry', ParameterTypeRegistry::class)
        ->args([
            '$locator' => tagged_locator(SettingsExtension::TAG_PARAMETER_TYPE)
        ])
        ;
    $services->alias(ParameterTypeRegistryInterface::class, 'jbtronics.settings.parameter_type_registry');

    $services->set('jbtronics.settings.storage_adapter_registry', StorageAdapterRegistry::class)
        ->args([
            '$locator' => tagged_locator(SettingsExtension::TAG_STORAGE_ADAPTER)
        ])
        ;
    $services->alias(StorageAdapterRegistryInterface::class, 'jbtronics.settings.storage_adapter_registry');

    $services->set('jbtronics.settings.settings_hydrator', SettingsHydrator::class)
        ->args([
            '$storageAdapterRegistry' => service('jbtronics.settings.storage_adapter_registry'),
            '$parameterTypeRegistry' => service('jbtronics.settings.parameter_type_registry'),
        ])
        ;
    $services->alias(SettingsHydratorInterface::class, 'jbtronics.settings.settings_hydrator');

    $services->set('jbtronics.settings.settings_resetter', SettingsResetter::class);
    $services->alias(SettingsResetterInterface::class, 'jbtronics.settings.settings_resetter');

    $services->set('jbtronics.settings.settings_validator', \Jbtronics\SettingsBundle\Manager\SettingsValidator::class)
        ->args([
            '$validator' => service('validator'),
        ]);
    $services->alias(\Jbtronics\SettingsBundle\Manager\SettingsValidatorInterface::class, 'jbtronics.settings.settings_validator');

    $services->set('jbtronics.settings.parameter_type_guesser', \Jbtronics\SettingsBundle\Metadata\ParameterTypeGuesser::class)
        ;
    $services->alias(\Jbtronics\SettingsBundle\Metadata\ParameterTypeGuesserInterface::class, 'jbtronics.settings.parameter_type_guesser');

    $services->set('jbtronics.settings.profiler_data_collector', SettingsCollector::class)
        ->tag('data_collector')
        ->args([
            '$configurationRegistry' => service('jbtronics.settings.settings_registry'),
            '$metadataManager' => service('jbtronics.settings.metadata_manager'),
            '$settingsManager' => service('jbtronics.settings.settings_manager'),
        ]);

    //Only register the twig extension if twig is available
    if (interface_exists(\Twig\Extension\ExtensionInterface::class)) {
        $services->set('jbtronics.settings.twig_extension', \Jbtronics\SettingsBundle\Twig\SettingsExtension::class)
            ->tag('twig.extension')
            ->args([
                '$settingsManager' => service('jbtronics.settings.settings_manager'),
            ]);
    }

    $services->set('jbtronics.settings.env_processor', \Jbtronics\SettingsBundle\DependencyInjection\SettingsEnvProcessor::class)
        ->tag('container.env_var_processor')
        ->args([
            '$settingsManager' => service('jbtronics.settings.settings_manager'),
            '$metadataManager' => service('jbtronics.settings.metadata_manager'),
        ]);

    /*********************************************************************************
     * Form subsystem
     *********************************************************************************/

    $services->set('jbtronics.settings.settings_form_builder', \Jbtronics\SettingsBundle\Form\SettingsFormBuilder::class)
        ->args([
            '$parameterTypeRegistry' => service('jbtronics.settings.parameter_type_registry'),
        ]);
    $services->alias(\Jbtronics\SettingsBundle\Form\SettingsFormBuilderInterface::class, 'jbtronics.settings.settings_form_builder');

    $services->set('jbtronics.settings.settings_form_factory', \Jbtronics\SettingsBundle\Form\SettingsFormFactory::class)
        ->args([
            '$settingsManager' => service('jbtronics.settings.settings_manager'),
            '$metadataManager' => service('jbtronics.settings.metadata_manager'),
            '$settingsFormBuilder' => service('jbtronics.settings.settings_form_builder'),
            '$formFactory' => service('form.factory'),
        ]);
    $services->alias(\Jbtronics\SettingsBundle\Form\SettingsFormFactoryInterface::class, 'jbtronics.settings.settings_form_factory');

    /**********************************************************************************
     * Parameter Types
     **********************************************************************************/
    $services->set(\Jbtronics\SettingsBundle\ParameterTypes\IntType::class);
    $services->set(\Jbtronics\SettingsBundle\ParameterTypes\StringType::class);
    $services->set(\Jbtronics\SettingsBundle\ParameterTypes\BoolType::class);
    $services->set(\Jbtronics\SettingsBundle\ParameterTypes\FloatType::class);
    $services->set(\Jbtronics\SettingsBundle\ParameterTypes\EnumType::class);

    /**********************************************************************************
     * Storage Adapters
     **********************************************************************************/
    $services->set(\Jbtronics\SettingsBundle\Storage\InMemoryStorageAdapter::class);
    $services->set(\Jbtronics\SettingsBundle\Storage\JSONFileStorageAdapter::class)
        ->args([
            '$storageDirectory' => '%kernel.project_dir%/var/jbtronics_settings/',
        ]);
};