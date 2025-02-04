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

namespace Jbtronics\SettingsBundle\Settings;

use Jbtronics\SettingsBundle\Metadata\EnvVarMode;
use Jbtronics\SettingsBundle\ParameterTypes\ParameterTypeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * This attribute marks a property inside a settings class as a settings entry of this class.
 * The value is mapped by the configured type and stored in the storage provider.
 * The attributes define various metadata for the configuration entry like a user friendly label, description and
 * other properties.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class SettingsParameter
{
    public readonly \Closure|string|null $envVarMapper;

    /**
     * @param  string|null  $type  The type of this configuration entry. This must be a class string of a service implementing ParameterTypeInterface. If it is not set, the type is guessed from the property type.
     * @phpstan-param class-string<ParameterTypeInterface> $type
     * @param  string|null  $name  The optional name of this configuration entry. If not set, the name of the property is used.
     * @param  string|TranslatableInterface|null  $label  A user-friendly label for this configuration entry, which is shown in the UI.
     * @param  string|TranslatableInterface|null  $description  A small description for this configuration entry, which is shown in the UI.
     * @param  array  $options  An array of extra options, which can detail configure the behavior of the ParameterType.
     * @param  string|null  $formType  The form type to use for this configuration entry. If not set, the form type is guessed from the parameter type.
     * @phpstan-param class-string<AbstractType>|null $formType
     * @param  array  $formOptions  An array of extra options, which are passed to the form type.
     * @param  bool|null  $nullable  Whether the value of the property can be null. If not set, the value is guessed from the property type.
     * @param  string[]|null  $groups  The groups, which this parameter should belong to. Groups can be used to only render subsets of the configuration entries in the UI. If not set, the parameter is assigned to the default group set in the settings class.
     * @param  string|null  $envVar  The name of the environment variable, which should be used to fill this parameter. If not set, the parameter is not filled by an environment variable.
     * @param  EnvVarMode  $envVarMode  The mode in which the environment variable should be used to fill the parameter. Defaults to EnvVarMode::INITIAL
     * @param  callable|string|null  $envVarMapper  A mapper, which is used to map the value from the environment variable to the parameter value. It can be either a ParameterTypeInterface service, or a callable, which takes the value from the environment variable as argument and returns the mapped value.
     * @phpstan-param callable(mixed): mixed|class-string<ParameterTypeInterface>|null $envVarMapper
     */
    public function __construct(
        public readonly ?string $type = null,
        public readonly ?string $name = null,
        public readonly string|TranslatableInterface|null $label = null,
        public readonly string|TranslatableInterface|null $description = null,
        public readonly array $options = [],
        public readonly ?string $formType = null,
        public readonly array $formOptions = [],
        public readonly ?bool $nullable = null,
        public readonly ?array $groups = null,
        public readonly ?string $envVar = null,
        public readonly EnvVarMode $envVarMode = EnvVarMode::INITIAL,
        callable|string|null $envVarMapper = null,
    ) {
        if (is_callable($envVarMapper)) {
            $envVarMapper = $envVarMapper(...);
        }

        $this->envVarMapper = $envVarMapper;
    }


}