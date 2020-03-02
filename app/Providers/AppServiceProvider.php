<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
      Blade::directive('sortedTableHeader', function ($expression) {
          list($path, $fieldName, $fieldText, $page) = explode(', ', $expression);

          return '<?php echo "<th scope=\"col\"><a class=\"orderby text-nowrap\" '
            .'href=\"'.url($path).'?page='.$page.'&sort='.$fieldName.'&ascdesc="'
            .'.(($sort == "'.$fieldName.'" && $ascdesc == "ASC") ? "DESC" : "ASC")."\">'
            .'<span>'.$fieldText.'</span>&nbsp;<span class=\"fas fa-sort"'
            .'.(($sort == "'.$fieldName.'") ? (($ascdesc == "ASC") ? "-up" : "-down") : "" )."\">'
            . '</span></a></th>"?>';


      });
    }
}
