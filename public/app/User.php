<?php

namespace App;

use Platform\Controllers\App;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\MediaLibrary\Models\Media;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use CommerceGuys\Intl\Currency\CurrencyRepository;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

use App\Scopes\AccountScope;

class User extends Authenticatable implements JWTSubject, HasMedia
{
    use Notifiable;
    use HasMediaTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'name', 'email', 'password',
    ];

    /**
     * Append programmatically added columns.
     *
     * @var array
     */
    protected $appends = [
      'account_active', 'avatar', 'plan_name', 'plan_limitations', 'expires_at', 'demo'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
      'email_verified_at' => 'datetime',
      'expires' => 'datetime',
      'social' => 'json',
      'settings' => 'json',
      'tags' => 'json',
      'attributes' => 'json',
      'meta' => 'json'
    ];

    public function registerMediaCollections() {
      $this
        ->addMediaCollection('avatar')
        ->singleFile();
    }

    public function registerMediaConversions(Media $media = null) {
        $this
          ->addMediaConversion('avatar')
          ->width(512)
          ->height(512)
          ->performOnCollections('avatar');
    }

    public function getJWTIdentifier() {
      return $this->getKey();
    }

    public function getJWTCustomClaims() {
      return [];
    }

    public static function boot() {
      parent::boot();

      if (auth()->check()) {
        static::addGlobalScope(new AccountScope(auth()->user()));
      }

      // On select
      static::retrieved(function ($model) {
      });

      // On update
      static::updating(function ($model) {
        if (auth()->check()) {
          $model->updated_by = auth()->user()->id;
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
        if (auth()->check()) {
        }
        $model->save();
      });

      // Deleted
      self::deleted(function ($model) {
        if (auth()->check()) {
        }
      });
    }

    /**
     * Get plan.
     *
     * @return string
     */
    public function getPlanNameAttribute() {
      if ($this->plan_id !== null) {
        return $this->plan->name;
      } else {
        return __('Trial');
      }
    }

    /**
     * Get plan limiations.
     *
     * @return string
     */
    public function getPlanLimitationsAttribute() {
      if ($this->plan_id !== null) {
        return $this->plan->limitations;
      } else {
        return [
          'sites' => 1
        ];
      }
    }

    /**
     * Get user sites
     *
     * @return json
     */
    public function getSites() {
      $sites = $this->sites;
      $sites = $sites->map(function ($record) {
        return collect($record)->only('uuid', 'name', 'short_url');
      });
      return $sites;
    }

    /**
     * Get user site by uuid
     *
     * @return json
     */
    public function getSite($uuid) {
      $site = $this->sites->where('uuid', $uuid)->first();
      $response = [];

      if (! empty($site)) {
        $response = $site->getSite();
      }
      return $response;
    }

    /**
     * Account is active.
     *
     * @return string
     */
    public function getAccountActiveAttribute() {
      if ($this->account !== null && $this->account->expires !== null) {
        return ! $this->account->expires->addDays(config('system.grace_period_days'))->isPast();
      } else {
        return true;
      }
    }

    /**
     * Expiration date in user timezone.
     *
     * @return date
     */
    public function getExpiresAtAttribute() {
      if ($this->expires !== null) {
        return $this->expires->timezone($this->getTimezone())->toDateTimeString();
      } else {
        return null;
      }
    }

    /**
     * Get avatar.
     *
     * @return string for use in <img> src
     */
    public function getAvatarAttribute() {
      if ($this->getFirstMediaUrl('avatar') !== '') {
        return request()->getSchemeAndHttpHost() . $this->getFirstMediaUrl('avatar', 'avatar');
      } else {
        return (string) \Avatar::create(strtoupper($this->name))->toBase64();
      }
    }

    /**
     * Money formatting
     */
    public function formatMoney($amount, $currency = 'USD', $formatHtml = false) {
      if ($currency == null) $currency = 'USD';
      $value = Money::{$currency}($amount);
      $currencies = new \Money\Currencies\ISOCurrencies();

      $numberFormatter = new \NumberFormatter($this->getLanguage(), \NumberFormatter::CURRENCY);
      $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $currencies);

      $amount = $moneyFormatter->format($value);
      if ($formatHtml) {
        $amount = explode($numberFormatter->getSymbol(0), $amount);
        $amount = $amount[0] . '<span class="cents">' . $numberFormatter->getSymbol(0) . $amount[1] . '</span>';
      }

      return $amount;
    }

    /**
     * Date / time formatting
     */
    public function formatDate($date, $format = 'date_medium') {
      if ($date !== null) {
        switch ($format) {
          case 'date_medium': $date = $date->timezone($this->getTimezone())->format($this->getUserDateFormat()); break;
          case 'datetime_medium': $date = $date->timezone($this->getTimezone())->format($this->getUserDateFormat() . ' ' . $this->getUserTimeFormat()); break;
          case 'friendly': $date = $date->timezone($this->getTimezone())->diffForHumans(); break;
        }
        return $date;
      } else {
        return null;
      }
    }

    /**
     * User language
     */
    public function getLanguage() {
      if ($this->language === NULL) {
        return config('system.default_language');
      } else {
        return $this->language;
      }
    }

    /**
     * User locale
     */
    public function getLocale() {
      if ($this->locale === NULL) {
        $language = $this->getLanguage();
        // If there is no default for user's language, use global default
        return config('system.language_defaults.' . $language . '.locale', config('system.default_locale'));
      } else {
        return $this->locale;
      }
    }

    /**
     * User timezone
     */
    public function getTimezone() {
      if ($this->timezone === NULL) {
        $language = $this->getLanguage();
        // If there is no default for user's language, use global default
        return config('system.language_defaults.' . $language . '.timezone', config('system.default_timezone'));
      } else {
        return $this->timezone;
      }
    }

    /**
     * User currency
     */
    public function getCurrency() {
      if ($this->currency_code === NULL) {
        $language = $this->getLanguage();
        // If there is no default for user's language, use global default
        return config('system.language_defaults.' . $language . '.currency', config('system.default_currency'));
      } else {
        return $this->currency_code;
      }
    }

    /**
     * Currency decimal points
     */
    public function getCurrencyFormat($get = null) {
      $currencyRepository = new CurrencyRepository;
      $currency = $currencyRepository->get($this->getCurrency());

      $format = [
          'numeric_code' => $currency->getNumericCode(),
          'fraction_digits' => $currency->getFractionDigits(),
          'name' => $currency->getName(),
          'symbol' => $currency->getSymbol(),
          'locale' => $currency->getLocale()
      ];

      return ($get === null) ? $format : $format[$get];
    }

    /**
     * Relationships
     * -------------
     */

    public function account() {
      return $this->belongsTo(\App\User::class, 'account_id', 'id');
    }

    public function plan() {
      return $this->belongsTo(\Platform\Models\Plan::class, 'plan_id', 'id');
    }

    public function users() {
      return $this->hasMany(\App\User::class, 'created_by', 'id');
    }

    public function sites() {
      return $this->hasMany(\Platform\Models\Site::class, 'created_by', 'id')->whereIsRoot();
    }

    public function customers() {
      return $this->hasMany(\App\Customer::class, 'created_by', 'id');
    }
}