<?php

namespace AvtoDev\StaticReferencesLaravel\Tests\References;

use AvtoDev\StaticReferencesLaravel\PreferencesProviders\AutoRegionsProvider;
use AvtoDev\StaticReferencesLaravel\References\AutoRegions\AutoRegionsReference;

class AutoRegionsReferenceTest extends AbstractReferenceTestCase
{
    /**
     * @var AutoRegionsReference
     */
    protected $reference_instance;

    /**
     * @var string
     */
    protected $reference_class = AutoRegionsReference::class;

    /**
     * @var string
     */
    protected $reference_provider_class = AutoRegionsProvider::class;

    /**
     * Тест базовых акцессоров данных.
     *
     * @return void
     */
    public function testBasicData()
    {
        $this->assertGreaterThan(10, count($this->reference_instance->all()));
    }

    /**
     * Тест метода `getByRegionCode()`.
     */
    public function testGetByRegionCode()
    {
        $this->assertEquals(
            'Республика Марий Эл',
            $this->reference_instance->getByRegionCode(12)->getTitle()
        );

        $this->assertEquals(
            12,
            $this->reference_instance->getByRegionCode(12)->getRegionCode()
        );

        $this->assertEquals(
            'RU-ME',
            $this->reference_instance->getByRegionCode(12)->getIso31662()
        );
    }

    /**
     * Тестируем метод `getByAutoCode()`.
     */
    public function testGetByAutoCode()
    {
        // Существующие области
        $moscow = 'Москва';
        $this->assertEquals($moscow, $this->reference_instance->getByAutoCode(77)->getTitle());
        $this->assertEquals($moscow, $this->reference_instance->getByAutoCode('077')->getTitle());
        $this->assertEquals($moscow, $this->reference_instance->getByAutoCode('арарара 077')->getTitle());
        $this->assertEquals(
            [77, 97, 99, 177, 197, 199, 799, 777],
            $this->reference_instance->getByAutoCode(77)->getAutoCode()
        );

        $sverdl_obl = 'Свердловская область';
        $this->assertEquals($sverdl_obl, $this->reference_instance->getByAutoCode(66)->getTitle());
        $this->assertEquals($sverdl_obl, $this->reference_instance->getByAutoCode('66')->getTitle());
        $this->assertEquals($sverdl_obl, $this->reference_instance->getByAutoCode('boom 66 пыщь')->getTitle());
        $this->assertEquals([66, 96, 196], $this->reference_instance->getByAutoCode(66)->getAutoCode());

        // И не существующие области
        $this->assertNull($this->reference_instance->getByAutoCode(997));
        $this->assertNull($this->reference_instance->getByAutoCode(299));
        $this->assertNull($this->reference_instance->getByAutoCode(0));
        $this->assertNull($this->reference_instance->getByAutoCode(null));
        $this->assertNull($this->reference_instance->getByAutoCode([]));
        $this->assertNull($this->reference_instance->getByAutoCode('трали вали'));
    }

    /**
     * Тестируем метод `getByTitle()`.
     */
    public function testGetByTitle()
    {
        $moscow = 'Москва';

        // Находит без опечаток
        $this->assertEquals($moscow, $this->reference_instance->getByTitle('Москва')->getTitle());

        // Находит с опечатками
        $this->assertEquals($moscow, $this->reference_instance->getByTitle('Мaсcква')->getTitle());
        $this->assertEquals($moscow, $this->reference_instance->getByTitle('Мсква')->getTitle());

        // Не находит, если передан совсем уж пиздец или пустая строка
        $this->assertNull($this->reference_instance->getByTitle('Мяусикывуа'));
        $this->assertNull($this->reference_instance->getByTitle(''));

        // Не находит, если включен режим строго соответствия
        $this->assertNull($this->reference_instance->getByTitle('Мaсcква', true));
        $this->assertNull($this->reference_instance->getByTitle('Мaсcкваaaa', true));

        // Ну и парочка дополнительных тестов на не строгое соответствие
        $expected = 86;
        $this->assertEquals($expected, $this->reference_instance->getByTitle('Хмао')->getRegionCode());
        $this->assertEquals($expected, $this->reference_instance->getByTitle('Югра')->getRegionCode());
        $this->assertEquals($expected, $this->reference_instance->getByTitle('Ханты-Мансийский')->getRegionCode());
    }

    /**
     * Тестируем метод `getByOkato()`.
     */
    public function testGetByOkato()
    {
        $sverdl_obl = 'Свердловская область';
        $this->assertEquals($sverdl_obl, $this->reference_instance->getByOkato(65)->getTitle());
        $this->assertEquals($sverdl_obl, $this->reference_instance->getByOkato('65')->getTitle());

        $this->assertEquals('Ямало-Ненецкий автономный округ',
            $this->reference_instance->getByOkato('71-9')->getTitle());
        $this->assertEquals('Ханты-Мансийский автономный округ - Югра', $this->reference_instance->getByOkato('71-8')
            ->getTitle());

        $this->assertNull($this->reference_instance->getByOkato(123));
        $this->assertNull($this->reference_instance->getByOkato(null));
        $this->assertNull($this->reference_instance->getByOkato([]));
    }

    /**
     * Тестируем метод `getByIso31662()`.
     */
    public function testGetByIso31662()
    {
        $sverdl_obl = 'Свердловская область';
        $this->assertEquals(
            $sverdl_obl,
            $this->reference_instance->getByIso31662('RU-SVE')->getTitle()
        );
        $this->assertEquals(
            $sverdl_obl,
            $this->reference_instance->getByIso31662('ууу RU-SVE ыыы')->getTitle()
        );
        $this->assertEquals(
            'Смоленская область',
            $this->reference_instance->getByIso31662('RU-SMO')->getTitle()
        );
        $this->assertEquals(
            'Республика Крым',
            $this->reference_instance->getByIso31662('RU-CR')->getTitle()
        );

        $this->assertNull($this->reference_instance->getByIso31662('123123'));
        $this->assertNull($this->reference_instance->getByIso31662('SDFD-RYGF'));
        $this->assertNull($this->reference_instance->getByIso31662(''));
    }
}