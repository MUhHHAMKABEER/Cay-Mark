<?php
// app/Models/Subscription.php
namespace App\Models;

use App\Services\NotificationService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'package_id', 'starts_at', 'ends_at', 'status',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::created(function (Subscription $subscription) {
            try {
                $subscription->loadMissing(['user', 'package']);
                if (! $subscription->user) {
                    return;
                }
                $title = $subscription->package?->title ?? 'CayMark plan';
                (new NotificationService())->subscriptionActivated($subscription->user, $title, (int) $subscription->id);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('subscriptionActivated notification failed', [
                    'subscription_id' => $subscription->id,
                    'message' => $e->getMessage(),
                ]);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
