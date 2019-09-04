<?php

namespace Platform\Models;

use Illuminate\Database\Eloquent\Model;
use Platform\Controllers\Core;
use Platform\Controllers\App;
use Spatie\MediaLibrary\Models\Media;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\Image\Manipulations;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class SitePage extends Model implements HasMedia
{
    use HasMediaTrait;

    protected $table = 'site_pages';

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

      $this
        ->addMediaCollection('earn_header_image')
        ->singleFile();

      $this
        ->addMediaCollection('rewards_header_image')
        ->singleFile();

      $this
        ->addMediaCollection('contact_header_image')
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

        $this
          ->addMediaConversion('header')
          ->width(1920)
          ->height(1280)
          ->performOnCollections('earn_header_image', 'rewards_header_image', 'contact_header_image');
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

      // On update
      static::updating(function ($model) {
        if (auth()->check()) {
          $model->updated_by = auth()->user()->id;

          // Slug
          $model->slug = Str::slug($model->title, '-') . '-' . Core\Secure::staticHash($model->id);
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
        $model->slug = Str::slug($model->title, '-') . '-' . Core\Secure::staticHash($model->id);
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
    public function getTestUrlAttribute() {
      return ($this->account != null) ? '//' . $this->account->app_host . '/site/' . $this->slug : null;
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

    public function getEarnHeaderImageAttribute() {
      return ($this->getFirstMediaUrl('earn_header_image') !== '') ? $this->getMedia('earn_header_image')[0]->getUrl('header') : null;
      //return ($this->getFirstMediaUrl('earn_header_image') !== '') ? $this->getFirstMediaUrl('earn_header_image') : null;
    }

    public function getRewardsHeaderImageAttribute() {
      return ($this->getFirstMediaUrl('rewards_header_image') !== '') ? $this->getMedia('rewards_header_image')[0]->getUrl('header') : null;
      //return ($this->getFirstMediaUrl('rewards_header_image') !== '') ? $this->getFirstMediaUrl('rewards_header_image') : null;
    }

    public function getContactHeaderImageAttribute() {
      return ($this->getFirstMediaUrl('contact_header_image') !== '') ? $this->getMedia('contact_header_image')[0]->getUrl('header') : null;
      //return ($this->getFirstMediaUrl('contact_header_image') !== '') ? $this->getFirstMediaUrl('contact_header_image') : null;
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

    public function site() {
      return $this->belongsTo(\Platform\Models\Site::class, 'site_id', 'id');
    }

    public function siteUsers() {
      return $this->hasMany(\App\SiteUser::class, 'site_id', 'id');
    }
}