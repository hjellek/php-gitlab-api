<?php namespace Gitlab\Tests\Api;

class GroupMergeRequestsTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAll()
    {
        $expectedArray = $this->getMultipleMergeRequestsData();

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/merge_requests', array())
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all(1));
    }

    /**
     * @test
     */
    public function shouldGetAllWithParams()
    {
        $expectedArray = $this->getMultipleMergeRequestsData();

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/merge_requests', [
                'page' => 2,
                'per_page' => 5,
                'labels' => 'label1,label2,label3',
                'milestone' => 'milestone1',
                'order_by' => 'updated_at',
                'state' => 'all',
                'sort' => 'desc',
                'scope' => 'all',
                'author_id' => 1,
                'assignee_id' => 1,
                'source_branch' => 'develop',
                'target_branch' => 'master',
            ])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all(1, [
            'page' => 2,
            'per_page' => 5,
            'labels' => 'label1,label2,label3',
            'milestone' => 'milestone1',
            'order_by' => 'updated_at',
            'state' => 'all',
            'sort' => 'desc',
            'scope' => 'all',
            'author_id' => 1,
            'assignee_id' => 1,
            'source_branch' => 'develop',
            'target_branch' => 'master',
        ]));
    }

    /**
     * @test
     */
    public function shouldGetAllWithDateTimeParams()
    {
        $expectedArray = $this->getMultipleMergeRequestsData();

        $createdAfter = new \DateTime('2018-01-01 00:00:00');
        $createdBefore = new \DateTime('2018-01-31 00:00:00');

        $expectedWithArray = [
            'created_after' => $createdAfter->format(DATE_ATOM),
            'created_before' => $createdBefore->format(DATE_ATOM),
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/merge_requests', $expectedWithArray)
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals(
            $expectedArray,
            $api->all(1, ['created_after' => $createdAfter, 'created_before' => $createdBefore])
        );
    }

    protected function getMultipleMergeRequestsData()
    {
        return array(
            array('id' => 1, 'title' => 'A merge request'),
            array('id' => 2, 'title' => 'Another merge request')
        );
    }

    protected function getApiClass()
    {
        return 'Gitlab\Api\GroupMergeRequests';
    }
}
