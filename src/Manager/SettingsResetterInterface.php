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

namespace Jbtronics\SettingsBundle\Manager;

use Jbtronics\SettingsBundle\Metadata\SettingsMetadata;

/**
 * This interface is used to reset a settings instance to their default values.
 */
interface SettingsResetterInterface
{
    /**
     * Resets the settings instance to their default values.
     * The instance is returned.
     * @template T of object
     * @param  object  $settings
     * @phpstan-param T $settings
     * @param SettingsMetadata $metadata The metadata, that should be used to reset the settings
     * @phpstan-param SettingsMetadata<T> $metadata
     * @return object
     * @phpstan-return T
     */
    public function resetSettings(object $settings, SettingsMetadata $metadata): object;

    /**
     * Creates a new instance of the given settings class with the default values set. The instance is not tracked
     * by the SettingsManager!
     * @template T of object
     * @param  SettingsMetadata  $metadata
     * @phpstan-param SettingsMetadata<T> $metadata
     * @return object
     * @phpstan-return T
     */
    public function newInstance(SettingsMetadata $metadata): object;
}