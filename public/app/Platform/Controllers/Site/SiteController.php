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
     * Test
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getTest() {
      $uuid = 'adfa2c83-b61a-4ef5-b33b-c7bf002759e1';
      $site = \Platform\Models\Site::where('uuid', $uuid)->first();
      $sitePages = \Platform\Models\Site::whereDescendantOrSelf($site)->get();
      dd($sitePages);
      return response()->json($sites, 200);
    }

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

    /**
     * Save site page
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postSavePage(Request $request) {
      $locale = request('locale', config('system.default_language'));
      $page = request('page', null);
      if ($page !== null) $page = json_decode($page);
      $site_uuid = request('site_uuid', null);

      $v = Validator::make((array) $page, [
        'name' => 'required|max:64',
      ]);

      if ($v->fails()) {
        return response()->json([
          'status' => 'error',
          'errors' => $v->errors()
        ], 422);
      }

      $sitePage = \Platform\Models\Site::where('created_by', auth()->user()->id)->where('uuid', $page->uuid)->first();

      if ($sitePage !== null) {
        $sitePage->name = $page->name;
        $sitePage->content = $page->content;
        $sitePage->save();

        return response()->json(['status' => 'success', 'msg' => trans('app.saved_successfully')], 200);
      }

      return response()->json(['status' => 'error', 'msg' => trans('app.processing_error')], 200);
    }
}
