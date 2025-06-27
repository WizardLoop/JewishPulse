<?php
use PHPUnit\Framework\TestCase;
use JewishPulse\Locales\Lang;

class LangTest extends TestCase
{
    public function testDefaultLangIsHebrew()
    {
        $contextMock = $this->createMock(stdClass::class);
        $lang = new Lang($contextMock);
        $this->assertEquals('he', $lang->getUserLang(999999)); 
    }
}
