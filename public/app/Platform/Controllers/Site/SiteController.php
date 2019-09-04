<?php 

namespace Platform\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Customer;
use App\Http\Controllers;
use Carbon\Carbon;
use Platform\Controllers\Core;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SiteController extends Controller {

    /*
     |--------------------------------------------------------------------------
     | Mobile Site Controller
     |--------------------------------------------------------------------------
     |
     | Mobile site related logic
     |--------------------------------------------------------------------------
     */

    /**
     * Get all sites for user
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getSites() {
      $sites = auth()->user()->getSites();
      return response()->json($sites, 200);
    }

    /**
     * Get user site by uuid
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getSite(Request $request) {
      $uuid = request('uuid', null);
      $site = auth()->user()->getSite($uuid);
      return response()->json($site, 200);
    }
}