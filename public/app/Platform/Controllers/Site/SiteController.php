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
     * Get site by slug
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getSiteBySlug(Request $request) {
      $siteSlug = request('siteSlug', null);
      $pageSlug = request('pageSlug', null);

      $site = \Platform\Models\Site::where('short_slug', $siteSlug)->first();

      if ($site !== null) {
        $site = $site->getSite($pageSlug);
        return response()->json($site, 200);
      } else {
        return response()->json(['status' => 404], 200);
      }
    }

    /**
     * Create page
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postCreateSite(Request $request) {
      $locale = request('locale', config('system.default_language'));
      $module = request('module', null);
      $name = request('name', null);

      $v = Validator::make(['name' => $name], [
        'name' => 'required|max:64',
      ]);

      if ($v->fails()) {
        return response()->json([
          'status' => 'error',
          'errors' => $v->errors()
        ], 422);
      }

      $site = new \Platform\Models\Site;
      $site->name = $name;
      $site->save();
      
      if ($site !== null) {
        $sitePage = new \Platform\Models\Site;
        $sitePage->name = 'Home';
        $sitePage->module = $module;
        $site->appendNode($sitePage);

        return response()->json(['status' => 'success', 'msg' => trans('app.site_created'), 'uuid' => $site->uuid], 200);
      }

      return response()->json(['status' => 'error', 'msg' => trans('app.processing_error')], 200);
    }

    /**
     * Save site
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postSaveSite(Request $request) {
      $locale = request('locale', config('system.default_language'));
      $sitePost = request('site', null);
      if ($sitePost !== null) $sitePost = json_decode($sitePost);
      $site_uuid = request('site_uuid', null);

      $v = Validator::make((array) $sitePost, [
        'siteName' => 'required|max:64',
      ]);

      if ($v->fails()) {
        return response()->json([
          'status' => 'error',
          'errors' => $v->errors()
        ], 422);
      }

      $site = \Platform\Models\Site::where('created_by', auth()->user()->id)->where('uuid', $site_uuid)->first();

      if ($site !== null) {
        // Parse images
        foreach ($site as $field => $content) {
          if (Str::startsWith($field, 'img') && ! Str::endsWith($field, 'FileName') && ! Str::startsWith($site->{$field}, 'http')) {

            // Remove earlier attached images
            $sitePage
              ->clearMediaCollection($field);

            if ($content !== '') {
              // Attach image
              $filename = $site->{$field . 'FileName'};
              $sitePage
                ->addMediaFromBase64($content)
                ->usingFileName($filename)
                ->sanitizingFileName(function($fileName) {
                  return strtolower(str_replace(['#', '/', '\\', ' '], '-', $fileName));
                })
                ->toMediaCollection($field, 'media');

              // Replace field with path
              $site->{$field} = request()->getSchemeAndHttpHost() . $sitePage->getFirstMediaUrl($field);
            } else {
              $site->{$field . 'FileName'} = '';
            }
          }
        }

        $site->name = $sitePost->siteName;
        $site->design = $sitePost->design;
        $site->save();

        return response()->json(['status' => 'success', 'msg' => trans('app.saved_successfully')], 200);
      }

      return response()->json(['status' => 'error', 'msg' => trans('app.processing_error')], 200);
    }

    /**
     * Delete site
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postDeleteSite(Request $request) {
      $locale = request('locale', config('system.default_language'));
      $site_uuid = request('site_uuid', null);

      $site = \Platform\Models\Site::where('created_by', auth()->user()->id)->where('uuid', $site_uuid)->first();

      if ($site !== null) {
        $site->delete();
        return response()->json(['status' => 'success', 'msg' => trans('app.site_deleted')], 200);
      }

      return response()->json(['status' => 'error', 'msg' => trans('app.processing_error')], 200);
    }

    /**
     * Add page
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postAddPage(Request $request) {
      $locale = request('locale', config('system.default_language'));
      $site_uuid = request('site_uuid', null);
      $module = request('module', null);
      $name = request('name', null);

      $v = Validator::make(['name' => $name], [
        'name' => 'required|max:64',
      ]);

      if ($v->fails()) {
        return response()->json([
          'status' => 'error',
          'errors' => $v->errors()
        ], 422);
      }

      $site = \Platform\Models\Site::where('created_by', auth()->user()->id)->where('uuid', $site_uuid)->first();

      if ($site !== null) {
        $sitePage = new \Platform\Models\Site;
        $sitePage->name = $name;
        $sitePage->module = $module['module'];
        $site->appendNode($sitePage);

        return response()->json(['status' => 'success', 'msg' => trans('app.page_created'), 'uuid' => $sitePage->uuid], 200);
      }

      return response()->json(['status' => 'error', 'msg' => trans('app.processing_error')], 200);
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

        // Parse content for images
        foreach ($page->content as $field => $content) {
          if (Str::startsWith($field, 'img') && ! Str::endsWith($field, 'FileName') && ! Str::startsWith($page->content->{$field}, 'http')) {

            // Remove earlier attached images
            $sitePage
              ->clearMediaCollection($field);

            if ($content !== '') {
              // Attach image
              $filename = $page->content->{$field . 'FileName'};
              $sitePage
                ->addMediaFromBase64($content)
                ->usingFileName($filename)
                ->sanitizingFileName(function($fileName) {
                  return strtolower(str_replace(['#', '/', '\\', ' '], '-', $fileName));
                })
                ->toMediaCollection($field, 'media');

              // Replace field with path
              $page->content->{$field} = request()->getSchemeAndHttpHost() . $sitePage->getFirstMediaUrl($field);
            } else {
              $page->content->{$field . 'FileName'} = '';
            }
          }
        }

        $sitePage->name = $page->name;
        $sitePage->content = $page->content;
        $sitePage->save();

        return response()->json(['status' => 'success', 'msg' => trans('app.saved_successfully')], 200);
      }

      return response()->json(['status' => 'error', 'msg' => trans('app.processing_error')], 200);
    }

    /**
     * Delete site page
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postDeletePage(Request $request) {
      $locale = request('locale', config('system.default_language'));
      $page_uuid = request('page_uuid', null);

      $sitePage = \Platform\Models\Site::where('created_by', auth()->user()->id)->where('uuid', $page_uuid)->first();

      if ($sitePage !== null) {
        // Check if it is last page (at least one page is required)
        $siblings = \Platform\Models\Site::where('created_by', auth()->user()->id)->where('parent_id', $sitePage->parent_id)->get();
        if ($siblings->count() > 1) {
          $sitePage->delete();
          return response()->json(['status' => 'success', 'msg' => trans('app.page_deleted')], 200);
        } else {
          return response()->json(['status' => 'error', 'msg' => trans('app.one_page_required')], 200);
        }
      }

      return response()->json(['status' => 'error', 'msg' => trans('app.processing_error')], 200);
    }
}
