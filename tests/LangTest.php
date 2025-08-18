<?php

namespace JewishPulse\Tests;

use PHPUnit\Framework\TestCase;
use JewishPulse\Locales\Lang;
use stdClass;

class LangTest extends TestCase
{
    public function testDefaultLangIsHebrew(): void
    {
        $contextMock = $this->createMock(stdClass::class);
        $lang = new Lang($contextMock);
        $this->assertEquals('he', $lang->getUserLang(999999));
    }
}
