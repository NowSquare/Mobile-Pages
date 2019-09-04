<?php 

namespace Platform\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class Plan extends Model
{
    protected $table = 'plans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'name'
    ];

    /**
     * Append programmatically added columns.
     *
     * @var array
     */
    protected $appends = [
      'user_count'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Field mutators.
     *
     * @var array
     */
    protected $casts = [
      'limitations' => 'json',
      'meta' => 'json'
    ];

    /**
     * Date/time fields that can be used with Carbon.
     *
     * @return array
     */
    public function getDates() {
      return [];
    }

    public static function boot() {
      parent::boot();

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
          $model->created_by = auth()->user()->id;
        }
      });
    }

    /**
     * Get user count for plan.
     *
     * @return string
     */
    public function getUserCountAttribute() {
      $users = $this->users()->get();
      return $users->count();
    }

    /**
     * Get plans for billing
     */
    public static function getPlansForBilling($role = 3) {
      $account = app()->make('account');
      $active_plan_id = (auth()->check()) ? auth()->user()->plan_id : null;

      $plans = Plan::where('role', $role)->where('active', 1)->orderBy('price', 'asc')->get();

      $plans = $plans->map(function ($record) use ($account, $active_plan_id) {
        $limitations = $record->limitations;
        $limitations['id'] = $record->id;
        $limitations['amount'] = $record->price;
        $limitations['price'] = $record->name;
        $limitations['currency'] = $account->getCurrency();
        $limitations['active'] = ($active_plan_id == $record->id) ? true : false;
        if (config('general.payment_provider') == 'paddle') {
          $limitations['remote_id'] = $record->product_id_paddle;
        } elseif (config('general.payment_provider') == '2checkout') {
          $limitations['remote_id'] = $record->product_id_2checkout;
        } elseif (config('general.payment_provider') == 'stripe') {
          $limitations['remote_id'] = $record->product_id_stripe;
        } else {
          $limitations['remote_id'] = $record->remote_product_id;
        }

        return $limitations;
      });

      return $plans->toArray();
    }

    /**
     * Get plans for site display
     *
     * @return array
     */
    public static function getPlansForSite($role = 3) {
      $plans = Plan::where('role', $role)->where('active', 1)->orderBy('price', 'asc')->get();

      $plans = $plans->map(function ($record) {
        $limitations = $record->limitations;
        $limitations['price'] = $record->name;

        return $limitations;
      });

      return $plans->toArray();
    }

    /**
     * Relationships
     * -------------
     */

    public function users() {
      return $this->hasMany(\App\User::class, 'plan_id', 'id');
    }
}