<?php 

namespace Armincms\Snail\Tests\Fixtures;

use Armincms\Snail\Properties\Number;
use Armincms\Snail\Properties\Text;
use Armincms\Snail\Schema;
use Illuminate\Http\Request;
use Armincms\Snail\Http\Request\SnailRequest;

class PostResourceVersioning extends Schema
{ 
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'Armincms\\Snail\\Tests\\Fixtures\\Post';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * Get the properties displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function properties(Request $request)
    {
        return [ 
            Number::make('id'),

            Text::make('name'), 
        ];
    } 

    /**
     * Get the URI key for the schema.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'posts';
    }
}