<?php

namespace Armincms\Snail\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Armincms\Snail\Snail; 

class SchemaVersionController extends Controller
{
    /**
     * List the schemas for api.
     *
     * @param  \Armincms\Snail\Http\Requests\ResourceIndexRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request)
    {     
        return Snail::version($request->route('version'), function($snail) use ($request) {
            $resource = $snail::resourceForKey($request->route('schema')); 

            return $resource::schema($request); 
        }); 
    } 
} 