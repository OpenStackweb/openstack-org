<?php

/**
 * Class MockYouTubeServiceGenerator
 */
trait MockYouTubeServiceGenerator
{
    /**
     * @param $methods
     * @param null $responseWill
     * @return mixed
     */
    protected function createMockYouTubeService($methods, $responseWill = null)
    {
        if (!is_array($methods)) {
            $methods = [$methods];
        }

        if (!$responseWill) {
            $responseWill = $this->returnSelf();
        }

        $methodMap = [];
        foreach ($methods as $methodName => $callback) {
            if (is_numeric($methodName)) {
                $methodName = $callback;
            }

            $expects = is_callable($callback) ? $callback($this) : $this->any();
            $methodMap[$methodName] = $expects;
        }


        $mockResponse = $this->getMockBuilder('GuzzleHttp\Stream\Stream')
            ->setMethods(['getBody', 'getContents'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockResponse
            ->expects($this->any())
            ->method('getBody')
            ->will($this->returnSelf());

        $mockResponse
            ->expects($this->any())
            ->method('getContents')
            ->will($responseWill);

        $mockService = $this->getMockBuilder('SummitVideoYouTubeService')
            ->setConstructorArgs([Injector::inst()->get('SummitVideoHTTPClient')])
            ->setMethods(array_keys($methodMap))
            ->getMock();

        foreach ($methodMap as $methodName => $expects) {
            $mockService
                ->expects($expects)
                ->method($methodName)
                ->will($this->returnValue($mockResponse));
        }

        return $mockService;
    }

}