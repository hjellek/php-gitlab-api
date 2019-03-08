<?php namespace Gitlab\Tests\Api;

class GroupIssuesTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAllIssues()
    {
        $expectedArray = array(
            array('id' => 1, 'title' => 'An issue'),
            array('id' => 2, 'title' => 'Another issue'),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('issues', array())
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all());
    }

    /**
     * @test
     */
    public function shouldGetGroupIssuesWithPagination()
    {
        $expectedArray = array(
            array('id' => 1, 'title' => 'An issue'),
            array('id' => 2, 'title' => 'Another issue'),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/issues', array('page' => 2, 'per_page' => 5))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all(1, ['page' => 2, 'per_page' => 5]));
    }

    /**
     * @test
     */
    public function shouldGetGroupIssuesWithParams()
    {
        $expectedArray = array(
            array('id' => 1, 'title' => 'An issue'),
            array('id' => 2, 'title' => 'Another issue'),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/issues', array('order_by' => 'created_at', 'sort' => 'desc', 'labels' => 'foo,bar', 'state' => 'opened'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all(1, array('order_by' => 'created_at', 'sort' => 'desc', 'labels' => 'foo,bar', 'state' => 'opened')));
    }

    protected function getApiClass()
    {
        return 'Gitlab\Api\GroupIssues';
    }
}
