<?php
/*
 * This file is part of jbtronics/settings-bundle (https://github.com/jbtronics/settings-bundle).
 *
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

namespace Jbtronics\SettingsBundle\Tests\ParameterTypes;

use Jbtronics\SettingsBundle\ParameterTypes\IntType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IntTypeTest extends TestCase
{

    private IntType $intType;

    public function setUp(): void
    {
        $this->intType = new IntType();
    }

    public function testConvertPHPToNormalized(): void
    {
        $metadata = $this->createMock(\Jbtronics\SettingsBundle\Metadata\ParameterMetadata::class);

        $this->assertEquals(1, $this->intType->convertPHPToNormalized(1, $metadata));
        $this->assertNull($this->intType->convertPHPToNormalized(null, $metadata));
    }

    public function testConvertPHPToNormalizedInvalidType(): void
    {
        $metadata = $this->createMock(\Jbtronics\SettingsBundle\Metadata\ParameterMetadata::class);

        $this->expectException(\LogicException::class);
        $this->intType->convertPHPToNormalized('1', $metadata);
    }

    public function testConvertNormalizedToPHP(): void
    {
        $metadata = $this->createMock(\Jbtronics\SettingsBundle\Metadata\ParameterMetadata::class);

        $this->assertEquals(1, $this->intType->convertNormalizedToPHP(1, $metadata));
        $this->assertNull($this->intType->convertNormalizedToPHP(null, $metadata));

        // Try to convert other values to int
        $this->assertEquals(1, $this->intType->convertNormalizedToPHP('1', $metadata));
        $this->assertEquals(1, $this->intType->convertNormalizedToPHP(1.1, $metadata));
    }

    public function testConfigureFormOptions(): void
    {
        $metadata = $this->createMock(\Jbtronics\SettingsBundle\Metadata\ParameterMetadata::class);

        $resolver  = new OptionsResolver();
        $this->intType->configureFormOptions($resolver, $metadata);

        $this->assertSame([], $resolver->resolve());
    }

    public function testGetFormType()
    {
        $metadata = $this->createMock(\Jbtronics\SettingsBundle\Metadata\ParameterMetadata::class);
        $this->assertEquals(\Symfony\Component\Form\Extension\Core\Type\IntegerType::class, $this->intType->getFormType($metadata));
    }
}
