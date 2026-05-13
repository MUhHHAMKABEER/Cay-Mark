<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\User;
use App\Notifications\GenericNotification;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendSubscriptionNotifications extends Command
{
    protected $signature = 'caymark:send-subscription-notifications';

    protected $description = 'Notify users when a subscription is ending soon or has recently ended (deduped per subscription).';

    public function handle(): int
    {
        $ns = new NotificationService();

        $this->notifyEndingSoon($ns);
        $this->notifyRecentlyEnded($ns);

        return self::SUCCESS;
    }

    protected function notifyEndingSoon(NotificationService $ns): void
    {
        $subscriptions = Subscription::query()
            ->whereNotNull('ends_at')
            ->where('ends_at', '>', now())
            ->where('ends_at', '<=', now()->addDays(7))
            ->with(['user', 'package'])
            ->get();

        foreach ($subscriptions as $subscription) {
            /** @var User|null $user */
            $user = $subscription->user;
            if (! $user) {
                continue;
            }
            if ($this->alreadyNotified($user, 'subscription_ending_soon', (int) $subscription->id)) {
                continue;
            }
            $title = $subscription->package?->title ?? 'CayMark plan';
            $ends = $subscription->ends_at?->timezone(config('app.timezone'))->format('F j, Y g:i A') ?? '';
            $ns->subscriptionEndingSoon($user, $title, $ends, (int) $subscription->id);
        }
    }

    protected function notifyRecentlyEnded(NotificationService $ns): void
    {
        $subscriptions = Subscription::query()
            ->whereNotNull('ends_at')
            ->where('ends_at', '<', now())
            ->where('ends_at', '>=', now()->subDays(3))
            ->with(['user', 'package'])
            ->get();

        foreach ($subscriptions as $subscription) {
            /** @var User|null $user */
            $user = $subscription->user;
            if (! $user) {
                continue;
            }
            if ($this->alreadyNotified($user, 'subscription_ended', (int) $subscription->id)) {
                continue;
            }
            $title = $subscription->package?->title ?? 'CayMark plan';
            $ns->subscriptionEnded($user, $title, (int) $subscription->id);
        }
    }

    protected function alreadyNotified(User $user, string $catalogType, int $subscriptionId): bool
    {
        return $user->notifications()
            ->where('type', GenericNotification::class)
            ->where('data->type', $catalogType)
            ->where('data->subscription_id', $subscriptionId)
            ->exists();
    }
}
