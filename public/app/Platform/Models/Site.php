<?php

namespace Platform\Models;

use Illuminate\Database\Eloquent\Model;
use Platform\Controllers\Core;
use Platform\Controllers\App;
use App\Scopes\AccountScope;
use Spatie\MediaLibrary\Models\Media;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\Image\Manipulations;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Kalnoy\Nestedset\NodeTrait;

class Site extends Model implements HasMedia
{
    use HasMediaTrait;
    use NodeTrait;

    protected $table = 'sites';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    ];

    /**
     * Appended columns.
     *
     * @var array
     */
    protected $appends = [
      'url',
      'short_url',
      'test_url'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['account'];

    /**
     * Field mutators.
     *
     * @var array
     */
    protected $casts = [
      'content' => 'json',
      'design' => 'json',
      'settings' => 'json',
      'tags' => 'json',
      'attributes' => 'json',
      'meta' => 'json'
    ];

    /**
     * Date/time fields that can be used with Carbon.
     *
     * @return array
     */
    public function getDates() {
      return ['created_at', 'updated_at'];
    }

    public static function boot() {
      parent::boot();

      static::addGlobalScope(new AccountScope(auth()->user()));

      // On update
      static::updating(function ($model) {
        if (auth()->check()) {
          $model->updated_by = auth()->user()->id;

          // Slug
          $model->short_slug = Core\Secure::staticHash($model->id);
          $model->slug = Str::slug($model->name, '-') . '-' . Core\Secure::staticHash($model->id);
        }
      });

      // On create
      self::creating(function ($model) {
        $model->uuid = Uuid::uuid4()->toString();

        if (auth()->check()) {
          $model->account_id = auth()->user()->account_id;
          $model->created_by = auth()->user()->id;
        }
      });

      // Created
      self::created(function ($model) {
        // Slug
        $model->short_slug = Core\Secure::staticHash($model->id);
        $model->slug = Str::slug($model->name, '-') . '-' . Core\Secure::staticHash($model->id);
        $model->save();
      });

      // Deleted
      self::deleted(function ($model) {
      });
    }

    /**
     * Get app internal test url
     *
     * @return string
     */
    public function getTestUrlAttribute($short = false) {
      $slug = ($short) ? $this->short_slug : $this->slug;
      return ($this->account != null) ? config('general.app_url', request()->getSchemeAndHttpHost()) . '/#/-/' . $slug : null;
    }

    /**
     * Get app url, returns test url if domain is not configured
     *
     * @return string
     */
    public function getUrlAttribute() {
      return ($this->host === null) ? $this->getTestUrlAttribute() : '//' . $this->host;
    }

    /**
     * Get app url, returns test url if domain is not configured
     *
     * @return string
     */
    public function getShortUrlAttribute() {
      return ($this->host === null) ? $this->getTestUrlAttribute(true) : '//' . $this->host;
    }

    /**
     * Get full site
     *
     * @return array
     */
    public function getSite($pageSlug = null) {
      $sitePages = \Platform\Models\Site::whereDescendantOrSelf($this)->defaultOrder()->get();

      $children = [];
      foreach ($sitePages as $index => $page) {
        if ($index > 0) {
          if ($pageSlug == $page->slug) $pageUuid = $page->uuid;
          $content = $page->content;
          $content['imgAboveContent'] = $content['imgAboveContent'] ?? '';
          $content['imgAboveContentFileName'] = $content['imgAboveContentFileName'] ?? '';
          $content['imgAvatar'] = $content['imgAvatar'] ?? '';
          $content['imgAvatarFileName'] = $content['imgAvatarFileName'] ?? '';
          $content['content'] = $content['content'] ?? '';

          $settings = $page->settings;
          $settings['showTitleBar'] = $content['settings']['showTitleBar'] ?? true;

          $content['settings'] = $settings;

          $position = 'amidst';
          if ($index == 1) $position = 'first';
          if ($index == count($sitePages) - 1) $position = 'last';
          if (count($sitePages) == 2) $position = 'single';

          switch ($page->module) {
            case 'Content': $icon = 'notes'; break;
            case 'Deal': $icon = 'store'; break;
            case 'BusinessCard': $icon = 'mdi-account-card-details-outline'; break;
            default: $icon = 'notes';
          }

          if ($page->module === null) $page->module = [];

          $children[] = [
            'uuid' => $page->uuid,
            'name' => $page->name,
            'path' => $page->slug,
            'header' => 'move',
            'position' => $position,
            'content' => $content,
            'module' => $page->module,
            'icon' => $icon
          ];
        }
      }

      if ($pageSlug === null) $pageUuid = $children[0]['uuid'];

      $response = [
        'status' => 200,
        'host' => config('general.app_url', request()->getSchemeAndHttpHost()),
        'path' => '-/' . $this->short_slug,
        'pageUuid' => $pageUuid,
        'design' => $sitePages[0]->getDesign(),
        'pages' => [
          [
            'uuid' => -1,
            'name' => $sitePages[0]->name,
            'icon' => 'mdi-qrcode',
            'selectable' => false,
            'expandable' => false,
            'children' => $children
          ]
        ]
      ];

      return $response;
    }  

    /**
     * Get app design params
     *
     * @return array
     */
    public function getDesign() {
      $response = [
        'bgColor' => $this->design['bgColor'] ?? '#eeeeee',
        'textColor' => $this->design['textColor'] ?? '#222222',
        'imgSiteBg' => $this->design['imgSiteBg'] ?? '',
        'imgSiteBgFileName' => $this->design['imgSiteBgFileName'] ?? '',
        'headerBgColor' => $this->design['headerBgColor'] ?? '#455a64',
        'headerTextColor' => $this->design['headerTextColor'] ?? '#ffffff',
        'titleBarBgColor' => $this->design['titleBarBgColor'] ?? '#607d8b',
        'titleBarTextColor' => $this->design['titleBarTextColor'] ?? '#eeeeee',
        'drawerBgColor' => $this->design['drawerBgColor'] ?? '#eeeeee',
        'drawerTextColor' => $this->design['drawerTextColor'] ?? '#222222',
        'drawerActiveBgColor' => $this->design['drawerActiveBgColor'] ?? '#424242',
        'drawerActiveTextColor' => $this->design['drawerActiveTextColor'] ?? '#fafafa'
      ];

      return $response;
    }  

    /**
     * Relationships
     * -------------
     */

    public function account() {
      return $this->belongsTo(\App\User::class, 'account_id', 'id');
    }

    public function user() {
      return $this->belongsTo(\App\User::class, 'created_by', 'id');
    }

    public function siteUsers() {
      return $this->hasMany(\App\SiteUser::class, 'site_id', 'id');
    }
}