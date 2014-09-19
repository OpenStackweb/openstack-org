<?php

class OpenStackApiTest  extends SapphireTest {

	public function testCreateApi(){
		$factory    = new OpenStackApiFactory;
		$api        = $factory->buildOpenStackApi('test','test','test api');
		$version    = $factory->buildOpenStackApiVersion(1,'nova',$api);
		$resource   = $factory->buildOpenStackApiResource('test','tests operations',$version);
		$endpoint_1 = $factory->buildOpenStackApiEndpoint('/test','get all tests','GET',$version,$resource);
		$endpoint_2 = $factory->buildOpenStackApiEndpoint('/test','add test','POST',$version,$resource);
	}
} 