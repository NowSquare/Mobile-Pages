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

class Site extends Model implements HasMedia
{
    use HasMediaTrait;

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
      'home_image',
      'home_item1_image',
      'home_item2_image',
      'home_item3_image',
      'url',
      'short_url',
      'test_url'
    ];

    public function registerMediaCollections() {

      $this
        ->addMediaCollection('home_image')
        ->singleFile();

      $this
        ->addMediaCollection('home_item1_image')
        ->singleFile();

      $this
        ->addMediaCollection('home_item2_image')
        ->singleFile();

      $this
        ->addMediaCollection('home_item3_image')
        ->singleFile();
    }

    public function registerMediaConversions(Media $media = null) {
        $this
          ->addMediaConversion('full_header')
          ->width(1280)
          ->height(1024)
          ->performOnCollections('home_image');

        $this
          ->addMediaConversion('item')
          ->width(640)
          ->height(480)
          ->performOnCollections('home_item1_image', 'home_item2_image', 'home_item3_image');
    }

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
      return ($this->account != null) ? request()->getSchemeAndHttpHost() . '/#/-/' . $slug : null;
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
    public function getSite() {
      $response = [
        'design' => $this->getDesign(),
        'pages' => [
          [
            'uuid' => -1,
            'title' => $this->name,
            'icon' => 'mdi-qrcode',
            'selectable' => false,
            'expandable' => false,
            'children' => $this->getPages()
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
        'bgColor' => $this['design->bgColor'] ?? '#eeeeee',
        'textColor' => $this['design->textColor'] ?? '#222222',
        'bgImg' => $this['design->bgImg'] ?? '',
        'headerBgColor' => $this['design->headerBgColor'] ?? '#455a64',
        'headerTextColor' => $this['design->headerTextColor'] ?? '#ffffff',
        'titleBarBgColor' => $this['design->titleBarBgColor'] ?? '#607d8b',
        'titleBarTextColor' => $this['design->titleBarTextColor'] ?? '#eeeeee'
      ];

      return $response;
    }  

    /**
     * Get all pages of site
     *
     * @return array
     */
    public function getPages() {
      $sitePages = $this->sitePages;
      $pages = [];

      if (count($sitePages) > 0) {
        foreach ($sitePages as $page) {
          $pages[] = [
            'uuid' => $page->uuid,
            'title' => $page->title,
            'order' => $page->order,
            'module' => 'Content',
            'icon' => 'notes',
            'content' => [
              'content' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit.',
              'imgAboveContent' => '',
              'imgBelowContent' => 'https://placeimg.com/500/300/nature'
            ]
          ];
        }
      }
      return $pages;
    }  

    /**
     * Images
     * -------------
     */

    public function getHomeImageAttribute() {
      return ($this->getFirstMediaUrl('home_image') !== '') ? $this->getMedia('home_image')[0]->getUrl('full_header') : null;
      //return ($this->getFirstMediaUrl('home_image') !== '') ? $this->getFirstMediaUrl('home_image') : null;
    }

    public function getHomeImageThumbAttribute() {
      return ($this->getFirstMediaUrl('home_image') !== '') ? $this->getMedia('home_image')[0]->getUrl('thumb') : null;
    }

    public function getHomeItem1ImageAttribute() {
      return ($this->getFirstMediaUrl('home_item1_image') !== '') ? $this->getMedia('home_item1_image')[0]->getUrl('item') : null;
      //return ($this->getFirstMediaUrl('home_item1_image') !== '') ? $this->getFirstMediaUrl('home_item1_image') : null;
    }

    public function getHomeItem2ImageAttribute() {
      return ($this->getFirstMediaUrl('home_item2_image') !== '') ? $this->getMedia('home_item2_image')[0]->getUrl('item') : null;
      //return ($this->getFirstMediaUrl('home_item2_image') !== '') ? $this->getFirstMediaUrl('home_item2_image') : null;
    }

    public function getHomeItem3ImageAttribute() {
      return ($this->getFirstMediaUrl('home_item3_image') !== '') ? $this->getMedia('home_item3_image')[0]->getUrl('item') : null;
      //return ($this->getFirstMediaUrl('home_item3_image') !== '') ? $this->getFirstMediaUrl('home_item3_image') : null;
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

    public function sitePages() {
      return $this->hasMany(\Platform\Models\SitePage::class, 'site_id', 'id')->orderBy('order');
    }

    public function siteUsers() {
      return $this->hasMany(\App\SiteUser::class, 'site_id', 'id');
    }
}