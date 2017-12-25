<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 24.12.2017
 * Time: 13:49
 */

namespace itscoding\facebookconnectortest\service;


use craft\elements\Entry;
use itscoding\facebookconnector\services\ConfigFileLoader;
use itscoding\facebookconnector\services\EntryPoster;
use itscoding\facebookconnectortest\BaseTestCase;


class EntryPosterTest extends BaseTestCase
{

    private $mockUrl = 'http://itscoding.ch';
    private $mockId = 1;
    private $mockMessage = 'hello mock';

    /**
     * @var EntryPoster
     */
    private $entryPoster;

    public function setUp()
    {
        parent::setUp();
        $this->entryPoster = new EntryPoster();
    }

    /**
     * @group unit
     */
    public function testEntryPosterServiceCreation()
    {
        $this->assertInstanceOf(EntryPoster::class, $this->entryPoster);
    }


    /**
     * @group unit
     */
    public function testGetPostDataWithDefaultConfig()
    {
        $fileLoader = $this->createMock(ConfigFileLoader::class);
        $fileLoader->method('getConfigFile')
            ->willReturn(function () {
                return [];
            });
        $this->entryPoster->setConfigFileLoader($fileLoader);
        $postData = $this->entryPoster->getPostData($this->createMockEntry());
        $this->assertEquals($this->mockUrl, $postData['link']);
        $this->assertEquals($this->mockId, $postData['entry_id']);
        $this->assertEquals($this->mockMessage, $postData['message']);
        $this->assertEquals('', $postData['caption']);
        $this->assertFalse($postData['post_on_facebook']);
    }

    /**
     * @group unit
     */
    public function testGetPostDataWithConfigurationFile()
    {
        $fileLoader = new ConfigFileLoader();
        $fileLoader->setConfigFile(__DIR__ . '/../mock/fieldconfig.php');
        $this->entryPoster->setConfigFileLoader($fileLoader);
        $postData = $this->entryPoster->getPostData($this->createMockEntry());
        $this->assertEquals('Simon Müller', $postData['caption']);
        $this->assertEquals($this->mockUrl, $postData['link']);
        $this->assertEquals(888, $postData['entry_id']);
        $this->assertEquals('mockMe', $postData['message']);
        $this->assertTrue($postData['post_on_facebook']);
    }


    /**
     * @return Entry|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createMockEntry()
    {
        $entryMock = $this->createMock(Entry::class);
        $entryMock->method('getUrl')
            ->willReturn($this->mockUrl);
        $entryMock->method('getFieldValue')
            ->will($this->onConsecutiveCalls(false, $this->mockMessage));
        $entryMock->id = $this->mockId;
        return $entryMock;
    }


}
