<?php

namespace App\Http\Responses;

class IndexProduct
{
    /**
     * @param array $results
     * @return array
     */
    public function response(array $results)
    {
	    $response = [];
        foreach ($results as $result) {
		    $response[] = $result['_source'];
        }

	    return $response;
    }
}
