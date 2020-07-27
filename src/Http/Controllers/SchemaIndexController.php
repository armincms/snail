<?php

namespace Armincms\Snail\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Armincms\Snail\Snail; 

class SchemaIndexController extends Controller
{
    /**
     * List the schemas for api.
     *
     * @param  \Armincms\Snail\Http\Requests\ResourceIndexRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request)
    {     
        $versions = Snail::availableVersions($request);

        return collect(array_combine($versions, $versions))->map(function($version) use ($request) {
            return Snail::version($version, function($snail) use ($request, $version) {
                if($resource = $snail::resourceForKey($request->route('schema'))) { 
                    return array_merge($resource::schema($request), [ 
                        'version' => $version, 
                    ]);
                } 
            }); 
        })->filter(); 
    } 
} 