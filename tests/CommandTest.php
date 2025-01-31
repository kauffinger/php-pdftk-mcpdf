<?php
namespace tests;

use PHPUnit\Framework\TestCase;
use kauffinger\pdftkmcpdf\Command;

class CommandTest extends TestCase
{
    public function testCanAddFiles()
    {
        $document1 = $this->getDocument1();
        $document2 = $this->getDocument2();
        $file = $this->getOutFile();

        $command = new Command;
        $this->assertEquals(0, $command->getFileCount());
        $this->assertInstanceOf('kauffinger\pdftkmcpdf\Command', $command->addFile($document1, 'A'));
        $this->assertInstanceOf('kauffinger\pdftkmcpdf\Command', $command->addFile($document2, 'B', 'complex\'"password'));
        $this->assertEquals(2, $command->getFileCount());
        $this->assertTrue($command->execute($file));
        $this->assertFileExists($file);

        $this->assertEquals("pdftk 'A'='$document1' 'B'='$document2' 'input_pw' 'B'='complex'\''\"password' 'output' '$file'", (string) $command);
    }

    public function testCanAddOptions()
    {
        $document1 = $this->getDocument1();
        $file = $this->getOutFile();

        $command = new Command;
        $this->assertInstanceOf('kauffinger\pdftkmcpdf\Command', $command->addFile($document1, 'A'));
        $this->assertInstanceOf('kauffinger\pdftkmcpdf\Command', $command->addOption('encrypt_40bit'));
        $this->assertInstanceOf('kauffinger\pdftkmcpdf\Command', $command->addOption('allow', 'Printing', false));
        $this->assertInstanceOf('kauffinger\pdftkmcpdf\Command', $command->addOption('owner_pw', 'complex\'"password'));
        $this->assertTrue($command->execute($file));
        $this->assertFileExists($file);

        $this->assertEquals("pdftk 'A'='$document1' 'output' '$file' 'encrypt_40bit' allow Printing 'owner_pw' 'complex'\''\"password'", (string) $command);
    }

    public function testCanSetAndGetOperationAndArgument()
    {
        $document1 = $this->getDocument1();
        $file = $this->getOutFile();

        $command = new Command;
        $this->assertInstanceOf('kauffinger\pdftkmcpdf\Command', $command->addFile($document1, 'A'));
        $this->assertInstanceOf('kauffinger\pdftkmcpdf\Command', $command->setOperation('cat'));
        $this->assertInstanceOf('kauffinger\pdftkmcpdf\Command', $command->setOperationArgument('A'));
        $this->assertTrue($command->execute($file));
        $this->assertFileExists($file);

        $this->assertEquals('cat', $command->getOperation());
        $this->assertEquals('A', $command->getOperationArgument());
        $this->assertEquals("pdftk 'A'='$document1' cat A 'output' '$file'", (string) $command);
    }

    public function testCanAddPageRanges()
    {
        $document1 = $this->getDocument1();
        $file = $this->getOutFile();

        $command = new Command;
        $this->assertInstanceOf('kauffinger\pdftkmcpdf\Command', $command->addFile($document1, 'A'));
        $this->assertInstanceOf('kauffinger\pdftkmcpdf\Command', $command->setOperation('cat'));
        $this->assertInstanceOf('kauffinger\pdftkmcpdf\Command', $command->addPageRange(1));
        $this->assertInstanceOf('kauffinger\pdftkmcpdf\Command', $command->addPageRange(1, null, 'A'));
        $this->assertInstanceOf('kauffinger\pdftkmcpdf\Command', $command->addPageRange(1, 3));
        $this->assertInstanceOf('kauffinger\pdftkmcpdf\Command', $command->addPageRange(1, 3, null, 'even'));
        $this->assertInstanceOf('kauffinger\pdftkmcpdf\Command', $command->addPageRange(1, 3, null, 'even', 'north'));
        $this->assertInstanceOf('kauffinger\pdftkmcpdf\Command', $command->addPageRange(1, 3, null, null, 'north'));
        $this->assertInstanceOf('kauffinger\pdftkmcpdf\Command', $command->addPageRange(1, 3, 'A'));
        $this->assertInstanceOf('kauffinger\pdftkmcpdf\Command', $command->addPageRange(1, 3, 'A', 'even'));
        $this->assertInstanceOf('kauffinger\pdftkmcpdf\Command', $command->addPageRange(1, 3, 'A', 'odd', 'east'));
        $this->assertInstanceOf('kauffinger\pdftkmcpdf\Command', $command->addPageRange(1, 3, 'A', null, 'east'));
        $this->assertInstanceOf('kauffinger\pdftkmcpdf\Command', $command->addPageRange(1, 3, null, null, 'east'));
        $this->assertInstanceOf('kauffinger\pdftkmcpdf\Command', $command->addPageRange(array(1,3)));
        $this->assertInstanceOf('kauffinger\pdftkmcpdf\Command', $command->addPageRange(array(1,3), null, 'A'));
        $this->assertTrue($command->execute($file));
        $this->assertFileExists($file);

        $this->assertEquals("pdftk 'A'='$document1' cat 1 A1 1-3 1-3even 1-3evennorth 1-3north A1-3 A1-3even A1-3oddeast A1-3east 1-3east 1 3 A1 A3 'output' '$file'", (string) $command);
    }

    protected function getDocument1()
    {
        return __DIR__ . '/files/document1.pdf';
    }

    protected function getDocument2()
    {
        return __DIR__ . '/files/document2.pdf';
    }

    protected function getForm()
    {
        return __DIR__ . '/files/form.pdf';
    }

    protected function getOutFile()
    {
        return __DIR__ . '/test.pdf';
    }
}
