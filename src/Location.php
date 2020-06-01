<?php  

namespace Armincms\Snail; 
 
use Armincms\Snail\Http\Requests\SnailRequest; 
use Illuminate\Http\Request; 
use Armincms\Snail\Properties\ID;
use Armincms\Snail\Properties\Text;
use Armincms\Snail\Properties\Number;
use Armincms\Snail\Properties\BelongsTo;
use Armincms\Snail\Properties\BelongsToMany;

class Location extends Schema 
{  
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'Zareismail\\NovaPolicy\\PolicyRole';

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
            ID::make(),

            Text::make('name'),

            BelongsToMany::make('permissions', 'permissions', Permission::class), 
        ];
    }

    /**
     * Build an "index" query for the given resource.
     *
     * @param  \Armincms\Snail\Http\Requests\SnailRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(SnailRequest $request, $query)
    {
        return $query;
    } 

    /**
     * Build a "detail" query for the given resource.
     *
     * @param  \Armincms\Snail\Http\Requests\SnailRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function detailQuery(SnailRequest $request, $query)
    {
        return parent::detailQuery($request, $query);
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     *
     * @param  \Armincms\Snail\Http\Requests\SnailRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function relatableQuery(SnailRequest $request, $query)
    {
        return parent::relatableQuery($request, $query);
    }
}