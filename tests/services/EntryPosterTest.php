<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 24.12.2017
 * Time: 13:49
 */

namespace itscoding\facebookconnectortest\service;


use craft\elements\Entry;
use itscoding\facebookconnector\FacebookConnector;
use itscoding\facebookconnector\services\EntryPoster;
use itscoding\facebookconnectortest\BaseTestCase;


class EntryPosterTest extends BaseTestCase
{

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
        $url = 'http://itscoding.ch';
        $id = 1;
        $subtitle = 'hello mock';
        $entryMock = $this->createMock(Entry::class);
        $entryMock->method('getUrl')
            ->willReturn($url);
        $entryMock->method('getFieldValue')
            ->will($this->onConsecutiveCalls(false,$subtitle));

        $entryMock->id = $id;
        $postData = $this->entryPoster->getPostData($entryMock);
        $this->assertEquals($url, $postData['link']);
        $this->assertEquals($id, $postData['entry_id']);
        $this->assertEquals($subtitle, $postData['message']);
        $this->assertFalse($postData['post_on_facebook']);
    }

}
